<?php

namespace App\Services;

use App\Models\Paper;
use App\Models\User;
use App\Models\Bid;
use App\Models\ReviewerExpertise;
use App\Models\ReviewAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AssignmentService
{
    private $papers;
    private $reviewers;
    private $assignments = [];
    private $config = [
        'min_reviews_per_paper' => 2,
        'max_reviews_per_paper' => 2,
        'max_papers_per_reviewer' => 10,
        'min_papers_per_reviewer' => 0,
        'weight_bid' => 0.4,
        'weight_expertise' => 0.4,
        'weight_load' => 0.2,
    ];

    /**
     * Run automatic assignment
     */
    public function autoAssign($conferenceYear, array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        
        // Load papers that need assignments (papers with less than 2 active reviews)
        $this->papers = Paper::where('conference_year', $conferenceYear)
            ->whereIn('status', ['submitted', 'under_review'])
            ->where(function($query) {
                $query->whereDoesntHave('reviews')
                    ->orWhereHas('reviews', function($q) {
                        $q->whereIn('status', ['pending', 'under_review', 'in_progress'])
                        ->havingRaw('COUNT(*) < 2');
                    });
            })
            ->with(['bids', 'authors', 'reviews.reviewer'])
            ->get();
            
        // Load potential reviewers
        $this->reviewers = User::where('is_reviewer', true)
            ->with(['expertise', 'reviewAssignments' => function($q) use ($conferenceYear) {
                $q->whereHas('paper', function($q2) use ($conferenceYear) {
                    $q2->where('conference_year', $conferenceYear);
                });
            }])
            ->get();

        // Clear existing pending assignments for papers that will be reassigned
        ReviewAssignment::where('status', 'pending')
            ->whereHas('paper', function($q) use ($conferenceYear) {
                $q->where('conference_year', $conferenceYear)
                ->whereIn('status', ['submitted', 'under_review']);
            })
            ->delete();

        foreach ($this->papers as $paper) {
            $this->assignReviewersToPaper($paper);
        }

        // Save assignments
        $savedCount = 0;
        foreach ($this->assignments as $assignment) {
            try {
                ReviewAssignment::create($assignment);
                $savedCount++;
            } catch (\Exception $e) {
                // Skip duplicate assignments
                continue;
            }
        }

        return $savedCount;
    }

    /**
     * Assign reviewers to a specific paper
     */
    private function assignReviewersToPaper(Paper $paper)
    {
        // Skip if paper already has enough reviews
        $currentReviews = $paper->reviews->whereIn('status', ['in_progress', 'completed'])->count();
        if ($currentReviews >= $this->config['max_reviews_per_paper']) {
            return;
        }
        
        $neededReviews = max(0, $this->config['min_reviews_per_paper'] - $currentReviews);
        if ($neededReviews <= 0) {
            return;
        }
        
        $candidates = $this->getCandidatesForPaper($paper);
        
        // Sort candidates by score
        $candidates = $candidates->sortByDesc('score');
        
        // Select top candidates
        $selected = $candidates->take(min($neededReviews, $this->config['max_reviews_per_paper']));
        
        foreach ($selected as $candidate) {
            $this->assignments[] = [
                'paper_id' => $paper->id,
                'reviewer_id' => $candidate['reviewer']->id,
                'assigned_by' => auth()->id() ?? 1,
                'status' => 'pending',
                'assigned_at' => now(),
                'deadline' => now()->addWeeks(2),
                'notes' => 'Auto-assigned based on algorithm',
            ];
        }
    }

    /**
     * Get candidate reviewers for a paper
     */
    private function getCandidatesForPaper(Paper $paper): Collection
    {
        $candidates = collect();
        
        foreach ($this->reviewers as $reviewer) {
            // Ensure reviewer is actually marked as reviewer
            if (!$reviewer->is_reviewer) {
                continue;
            }
            
            // Check for conflicts
            if ($this->hasConflict($reviewer, $paper)) {
                continue;
            }
            
            // Check if already assigned to this paper
            if ($paper->reviews->where('reviewer_id', $reviewer->id)->isNotEmpty()) {
                continue;
            }
            
            // Check current load
            $currentLoad = $reviewer->reviewAssignments
                ->whereIn('status', ['pending', 'under_review', 'in_progress'])
                ->count();
                
            if ($currentLoad >= $this->config['max_papers_per_reviewer']) {
                continue;
            }
            
            // Calculate scores
            $bidScore = $this->calculateBidScore($reviewer, $paper);
            $expertiseScore = $this->calculateExpertiseScore($reviewer, $paper);
            $loadScore = $this->calculateLoadScore($currentLoad);
            
            $totalScore = (
                $bidScore * $this->config['weight_bid'] +
                $expertiseScore * $this->config['weight_expertise'] +
                $loadScore * $this->config['weight_load']
            );
            
            $candidates->push([
                'reviewer' => $reviewer,
                'bid_score' => $bidScore,
                'expertise_score' => $expertiseScore,
                'load_score' => $loadScore,
                'score' => $totalScore,
                'current_load' => $currentLoad,
            ]);
        }
        
        return $candidates;
    }

    /**
     * Check for conflicts between reviewer and paper
     */
    private function hasConflict(User $reviewer, Paper $paper): bool
    {
        // Check if reviewer is an author
        if ($paper->authors->contains('id', $reviewer->id)) {
            return true;
        }
        
        // Check if reviewer has declared conflict via bid
        $conflictBid = $paper->bids
            ->where('reviewer_id', $reviewer->id)
            ->where('preference', 'conflict')
            ->first();
            
        return $conflictBid !== null;
    }

    /**
     * Calculate bid score
     */
    private function calculateBidScore(User $reviewer, Paper $paper): float
    {
        $bid = $paper->bids->where('reviewer_id', $reviewer->id)->first();
        
        if (!$bid) {
            return 0.5; // Neutral score for no bid
        }
        
        $preferenceScores = [
            'very_high' => 5,
            'high' => 4,
            'medium' => 3,
            'low' => 2,
            'very_low' => 1,
            'conflict' => -100,
            'no_bid' => 0,
        ];
        
        $score = $preferenceScores[$bid->preference] ?? 0;
        return max(0, $score / 5.0); // Normalize to 0-1
    }

    /**
     * Calculate expertise score
     */
    private function calculateExpertiseScore(User $reviewer, Paper $paper): float
    {
        $keywords = explode(',', $paper->keywords);
        $topicArea = $paper->topic_area;
        
        $totalScore = 0;
        $matchedTopics = 0;
        
        // If reviewer has no expertise, return low default
        if ($reviewer->expertise->isEmpty()) {
            return 0.2;
        }
        
        // Level scores
        $levelScores = [
            'expert' => 5,
            'proficient' => 4,
            'familiar' => 3,
            'basic' => 2,
        ];
        
        // Match keywords with reviewer's expertise
        foreach ($keywords as $keyword) {
            $keyword = trim(strtolower($keyword));
            
            foreach ($reviewer->expertise as $expertise) {
                $expertiseTopic = strtolower($expertise->topic);
                
                if (str_contains($expertiseTopic, $keyword) || 
                    str_contains($keyword, $expertiseTopic)) {
                    
                    $levelScore = $levelScores[$expertise->level] ?? 1;
                    $confidenceScore = $expertise->confidence / 5.0;
                    
                    $totalScore += $levelScore * $confidenceScore;
                    $matchedTopics++;
                    break;
                }
            }
        }
        
        // Also match main topic area (weighted double)
        foreach ($reviewer->expertise as $expertise) {
            $expertiseTopic = strtolower($expertise->topic);
            $paperTopic = strtolower($topicArea);
            
            if (str_contains($expertiseTopic, $paperTopic) || 
                str_contains($paperTopic, $expertiseTopic)) {
                
                $levelScore = $levelScores[$expertise->level] ?? 1;
                $confidenceScore = $expertise->confidence / 5.0;
                $totalScore += ($levelScore * $confidenceScore) * 2; // Double weight
                $matchedTopics++;
                break;
            }
        }
        
        if ($matchedTopics === 0) {
            return 0.2; // Low default for no match
        }
        
        // Normalize to 0-1 (max possible score per match is 5)
        $maxPossibleScore = $matchedTopics * 5;
        return min($totalScore / $maxPossibleScore, 1.0);
    }

    /**
     * Calculate load balancing score
     */
    private function calculateLoadScore(int $currentLoad): float
    {
        $maxLoad = $this->config['max_papers_per_reviewer'];
        
        if ($currentLoad === 0) {
            return 1.0; // Highest priority for reviewers with no load
        }
        
        if ($currentLoad >= $maxLoad) {
            return 0.0; // No priority for overloaded reviewers
        }
        
        // Exponential decay: reviewers with more load get lower scores
        return exp(-$currentLoad / ($maxLoad / 2));
    }

    /**
     * Manual assignment with suggestions
     */
    public function suggestReviewers(Paper $paper, int $limit = 10): Collection
    {
        // Get all reviewers (users with is_reviewer = true)
        $reviewers = User::where('is_admin', false)
            ->where('is_reviewer', true)  // Make sure we only get reviewers
            ->with(['expertise', 'reviewAssignments' => function($q) use ($paper) {
                $q->where('paper_id', $paper->id)
                    ->orWhere(function($query) {
                        $query->whereIn('status', ['pending', 'under_review', 'in_progress']);
                    });
            }])
            ->get();
        
        $this->reviewers = $reviewers;
        $candidates = $this->getCandidatesForPaper($paper);
        
        if ($candidates->isEmpty()) {
            return collect();
        }
        
        return $candidates->sortByDesc('score')
            ->take($limit)
            ->values()
            ->map(function($candidate) {
                $reviewer = $candidate['reviewer'];
                
                // Calculate match score percentage
                $matchScore = round($candidate['score'] * 100);
                
                // Get expertise topics
                $expertiseTopics = $reviewer->expertise->map(function($exp) {
                    return [
                        'name' => $exp->topic,
                        'level' => $exp->level,
                        'confidence' => $exp->confidence
                    ];
                });
                
                return [
                    'id' => $reviewer->id,
                    'first_name' => $reviewer->first_name,
                    'last_name' => $reviewer->last_name,
                    'full_name' => $reviewer->first_name . ' ' . $reviewer->last_name,
                    'email' => $reviewer->email,
                    'institution' => $reviewer->institution ?? 'Not specified',
                    'assigned_count' => $candidate['current_load'],
                    'match_score' => $matchScore,
                    'bid_score' => round($candidate['bid_score'] * 100),
                    'expertise_score' => round($candidate['expertise_score'] * 100),
                    'load_score' => round($candidate['load_score'] * 100),
                    'expertise' => $expertiseTopics,
                    'expertise_levels' => $expertiseTopics->map(function($exp) {
                        $levelLabels = [
                            'expert' => 'Expert',
                            'proficient' => 'Proficient',
                            'familiar' => 'Familiar',
                            'basic' => 'Basic'
                        ];
                        return [
                            'topic' => $exp['name'],
                            'level' => $levelLabels[$exp['level']] ?? $exp['level']
                        ];
                    }),
                ];
            });
    }
}