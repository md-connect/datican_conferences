<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CameraReady extends Model
{
    use HasFactory;

    protected $table = 'camera_ready';

    protected $fillable = [
        'paper_id',
        'file_path',
        'file_name',
        'file_size',
        'format',
        'copyright_form_path',
        'copyright_signed',
        'author_order',
        'metadata',
        'changes_summary',
        'status',
        'rejection_reason',
        'submitted_by',
        'approved_by',
        'submitted_at',
        'approved_at'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'copyright_signed' => 'boolean',
        'author_order' => 'array',
        'metadata' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function proceedings()
    {
        return $this->hasOne(PaperProceeding::class, 'paper_id', 'paper_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Attributes
     */
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

    /**
     * Submit camera ready version
     */
    public function submit($file, $copyrightForm = null)
    {
        // Save file
        $fileName = time() . '_cameraready_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('camera_ready/' . $this->paper->conference_year, $fileName, 'public');

        $this->update([
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'status' => 'submitted',
            'submitted_at' => now(),
            'submitted_by' => auth()->id(),
        ]);

        // Update paper status
        $this->paper->update(['status' => 'camera_ready']);
    }

    /**
     * Approve camera ready version
     */
    public function approve($approvedBy)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }
}