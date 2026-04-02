<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_id', 'reviewer_id', 'assigned_by', 'status', 'assigned_at', 'deadline', 'notes',
        'overall_score', 'scores', 'comments_author', 'comments_chair', 'confidence',
        'summary', 'strengths', 'weaknesses', 'suggestions', 'recommendation',
        'started_at', 'submitted_at', 'due_date', 'is_anonymous',
        // Revision fields
        'revision_suggestions',
        'is_revision_review',
        'original_review_id',
        'paper_version',
        // NEW SCORING CRITERIA FIELDS
        'criteria_relevance',
        'criteria_originality',
        'criteria_quality',
        'criteria_impact',
        'criteria_clarity',
        'criteria_contribution',
        'total_score',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'deadline' => 'datetime',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'due_date' => 'datetime',
        'scores' => 'array',
        'is_anonymous' => 'boolean',
        // Revision casts
        'is_revision_review' => 'boolean',
        'paper_version' => 'integer',
        // NEW SCORING CRITERIA CASTS
        'criteria_relevance' => 'integer',
        'criteria_originality' => 'integer',
        'criteria_quality' => 'integer',
        'criteria_impact' => 'integer',
        'criteria_clarity' => 'integer',
        'criteria_contribution' => 'integer',
        'total_score' => 'integer',
    ];

    protected $appends = ['recommendation_text', 'is_overdue', 'score_percentage', 'score_badge_class'];

    /**
     * Relationships
     */
    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDeclined($query)
    {
        return $query->where('status', 'declined');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                     ->whereIn('status', ['pending', 'under_review', 'in_progress']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'under_review', 'in_progress']);
    }

    /**
     * Accessors
     */
    
    /**
     * Check if assignment is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline && 
               $this->deadline < now() && 
               in_array($this->status, ['pending', 'under_review', 'in_progress']);
    }

    /**
     * Get recommendation text
     */
    public function getRecommendationTextAttribute(): string
    {
        $recommendationTexts = [
            'strong_accept' => 'Strong Accept',
            'accept' => 'Accept',
            'weak_accept' => 'Weak Accept',
            'borderline' => 'Borderline',
            'weak_reject' => 'Weak Reject',
            'reject' => 'Reject',
            'strong_reject' => 'Strong Reject',
        ];
        
        return $recommendationTexts[$this->recommendation] ?? 'No recommendation';
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $statusColors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'accepted' => 'bg-blue-100 text-blue-800',
            'under_review' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-indigo-100 text-indigo-800',
            'completed' => 'bg-green-100 text-green-800',
            'declined' => 'bg-red-100 text-red-800',
        ];
        
        $colorClass = $statusColors[$this->status] ?? 'bg-gray-100 text-gray-800';
        
        $statusText = $this->status === 'under_review' 
            ? 'Under Review' 
            : ucfirst(str_replace('_', ' ', $this->status));
        
        return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $colorClass . '">' .
               $statusText .
               '</span>';
    }

    /**
     * Get confidence text
     */
    public function getConfidenceTextAttribute(): string
    {
        $confidenceTexts = [
            'expert' => 'Expert',
            'familiar' => 'Familiar',
            'passing' => 'Passing Knowledge',
            'knowledgeable' => 'Knowledgeable',
        ];
        
        return $confidenceTexts[$this->confidence] ?? 'Not specified';
    }

    /**
     * Get days remaining until deadline
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->deadline) {
            return null;
        }
        
        return now()->diffInDays($this->deadline, false);
    }

    /**
     * Get time remaining text
     */
    public function getTimeRemainingAttribute(): string
    {
        if (!$this->deadline) {
            return 'No deadline';
        }
        
        $days = $this->days_remaining;
        
        if ($days < 0) {
            return 'Overdue by ' . abs($days) . ' day' . (abs($days) !== 1 ? 's' : '');
        } elseif ($days === 0) {
            return 'Due today';
        } elseif ($days === 1) {
            return 'Due tomorrow';
        } else {
            return $days . ' days remaining';
        }
    }

    // NEW METHODS FOR SCORING CRITERIA
    
    /**
     * Get formatted criteria scores as array
     */
    public function getCriteriaScoresAttribute(): array
    {
        return [
            'relevance' => $this->criteria_relevance,
            'originality' => $this->criteria_originality,
            'quality' => $this->criteria_quality,
            'impact' => $this->criteria_impact,
            'clarity' => $this->criteria_clarity,
            'contribution' => $this->criteria_contribution,
            'total' => $this->total_score,
        ];
    }
    
    /**
     * Get the total score percentage (0-100)
     */
    public function getScorePercentageAttribute(): int
    {
        return $this->total_score ? round(($this->total_score / 100) * 100) : 0;
    }
    
    /**
     * Get score badge class based on total score
     */
    public function getScoreBadgeClassAttribute(): string
    {
        if (!$this->total_score) return 'bg-gray-100 text-gray-800';
        
        if ($this->total_score >= 80) return 'bg-green-100 text-green-800';
        if ($this->total_score >= 60) return 'bg-yellow-100 text-yellow-800';
        return 'bg-red-100 text-red-800';
    }
    
    /**
     * Get score description based on total score
     */
    public function getScoreDescriptionAttribute(): string
    {
        if (!$this->total_score) return 'Not rated';
        
        if ($this->total_score >= 90) return 'Excellent';
        if ($this->total_score >= 80) return 'Very Good';
        if ($this->total_score >= 70) return 'Good';
        if ($this->total_score >= 60) return 'Satisfactory';
        if ($this->total_score >= 50) return 'Below Average';
        return 'Poor';
    }
    
    /**
     * Get individual criterion rating text
     */
    public function getRelevanceRatingAttribute(): string
    {
        return $this->getCriterionRating($this->criteria_relevance, 20);
    }
    
    public function getOriginalityRatingAttribute(): string
    {
        return $this->getCriterionRating($this->criteria_originality, 20);
    }
    
    public function getQualityRatingAttribute(): string
    {
        return $this->getCriterionRating($this->criteria_quality, 15);
    }
    
    public function getImpactRatingAttribute(): string
    {
        return $this->getCriterionRating($this->criteria_impact, 15);
    }
    
    public function getClarityRatingAttribute(): string
    {
        return $this->getCriterionRating($this->criteria_clarity, 10);
    }
    
    public function getContributionRatingAttribute(): string
    {
        return $this->getCriterionRating($this->criteria_contribution, 10);
    }
    
    /**
     * Helper method to get rating text for a criterion
     */
    private function getCriterionRating($score, $max): string
    {
        if (!$score) return 'Not rated';
        
        $percentage = ($score / $max) * 100;
        
        if ($percentage >= 85) return 'Excellent';
        if ($percentage >= 70) return 'Good';
        if ($percentage >= 50) return 'Satisfactory';
        return 'Needs Improvement';
    }

    // NEW METHODS FOR PEER REVIEW (2 REVIEWERS PER PAPER)
    
    /**
     * Check if this is the second review for the paper
     */
    public function isSecondReview(): bool
    {
        $completedReviews = ReviewAssignment::where('paper_id', $this->paper_id)
            ->where('status', 'completed')
            ->count();
        
        return $completedReviews >= 1;
    }
    
    /**
     * Check if all reviews for the paper are completed (max 2)
     */
    public function allReviewsCompleted(): bool
    {
        $totalAssignments = ReviewAssignment::where('paper_id', $this->paper_id)
            ->where('status', '!=', 'declined')
            ->count();
        
        $completedAssignments = ReviewAssignment::where('paper_id', $this->paper_id)
            ->where('status', 'completed')
            ->count();
        
        // For peer review with max 2 reviewers
        return $completedAssignments >= 2 && $completedAssignments == $totalAssignments;
    }
    
    /**
     * Get the review number for this assignment (1st or 2nd reviewer)
     */
    public function getReviewNumberAttribute(): int
    {
        $completedCount = ReviewAssignment::where('paper_id', $this->paper_id)
            ->where('status', 'completed')
            ->where('id', '<=', $this->id)
            ->count();
        
        return $completedCount;
    }
    
    /**
     * Get the average score of both reviewers for the paper
     */
    public static function getAverageScoreForPaper($paperId)
    {
        $completedReviews = self::where('paper_id', $paperId)
            ->where('status', 'completed')
            ->get();
        
        if ($completedReviews->isEmpty()) {
            return null;
        }
        
        $totalScore = $completedReviews->sum('total_score');
        return round($totalScore / $completedReviews->count(), 2);
    }
    
    /**
     * Check if review can be edited
     */
    public function getCanEditAttribute(): bool
    {
        return $this->reviewer_id === auth()->id() && 
               $this->status !== 'completed' && 
               $this->status !== 'declined';
    }

    /**
     * Check if review can be accepted
     */
    public function getCanAcceptAttribute(): bool
    {
        return $this->reviewer_id === auth()->id() && 
               $this->status === 'pending';
    }

    /**
     * Check if review can be declined
     */
    public function getCanDeclineAttribute(): bool
    {
        return $this->reviewer_id === auth()->id() && 
               $this->status === 'pending';
    }

    /**
     * Check if review can be submitted
     */
    public function getCanSubmitAttribute(): bool
    {
        return $this->reviewer_id === auth()->id() && 
               in_array($this->status, ['under_review', 'in_progress']);
    }

    /**
     * Check if review is viewable by current user
     */
    public function getCanViewAttribute(): bool
    {
        return $this->reviewer_id === auth()->id() || 
               auth()->user()->is_admin;
    }

    /**
     * Accept assignment
     */
    public function accept(): void
    {
        $this->update([
            'status' => 'under_review',
            'started_at' => now()
        ]);
    }

    /**
     * Decline assignment
     */
    public function decline(): void
    {
        $this->update(['status' => 'declined']);
    }

    /**
     * Start review
     */
    public function startReview(): void
    {
        if ($this->status === 'under_review') {
            $this->update(['status' => 'in_progress']);
        }
    }

    /**
     * Save as draft
     */
    public function saveDraft(array $data): void
    {
        $this->update(array_merge($data, [
            'status' => 'in_progress',
            'submitted_at' => null,
        ]));
    }

    /**
     * Submit review
     */
    public function submitReview(array $data): void
    {
        $this->update(array_merge($data, [
            'status' => 'completed',
            'submitted_at' => now(),
        ]));
    }

    /**
     * Check if scores are valid
     */
    public function hasValidScores(): bool
    {
        if (!$this->scores || !is_array($this->scores)) {
            return false;
        }
        
        // Check if all scores are between 1-5
        foreach ($this->scores as $score) {
            if (!is_numeric($score) || $score < 1 || $score > 5) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Calculate average score
     */
    public function getAverageScoreAttribute(): ?float
    {
        if (!$this->hasValidScores()) {
            return $this->overall_score;
        }
        
        $scores = array_values($this->scores);
        return count($scores) > 0 ? array_sum($scores) / count($scores) : null;
    }

    /**
     * Get review quality indicator
     */
    public function getQualityIndicatorAttribute(): string
    {
        if (!$this->comments_author || strlen($this->comments_author) < 100) {
            return 'low';
        }
        
        $wordCount = str_word_count($this->comments_author);
        
        if ($wordCount > 300) {
            return 'high';
        } elseif ($wordCount > 150) {
            return 'medium';
        } else {
            return 'low';
        }
    }
}