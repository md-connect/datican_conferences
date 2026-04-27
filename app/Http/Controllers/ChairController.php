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
use Illuminate\Support\Facades\Mail;
use App\Mail\PaperDecisionMail;

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
        
        // Get papers that are under review (have at least one assignment)
        $papersUnderReview = Paper::where('conference_year', $year)
            ->where('status', 'under_review')
            ->withCount(['reviewAssignments as total_assignments'])
            ->withCount(['reviewAssignments as completed_assignments' => function($query) {
                $query->where('status', 'completed');
            }])
            ->having('total_assignments', '>', 0)
            ->get();
        
        // Calculate review completion statistics
        $papersWithBothReviews = $papersUnderReview->filter(function($paper) {
            return $paper->completed_assignments >= 2;
        })->count();
        
        $papersWithOneReview = $papersUnderReview->filter(function($paper) {
            return $paper->completed_assignments == 1;
        })->count();
        
        $papersWithNoReviews = $papersUnderReview->filter(function($paper) {
            return $paper->completed_assignments == 0;
        })->count();
        
        // ========== NEW DECISION STATS ==========
        $acceptedPapers = Paper::where('conference_year', $year)
            ->where('status', 'accepted')
            ->count();
        
        $rejectedPapers = Paper::where('conference_year', $year)
            ->where('status', 'rejected')
            ->count();
        
        $needingDecision = Paper::where('conference_year', $year)
            ->where('status', 'under_review')
            ->whereHas('reviewAssignments', function($q) {
                $q->where('status', 'completed');
            }, '>=', 2)
            ->count();
        
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
            'conference_registrations' => ConferenceRegistration::count(),
            'total_users' => User::count(),
            'reviews_completed' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->where('status', 'completed')->count(),
            'papers_under_review' => $papersUnderReview->count(),
            'papers_with_both_reviews' => $papersWithBothReviews,
            'papers_with_one_review' => $papersWithOneReview,
            'papers_with_no_reviews' => $papersWithNoReviews,
            // ========== DECISION STATS - FIXED ==========
            'accepted_papers' => Paper::where('conference_year', $year)
                ->whereIn('decision', ['accept', 'accept_with_minor_revision', 'accept_with_major_revision'])
                ->count(),
            'rejected_papers' => Paper::where('conference_year', $year)
                ->where('decision', 'reject')
                ->count(),
            'needing_decision' => Paper::where('conference_year', $year)
                ->where('status', 'under_review')
                ->whereHas('reviewAssignments', function($q) {
                    $q->where('status', 'completed');
                }, '>=', 2)
                ->whereNull('decision')
                ->count(),
        ];
        
        // Get papers needing decisions (papers with at least 2 completed reviews)
        $pendingDecisions = Paper::where('conference_year', $year)
            ->where('status', 'under_review')
            ->withCount(['reviewAssignments as total_assignments'])
            ->withCount(['reviewAssignments as completed_assignments_count' => function($query) {
                $query->where('status', 'completed');
            }])
            ->having('total_assignments', '>', 0)
            ->having('completed_assignments_count', '>=', 2)
            ->with(['reviewAssignments' => function($query) {
                $query->where('status', 'completed');
            }])
            ->get()
            ->each(function($paper) {
                $paper->average_score = $paper->reviewAssignments->avg(function($review) {
                    return ($review->criteria_relevance ?? 0) + 
                        ($review->criteria_originality ?? 0) + 
                        ($review->criteria_quality ?? 0) + 
                        ($review->criteria_impact ?? 0) + 
                        ($review->criteria_clarity ?? 0) + 
                        ($review->criteria_contribution ?? 0);
                });
                $paper->review_count = $paper->reviewAssignments->count();
            });
        
            
        // Get papers needing reviewers
        $papersNeedingReviewers = Paper::where('conference_year', $year)
            ->whereIn('status', ['submitted', 'abstract_submitted', 'under_review'])
            ->withCount(['reviewAssignments as total_assigned' => function($query) {
                $query->whereIn('status', ['pending', 'under_review', 'in_progress', 'accepted', 'completed']);
            }])
            ->having('total_assigned', '<', 2)
            ->with(['reviewAssignments' => function($query) {
                $query->whereIn('status', ['pending', 'under_review', 'in_progress', 'accepted', 'completed'])
                    ->with('reviewer');
            }])
            ->latest()
            ->take(10)
            ->get();
        
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
        
        // Calculate average review time and average score for each reviewer
        foreach ($reviewerPerformance as $reviewer) {
            $reviewer->avg_review_time = ReviewAssignment::where('reviewer_id', $reviewer->id)
                ->where('status', 'completed')
                ->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                })
                ->whereNotNull('assigned_at')
                ->whereNotNull('submitted_at')
                ->avg(DB::raw('DATEDIFF(submitted_at, assigned_at)'));
            
            $completedReviews = ReviewAssignment::where('reviewer_id', $reviewer->id)
                ->where('status', 'completed')
                ->whereHas('paper', function($q) use ($year) {
                    $q->where('conference_year', $year);
                })
                ->get();
            
            $totalScore = 0;
            foreach ($completedReviews as $review) {
                $totalScore += ($review->criteria_relevance ?? 0) + 
                            ($review->criteria_originality ?? 0) + 
                            ($review->criteria_quality ?? 0) + 
                            ($review->criteria_impact ?? 0) + 
                            ($review->criteria_clarity ?? 0) + 
                            ($review->criteria_contribution ?? 0);
            }
            $reviewer->avg_score = $completedReviews->count() > 0 ? round($totalScore / $completedReviews->count(), 1) : null;
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
            $daysDiff = now()->diffInDays($date, false);
            
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
            'stats', 
            'pendingDecisions', 
            'papersNeedingReviewers',
            'recentSubmissions', 
            'reviewerPerformance', 
            'topicsDistribution', 
            'deadlines', 
            'year'
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
            // Calculate total score from individual criteria
            $totalScore = ($review->criteria_relevance ?? 0) + 
                        ($review->criteria_originality ?? 0) + 
                        ($review->criteria_quality ?? 0) + 
                        ($review->criteria_impact ?? 0) + 
                        ($review->criteria_clarity ?? 0) + 
                        ($review->criteria_contribution ?? 0);
            
            // Calculate percentage (out of 100)
            $percentageScore = $totalScore; // Total is already out of 100 (20+20+15+15+15+15 = 100)
            
            return [
                'Paper ID' => $review->paper->anonymous_id,
                'Paper Title' => $review->paper->title,
                'Reviewer Name' => $review->reviewer->first_name . ' ' . $review->reviewer->last_name,
                'Reviewer Email' => $review->reviewer->email,
                'Status' => $review->status,
                'Overall Score' => $percentageScore ?: 'N/A',
                'Relevance (20)' => $review->criteria_relevance ?? 'N/A',
                'Originality (20)' => $review->criteria_originality ?? 'N/A',
                'Quality (15)' => $review->criteria_quality ?? 'N/A',
                'Impact (15)' => $review->criteria_impact ?? 'N/A',
                'Clarity (15)' => $review->criteria_clarity ?? 'N/A',
                'Contribution (15)' => $review->criteria_contribution ?? 'N/A',
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
     * Export peer review comparison data
     */
    public function exportPeerReview($type)
    {
        $papers = Paper::with(['reviewAssignments' => function($query) {
            $query->where('status', 'completed')->orderBy('id');
        }, 'reviewAssignments.reviewer'])->get();
        
        $data = $papers->filter(function($paper) {
            return $paper->reviewAssignments->count() >= 2;
        })->map(function($paper) {
            $reviews = $paper->reviewAssignments->sortBy('id');
            $review1 = $reviews->first();
            $review2 = $reviews->last();
            
            // Calculate reviewer 1 total
            $review1Total = ($review1->criteria_relevance ?? 0) + 
                            ($review1->criteria_originality ?? 0) + 
                            ($review1->criteria_quality ?? 0) + 
                            ($review1->criteria_impact ?? 0) + 
                            ($review1->criteria_clarity ?? 0) + 
                            ($review1->criteria_contribution ?? 0);
            
            // Calculate reviewer 2 total
            $review2Total = ($review2->criteria_relevance ?? 0) + 
                            ($review2->criteria_originality ?? 0) + 
                            ($review2->criteria_quality ?? 0) + 
                            ($review2->criteria_impact ?? 0) + 
                            ($review2->criteria_clarity ?? 0) + 
                            ($review2->criteria_contribution ?? 0);
            
            return [
                'Paper ID' => $paper->anonymous_id,
                'Paper Title' => $paper->title,
                'Reviewer 1' => $review1->reviewer->first_name . ' ' . $review1->reviewer->last_name,
                'Reviewer 1 - Relevance (20)' => $review1->criteria_relevance ?? 'N/A',
                'Reviewer 1 - Originality (20)' => $review1->criteria_originality ?? 'N/A',
                'Reviewer 1 - Quality (15)' => $review1->criteria_quality ?? 'N/A',
                'Reviewer 1 - Impact (15)' => $review1->criteria_impact ?? 'N/A',
                'Reviewer 1 - Clarity (10)' => $review1->criteria_clarity ?? 'N/A',
                'Reviewer 1 - Contribution (10)' => $review1->criteria_contribution ?? 'N/A',
                'Reviewer 1 - Total' => $review1Total,
                'Reviewer 2' => $review2->reviewer->first_name . ' ' . $review2->reviewer->last_name,
                'Reviewer 2 - Relevance (20)' => $review2->criteria_relevance ?? 'N/A',
                'Reviewer 2 - Originality (20)' => $review2->criteria_originality ?? 'N/A',
                'Reviewer 2 - Quality (15)' => $review2->criteria_quality ?? 'N/A',
                'Reviewer 2 - Impact (15)' => $review2->criteria_impact ?? 'N/A',
                'Reviewer 2 - Clarity (10)' => $review2->criteria_clarity ?? 'N/A',
                'Reviewer 2 - Contribution (10)' => $review2->criteria_contribution ?? 'N/A',
                'Reviewer 2 - Total' => $review2Total,
                'Score Difference' => abs($review1Total - $review2Total),
                'Average Total Score' => round(($review1Total + $review2Total) / 2, 1),
            ];
        });
        
        return $this->toCsv($data, 'DATICAN_2026_Conference_peer_review_comparison.csv');
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
            'decision_notes' => $request->decision_notes,
            'all_input' => $request->all()
        ]);
        
        // Basic validation - No revision_deadline
        $validator = Validator::make($request->all(), [
            'decision' => 'required|in:accept,accept_with_minor_revision,accept_with_major_revision,reject',
            'decision_notes' => 'nullable|string|max:1000',
        ]);
        
        \Log::info('After basic validation', [
            'validator_passes' => !$validator->fails(),
            'errors' => $validator->errors()->all()
        ]);
        
        if ($validator->fails()) {
            \Log::warning('Validation failed', [
                'errors' => $validator->errors()->all(),
                'decision' => $request->decision,
            ]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        \Log::info('Validation passed, proceeding with update');
        
        try {
            // Determine new status based on decision
            $status = match($request->decision) {
                'accept' => 'accepted',
                'accept_with_minor_revision', 'accept_with_major_revision' => 'needs_revision',
                'reject' => 'rejected',
                default => 'under_review'
            };
            
            \Log::info('Status determined', ['status' => $status]);
            
            // Prepare update data for paper
            $updateData = [
                'decision' => $request->decision,
                'decision_notes' => $request->decision_notes,
                'decision_made_at' => now(),
                'decision_made_by' => auth()->id(),
                'status' => $status,
            ];
            
            // Add revision data for revision decisions (NO deadline)
            if (in_array($request->decision, ['accept_with_minor_revision', 'accept_with_major_revision'])) {
                $updateData['needs_revision'] = true;
                $updateData['revision_requested_at'] = now();
                $updateData['revision_notes'] = $request->decision_notes;
                
                \Log::info('Added revision data to paper', [
                    'needs_revision' => true,
                    'revision_requested_at' => now(),
                    'revision_notes' => substr($request->decision_notes ?? '', 0, 100)
                ]);
            }
            
            \Log::info('Final update data for paper', $updateData);
            
            // Update paper
            $paper->update($updateData);
            
            // Save chair decision to all completed review assignments
            $updatedReviews = $paper->reviewAssignments()
                ->where('status', 'completed')
                ->update([
                    'chair_decision' => $request->decision,
                    'chair_decision_notes' => $request->decision_notes,
                    'chair_decision_made_at' => now(),
                    'chair_decision_made_by' => auth()->id()
                ]);
            
            \Log::info('Chair decision saved to review assignments', [
                'updated_reviews_count' => $updatedReviews
            ]);
            
            \Log::info('Paper updated successfully', [
                'paper_id' => $paper->id,
                'new_status' => $paper->fresh()->status,
                'decision' => $paper->fresh()->decision,
                'needs_revision' => $paper->fresh()->needs_revision,
                'has_revision_notes' => !is_null($paper->fresh()->revision_notes)
            ]);
            
            // ========== SEND EMAIL NOTIFICATION ==========
            try {
                $emailSent = false;
                $recipients = [];
                
                foreach ($paper->authors as $author) {
                    if (filter_var($author->email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($author->email)->send(new PaperDecisionMail(
                            $paper,
                            $request->decision,
                            $request->decision_notes
                        ));
                        $recipients[] = $author->email;
                        $emailSent = true;
                        
                        \Log::info('Decision email sent to author', [
                            'paper_id' => $paper->id,
                            'author_email' => $author->email,
                            'author_name' => $author->first_name . ' ' . $author->last_name
                        ]);
                    }
                }
                
                if ($emailSent) {
                    \Log::info('All decision emails sent successfully', [
                        'paper_id' => $paper->id,
                        'recipients_count' => count($recipients),
                        'recipients' => $recipients
                    ]);
                } else {
                    \Log::warning('No valid email addresses found for paper authors', [
                        'paper_id' => $paper->id,
                        'authors_count' => $paper->authors->count()
                    ]);
                }
                
            } catch (\Exception $emailError) {
                // Don't fail the decision if email fails - just log the error
                \Log::error('Failed to send decision notification emails', [
                    'paper_id' => $paper->id,
                    'error' => $emailError->getMessage(),
                    'trace' => $emailError->getTraceAsString()
                ]);
            }
            
            // Prepare success message with email status
            $emailStatus = $emailSent ?? false ? ' Notification sent to authors.' : '';
            
            if ($request->decision == 'accept') {
                $message = 'Paper accepted successfully!' . $emailStatus;
            } elseif ($request->decision == 'reject') {
                $message = 'Paper rejected successfully!' . $emailStatus;
            } elseif ($request->decision == 'accept_with_minor_revision') {
                $message = 'Paper accepted with minor revisions requested.' . $emailStatus;
            } elseif ($request->decision == 'accept_with_major_revision') {
                $message = 'Paper accepted with major revisions requested.' . $emailStatus;
            } else {
                $message = 'Decision submitted successfully!' . $emailStatus;
            }
            
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

    /**
     * Resend decision email to authors
     */
    public function resendDecisionEmail(Paper $paper)
    {
        if (!$paper->decision_made_at) {
            return redirect()->back()->with('error', 'No decision has been made for this paper yet.');
        }
        
        try {
            $recipients = [];
            foreach ($paper->authors as $author) {
                if (filter_var($author->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($author->email)->send(new PaperDecisionMail(
                        $paper,
                        $paper->decision,
                        $paper->decision_notes,
                        $paper->revision_deadline
                    ));
                    $recipients[] = $author->email;
                }
            }
            
            \Log::info('Decision email resent', [
                'paper_id' => $paper->id,
                'recipients' => $recipients
            ]);
            
            return redirect()->back()->with('success', 'Decision email resent to ' . count($recipients) . ' author(s).');
            
        } catch (\Exception $e) {
            \Log::error('Failed to resend decision email', [
                'paper_id' => $paper->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to resend email: ' . $e->getMessage());
        }
    }
}