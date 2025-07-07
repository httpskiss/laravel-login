<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'method',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected $appends = ['hours_worked'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getHoursWorkedAttribute()
    {
        if (!$this->time_in || !$this->time_out) {
            return '0h 0m';
        }

        $start = Carbon::parse($this->time_in);
        $end = Carbon::parse($this->time_out);
        $diff = $start->diff($end);

        return $diff->h . 'h ' . $diff->i . 'm';
    }
}