<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use App\Models\ReviewAssignment;
use App\Models\User;
use App\Models\ConferenceRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ChairController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('chair');
    }

    /**
     * Display chair dashboard
     */
    public function dashboard(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        // Get statistics
        $stats = [
            'papers' => Paper::where('conference_year', $year)->count(),
            'reviewers' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->distinct('reviewer_id')->count(),
            'pending_reviews' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->where('status', 'pending')->count(),
            'acceptance_rate' => $this->calculateAcceptanceRate($year),
        ];
        
        // Get papers needing decisions (papers where all assigned reviews are completed)
        $pendingDecisions = Paper::where('conference_year', $year)
            ->where('status', 'under_review')
            ->withCount(['reviewAssignments as total_assignments'])
            ->withCount(['reviewAssignments as completed_assignments_count' => function($query) {
                $query->where('status', 'completed');
            }])
            ->having('total_assignments', '>', 0) // Papers with at least one assignment
            ->havingRaw('completed_assignments_count = total_assignments') // All reviews completed
            ->with(['reviewAssignments' => function($query) {
                $query->where('status', 'completed');
            }])
            ->get()
            ->each(function($paper) {
                $paper->average_score = $paper->reviewAssignments->avg('overall_score');
                $paper->review_count = $paper->reviewAssignments->count();
            });
        
        // Get recent submissions
        $recentSubmissions = Paper::where('conference_year', $year)
            ->whereIn('status', ['submitted', 'abstract_submitted'])
            ->whereDoesntHave('reviewAssignments')
            ->latest()
            ->take(10)
            ->get();

        
        // Get reviewer performance
        $reviewerPerformance = User::where('is_reviewer', true)
            ->withCount(['reviewAssignments as assigned_count' => function($query) use ($year) {
                $query->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                });
            }])
            ->withCount(['reviewAssignments as completed_count' => function($query) use ($year) {
                $query->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                })->where('status', 'completed');
            }])
            ->withCount(['reviewAssignments as pending_count' => function($query) use ($year) {
                $query->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                })->where('status', 'pending');
            }])
            ->withCount(['reviewAssignments as in_progress_count' => function($query) use ($year) {
                $query->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                })->whereIn('status', ['accepted', 'in_progress']);
            }])
            ->having('assigned_count', '>', 0)
            ->get();
        
        // Calculate average review time for each reviewer
        foreach ($reviewerPerformance as $reviewer) {
            $reviewer->avg_review_time = ReviewAssignment::where('reviewer_id', $reviewer->id)
                ->where('status', 'completed')
                ->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                })
                ->whereNotNull('assigned_at')
                ->whereNotNull('submitted_at')
                ->avg(DB::raw('DATEDIFF(submitted_at, assigned_at)'));
        }
        
        // Get topics distribution
        $topicsDistribution = Paper::where('conference_year', $year)
            ->select('topic_area as name', DB::raw('count(*) as papers_count'))
            ->groupBy('topic_area')
            ->orderByDesc('papers_count')
            ->get();
        
        // Get important deadlines
        $deadlines = [];

$dates = [
    ['title' => 'Paper Submission Deadline', 'description' => 'Final date for paper submissions', 'month' => 3, 'day' => 15],
    ['title' => 'Review Deadline', 'description' => 'All reviews must be completed', 'month' => 4, 'day' => 15],
    ['title' => 'Camera Ready Deadline', 'description' => 'Final camera-ready versions due', 'month' => 5, 'day' => 1],
];

foreach ($dates as $item) {
    $date = Carbon::create($year, $item['month'], $item['day']);
    $isPast = $date->isPast();
    $daysDiff = now()->diffInDays($date, false); // false means negative if past
    
    $deadlines[] = (object)[
        'title' => $item['title'],
        'description' => $item['description'],
        'date' => $date,
        'is_past' => $isPast,
        'is_approaching' => !$isPast && $date->diffInDays(now()) <= 30,
        'days_left' => max(0, floor($daysDiff)),
    ];
}

        
        return view('dashboard.chair', compact(
            'stats', 'pendingDecisions', 'recentSubmissions', 
            'reviewerPerformance', 'topicsDistribution', 'deadlines', 'year'
        ));
    }

    /**
     * Manage papers (for chairs)
     */
    public function papers(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $status = $request->input('status');
        $topic = $request->input('topic');
        
        $query = Paper::where('conference_year', $year)
            ->with(['authors', 'reviewAssignments' => function($query) {
                $query->where('status', 'completed');
            }]);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($topic) {
            $query->where('topic_area', $topic);
        }
        
        $papers = $query->latest()->paginate(20);
        
        // Get unique topics for filter
        $topics = Paper::where('conference_year', $year)
            ->select('topic_area')
            ->distinct()
            ->pluck('topic_area');
        
        return view('chair.papers', compact('papers', 'year', 'status', 'topic', 'topics'));
    }

    /**
     * Manage reviews (for chairs)
     */
    public function reviews(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $status = $request->input('status');
        $reviewer_id = $request->input('reviewer_id');
        
        $query = ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })
            ->with(['paper', 'reviewer']);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($reviewer_id) {
            $query->where('reviewer_id', $reviewer_id);
        }
        
        $reviews = $query->latest()->paginate(20);
        
        // Get reviewers for filter
        $reviewers = User::where('is_reviewer', true)
            ->whereHas('reviewAssignments', function($q) use ($year) {
                $q->whereHas('paper', function($q2) use ($year) {
                    $q2->where('conference_year', $year);
                });
            })
            ->get();
        
        return view('chair.reviews', compact('reviews', 'year', 'status', 'reviewer_id', 'reviewers'));
    }

    /**
     * Manage reviewers (for chairs)
     */
    public function reviewers(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        $reviewers = User::where('is_reviewer', true)
            ->withCount(['reviewAssignments as assigned_count' => function($query) use ($year) {
                $query->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                });
            }])
            ->withCount(['reviewAssignments as completed_count' => function($query) use ($year) {
                $query->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                })->where('status', 'completed');
            }])
            ->withCount(['reviewAssignments as pending_count' => function($query) use ($year) {
                $query->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                })->whereIn('status', ['pending', 'accepted', 'in_progress']);
            }])
            ->orderByDesc('assigned_count')
            ->get();
        
        return view('chair.reviewers', compact('reviewers', 'year'));
    }

    /**
     * Show paper decision form
     */
    public function showDecisionForm(Paper $paper)
    {
        $paper->load(['authors', 'reviewAssignments' => function($query) {
            $query->where('status', 'completed')->with('reviewer');
        }]);
        
        return view('chair.decision', compact('paper'));
    }

    /**
     * Make paper decision
     */

    public function makeDecision(Request $request, Paper $paper)
    {
        // Log for debugging
        \Log::info('Making decision for paper', [
            'paper_id' => $paper->id,
            'decision' => $request->decision,
        ]);
        
        // Basic validation
        $validator = Validator::make($request->all(), [
            'decision' => 'required|in:accept,reject,revise',
            'decision_notes' => 'nullable|string|max:1000',
        ]);
        
        // Only require revision_deadline for 'revise' decision
        if ($request->decision === 'revise') {
            $validator->addRules([
                'revision_deadline' => 'required|date|after:today'
            ]);
        } else {
            // Make it nullable for other decisions
            $validator->addRules([
                'revision_deadline' => 'nullable|date|after:today'
            ]);
        }
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // Determine new status
            $status = match($request->decision) {
                'accept' => 'accepted',
                'reject' => 'rejected',
                'revise' => 'needs_revision',
                default => 'under_review'
            };
            
            // Prepare update data
            $updateData = [
                'decision' => $request->decision,
                'decision_notes' => $request->decision_notes,
                'decision_made_at' => now(),
                'decision_made_by' => auth()->id(),
                'status' => $status,
            ];
            
            // Only add revision_deadline if provided
            if ($request->filled('revision_deadline')) {
                $updateData['revision_deadline'] = $request->revision_deadline;
            }
            
            // Update paper
            $paper->update($updateData);
            
            \Log::info('Paper decision saved', [
                'paper_id' => $paper->id,
                'status' => $status,
                'decision' => $request->decision,
                'has_revision_deadline' => $request->filled('revision_deadline')
            ]);
            
            return redirect()->route('chair.papers')
                ->with('success', 'Decision submitted successfully! Paper is now: ' . ucfirst($status));
                
        } catch (\Exception $e) {
            \Log::error('Failed to save decision: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to save decision: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Send reminder to reviewer
     */
    public function sendReminder(ReviewAssignment $review)
    {
        // TODO: Send reminder email to reviewer
        
        // Log the reminder
        activity()
            ->performedOn($review)
            ->causedBy(auth()->user())
            ->log('sent reminder to reviewer');
        
        return redirect()->back()
            ->with('success', 'Reminder sent to reviewer.');
    }

    /**
     * Reassign review
     */
    public function reassign(Request $request, ReviewAssignment $review)
    {
        $request->validate([
            'new_reviewer_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);
        
        // Create new assignment
        $newAssignment = ReviewAssignment::create([
            'paper_id' => $review->paper_id,
            'reviewer_id' => $request->new_reviewer_id,
            'assigned_by' => auth()->id(),
            'status' => 'pending',
            'assigned_at' => now(),
            'deadline' => $review->deadline,
            'notes' => 'Reassigned from reviewer #' . $review->reviewer_id . '. Reason: ' . ($request->reason ?? 'No reason provided'),
        ]);
        
        // Update old assignment
        $review->update([
            'status' => 'declined',
            'notes' => $review->notes . "\n\nReassigned to reviewer #" . $request->new_reviewer_id . " on " . now()->format('Y-m-d H:i:s'),
        ]);
        
        // TODO: Send notifications
        
        return redirect()->route('chair.reviews')
            ->with('success', 'Review reassigned successfully!');
    }

    /**
     * Toggle reviewer status
     */
    public function toggleReviewer(User $user)
    {
        $user->update(['is_reviewer' => !$user->is_reviewer]);
        
        $status = $user->is_reviewer ? 'enabled' : 'disabled';
        
        return redirect()->back()
            ->with('success', "Reviewer status {$status} for {$user->full_name}");
    }

    /**
     * Toggle chair status
     */
    public function toggleChair(User $user)
    {
        $user->update(['is_chair' => !$user->is_chair]);
        
        $status = $user->is_chair ? 'granted' : 'revoked';
        
        return redirect()->back()
            ->with('success', "Chair privileges {$status} for {$user->full_name}");
    }

    /**
     * Helper method to calculate acceptance rate
     */
    private function calculateAcceptanceRate($year)
    {
        $total = Paper::where('conference_year', $year)->count();
        $accepted = Paper::where('conference_year', $year)->where('status', 'accepted')->count();
        
        return $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
    }
}