<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelAuthorityApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_authority_id',
        'approval_type',
        'approved_by',
        'approver_role',
        'status',
        'comments',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function travelAuthority()
    {
        return $this->belongsTo(TravelAuthority::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}