<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paper extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'abstract',
        'keywords',
        'topic_area',
        'submission_type',
        'file_path',
        'file_name',
        'file_size',
        'status',
        'anonymous_id',
        'is_anonymous',
        'author_comments',
        'decision',
        'decision_notes',
        'decision_made_at',
        'decision_made_by',
        'submitted_at',
        'review_due_date',
        'revision_deadline',
        'conference_year',
        'created_by',
        'updated_by',
        // NEW FIELDS
        'needs_revision',
        'revision_requested_at',
        'revision_submitted_at',
        'revision_notes',
        'has_revision_recommendations',
        'revision_recommendation_count',
        'version',
        'all_reviews_completed',
        'abstract_accepted_at',
        'full_paper_deadline',
        'needs_revision',
        'revision_requested_at',
        'revision_deadline',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'revision_deadline' => 'date',
        'review_due_date' => 'datetime',
        'decision_made_at' => 'datetime',
        'is_anonymous' => 'boolean',
        'file_size' => 'integer',
        // NEW CASTS
        'needs_revision' => 'boolean',
        'revision_requested_at' => 'datetime',
        'revision_submitted_at' => 'datetime',
        'has_revision_recommendations' => 'boolean',
        'revision_recommendation_count' => 'integer',
        'version' => 'integer',
        'all_reviews_completed' => 'boolean',
        'abstract_accepted_at' => 'datetime',
        'full_paper_deadline' => 'date',
        'revision_deadline' => 'date',
    ];

    protected $appends = ['author_list', 'file_size_formatted', 'status_badge', 'is_abstract_only', 'is_full_paper'];

    protected static function booted()
    {
        static::creating(function ($paper) {
            if (empty($paper->anonymous_id)) {
                $year = date('Y');
                $existingIds = Paper::where('conference_year', $year)
                                    ->pluck('anonymous_id')
                                    ->toArray();

                $nextNumber = 1;

                if (!empty($existingIds)) {
                    $numbers = array_map(function ($id) {
                        return (int) substr($id, -4);
                    }, $existingIds);
                    $nextNumber = max($numbers) + 1;
                }

                $paper->anonymous_id = "{$year}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
            
            // Set default version
            if (empty($paper->version)) {
                $paper->version = 1;
            }
        });
        
        static::updating(function ($paper) {
            // Increment version when revision is submitted
            if ($paper->isDirty('revision_submitted_at') && $paper->revision_submitted_at !== null) {
                $paper->version = $paper->version + 1;
            }
        });
    }

    /**
     * Relationships
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'paper_authors')
                    ->withPivot('is_corresponding', 'author_order')
                    ->withTimestamps()
                    ->orderBy('paper_authors.author_order');
    }

    public function correspondingAuthor()
    {
        return $this->authors()->wherePivot('is_corresponding', true)->first();
    }

    public function registrations(): BelongsToMany
    {
        return $this->belongsToMany(ConferenceRegistration::class, 'paper_registrations')
                    ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(ReviewAssignment::class, 'paper_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function cameraReady(): HasOne
    {
        return $this->hasOne(CameraReady::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('conference_year', $year);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('authors', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }
    
    // NEW SCOPES
    public function scopeAbstractOnly($query)
    {
        return $query->where('submission_type', 'abstract_only');
    }
    
    public function scopeFullPaper($query)
    {
        return $query->where('submission_type', 'full_paper');
    }
    
    public function scopeNeedsRevision($query)
    {
        return $query->where('needs_revision', true);
    }
    
    public function scopeWithRevisionRecommendations($query)
    {
        return $query->where('has_revision_recommendations', true);
    }

    /**
     * Attributes
     */
    public function getAuthorListAttribute()
    {
        return $this->authors->map(function ($author) {
            return $author->first_name . ' ' . $author->last_name;
        })->join(', ');
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'under_review' => 'bg-yellow-100 text-yellow-800',
            'accepted' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'camera_ready' => 'bg-purple-100 text-purple-800',
            'abstract_submitted' => 'bg-orange-100 text-orange-800',
            'abstract_under_review' => 'bg-yellow-100 text-yellow-800',
            'abstract_accepted' => 'bg-emerald-100 text-emerald-800',
            'abstract_rejected' => 'bg-red-100 text-red-800',
            'needs_revision' => 'bg-yellow-100 text-yellow-800',
        ];

        $status = ucfirst(str_replace('_', ' ', $this->status));
        return "<span class='px-2 py-1 text-xs rounded-full {$badges[$this->status]}'>{$status}</span>";
    }

    public function getAverageScoreAttribute()
    {
        return $this->reviews()->avg('overall_score') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }
    
    // NEW ATTRIBUTES
    public function getIsAbstractOnlyAttribute()
    {
        return $this->submission_type === 'abstract_only';
    }
    
    public function getIsFullPaperAttribute()
    {
        return $this->submission_type === 'full_paper';
    }
    
    public function getCanSubmitFullPaperAttribute()
    {
        return $this->submission_type === 'abstract_only' && 
               !$this->file_path && 
               in_array($this->status, ['abstract_accepted', 'accepted']);
    }
    
    public function getRevisionRecommendationsListAttribute()
    {
        return $this->reviews()
            ->where('status', 'completed')
            ->whereNotNull('revision_suggestions')
            ->pluck('revision_suggestions');
    }

    public function canBeEditedBy($user)
    {
        if (!$this->authors()->where('users.id', $user->id)->exists()) {
            return false;
        }
        
        // Allow editing in these states
        $editableStates = ['draft', 'needs_revision'];
        
        // Also allow if abstract is accepted (to upload full paper)
        if ($this->status === 'abstract_accepted' && $this->submission_type === 'abstract_only') {
            return true;
        }
        
        return in_array($this->status, $editableStates);
    }

    public function canBeReviewedBy($user)
    {
        return $this->reviews()->where('reviewer_id', $user->id)->exists();
    }

    public function reviewAssignments()
    {
        return $this->hasMany(ReviewAssignment::class, 'paper_id');
    }
    
    /**
     * Get completed reviews for this paper
     */
    public function completedReviews()
    {
        return $this->reviewAssignments()->where('status', 'completed');
    }
    
    // NEW METHODS
    public function updateRevisionRecommendationStatus()
    {
        $revisionCount = $this->reviews()
            ->where('status', 'completed')
            ->whereIn('recommendation', ['minor_revisions', 'major_revisions'])
            ->count();
        
        $this->update([
            'has_revision_recommendations' => $revisionCount > 0,
            'revision_recommendation_count' => $revisionCount,
        ]);
    }
    
    public function checkAllReviewsCompleted()
    {
        $totalAssignments = $this->reviewAssignments()
            ->where('status', '!=', 'declined')
            ->count();
        
        $completedAssignments = $this->reviewAssignments()
            ->where('status', 'completed')
            ->count();
        
        $allCompleted = ($totalAssignments > 0) && ($completedAssignments === $totalAssignments);
        
        $this->update(['all_reviews_completed' => $allCompleted]);
        
        return $allCompleted;
    }
}