<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'firstname',
        'middlename',
        'lastname',
        'email',
        'phone_number',
        'institution',
        'gender',
        'is_datican_member',
        'datican_status',
        'is_presenting_paper'
    ];

    protected $casts = [
        'is_datican_member' => 'boolean',
        'is_presenting_paper' => 'boolean',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->title} {$this->firstname} {$this->lastname}";
    }

    public function scopePresentingPapers($query)
    {
        return $query->where('is_presenting_paper', true);
    }

    public function scopeDaticanMembers($query)
    {
        return $query->where('is_datican_member', true);
    }
}