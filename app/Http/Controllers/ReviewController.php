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
                $query->select('id', 'anonymous_id', 'title', 'topic_area', 'submission_type', 'status', 'needs_revision', 'revision_submitted_at', 'version');
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
        
        // Load the paper with all necessary relationships
        $paper = $review->paper()->with(['authors', 'reviews' => function($query) {
                $query->where('status', 'completed')->with('reviewer');
            }])->first();
        
        // Check if this is a revision review
        $isRevision = $paper->needs_revision === false && 
                      $paper->revision_submitted_at !== null &&
                      $paper->status === 'under_review';
        
        // Update status to in_progress if it's under_review
        if ($review->status === 'under_review') {
            $review->update(['status' => 'in_progress']);
        }
        
        // Get previous review versions if any
        $previousReviews = ReviewAssignment::where('paper_id', $paper->id)
            ->where('reviewer_id', Auth::id())
            ->where('status', 'completed')
            ->where('id', '!=', $review->id)
            ->latest()
            ->get();
        
        // Get revision suggestions from previous reviews if this is a revision
        $previousRevisionSuggestions = [];
        if ($isRevision) {
            $previousRevisionSuggestions = ReviewAssignment::where('paper_id', $paper->id)
                ->where('status', 'completed')
                ->whereNotNull('revision_suggestions')
                ->latest()
                ->get()
                ->pluck('revision_suggestions')
                ->toArray();
        }
        
        return view('reviews.create', compact('paper', 'review', 'isRevision', 'previousReviews', 'previousRevisionSuggestions'));
    }

    /**
     * Store a newly created review (first submission)
     */
    public function store(Request $request)
    {
        // Determine if it's a draft based on which button was clicked
        $isDraft = $request->has('save_draft') && $request->save_draft == '1';

        $rules = [
            'paper_id' => 'required|exists:papers,id',
            'is_revision_review' => 'nullable|boolean',
            'original_review_id' => 'nullable|exists:review_assignments,id',
        ];

        if (!$isDraft) {
            $rules += [
                'overall_score' => 'required|integer|min:1|max:5',
                'recommendation' => 'required|in:strong_accept,accept,weak_accept,borderline,weak_reject,reject,strong_reject,minor_revisions,major_revisions',
                'comments_author' => 'required|string|min:50',
                'revision_suggestions' => 'nullable|required_if:recommendation,minor_revisions,major_revisions|string|max:2000',
            ];
        }

        $request->validate($rules);

        // Find the review assignment
        $review = ReviewAssignment::where('paper_id', $request->paper_id)
            ->where('reviewer_id', Auth::id())
            ->firstOrFail();
        
        // Get the paper
        $paper = Paper::find($request->paper_id);
        
        // Prepare update data
        $updateData = [];
        
        if ($request->has('overall_score')) {
            $updateData['overall_score'] = $request->overall_score;
        }
        
        if ($request->has('recommendation')) {
            $updateData['recommendation'] = $request->recommendation;
        }
        
        if ($request->has('comments_author')) {
            $updateData['comments_author'] = $request->comments_author;
        }
        
        $optionalFields = ['comments_chair', 'strengths', 'weaknesses', 'suggestions', 'summary', 'confidence', 'revision_suggestions'];
        foreach ($optionalFields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }
        
        if ($request->has('scores')) {
            $updateData['scores'] = $request->scores ? json_encode($request->scores) : null;
        }
        
        // Add revision tracking
        if ($request->is_revision_review) {
            $updateData['is_revision_review'] = true;
            $updateData['original_review_id'] = $request->original_review_id;
            $updateData['paper_version'] = $paper->version ?? 1;
        }
        
        // Determine if it's a draft or final submission
        if ($isDraft) {
            $updateData['status'] = 'in_progress';
            $updateData['submitted_at'] = null;
            $message = 'Review saved as draft.';
        } else {
            $updateData['status'] = 'completed';
            $updateData['submitted_at'] = now();
            
            // Check if this review recommends revisions
            if (in_array($request->recommendation, ['minor_revisions', 'major_revisions'])) {
                $paper->updateRevisionRecommendationStatus();
            }
            
            // Check if this completes all reviews for the paper
            $paper->checkAllReviewsCompleted();
            
            $message = 'Review submitted successfully!';
            
            // Add revision-specific message
            if ($request->is_revision_review) {
                $message = 'Revision review submitted successfully!';
            } elseif (in_array($request->recommendation, ['minor_revisions', 'major_revisions'])) {
                $message = 'Review submitted with revision recommendations. The chair will review these suggestions.';
            }
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
        
        $review->load(['paper' => function($query) {
                $query->with(['authors', 'reviews' => function($q) {
                    $q->where('status', 'completed');
                }]);
            }, 'reviewer']);
        
        // Decode scores if they exist
        if ($review->scores) {
            $review->scores = json_decode($review->scores, true);
        }
        
        // Get the paper's revision history
        $paper = $review->paper;
        $hasRevisions = $paper->revision_submitted_at !== null;
        
        // Get original review if this is a revision review
        $originalReview = null;
        if ($review->original_review_id) {
            $originalReview = ReviewAssignment::with(['reviewer', 'paper'])
                ->find($review->original_review_id);
        }
        
        // Get all reviews for this paper to show revision history
        $allReviews = ReviewAssignment::where('paper_id', $paper->id)
            ->where('status', 'completed')
            ->with('reviewer')
            ->orderBy('paper_version')
            ->orderBy('created_at')
            ->get();
        
        return view('reviews.show', compact('review', 'hasRevisions', 'originalReview', 'allReviews'));
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
        
        $paper = $review->paper()->with(['authors', 'reviews' => function($query) {
                $query->where('status', 'completed')->with('reviewer');
            }])->first();
        
        // Check if this is a revision review
        $isRevision = $paper->needs_revision === false && 
                      $paper->revision_submitted_at !== null &&
                      $paper->status === 'under_review';
        
        // Get previous review versions if any
        $previousReviews = ReviewAssignment::where('paper_id', $paper->id)
            ->where('reviewer_id', Auth::id())
            ->where('status', 'completed')
            ->where('id', '!=', $review->id)
            ->latest()
            ->get();
        
        // Get revision suggestions from previous reviews if this is a revision
        $previousRevisionSuggestions = [];
        if ($isRevision) {
            $previousRevisionSuggestions = ReviewAssignment::where('paper_id', $paper->id)
                ->where('status', 'completed')
                ->whereNotNull('revision_suggestions')
                ->latest()
                ->get()
                ->pluck('revision_suggestions')
                ->toArray();
        }
        
        return view('reviews.create', compact('paper', 'review', 'isRevision', 'previousReviews', 'previousRevisionSuggestions'));
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
        
        // Determine if it's a draft based on which button was clicked
        $isDraft = $request->has('save_draft') && $request->save_draft == '1';

        // Log for debugging
        \Log::info('Update request', [
            'review_id' => $review->id,
            'has_save_draft' => $request->has('save_draft'),
            'save_draft_value' => $request->input('save_draft'),
            'has_submit' => $request->has('submit_review'),
            'isDraft' => $isDraft,
            'all_input' => $request->all()
        ]);

        $rules = [
            'is_revision_review' => 'nullable|boolean',
            'original_review_id' => 'nullable|exists:review_assignments,id',
        ];

        if (!$isDraft) {
            $rules += [
                'overall_score' => 'required|integer|min:1|max:5',
                'recommendation' => 'required|in:strong_accept,accept,weak_accept,borderline,weak_reject,reject,strong_reject,minor_revisions,major_revisions',
                'comments_author' => 'required|string|min:50',
                'revision_suggestions' => 'nullable|required_if:recommendation,minor_revisions,major_revisions|string|max:2000',
            ];
        }

        $request->validate($rules);
        
        $paper = Paper::find($review->paper_id);
        
        // Prepare update data - only include fields that were actually submitted
        $updateData = [];
        
        // Only include score fields if they were submitted (for drafts, they might be empty)
        if ($request->has('overall_score')) {
            $updateData['overall_score'] = $request->overall_score;
        }
        
        if ($request->has('recommendation')) {
            $updateData['recommendation'] = $request->recommendation;
        }
        
        if ($request->has('comments_author')) {
            $updateData['comments_author'] = $request->comments_author;
        }
        
        // Always include these fields if present (they can be empty for drafts)
        $optionalFields = ['comments_chair', 'strengths', 'weaknesses', 'suggestions', 'summary', 'confidence', 'revision_suggestions'];
        foreach ($optionalFields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }
        
        // Handle scores JSON
        if ($request->has('scores')) {
            $updateData['scores'] = $request->scores ? json_encode($request->scores) : null;
        }
        
        // Add revision tracking
        if ($request->is_revision_review) {
            $updateData['is_revision_review'] = true;
            $updateData['original_review_id'] = $request->original_review_id;
            $updateData['paper_version'] = $paper->version ?? 1;
        }
        
        // Determine if it's a draft or final submission based on the button clicked
        if ($isDraft) {
            $updateData['status'] = 'in_progress';
            $updateData['submitted_at'] = null;
            $message = 'Review saved as draft.';
        } else {
            $updateData['status'] = 'completed';
            $updateData['submitted_at'] = now();
            
            // Check if this review recommends revisions
            if (in_array($request->recommendation, ['minor_revisions', 'major_revisions'])) {
                $paper->updateRevisionRecommendationStatus();
            }
            
            // Check if this completes all reviews for the paper
            $paper->checkAllReviewsCompleted();
            
            $message = 'Review submitted successfully!';
            
            // Add revision-specific message
            if ($request->is_revision_review) {
                $message = 'Revision review submitted successfully!';
            } elseif (in_array($request->recommendation, ['minor_revisions', 'major_revisions'])) {
                $message = 'Review submitted with revision recommendations. The chair will review these suggestions.';
            }
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

    /**
     * Check if all reviews for a paper are completed
     */
    private function checkPaperReviewStatus(Paper $paper)
    {
        $totalAssignments = ReviewAssignment::where('paper_id', $paper->id)
            ->where('status', '!=', 'declined')
            ->count();
        
        $completedAssignments = ReviewAssignment::where('paper_id', $paper->id)
            ->where('status', 'completed')
            ->count();
        
        // If all assigned reviews are completed, update paper status
        if ($totalAssignments > 0 && $totalAssignments === $completedAssignments) {
            // Only update if paper is under review
            if ($paper->status === 'under_review') {
                // You might want to notify the chair that all reviews are complete
                Log::info('All reviews completed for paper', [
                    'paper_id' => $paper->id,
                    'paper_title' => $paper->title
                ]);
                
                // Optionally, you can update a flag on the paper
                $paper->update(['all_reviews_completed' => true]);
            }
        }
    }

    /**
     * Get revision history for a paper (for reviewers)
     */
    public function revisionHistory(Paper $paper)
    {
        // Check if user is assigned to review this paper
        $review = ReviewAssignment::where('paper_id', $paper->id)
            ->where('reviewer_id', Auth::id())
            ->exists();
        
        if (!$review && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        $paper->load(['revisions' => function($query) {
            $query->latest();
        }]);
        
        $reviews = ReviewAssignment::where('paper_id', $paper->id)
            ->where('status', 'completed')
            ->with('reviewer')
            ->latest()
            ->get()
            ->groupBy('paper_version');
        
        return view('reviews.revision-history', compact('paper', 'reviews'));
    }

    /**
     * Download paper file for review
     */
    public function downloadPaper(Paper $paper)
    {
        // Check if user is assigned to review this paper
        $review = ReviewAssignment::where('paper_id', $paper->id)
            ->where('reviewer_id', Auth::id())
            ->exists();
        
        if (!$review && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!\Storage::disk('public')->exists($paper->file_path)) {
            abort(404, 'File not found.');
        }
        
        return \Storage::disk('public')->download(
            $paper->file_path,
            $paper->anonymous_id . '_' . $paper->file_name
        );
    }
}