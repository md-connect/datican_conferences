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
        
        // Get statistics - UPDATED with new metrics
        $stats = [
            'papers' => Paper::where('conference_year', $year)->count(),
            'reviewers' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->distinct('reviewer_id')->count(),
            'pending_reviews' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->where('status', 'pending')->count(),
            'acceptance_rate' => $this->calculateAcceptanceRate($year),
            // NEW STATS
            'conference_registrations' => ConferenceRegistration::count(),
            'total_users' => User::count(),
            'reviews_completed' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->where('status', 'completed')->count(),
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
     * View all conference registrations
     */
    public function registrations(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        $registrations = ConferenceRegistration::when($request->filled('search'), function($query) use ($request) {
                return $query->where('firstname', 'like', "%{$request->search}%")
                    ->orWhere('lastname', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('institution', 'like', "%{$request->search}%");
            })
            ->when($request->filled('year'), function($query) use ($year) {
                return $query->whereYear('created_at', $year);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('chair.registrations', compact('registrations', 'year'));
    }

    /**
     * Export registrations to CSV
     */
    public function exportRegistrations()
    {
        $registrations = ConferenceRegistration::all();
        
        $data = $registrations->map(function($registration) {
            return [
                'ID' => $registration->id,
                'Title' => $registration->title,
                'First Name' => $registration->firstname,
                'Last Name' => $registration->lastname,
                'Email' => $registration->email,
                'Phone' => $registration->phone_number,
                'Institution' => $registration->institution,
                'Gender' => $registration->gender,
                'DATIAN Member' => $registration->is_datican_member ? 'Yes' : 'No',
                'DATIAN Status' => $registration->datican_status,
                'Presenting Paper' => $registration->is_presenting_paper ? 'Yes' : 'No',
                'Registration Date' => $registration->created_at->format('Y-m-d H:i:s'),
            ];
        });
        
        return $this->toCsv($data, 'DATICAN_Conference_registrations_2026.csv');
    }

    /**
     * Export papers to CSV
     */
    public function exportPapers()
    {
        $papers = Paper::with(['authors'])->get();
        
        $data = $papers->map(function($paper) {
            return [
                'ID' => $paper->anonymous_id,
                'Title' => $paper->title,
                'Status' => $paper->status,
                'Decision' => $paper->decision,
                'Authors' => $paper->author_list,
                'Topic Area' => $paper->topic_area,
                'Submission Type' => $paper->submission_type,
                'Submission Date' => $paper->submitted_at?->format('Y-m-d H:i:s'),
                'Review Count' => $paper->review_count,
                'Average Score' => round($paper->average_score, 2),
                'Keywords' => $paper->keywords,
            ];
        });
        
        return $this->toCsv($data, 'DATICAN_Conference_papers_2026.csv');
    }

    /**
     * Export reviews to CSV
     */
    public function exportReviews()
    {
        $reviews = ReviewAssignment::with(['paper', 'reviewer'])->get();
        
        $data = $reviews->map(function($review) {
            return [
                'Paper ID' => $review->paper->anonymous_id,
                'Paper Title' => $review->paper->title,
                'Reviewer Name' => $review->reviewer->first_name . ' ' . $review->reviewer->last_name,
                'Reviewer Email' => $review->reviewer->email,
                'Status' => $review->status,
                'Overall Score' => $review->overall_score,
                'Recommendation' => $review->recommendation,
                'Confidence' => $review->confidence,
                'Assigned Date' => $review->assigned_at?->format('Y-m-d H:i:s'),
                'Submitted Date' => $review->submitted_at?->format('Y-m-d H:i:s'),
                'Deadline' => $review->deadline?->format('Y-m-d H:i:s'),
            ];
        });
        
        return $this->toCsv($data, 'DATICAN_Conference_reviews_2026.csv');
    }

    /**
     * Helper method to convert data to CSV and download
     */
    private function toCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            
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
        // Log all incoming data
        \Log::info('=== MAKE DECISION START ===', [
            'paper_id' => $paper->id,
            'paper_title' => $paper->title,
            'decision' => $request->decision,
            'has_revision_deadline' => $request->has('revision_deadline'),
            'revision_deadline' => $request->revision_deadline,
            'decision_notes' => $request->decision_notes,
            'all_input' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);
        
        // Basic validation
        $validator = Validator::make($request->all(), [
            'decision' => 'required|in:accept,reject,revise',
            'decision_notes' => 'nullable|string|max:1000',
        ]);
        
        \Log::info('After basic validation', [
            'validator_passes' => !$validator->fails(),
            'errors' => $validator->errors()->all()
        ]);
        
        // Only require revision_deadline for 'revise' decision
        if ($request->decision === 'revise') {
            \Log::info('Adding revision_deadline validation rules');
            $validator->addRules([
                'revision_deadline' => 'required|date|after:today'
            ]);
            
            // Log the rule addition
            \Log::info('Revision deadline validation added', [
                'rules' => $validator->getRules()
            ]);
        }
        
        if ($validator->fails()) {
            \Log::warning('Validation failed', [
                'errors' => $validator->errors()->all(),
                'decision' => $request->decision,
                'revision_deadline' => $request->revision_deadline
            ]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        \Log::info('Validation passed, proceeding with update');
        
        try {
            // Determine new status
            $status = match($request->decision) {
                'accept' => 'accepted',
                'reject' => 'rejected',
                'revise' => 'needs_revision',
                default => 'under_review'
            };
            
            \Log::info('Status determined', ['status' => $status]);
            
            // Prepare update data
            $updateData = [
                'decision' => $request->decision,
                'decision_notes' => $request->decision_notes,
                'decision_made_at' => now(),
                'decision_made_by' => auth()->id(),
                'status' => $status,
            ];
            
            \Log::info('Base update data prepared', $updateData);
            
            // Only add revision_deadline for revise decisions
            if ($request->decision === 'revise' && $request->filled('revision_deadline')) {
                $updateData['revision_deadline'] = $request->revision_deadline;
                $updateData['needs_revision'] = true;
                $updateData['revision_requested_at'] = now();
                
                \Log::info('Added revision data', [
                    'revision_deadline' => $request->revision_deadline,
                    'needs_revision' => true
                ]);
            }
            
            \Log::info('Final update data', $updateData);
            
            // Update paper
            $paper->update($updateData);
            
            \Log::info('Paper updated successfully', [
                'paper_id' => $paper->id,
                'new_status' => $paper->fresh()->status
            ]);
            
            $message = match($request->decision) {
                'accept' => 'Paper accepted successfully!',
                'reject' => 'Paper rejected successfully!',
                'revise' => 'Revision requested. Deadline: ' . 
                        \Carbon\Carbon::parse($request->revision_deadline)->format('F d, Y'),
                default => 'Decision submitted successfully!'
            };
            
            return redirect()->route('chair.papers')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            \Log::error('Failed to save decision: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
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