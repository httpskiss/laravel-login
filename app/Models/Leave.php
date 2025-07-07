<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'days',
        'reason',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public const TYPES = [
        'vacation' => 'Vacation',
        'sick' => 'Sick',
        'emergency' => 'Emergency',
        'maternity' => 'Maternity',
        'paternity' => 'Paternity',
        'bereavement' => 'Bereavement',
        'other' => 'Other'
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getFormattedDatesAttribute(): string
    {
        return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
    }

    public function getDurationAttribute(): string
    {
        return $this->days . ' ' . str('day')->plural($this->days);
    }
}