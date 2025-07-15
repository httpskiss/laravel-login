<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'total_hours',
        'status',
        'notes',
        'ip_address',
        'device_info',
        'location',
        'is_regularized',
        'regularization_reason',
        'regularized_by'
    ];


    protected $casts = [
        'date' => 'date'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function regularizedBy()
    {
        return $this->belongsTo(User::class, 'regularized_by');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);
    }
}