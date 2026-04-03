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
        
        // Papers needing assignments - INCLUDE papers with declined reviews
        $papersQuery = Paper::where('conference_year', $year)
            ->whereIn('status', ['submitted', 'abstract_submitted', 'under_review']);
        
        // For papers tab, show papers that need reviewers
        if ($tab === 'papers') {
            $papersQuery->where(function($query) {
                $query->where(function($q) {
                    // Papers with no reviews at all
                    $q->doesntHave('reviews');
                })->orWhere(function($q) {
                    // Papers with less than 2 active reviews (not declined)
                    $q->whereHas('reviews', function($r) {
                        $r->whereIn('status', ['pending', 'under_review', 'in_progress']);
                    }, '<', 2)
                    ->orWhereHas('reviews', function($r) {
                        // Papers where ALL reviews are declined
                        $r->where('status', 'declined')
                          ->havingRaw('COUNT(*) = (SELECT COUNT(*) FROM review_assignments WHERE paper_id = papers.id)');
                    });
                });
            });
        }

        $papers = $papersQuery->with(['reviews' => function($query) {
                $query->whereIn('status', ['pending', 'under_review', 'in_progress', 'declined'])
                    ->with('reviewer');
            }, 'bids'])
            ->orderBy('submitted_at')
            ->get()
            ->filter(function($paper) {
                // A paper needs a reviewer if:
                // 1. It has no reviews, OR
                // 2. It has less than 2 active reviews (pending/under_review/in_progress), OR
                // 3. All its reviews are declined
                $activeReviews = $paper->reviews->filter(function($review) {
                    return in_array($review->status, ['pending', 'under_review', 'in_progress']);
                });
                
                $declinedReviews = $paper->reviews->filter(function($review) {
                    return $review->status === 'declined';
                });
                
                return $paper->reviews->isEmpty() || 
                       $activeReviews->count() < 2 || 
                       ($declinedReviews->count() > 0 && $activeReviews->count() == 0);
            });
        
        // Available reviewers - exclude those who already declined this specific paper
        $reviewers = User::where('is_reviewer', true)
            ->with(['expertise', 'reviewAssignments' => function($q) use ($year) {
                $q->whereHas('paper', function($q2) use ($year) {
                    $q2->where('conference_year', $year);
                })->whereIn('status', ['pending', 'under_review', 'in_progress']);
            }])
            ->get()
            ->map(function($reviewer) use ($papers) {
                // Get all papers this reviewer has declined
                $reviewer->declined_paper_ids = ReviewAssignment::where('reviewer_id', $reviewer->id)
                    ->where('status', 'declined')
                    ->pluck('paper_id')
                    ->toArray();
                return $reviewer;
            });
        
        // Statistics
        $totalPapers = Paper::where('conference_year', $year)
            ->whereIn('status', ['submitted', 'abstract_submitted', 'under_review'])
            ->count();
            
        // Papers needing assignments (papers with less than 2 active reviews)
        $papersNeedingAssignments = Paper::where('conference_year', $year)
            ->whereIn('status', ['submitted', 'abstract_submitted', 'under_review'])
            ->where(function($query) {
                $query->whereDoesntHave('reviews')
                    ->orWhereHas('reviews', function($q) {
                        $q->whereIn('status', ['pending', 'under_review', 'in_progress'])
                          ->havingRaw('COUNT(*) < 2');
                    })
                    ->orWhereHas('reviews', function($q) {
                        $q->where('status', 'declined')
                          ->havingRaw('COUNT(*) = (SELECT COUNT(*) FROM review_assignments WHERE paper_id = papers.id)');
                    });
            })
            ->count();

        $totalAssignedReviews = ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })
            ->whereIn('status', ['pending', 'under_review', 'in_progress'])
            ->count();

        $activeReviewers = User::where('is_reviewer', true)
            ->whereHas('reviewAssignments', function($q) use ($year) {
                $q->whereHas('paper', function($q2) use ($year) {
                    $q2->where('conference_year', $year);
                })->whereIn('status', ['pending', 'under_review', 'in_progress']);
            })
            ->count();
        
        $avgLoad = $activeReviewers > 0 ? round($totalAssignedReviews / $activeReviewers, 1) : 0;
        
        // Calculate max possible reviews (2 per paper that needs reviewers)
        $maxPossibleReviews = $papersNeedingAssignments * 2;
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

}