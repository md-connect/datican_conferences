<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewCriterion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'weight',
        'min_score',
        'max_score',
        'is_active',
        'options'
    ];

    protected $casts = [
        'weight' => 'integer',
        'min_score' => 'integer',
        'max_score' => 'integer',
        'is_active' => 'boolean',
        'options' => 'array'
    ];

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Attributes
     */
    public function getScoreRangeAttribute()
    {
        return range($this->min_score, $this->max_score);
    }

    public function getScoreLabelsAttribute()
    {
        $defaultLabels = [
            1 => 'Poor',
            2 => 'Fair',
            3 => 'Good',
            4 => 'Very Good',
            5 => 'Excellent'
        ];
        
        return $this->options['labels'] ?? $defaultLabels;
    }
}