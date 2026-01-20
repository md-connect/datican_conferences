<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_id',
        'user_id',
        'parent_id',
        'content',
        'type',
        'visibility',
        'is_resolved',
        'metadata'
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Relationships
     */
    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Discussion::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Discussion::class, 'parent_id')->orderBy('created_at');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'discussion_participants')
                    ->withPivot('role', 'has_unread')
                    ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeForPaper($query, $paperId)
    {
        return $query->where('paper_id', $paperId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeVisibleTo($query, $user, $paper)
    {
        $userRoles = $this->getUserRoles($user, $paper);
        
        return $query->where(function($q) use ($userRoles) {
            foreach ($userRoles as $role) {
                $q->orWhere('visibility', $role . 's'); // Convert role to visibility
            }
            $q->orWhere('visibility', 'public');
        });
    }

    /**
     * Get user roles for a paper
     */
    private function getUserRoles($user, $paper)
    {
        $roles = [];
        
        if ($user->is_admin) {
            $roles[] = 'chair';
        }
        
        if ($paper->authors()->where('users.id', $user->id)->exists()) {
            $roles[] = 'author';
        }
        
        if ($paper->reviews()->where('reviewer_id', $user->id)->exists()) {
            $roles[] = 'reviewer';
        }
        
        return $roles;
    }

    /**
     * Add participant to discussion
     */
    public function addParticipant($userId, $role = 'reviewer')
    {
        $this->participants()->syncWithoutDetaching([
            $userId => ['role' => $role]
        ]);
    }

    /**
     * Mark as read for user
     */
    public function markAsRead($userId)
    {
        $this->participants()->updateExistingPivot($userId, [
            'has_unread' => false
        ]);
    }
}