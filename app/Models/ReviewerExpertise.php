<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewerExpertise extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic',
        'level',
        'confidence'
    ];

    protected $casts = [
        'confidence' => 'integer',
    ];

    /**
     * Relationships
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get expertise score for matching
     */
    public function getExpertiseScoreAttribute()
    {
        $levelScores = [
            'expert' => 5,
            'proficient' => 4,
            'familiar' => 3,
            'basic' => 2,
        ];
        
        $levelScore = $levelScores[$this->level] ?? 1;
        return $levelScore * ($this->confidence / 5.0);
    }
}