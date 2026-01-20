<?php

namespace App\Policies;

use App\Models\Discussion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiscussionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Discussion $discussion)
    {
        // User can view if they're a participant or have appropriate role
        return $discussion->participants()->where('user_id', $user->id)->exists() ||
               $user->is_admin ||
               $discussion->paper->authors()->where('users.id', $user->id)->exists() ||
               $discussion->paper->reviews()->where('reviewer_id', $user->id)->exists();
    }

    public function update(User $user, Discussion $discussion)
    {
        return $discussion->user_id === $user->id;
    }

    public function delete(User $user, Discussion $discussion)
    {
        return $user->is_admin || $discussion->user_id === $user->id;
    }

    public function resolve(User $user, Discussion $discussion)
    {
        return $user->is_admin;
    }
}