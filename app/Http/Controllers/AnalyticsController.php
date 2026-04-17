<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use App\Models\User;
use App\Models\ReviewAssignment;
use App\Models\ConferenceRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->is_admin && !auth()->user()->is_chair) {
                abort(403, 'Unauthorized. Admin or chair privileges required.');
            }
            return $next($request);
        });
    }

    /**
     * Main dashboard
     */
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        $stats = [
            'papers' => $this->getPaperStats($year),
            'reviews' => $this->getReviewStats($year),
            'users' => $this->getUserStats($year),
            'timeline' => $this->getTimelineStats($year),
            'geographic' => $this->getGeographicStats($year),
            'topic_distribution' => $this->getTopicDistribution($year),
        ];
        
        return view('analytics.index', compact('stats', 'year'));
    }

    /**
     * Paper statistics
     */
    private function getPaperStats($year)
    {
        return [
            'total' => Paper::byYear($year)->count(),
            'by_status' => Paper::byYear($year)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'by_type' => Paper::byYear($year)
                ->select('submission_type', DB::raw('count(*) as count'))
                ->groupBy('submission_type')
                ->pluck('count', 'submission_type')
                ->toArray(),
            'submissions_per_day' => Paper::byYear($year)
                ->whereNotNull('submitted_at')
                ->select(DB::raw('DATE(submitted_at) as date'), DB::raw('count(*) as count'))
                ->groupBy(DB::raw('DATE(submitted_at)'))
                ->orderBy('date')
                ->get()
                ->mapWithKeys(fn($item) => [$item->date => $item->count])
                ->toArray(),
            'acceptance_rate' => $this->calculateAcceptanceRate($year),
        ];
    }

    /**
     * Review statistics
     */
    private function getReviewStats($year)
    {
        $reviews = ReviewAssignment::whereHas('paper', function($q) use ($year) {
            $q->where('conference_year', $year);
        });
        
        return [
            'total' => $reviews->count(),
            'completed' => $reviews->where('status', 'completed')->count(),
            'pending' => $reviews->where('status', '!=', 'completed')->count(),
            'average_score' => round($reviews->avg('overall_score') ?? 0, 2),
            'by_recommendation' => $reviews->whereNotNull('recommendation')
                ->select('recommendation', DB::raw('count(*) as count'))
                ->groupBy('recommendation')
                ->pluck('count', 'recommendation')
                ->toArray(),
            'review_turnaround' => $this->calculateReviewTurnaround($year),
            'reviewer_load' => $this->calculateReviewerLoad($year),
        ];
    }

    /**
     * User statistics
     */
    private function getUserStats($year)
    {
        return [
            'total_authors' => Paper::byYear($year)
                ->withCount('authors')
                ->get()
                ->sum('authors_count'),
            'unique_authors' => DB::table('paper_authors')
                ->join('papers', 'paper_authors.paper_id', '=', 'papers.id')
                ->where('papers.conference_year', $year)
                ->distinct('paper_authors.user_id')
                ->count('paper_authors.user_id'),
            'reviewers' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->distinct('reviewer_id')->count('reviewer_id'),
            'active_reviewers' => ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })->where('status', 'completed')
              ->distinct('reviewer_id')
              ->count('reviewer_id'),
            'countries' => $this->getCountryStats($year),
        ];
    }

    /**
     * Timeline statistics
     */
    private function getTimelineStats($year)
    {
        $papers = Paper::byYear($year)
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at')
            ->get();
            
        $reviews = ReviewAssignment::whereHas('paper', function($q) use ($year) {
            $q->where('conference_year', $year);
        })->whereNotNull('submitted_at')
          ->orderBy('submitted_at')
          ->get();
          
        return [
            'paper_submissions' => $papers->groupBy(function($paper) {
                return $paper->submitted_at->format('Y-m-d');
            })->map->count(),
            'review_submissions' => $reviews->groupBy(function($review) {
                return $review->submitted_at->format('Y-m-d');
            })->map->count(),
            'cumulative_papers' => $this->calculateCumulative($papers, 'submitted_at'),
            'cumulative_reviews' => $this->calculateCumulative($reviews, 'submitted_at'),
        ];
    }

    /**
     * Geographic statistics
     */
    private function getGeographicStats($year)
    {
        // This would require adding country field to users or registrations
        // For now, using institution as proxy
        $institutions = ConferenceRegistration::whereHas('papers', function($q) use ($year) {
            $q->where('conference_year', $year);
        })->select('institution', DB::raw('count(*) as count'))
          ->groupBy('institution')
          ->orderByDesc('count')
          ->limit(10)
          ->get()
          ->pluck('count', 'institution')
          ->toArray();
          
        return [
            'top_institutions' => $institutions,
        ];
    }

    /**
     * Topic distribution
     */
    private function getTopicDistribution($year)
    {
        return Paper::byYear($year)
            ->select('topic_area', DB::raw('count(*) as count'))
            ->groupBy('topic_area')
            ->orderByDesc('count')
            ->get()
            ->pluck('count', 'topic_area')
            ->toArray();
    }

    /**
     * Helper methods
     */
    private function calculateAcceptanceRate($year)
    {
        $total = Paper::byYear($year)->count();
        $accepted = Paper::byYear($year)->where('status', 'accepted')->count();
        
        return $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
    }

    private function calculateReviewTurnaround($year)
    {
        $reviews = ReviewAssignment::whereHas('paper', function($q) use ($year) {
            $q->where('conference_year', $year);
        })->whereNotNull('assigned_at')
          ->whereNotNull('submitted_at')
          ->get();
          
        if ($reviews->isEmpty()) return 0;
        
        $totalDays = $reviews->sum(function($review) {
            return $review->assigned_at->diffInDays($review->submitted_at);
        });
        
        return round($totalDays / $reviews->count(), 2);
    }

    private function calculateReviewerLoad($year)
    {
        $loads = ReviewAssignment::whereHas('paper', function($q) use ($year) {
            $q->where('conference_year', $year);
        })->select('reviewer_id', DB::raw('count(*) as count'))
          ->groupBy('reviewer_id')
          ->get()
          ->pluck('count')
          ->toArray();
          
        return [
            'average' => count($loads) > 0 ? round(array_sum($loads) / count($loads), 2) : 0,
            'max' => count($loads) > 0 ? max($loads) : 0,
            'min' => count($loads) > 0 ? min($loads) : 0,
            'distribution' => array_count_values($loads),
        ];
    }

    private function getCountryStats($year)
    {
        // This is a placeholder - you'd need to add country field
        return [
            'total' => 0,
            'top_countries' => [],
        ];
    }

    private function calculateCumulative($items, $dateField)
    {
        $cumulative = [];
        $count = 0;
        
        foreach ($items as $item) {
            $date = $item->$dateField->format('Y-m-d');
            $count++;
            $cumulative[$date] = $count;
        }
        
        return $cumulative;
    }

    /**
     * Export data
     */
    public function export(Request $request, $type)
    {
        $year = $request->input('year', date('Y'));
        
        switch ($type) {
            case 'papers':
                return $this->exportPapers($year);
            case 'reviews':
                return $this->exportReviews($year);
            case 'authors':
                return $this->exportAuthors($year);
            case 'statistics':
                return $this->exportStatistics($year);
            default:
                abort(404);
        }
    }

    /**
     * Export papers to CSV (matching chair format)
     */
    public function exportPapers($year = null)
    {
        $year = $year ?? date('Y');
        
        $papers = Paper::byYear($year)
            ->with(['authors'])
            ->orderBy('id')
            ->get();
        
        $data = $papers->map(function($paper) {
            return [
                'ID' => $paper->anonymous_id,
                'Title' => $paper->title,
                'Status' => ucfirst(str_replace('_', ' ', $paper->status)),
                'Decision' => $paper->decision ? ucfirst($paper->decision) : 'Pending',
                'Authors' => $paper->author_list,
                'Topic Area' => $paper->topic_area,
                'Submission Type' => $paper->submission_type === 'abstract_only' ? 'Abstract Only' : 'Full Paper',
                'Submission Date' => $paper->submitted_at?->format('Y-m-d H:i:s'),
                'Review Count' => $paper->review_count,
                'Average Score' => round($paper->average_score, 2),
                'Keywords' => $paper->keywords,
            ];
        });
        
        return $this->toCsv($data, "DATICAN_Conference_papers_{$year}.csv");
    }

    /**
     * Export reviews to CSV (matching chair format)
     */
    public function exportReviews($year = null)
    {
        $year = $year ?? date('Y');
        
        $reviews = ReviewAssignment::whereHas('paper', function($q) use ($year) {
                $q->where('conference_year', $year);
            })
            ->with(['paper', 'reviewer'])
            ->get();
        
        $data = $reviews->map(function($review) {
            // Calculate total score if completed
            $totalScore = null;
            if ($review->status === 'completed') {
                $totalScore = ($review->criteria_relevance ?? 0) + 
                            ($review->criteria_originality ?? 0) + 
                            ($review->criteria_quality ?? 0) + 
                            ($review->criteria_impact ?? 0) + 
                            ($review->criteria_clarity ?? 0) + 
                            ($review->criteria_contribution ?? 0);
            }
            
            return [
                'Paper ID' => $review->paper->anonymous_id,
                'Paper Title' => $review->paper->title,
                'Reviewer Name' => $review->reviewer->first_name . ' ' . $review->reviewer->last_name,
                'Reviewer Email' => $review->reviewer->email,
                'Status' => ucfirst(str_replace('_', ' ', $review->status)),
                'Overall Score' => $totalScore ?? 'N/A',
                'Recommendation' => $review->recommendation_text ?? 'N/A',
                'Confidence' => $review->confidence ?? 'N/A',
                'Assigned Date' => $review->assigned_at?->format('Y-m-d H:i:s'),
                'Submitted Date' => $review->submitted_at?->format('Y-m-d H:i:s'),
                'Deadline' => $review->deadline?->format('Y-m-d H:i:s'),
                'Turnaround Days' => $review->assigned_at && $review->submitted_at 
                    ? $review->assigned_at->diffInDays($review->submitted_at) 
                    : 'N/A',
            ];
        });
        
        return $this->toCsv($data, "DATICAN_Conference_reviews_{$year}.csv");
    }

    /**
     * Export authors to CSV
     */
    public function exportAuthors($year = null)
    {
        $year = $year ?? date('Y');
        
        $papers = Paper::byYear($year)
            ->with(['authors'])
            ->orderBy('id')
            ->get();
        
        $data = collect();
        
        foreach ($papers as $paper) {
            $authors = $paper->authors->sortBy('pivot.author_order');
            $firstAuthor = $authors->first();
            
            foreach ($authors as $index => $author) {
                $data->push([
                    'Paper ID' => $paper->anonymous_id,
                    'Paper Title' => $paper->title,
                    'Author Order' => $author->pivot->author_order,
                    'Is Corresponding Author' => $author->pivot->is_corresponding_author ? 'Yes' : 'No',
                    'Title' => $author->title ?? 'N/A',
                    'First Name' => $author->first_name,
                    'Last Name' => $author->last_name,
                    'Email' => $author->email,
                    'Institution' => $author->institution ?? 'N/A',
                    'ORCID' => $author->orcid_id ?? 'N/A',
                ]);
            }
        }
        
        return $this->toCsv($data, "DATICAN_Conference_authors_{$year}.csv");
    }

    /**
     * Export statistics summary to CSV
     */
    public function exportStatistics($year = null)
    {
        $year = $year ?? date('Y');
        
        $paperStats = $this->getPaperStats($year);
        $reviewStats = $this->getReviewStats($year);
        $userStats = $this->getUserStats($year);
        
        $data = collect([
            [
                'Metric Category' => 'Papers',
                'Metric Name' => 'Total Papers Submitted',
                'Value' => $paperStats['total'],
            ],
            [
                'Metric Category' => 'Papers',
                'Metric Name' => 'Full Papers',
                'Value' => $paperStats['by_type']['full_paper'] ?? 0,
            ],
            [
                'Metric Category' => 'Papers',
                'Metric Name' => 'Abstract Only',
                'Value' => $paperStats['by_type']['abstract_only'] ?? 0,
            ],
            [
                'Metric Category' => 'Papers',
                'Metric Name' => 'Acceptance Rate (%)',
                'Value' => $paperStats['acceptance_rate'],
            ],
            [
                'Metric Category' => 'Reviews',
                'Metric Name' => 'Total Reviews Assigned',
                'Value' => $reviewStats['total'],
            ],
            [
                'Metric Category' => 'Reviews',
                'Metric Name' => 'Completed Reviews',
                'Value' => $reviewStats['completed'],
            ],
            [
                'Metric Category' => 'Reviews',
                'Metric Name' => 'Pending Reviews',
                'Value' => $reviewStats['pending'],
            ],
            [
                'Metric Category' => 'Reviews',
                'Metric Name' => 'Average Score',
                'Value' => $reviewStats['average_score'],
            ],
            [
                'Metric Category' => 'Reviews',
                'Metric Name' => 'Average Turnaround (days)',
                'Value' => $reviewStats['review_turnaround'],
            ],
            [
                'Metric Category' => 'Users',
                'Metric Name' => 'Total Authors',
                'Value' => $userStats['total_authors'],
            ],
            [
                'Metric Category' => 'Users',
                'Metric Name' => 'Unique Authors',
                'Value' => $userStats['unique_authors'],
            ],
            [
                'Metric Category' => 'Users',
                'Metric Name' => 'Total Reviewers',
                'Value' => $userStats['reviewers'],
            ],
            [
                'Metric Category' => 'Users',
                'Metric Name' => 'Active Reviewers',
                'Value' => $userStats['active_reviewers'],
            ],
            [
                'Metric Category' => 'Reviewer Load',
                'Metric Name' => 'Average Papers per Reviewer',
                'Value' => $reviewStats['reviewer_load']['average'],
            ],
            [
                'Metric Category' => 'Reviewer Load',
                'Metric Name' => 'Max Papers per Reviewer',
                'Value' => $reviewStats['reviewer_load']['max'],
            ],
            [
                'Metric Category' => 'Reviewer Load',
                'Metric Name' => 'Min Papers per Reviewer',
                'Value' => $reviewStats['reviewer_load']['min'],
            ],
        ]);
        
        // Add status breakdown
        foreach ($paperStats['by_status'] as $status => $count) {
            $data->push([
                'Metric Category' => 'Papers by Status',
                'Metric Name' => ucfirst(str_replace('_', ' ', $status)),
                'Value' => $count,
            ]);
        }
        
        // Add topic distribution
        $topics = $this->getTopicDistribution($year);
        foreach ($topics as $topic => $count) {
            $data->push([
                'Metric Category' => 'Papers by Topic',
                'Metric Name' => ucfirst($topic),
                'Value' => $count,
            ]);
        }
        
        // Add recommendation breakdown
        foreach ($reviewStats['by_recommendation'] as $rec => $count) {
            $data->push([
                'Metric Category' => 'Reviews by Recommendation',
                'Metric Name' => ucfirst($rec),
                'Value' => $count,
            ]);
        }
        
        return $this->toCsv($data, "DATICAN_Conference_statistics_{$year}.csv");
    }

    /**
     * Export peer review comparison data
     */
    public function exportPeerReview(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        $papers = Paper::byYear($year)
            ->with(['reviewAssignments' => function($query) {
                $query->where('status', 'completed')->orderBy('id');
            }, 'reviewAssignments.reviewer'])
            ->get();
        
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
                'Reviewer 1 - Recommendation' => $review1->recommendation_text ?? 'N/A',
                'Reviewer 2' => $review2->reviewer->first_name . ' ' . $review2->reviewer->last_name,
                'Reviewer 2 - Relevance (20)' => $review2->criteria_relevance ?? 'N/A',
                'Reviewer 2 - Originality (20)' => $review2->criteria_originality ?? 'N/A',
                'Reviewer 2 - Quality (15)' => $review2->criteria_quality ?? 'N/A',
                'Reviewer 2 - Impact (15)' => $review2->criteria_impact ?? 'N/A',
                'Reviewer 2 - Clarity (10)' => $review2->criteria_clarity ?? 'N/A',
                'Reviewer 2 - Contribution (10)' => $review2->criteria_contribution ?? 'N/A',
                'Reviewer 2 - Total' => $review2Total,
                'Reviewer 2 - Recommendation' => $review2->recommendation_text ?? 'N/A',
                'Score Difference' => abs($review1Total - $review2Total),
                'Average Total Score' => round(($review1Total + $review2Total) / 2, 1),
            ];
        });
        
        return $this->toCsv($data, "DATICAN_Conference_peer_review_comparison_{$year}.csv");
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
}