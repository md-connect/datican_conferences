<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ReviewAssignment; 

class ReviewPolicy
{
    public function view(User $user, ReviewAssignment $review) // Change parameter type
    {
        return $user->id === $review->reviewer_id || $user->is_admin;
    }

    public function update(User $user, ReviewAssignment $review) // Change parameter type
    {
        return $user->id === $review->reviewer_id && $review->status !== 'completed';
    }

    public function delete(User $user, ReviewAssignment $review) // Change parameter type
    {
        return $user->is_admin;
    }
}