<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 
        'is_admin', 'is_chair', 'is_reviewer', 'affiliation',
    ];

    protected $hidden = [
        'password', 'remember_token', 
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_chair' => 'boolean', // Add this
        'is_reviewer' => 'boolean',
    ];

    // Add these relationships
    public function conferenceRegistration()
    {
        return $this->hasOne(ConferenceRegistration::class);
    }

    public function papers()
    {
        return $this->belongsToMany(Paper::class, 'paper_authors');
    }

    public function reviews()
    {
        return $this->hasMany(ReviewAssignment::class, 'reviewer_id');
    }
    
    public function reviewAssignments()
    {
        return $this->hasMany(ReviewAssignment::class, 'reviewer_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class, 'reviewer_id');
    }

    public function expertise()
    {
        return $this->hasMany(ReviewerExpertise::class, 'user_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Remove the old isChair() method and replace with proper accessors
    public function isChairUser()
    {
        return $this->is_chair || $this->is_admin; // Admin can also act as chair
    }
    
    // For backward compatibility if you're using isChair() elsewhere
    public function isChair()
    {
        return $this->isChairUser();
    }

}