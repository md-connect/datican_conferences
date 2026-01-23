<?php

namespace App\Http\Controllers;

use App\Models\ReviewAssignment;
use App\Models\Paper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

        /**
     * Display a listing of the user's reviews
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get paginated reviews for the table
        $reviews = ReviewAssignment::with(['paper' => function($query) {
                $query->select('id', 'anonymous_id', 'title', 'topic_area', 'submission_type');
            }])
            ->where('reviewer_id', $user->id)
            ->whereIn('status', ['pending', 'under_review', 'in_progress', 'completed'])
            ->orderByRaw("FIELD(status, 'pending', 'under_review', 'in_progress', 'completed', 'declined')")
            ->orderBy('deadline')
            ->paginate(10);
        
        // Get ALL reviews for stats calculation (not paginated)
        $allReviews = ReviewAssignment::where('reviewer_id', $user->id)
            ->whereIn('status', ['pending', 'under_review', 'in_progress', 'completed'])
            ->get();
        
        // Count overdue reviews from all reviews
        $overdueCount = $allReviews->where('deadline', '<', now())
            ->whereNotIn('status', ['completed', 'declined'])
            ->count();
        
        // Calculate stats from all reviews
        $reviewStats = [
            'total' => $allReviews->count(),
            'pending' => $allReviews->where('status', 'pending')->count(),
            'in_progress' => $allReviews->whereIn('status', ['under_review', 'in_progress'])->count(),
            'completed' => $allReviews->where('status', 'completed')->count(),
            'overdue' => $overdueCount,
        ];
        
        return view('reviews.my', compact('reviews', 'overdueCount', 'reviewStats'));
    }

    
        /**
     * Show the form for creating/editing a review
     */
    public function create(Request $request)
    {
        $paperId = $request->input('paper');
        
        if (!$paperId) {
            abort(404, 'Paper not specified.');
        }
        
        // Find the review assignment for this paper and reviewer
        $review = ReviewAssignment::where('paper_id', $paperId)
            ->where('reviewer_id', Auth::id())
            ->firstOrFail();
        
        if ($review->status === 'declined') {
            return redirect()->route('reviews.my')
                ->with('error', 'You have declined this review assignment.');
        }
        
        // Load the paper
        $paper = $review->paper;
        
        // Update status to in_progress if it's under_review
        if ($review->status === 'under_review') {
            $review->update(['status' => 'in_progress']);
        }
        
        return view('reviews.create', compact('paper', 'review'));
    }
    

    /**
     * Store a newly created review (first submission)
     */
    public function store(Request $request)
    {
        $request->validate([
            'paper_id' => 'required|exists:papers,id',
            'overall_score' => 'required|integer|min:1|max:5',
            'recommendation' => 'required|in:strong_accept,accept,weak_accept,borderline,weak_reject,reject,strong_reject',
            'comments_author' => 'required|string|min:50',
            'comments_chair' => 'nullable|string',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'suggestions' => 'nullable|string',
            'summary' => 'nullable|string',
            'confidence' => 'nullable|in:expert,familiar,passing,knowledgeable',
            'scores' => 'nullable|array',
            'save_draft' => 'nullable|boolean',
        ]);
        
        // Find the review assignment
        $review = ReviewAssignment::where('paper_id', $request->paper_id)
            ->where('reviewer_id', Auth::id())
            ->firstOrFail();
        
        // Prepare update data
        $updateData = [
            'overall_score' => $request->overall_score,
            'recommendation' => $request->recommendation,
            'comments_author' => $request->comments_author,
            'comments_chair' => $request->comments_chair,
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'suggestions' => $request->suggestions,
            'summary' => $request->summary,
            'confidence' => $request->confidence,
            'scores' => $request->scores ? json_encode($request->scores) : null,
        ];
        
        // Determine if it's a draft or final submission
        if ($request->has('save_draft') && $request->save_draft) {
            $updateData['status'] = 'in_progress';
            $updateData['submitted_at'] = null;
            $message = 'Review saved as draft.';
        } else {
            $updateData['status'] = 'completed';
            $updateData['submitted_at'] = now();
            $message = 'Review submitted successfully!';
        }
        
        // Update the review assignment
        $review->update($updateData);
        
        return redirect()->route('reviews.my')
            ->with('success', $message);
    }

    /**
     * Display the specified review
     */
    public function show(ReviewAssignment $review)
    {
        // Check authorization
        if ($review->reviewer_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $review->load(['paper', 'reviewer']);
        
        // Decode scores if they exist
        if ($review->scores) {
            $review->scores = json_decode($review->scores, true);
        }
        
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing a review
     */
    public function edit(ReviewAssignment $review)
    {
        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($review->status === 'completed') {
            return redirect()->route('reviews.show', $review)
                ->with('error', 'Completed reviews cannot be edited.');
        }
        
        // Start review if it's under_review
        if ($review->status === 'under_review') {
            $review->update(['status' => 'in_progress']);
        }
        
        $paper = $review->paper;
        
        
        return view('reviews.create', compact('paper', 'review'));
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, ReviewAssignment $review)
    {
        // Check authorization
        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'overall_score' => 'required|integer|min:1|max:5',
            'recommendation' => 'required|in:strong_accept,accept,weak_accept,borderline,weak_reject,reject,strong_reject',
            'comments_author' => 'required|string|min:50',
            'comments_chair' => 'nullable|string',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'suggestions' => 'nullable|string',
            'summary' => 'nullable|string',
            'confidence' => 'nullable|in:expert,familiar,passing,knowledgeable',
            'scores' => 'nullable|array',
            'save_draft' => 'nullable|boolean',
        ]);
        
        // Prepare update data
        $updateData = [
            'overall_score' => $request->overall_score,
            'recommendation' => $request->recommendation,
            'comments_author' => $request->comments_author,
            'comments_chair' => $request->comments_chair,
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'suggestions' => $request->suggestions,
            'summary' => $request->summary,
            'confidence' => $request->confidence,
            'scores' => $request->scores ? json_encode($request->scores) : null,
        ];
        
        // Determine if it's a draft or final submission
        if ($request->has('save_draft') && $request->save_draft) {
            $updateData['status'] = 'in_progress';
            $updateData['submitted_at'] = null;
            $message = 'Review saved as draft.';
        } else {
            $updateData['status'] = 'completed';
            $updateData['submitted_at'] = now();
            $message = 'Review updated successfully!';
        }
        
        // Update the review assignment
        $review->update($updateData);
        
        return redirect()->route('reviews.my')
            ->with('success', $message);
    }

    /**
     * Accept a review assignment
     */
    public function accept(ReviewAssignment $review)
    {
        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $review->update([
        'status' => 'under_review',
        'started_at' => now()
    ]);
        
        return redirect()->route('reviews.edit', $review)
            ->with('success', 'Review assignment accepted. You can now start reviewing.');
    }

    /**
     * Decline a review assignment
     */
    public function decline(ReviewAssignment $review)
    {
        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $review->update(['status' => 'declined']);
        
        return redirect()->route('reviews.my')
            ->with('success', 'Review assignment declined.');
    }

    /**
     * Start a review (alternative method for direct paper review)
     */
    public function startReview(Paper $paper)
    {
        // Check if user is assigned to review this paper
        $review = ReviewAssignment::where('paper_id', $paper->id)
            ->where('reviewer_id', Auth::id())
            ->first();
        
        if (!$review) {
            abort(403, 'You are not assigned to review this paper.');
        }
        
        return $this->edit($review);
    }

    /**
     * Get review statistics for dashboard
     */
    public function stats()
    {
        $user = auth()->user();
        
        $stats = ReviewAssignment::where('reviewer_id', $user->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status IN ("under_review", "in_progress") THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "declined" THEN 1 ELSE 0 END) as declined
            ')
            ->first();
        
        return response()->json($stats);
    }
}