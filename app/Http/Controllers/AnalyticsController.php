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

    private function exportPapers($year)
    {
        $papers = Paper::byYear($year)
            ->with(['authors', 'reviews'])
            ->get()
            ->map(function($paper) {
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
            
        return $this->toCsv($papers, "papers_{$year}.csv");
    }

    private function exportReviews($year)
    {
        $reviews = ReviewAssignment::whereHas('paper', function($q) use ($year) {
            $q->where('conference_year', $year);
        })->with(['paper', 'reviewer'])
          ->get()
          ->map(function($review) {
              return [
                  'Paper ID' => $review->paper->anonymous_id,
                  'Paper Title' => $review->paper->title,
                  'Reviewer' => $review->reviewer->full_name,
                  'Status' => $review->status,
                  'Overall Score' => $review->overall_score,
                  'Recommendation' => $review->recommendation_text,
                  'Assigned Date' => $review->assigned_at?->format('Y-m-d H:i:s'),
                  'Submitted Date' => $review->submitted_at?->format('Y-m-d H:i:s'),
                  'Turnaround Days' => $review->assigned_at && $review->submitted_at 
                    ? $review->assigned_at->diffInDays($review->submitted_at) 
                    : null,
              ];
          });
          
        return $this->toCsv($reviews, "reviews_{$year}.csv");
    }

    private function toCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()));
            }
            
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}