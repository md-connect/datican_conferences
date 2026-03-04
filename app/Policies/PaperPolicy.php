<?php

namespace App\Policies;

use App\Models\Paper;
use App\Models\User;

class PaperPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view papers
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Paper $paper): bool
    {
        // Users can view if they are admin, chair, or author
        if ($user->is_admin || $user->is_chair) {
            return true;
        }
        
        // Check if user is an author of the paper
        return $paper->authors()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create papers
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Paper $paper): bool
{
    \Log::info('========== PAPER POLICY UPDATE ==========', [
        'timestamp' => now()->toDateTimeString(),
        'user_id' => $user->id,
        'user_email' => $user->email,
        'paper_id' => $paper->id,
        'paper_status' => $paper->status,
        'paper_submission_type' => $paper->submission_type,
        'is_admin' => $user->is_admin,
        'is_chair' => $user->is_chair ?? false
    ]);
    
    // Users can update if they are admin or chair
    if ($user->is_admin || $user->is_chair) {
        \Log::info('POLICY: Allowed - user is admin/chair');
        return true;
    }
    
    // Check if user is an author of the paper
    \Log::info('POLICY: Checking author status');
    $authorQuery = $paper->authors()->where('users.id', $user->id);
    $isAuthor = $authorQuery->exists();
    
    // Get all authors for debugging
    $allAuthors = $paper->authors->map(function($author) {
        return [
            'id' => $author->id,
            'email' => $author->email,
            'name' => $author->name
        ];
    })->toArray();
    
    \Log::info('POLICY: Author check results', [
        'is_author' => $isAuthor,
        'current_user_id' => $user->id,
        'all_authors' => $allAuthors,
        'author_count' => $paper->authors->count()
    ]);
    
    if (!$isAuthor) {
        \Log::info('POLICY: Denied - user is not an author');
        return false;
    }
    
    // Log the status check
    \Log::info('POLICY: Checking status', [
        'current_status' => $paper->status,
        'editable_statuses' => ['draft', 'submitted', 'needs_revision'],
        'is_abstract_accepted' => ($paper->status === 'abstract_accepted' && $paper->submission_type === 'abstract_only')
    ]);
    
    // Authors can edit papers in these statuses
    $editableStatuses = ['draft', 'submitted', 'needs_revision'];
    
    // Also allow if abstract is accepted (to upload full paper)
    if ($paper->status === 'abstract_accepted' && $paper->submission_type === 'abstract_only') {
        \Log::info('POLICY: Allowed - abstract accepted, can upload full paper');
        return true;
    }
    
    $result = in_array($paper->status, $editableStatuses);
    
    \Log::info('POLICY: Final decision', [
        'status_in_array' => $result,
        'result' => $result
    ]);
    
    \Log::info('========== POLICY UPDATE END ==========');
    
    return $result;
}
public function before(User $user, $ability)
{
    \Log::info('========== POLICY BEFORE ==========', [
        'user_id' => $user->id,
        'user_email' => $user->email,
        'ability' => $ability,
        'is_admin' => $user->is_admin,
        'is_chair' => $user->is_chair ?? false
    ]);
    
    // Return null to let other methods decide
    return null;
}
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Paper $paper): bool
    {
        // Only admins and chairs can delete papers
        return $user->is_admin || $user->is_chair;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Paper $paper): bool
    {
        return $user->is_admin || $user->is_chair;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Paper $paper): bool
    {
        return $user->is_admin || $user->is_chair;
    }

    /**
     * Determine whether the user can submit the model.
     */
    public function submit(User $user, Paper $paper): bool
    {
        // Users can submit if they are author and paper is draft
        // Admins and chairs can also submit on behalf of authors
        if ($user->is_admin || $user->is_chair) {
            return true;
        }
        
        return $paper->authors()->where('users.id', $user->id)->exists()
            && $paper->status === 'draft';
    }

    /**
     * Determine whether the user can download the model.
     */
    public function download(User $user, Paper $paper): bool
    {
        // Admins and chairs can download any paper
        if ($user->is_admin || $user->is_chair) {
            return true;
        }
        
        // Authors can download their own papers
        if ($paper->authors()->where('users.id', $user->id)->exists()) {
            return true;
        }
        
        // Reviewers can download papers they're assigned to
        if ($user->is_reviewer) {
            return $paper->reviews()->where('reviewer_id', $user->id)->exists();
        }
        
        return false;
    }

    /**
     * Determine whether the user can update the status.
     */
    public function updateStatus(User $user, Paper $paper): bool
    {
        // Only admins and chairs can update paper status
        return $user->is_admin || $user->is_chair;
    }

    /**
     * Determine whether the user has admin privileges for papers.
     */
    public function admin(User $user): bool
    {
        // Only admins and chairs have admin privileges
        return $user->is_admin || $user->is_chair;
    }

    /**
     * Determine whether the user can make decisions on papers.
     */
    public function decide(User $user, Paper $paper): bool
    {
        // Only admins and chairs can make decisions on papers
        return $user->is_admin || $user->is_chair;
    }

    /**
     * Determine whether the user can assign reviewers to papers.
     */
    public function assignReviewers(User $user, Paper $paper): bool
    {
        // Only admins and chairs can assign reviewers
        return $user->is_admin || $user->is_chair;
    }

    /**
     * Determine whether the user can view all papers (for chair dashboard).
     */
    public function viewAll(User $user): bool
    {
        // Admins and chairs can view all papers
        return $user->is_admin || $user->is_chair;
    }
}