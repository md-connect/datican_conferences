<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use App\Models\User;
use App\Models\ReviewAssignment;
use App\Services\AssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AssignmentController extends Controller
{
    protected $assignmentService;
    
    public function __construct(AssignmentService $assignmentService)
    {
        $this->middleware('auth');
        // Change this line to allow both admins AND chairs
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->is_admin && !auth()->user()->is_chair) {
                abort(403, 'Unauthorized. Admin or chair privileges required.');
            }
            return $next($request);
        });
        $this->assignmentService = $assignmentService;
    }

    /**
     * Display assignment management interface
     */
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $tab = $request->input('tab', 'papers');
        $paperId = $request->input('paper');
        
        // Papers needing assignments
        $papersQuery = Paper::where('conference_year', $year)
            ->whereIn('status', ['submitted', 'under_review']);
        
        // For papers tab, only show papers that need reviewers
        if ($tab === 'papers') {
            $papersQuery->withCount(['reviews as pending_reviews_count' => function($query) {
                $query->whereIn('status', ['pending', 'accepted']);
            }])
            ->having('pending_reviews_count', '<', 3);
        }
        
        $papers = $papersQuery->with(['reviews' => function($query) {
                $query->whereIn('status', ['pending', 'accepted'])
                      ->with('reviewer');
            }, 'bids'])
            ->orderBy('submitted_at')
            ->get();
        
        // Available reviewers
        $reviewers = User::where('is_reviewer', true)
            ->with(['expertise', 'reviewAssignments' => function($q) use ($year) {
                $q->whereHas('paper', function($q2) use ($year) {
                    $q2->where('conference_year', $year);
                })->whereIn('status', ['pending', 'accepted']);
            }])
            ->get();
        
        // Statistics
        $totalPapers = Paper::where('conference_year', $year)
            ->whereIn('status', ['submitted', 'under_review'])
            ->count();
            
        $papersNeedingAssignments = Paper::where('conference_year', $year)
            ->whereIn('status', ['submitted', 'under_review'])
            ->withCount(['reviews as pending_reviews_count' => function($query) {
                $query->whereIn('status', ['pending', 'accepted']);
            }])
            ->having('pending_reviews_count', '<', 3)
            ->count();
        
        $totalAssignedReviews = ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })
            ->whereIn('status', ['pending', 'accepted'])
            ->count();

        $activeReviewers = User::where('is_reviewer', true)
            ->whereHas('reviewAssignments', function($q) use ($year) {
                $q->whereHas('paper', function($q2) use ($year) {
                    $q2->where('conference_year', $year);
                })->whereIn('status', ['pending', 'accepted']);
            })
            ->count();
        
        $avgLoad = $activeReviewers > 0 ? round($totalAssignedReviews / $activeReviewers, 1) : 0;
        
        $maxPossibleReviews = $totalPapers * 3;
        $coverage = $maxPossibleReviews > 0 ? round(($totalAssignedReviews / $maxPossibleReviews) * 100, 1) : 0;
        
        $stats = [
            'papers' => $papersNeedingAssignments,
            'reviewers' => $reviewers->count(),
            'avg_load' => $avgLoad,
            'coverage' => $coverage
        ];
        
        return view('assignments.index', compact('papers', 'reviewers', 'stats'));
    }

    /**
     * Show form to assign reviewers to a specific paper
     */
    public function assign(Request $request, Paper $paper)
    {
        $year = $request->input('year', date('Y'));
        $tab = $request->input('tab', 'papers');
        
        // Get suggested reviewers - fix this to return proper structure
        $suggestedReviewers = $this->assignmentService->suggestReviewers($paper, 10);
        
        // Debug: Check what's being returned
        // dd($suggestedReviewers);
        
        // Get all available reviewers - ONLY users marked as reviewers
        $reviewers = User::where('is_admin', false)
            ->where('is_reviewer', true) // ADD THIS LINE to only get reviewers
            ->where('id', '!=', Auth::id()) // Exclude current user (admin)
            ->with(['expertise', 'reviewAssignments' => function($q) use ($year) {
                $q->whereHas('paper', function($q2) use ($year) {
                    $q2->where('conference_year', $year);
                })->whereIn('status', ['pending', 'accepted']);
            }])
            ->get();
        
        return view('assignments.assign', compact('paper', 'suggestedReviewers', 'reviewers', 'year', 'tab'));
    }

    /**
     * Manual assignment - Process form submission
     */
    public function manualAssign(Request $request)
    {
        $request->validate([
            'paper_id' => 'required|exists:papers,id',
            'reviewer_ids' => 'required|array',
            'reviewer_ids.*' => 'exists:users,id'
        ]);
        
        $paper = Paper::findOrFail($request->paper_id);
        $assignedCount = 0;
        
        foreach ($request->reviewer_ids as $reviewerId) {
            // Check for conflicts
            if ($paper->authors()->where('users.id', $reviewerId)->exists()) {
                Session::flash('warning', 'Reviewer ' . $reviewerId . ' is an author of this paper - skipped.');
                continue;
            }
            
            // Check if assignment already exists
            $existing = ReviewAssignment::where('paper_id', $paper->id)
                ->where('reviewer_id', $reviewerId)
                ->first();
                
            if ($existing) {
                // Update existing assignment
                $existing->update([
                    'status' => 'pending',
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                    'deadline' => now()->addWeeks(2),
                    'updated_at' => now()
                ]);
                $assignedCount++;
            } else {
                // Create new assignment
                ReviewAssignment::create([
                    'paper_id' => $paper->id,
                    'reviewer_id' => $reviewerId,
                    'assigned_by' => Auth::id(),
                    'status' => 'pending',
                    'assigned_at' => now(),
                    'deadline' => now()->addWeeks(2),
                    'notes' => 'Manually assigned by chair'
                ]);
                $assignedCount++;
            }
        }
        
        // Update paper status if this is the first assignment
        if ($assignedCount > 0 && $paper->status == 'submitted') {
            $paper->update(['status' => 'under_review']);
        }
        
        $year = $request->input('year', date('Y'));
        $tab = $request->input('tab', 'papers');
        
        Session::flash('success', "{$assignedCount} reviewer(s) assigned successfully!");
        return redirect()->route('assignments.index', ['year' => $year, 'tab' => $tab]);
    }

    /**
     * Run automatic assignment
     */
    public function autoAssign(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $config = [
            'min_reviews_per_paper' => $request->input('min_reviews', 3),
            'max_reviews_per_paper' => $request->input('max_reviews', 5),
            'max_papers_per_reviewer' => $request->input('max_papers', 10)
        ];
        
        try {
            $count = $this->assignmentService->autoAssign($year, $config);
            
            Session::flash('success', "Automatic assignment completed! {$count} assignments created.");
            return redirect()->route('assignments.index', ['year' => $year]);
        } catch (\Exception $e) {
            Session::flash('error', 'Error during assignment: ' . $e->getMessage());
            return redirect()->route('assignments.index', ['year' => $year]);
        }
    }

    /**
     * Get reviewer suggestions for a paper
     */
    public function suggest(Request $request, Paper $paper)
    {
        $year = $request->input('year', date('Y'));
        $tab = $request->input('tab', 'papers');
        
        $suggestedReviewers = $this->assignmentService->suggestReviewers($paper, 10);
        
        return view('assignments.suggest', compact('paper', 'suggestedReviewers', 'year', 'tab'));
    }

    /**
     * Remove an assignment
     */
    public function destroy(Request $request, ReviewAssignment $assignment)
    {
        $year = $request->input('year', date('Y'));
        $tab = $request->input('tab', 'assignments');
        
        $assignment->delete();
        
        Session::flash('success', 'Assignment removed successfully!');
        return redirect()->route('assignments.index', ['year' => $year, 'tab' => $tab]);
    }
    
    /**
     * Show configuration page
     */
    public function config(Request $request)
    {
        return view('assignments.config');
    }
}