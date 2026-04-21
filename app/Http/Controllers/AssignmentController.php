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
    
    // ========== FIXED: Papers needing assignments ==========
    // A paper needs reviewers ONLY if:
    // 1. It has LESS THAN 2 active reviews (pending/under_review/in_progress)
    // 2. AND it has LESS THAN 2 completed reviews (not fully reviewed)
    $papersNeedingAssignments = Paper::where('conference_year', $year)
        ->whereIn('status', ['submitted', 'abstract_submitted', 'under_review'])
        ->where(function($query) {
            // Less than 2 active reviews
            $query->whereDoesntHave('reviews', function($q) {
                $q->whereIn('status', ['pending', 'under_review', 'in_progress']);
            }, '>=', 2)
            // AND less than 2 completed reviews
            ->whereDoesntHave('reviews', function($q) {
                $q->where('status', 'completed');
            }, '>=', 2);
        })
        ->count();
    
    // For papers tab, show papers that need reviewers
    $papers = collect(); // Default empty collection
    if ($tab === 'papers') {
        $papers = Paper::where('conference_year', $year)
            ->whereIn('status', ['submitted', 'abstract_submitted', 'under_review'])
            ->where(function($query) {
                $query->whereDoesntHave('reviews', function($q) {
                    $q->whereIn('status', ['pending', 'under_review', 'in_progress']);
                }, '>=', 2)
                ->whereDoesntHave('reviews', function($q) {
                    $q->where('status', 'completed');
                }, '>=', 2);
            })
            ->with(['reviews' => function($query) {
                $query->whereIn('status', ['pending', 'under_review', 'in_progress', 'declined', 'completed'])
                    ->with('reviewer');
            }, 'bids'])
            ->orderBy('submitted_at')
            ->get();
    }
    
    // ========== STATISTICS ==========
    
    // Total active reviews (pending, under_review, in_progress)
    $totalActiveReviews = ReviewAssignment::whereHas('paper', function($q) use ($year) {
            $q->where('conference_year', $year);
        })
        ->whereIn('status', ['pending', 'under_review', 'in_progress'])
        ->count();

    // Total completed reviews
    $totalCompletedReviews = ReviewAssignment::whereHas('paper', function($q) use ($year) {
            $q->where('conference_year', $year);
        })
        ->where('status', 'completed')
        ->count();

    // All reviewers
    $totalReviewers = User::where('is_reviewer', true)->count();

    // Reviewers with active assignments (currently working)
    $activeReviewers = User::where('is_reviewer', true)
        ->whereHas('reviewAssignments', function($q) use ($year) {
            $q->whereHas('paper', function($q2) use ($year) {
                $q2->where('conference_year', $year);
            })->whereIn('status', ['pending', 'under_review', 'in_progress']);
        })
        ->count();

    // Reviewers without any active assignments (available)
    $availableReviewers = User::where('is_reviewer', true)
        ->whereDoesntHave('reviewAssignments', function($q) use ($year) {
            $q->whereHas('paper', function($q2) use ($year) {
                $q2->where('conference_year', $year);
            })->whereIn('status', ['pending', 'under_review', 'in_progress']);
        })
        ->count();

    // Average load calculation
    $avgLoad = $activeReviewers > 0 ? round($totalActiveReviews / $activeReviewers, 1) : 0;

    // Required reviews total (each paper needing assignments needs 2 reviews)
    $requiredReviews = $papersNeedingAssignments * 2;

    // Coverage percentage (avoid division by zero)
    $coverage = $requiredReviews > 0 ? round(($totalActiveReviews / $requiredReviews) * 100, 1) : 0;

    // Completion rate (completed vs total assigned that are not declined)
    $totalAssigned = ReviewAssignment::whereHas('paper', function($q) use ($year) {
            $q->where('conference_year', $year);
        })
        ->whereIn('status', ['pending', 'under_review', 'in_progress', 'completed'])
        ->count();
        
    $completionRate = $totalAssigned > 0 ? round(($totalCompletedReviews / $totalAssigned) * 100, 1) : 0;

    $stats = [
        'papers' => $papersNeedingAssignments,
        'reviewers' => $totalReviewers,
        'active_reviewers' => $activeReviewers,
        'available_reviewers' => $availableReviewers,
        'avg_load' => $avgLoad,
        'coverage' => $coverage,
        'total_active_reviews' => $totalActiveReviews,
        'total_completed_reviews' => $totalCompletedReviews,
        'completion_rate' => $completionRate,
    ];
    
    // Get all assignments for the Assignments tab
    $assignments = null;
    if ($tab === 'assignments') {
        $assignmentsQuery = ReviewAssignment::with(['paper', 'reviewer'])
            ->whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            });
        
        if ($request->filled('status')) {
            $assignmentsQuery->where('status', $request->status);
        }
        
        if ($request->filled('reviewer_id')) {
            $assignmentsQuery->where('reviewer_id', $request->reviewer_id);
        }
        
        $assignments = $assignmentsQuery->orderBy('created_at', 'desc')->paginate(20);
    } else {
        $assignments = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
    }
    
    // Get reviewers for the Reviewers tab
    $reviewers = User::where('is_reviewer', true)
        ->with(['expertise', 'reviewAssignments' => function($q) use ($year) {
            $q->whereHas('paper', function($q2) use ($year) {
                $q2->where('conference_year', $year);
            })->whereIn('status', ['pending', 'under_review', 'in_progress']);
        }])
        ->get();
    
    return view('assignments.index', compact('papers', 'reviewers', 'stats', 'assignments', 'year'));
}

    /**
     * Reset a paper for reassignment after all reviews declined
     */
    public function resetForReassignment(Paper $paper)
    {
        // Check if all reviews are declined
        $activeReviews = $paper->reviews()
            ->whereIn('status', ['pending', 'under_review', 'in_progress'])
            ->count();
        
        $declinedReviews = $paper->reviews()
            ->where('status', 'declined')
            ->count();
        
        $totalReviews = $paper->reviews()->count();
        
        // Only allow reset if there are no active reviews and at least one declined
        if ($activeReviews > 0 || $declinedReviews === 0) {
            return back()->with('error', 'Paper cannot be reset at this time.');
        }
        
        // Update paper status to show it needs reviewers
        $paper->update([
            'status' => $paper->submission_type === 'abstract_only' ? 'abstract_submitted' : 'submitted',
            'needs_reviewer' => true
        ]);
        
        // Log the reset
        \Log::info('Paper reset for reassignment', [
            'paper_id' => $paper->id,
            'paper_title' => $paper->title,
            'declined_count' => $declinedReviews,
            'total_reviews' => $totalReviews
        ]);
        
        return redirect()->route('assignments.index')
            ->with('success', 'Paper has been reset and is now available for new reviewer assignments.');
    }
    /**
     * Show form to assign reviewers to a specific paper
     */
    public function assign(Request $request, Paper $paper)
    {
        $year = $request->input('year', date('Y'));
        $tab = $request->input('tab', 'papers');
        
        // Get suggested reviewers
        $suggestedReviewers = $this->assignmentService->suggestReviewers($paper, 10);
        
        // Get all available reviewers - ONLY users marked as reviewers
        $reviewers = User::where('is_reviewer', true)
            ->where('id', '!=', Auth::id())
            ->with(['expertise', 'reviewAssignments' => function($q) use ($year) {
                $q->whereHas('paper', function($q2) use ($year) {
                    $q2->where('conference_year', $year);
                })->whereIn('status', ['pending', 'under_review', 'in_progress']);
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
                // Create new assignment - MAKE SURE THIS PART WORKS
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
        // This should work for abstract_submitted papers
        if ($assignedCount > 0 && in_array($paper->status, ['submitted', 'abstract_submitted'])) {
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

    /**
     * Display all assignments (for chair/admin)
     */
    public function allAssignments(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $status = $request->input('status');
        $reviewerId = $request->input('reviewer_id');
        
        // Build query
        $query = ReviewAssignment::with(['paper', 'reviewer', 'assignedBy'])
            ->whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            });
        
        // Apply filters
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        
        if ($reviewerId) {
            $query->where('reviewer_id', $reviewerId);
        }
        
        // Get paginated results
        $assignments = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get reviewers for filter dropdown
        $reviewers = User::where('is_reviewer', true)
            ->orderBy('first_name')
            ->get();
        
        // Statistics
        $stats = [
            'total' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->count(),
            'pending' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->where('status', 'pending')->count(),
            'under_review' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->where('status', 'under_review')->count(),
            'in_progress' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->where('status', 'in_progress')->count(),
            'completed' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->where('status', 'completed')->count(),
            'declined' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->where('status', 'declined')->count(),
        ];
        
        return view('assignments.all', compact('assignments', 'reviewers', 'stats', 'year', 'status', 'reviewerId'));
    }

    /**
     * Export all assignments to CSV
     */
    public function exportAssignments(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $status = $request->input('status');
        $reviewerId = $request->input('reviewer_id');
        
        // Build query
        $query = ReviewAssignment::with(['paper', 'reviewer', 'assignedBy'])
            ->whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            });
        
        // Apply filters
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        
        if ($reviewerId) {
            $query->where('reviewer_id', $reviewerId);
        }
        
        $assignments = $query->orderBy('created_at', 'desc')->get();
        
        // Prepare data for CSV
        $data = $assignments->map(function($assignment) {
            // Calculate total score if completed
            $totalScore = null;
            if ($assignment->status === 'completed') {
                $totalScore = ($assignment->criteria_relevance ?? 0) + 
                            ($assignment->criteria_originality ?? 0) + 
                            ($assignment->criteria_quality ?? 0) + 
                            ($assignment->criteria_impact ?? 0) + 
                            ($assignment->criteria_clarity ?? 0) + 
                            ($assignment->criteria_contribution ?? 0);
            }
            
            return [
                'Assignment ID' => $assignment->id,
                'Paper ID' => $assignment->paper->anonymous_id,
                'Paper Title' => $assignment->paper->title,
                'Topic Area' => $assignment->paper->topic_area,
                'Reviewer Name' => $assignment->reviewer->first_name . ' ' . $assignment->reviewer->last_name,
                'Reviewer Email' => $assignment->reviewer->email,
                'Status' => ucfirst(str_replace('_', ' ', $assignment->status)),
                'Assigned Date' => $assignment->assigned_at?->format('Y-m-d H:i:s'),
                'Deadline' => $assignment->deadline?->format('Y-m-d H:i:s'),
                'Submitted Date' => $assignment->submitted_at?->format('Y-m-d H:i:s'),
                'Days to Complete' => $assignment->submitted_at ? 
                    $assignment->assigned_at->diffInDays($assignment->submitted_at) : 'N/A',
                'On Time' => $assignment->submitted_at && $assignment->deadline ? 
                    ($assignment->submitted_at <= $assignment->deadline ? 'Yes' : 'No') : 'N/A',
                'Total Score (0-100)' => $totalScore ?? 'N/A',
                'Recommendation' => $assignment->recommendation_text,
                'Confidence' => $assignment->confidence ?? 'N/A',
            ];
        });
        
        $filename = 'assignments_' . $year . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        return $this->toCsv($data, $filename);
    }

    /**
     * Helper method to convert data to CSV and download
     */
    private function toCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        
        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers if data exists
            if ($data->isNotEmpty()) {
                fputcsv($handle, array_keys($data->first()));
                
                foreach ($data as $row) {
                    fputcsv($handle, array_values($row));
                }
            }
            
            fclose($handle);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function sendReminder(Request $request, ReviewAssignment $assignment)
    {
        // TODO: Implement email sending logic
        \Log::info('Reminder sent for assignment', [
            'assignment_id' => $assignment->id,
            'reviewer' => $assignment->reviewer->email,
            'message' => $request->message
        ]);
        
        return redirect()->back()->with('success', 'Reminder sent successfully!');
    }

    /**
 * Force assign a reviewer to a paper (bypasses all limits)
 * This method allows assigning a reviewer even if:
 * - Paper already has max reviewers
 * - Reviewer is overloaded
 * - Reviewer has conflicts (with warning)
 */
public function forceAssign(Request $request, Paper $paper)
{
    $request->validate([
        'reviewer_id' => 'required|exists:users,id',
        'reason' => 'nullable|string|max:500',
        'override_conflicts' => 'sometimes|boolean',
    ]);
    
    $reviewer = User::findOrFail($request->reviewer_id);
    
    // Check if reviewer is actually a reviewer
    if (!$reviewer->is_reviewer) {
        return response()->json([
            'success' => false,
            'message' => 'This user is not marked as a reviewer. Please mark them as a reviewer first.'
        ], 422);
    }
    
    $warnings = [];
    $overrideConflicts = $request->boolean('override_conflicts', false);
    
    // Check for conflicts (with override option)
    $hasConflict = $paper->authors()->where('users.id', $reviewer->id)->exists();
    if ($hasConflict) {
        if (!$overrideConflicts) {
            return response()->json([
                'success' => false,
                'message' => 'This reviewer is an author of the paper. Use "override conflicts" to force assign.',
                'has_conflict' => true
            ], 422);
        }
        $warnings[] = 'Reviewer is an author of this paper (overridden).';
    }
    
    // Check if already assigned
    $existingAssignment = ReviewAssignment::where('paper_id', $paper->id)
        ->where('reviewer_id', $reviewer->id)
        ->first();
    
    if ($existingAssignment) {
        if ($existingAssignment->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'This reviewer has already completed a review for this paper.'
            ], 422);
        }
        
        // Reactivate declined or pending assignment
        $existingAssignment->update([
            'status' => 'pending',
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
            'deadline' => now()->addWeeks(2),
            'notes' => 'Force reassigned by chair. Reason: ' . ($request->reason ?? 'No reason provided'),
            'updated_at' => now()
        ]);
        
        $message = "Reviewer re-assigned successfully! Previous assignment reactivated.";
        $action = "reactivated";
        
    } else {
        // Create new assignment
        $assignment = ReviewAssignment::create([
            'paper_id' => $paper->id,
            'reviewer_id' => $reviewer->id,
            'assigned_by' => Auth::id(),
            'status' => 'pending',
            'assigned_at' => now(),
            'deadline' => now()->addWeeks(2),
            'notes' => 'Force assigned by chair. Reason: ' . ($request->reason ?? 'No reason provided')
        ]);
        
        $message = "Reviewer force assigned successfully!";
        $action = "assigned";
    }
    
    // Log the force assignment
    \Log::info('Force assignment performed', [
        'paper_id' => $paper->id,
        'paper_title' => $paper->title,
        'reviewer_id' => $reviewer->id,
        'reviewer_name' => $reviewer->name,
        'assigned_by' => Auth::id(),
        'assigned_by_name' => Auth::user()->name,
        'reason' => $request->reason,
        'override_conflicts' => $overrideConflicts,
        'warnings' => $warnings,
        'action' => $action
    ]);
    
    // Update paper status if needed
    if (in_array($paper->status, ['submitted', 'abstract_submitted'])) {
        $paper->update(['status' => 'under_review']);
    }
    
    // Get current assignment count
    $currentAssignments = $paper->reviewAssignments()
        ->whereIn('status', ['pending', 'under_review', 'in_progress'])
        ->count();
    
    return response()->json([
        'success' => true,
        'message' => $message,
        'warnings' => $warnings,
        'assignment_count' => $currentAssignments,
        'max_reviewers' => 2, // Your max reviewers per paper
        'action' => $action
    ]);
}

}