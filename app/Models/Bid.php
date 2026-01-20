<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_id',
        'reviewer_id',
        'preference',
        'comments',
        'expertise_scores'
    ];

    protected $casts = [
        'expertise_scores' => 'array',
    ];

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

    /**
     * Scopes
     */
    public function scopePositive($query)
    {
        return $query->whereIn('preference', ['very_high', 'high', 'medium']);
    }

    public function scopeNegative($query)
    {
        return $query->whereIn('preference', ['low', 'very_low']);
    }

    public function scopeConflicts($query)
    {
        return $query->where('preference', 'conflict');
    }

    public function scopeForReviewer($query, $reviewerId)
    {
        return $query->where('reviewer_id', $reviewerId);
    }

    /**
     * Attributes
     */
    public function getPreferenceTextAttribute()
    {
        $preferences = [
            'very_high' => 'Very High Interest',
            'high' => 'High Interest',
            'medium' => 'Medium Interest',
            'low' => 'Low Interest',
            'very_low' => 'Very Low Interest',
            'conflict' => 'Conflict of Interest',
            'no_bid' => 'No Bid',
        ];
        
        return $preferences[$this->preference] ?? 'Not Specified';
    }

    public function getPreferenceScoreAttribute()
    {
        $scores = [
            'very_high' => 5,
            'high' => 4,
            'medium' => 3,
            'low' => 2,
            'very_low' => 1,
            'conflict' => -100, // Heavy penalty for conflicts
            'no_bid' => 0,
        ];
        
        return $scores[$this->preference] ?? 0;
    }

    /**
     * Check if bid indicates conflict
     */
    public function getIsConflictAttribute()
    {
        return $this->preference === 'conflict';
    }
}