<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PaperAuthor extends Pivot
{
    protected $table = 'paper_authors';
    
    protected $casts = [
        'is_corresponding' => 'boolean',
    ];

    public $timestamps = true;
}