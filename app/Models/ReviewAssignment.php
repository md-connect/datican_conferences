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
        'started_at', 'submitted_at', 'due_date', 'is_anonymous'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'deadline' => 'datetime',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'due_date' => 'datetime',
        'scores' => 'array',
        'is_anonymous' => 'boolean',
    ];

    protected $appends = ['recommendation_text', 'is_overdue'];

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
                     ->whereIn('status', ['pending', 'accepted', 'in_progress']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'accepted', 'in_progress']);
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
               in_array($this->status, ['pending', 'accepted', 'in_progress']);
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
            'in_progress' => 'bg-indigo-100 text-indigo-800',
            'completed' => 'bg-green-100 text-green-800',
            'declined' => 'bg-red-100 text-red-800',
        ];
        
        $colorClass = $statusColors[$this->status] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $colorClass . '">' .
               ucfirst(str_replace('_', ' ', $this->status)) .
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
               in_array($this->status, ['accepted', 'in_progress']);
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
            'status' => 'accepted',
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
     * Start review (when reviewer first opens edit page)
     */
    public function startReview(): void
    {
        if ($this->status === 'accepted') {
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