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
        'location'
    ];

    protected $dates = ['date'];
    protected $casts = [
        'date' => 'date:Y-m-d',
        'time_in' => 'datetime:H:i:s',
        'time_out' => 'datetime:H:i:s',
        'total_hours' => 'float' 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);
    }

    public function calculateTotalHours()
    {
        if ($this->time_in && $this->time_out) {
            try {
                $start = Carbon::parse($this->time_in);
                $end = Carbon::parse($this->time_out);
                
                // Calculate total hours with minutes as decimal
                $totalMinutes = $end->diffInMinutes($start);
                $this->total_hours = (float)round($totalMinutes / 60, 2); // Explicitly cast to float
                
                $this->save();
                return $this->total_hours;
            } catch (\Exception $e) {
                \Log::error("Error calculating total hours for attendance ID {$this->id}: " . $e->getMessage());
                $this->total_hours = 0.0; // Set default value
                $this->save();
                return 0;
            }
        }
        $this->total_hours = 0.0; // Set default value if no time_in/time_out
        $this->save();
        return 0;
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        $colors = [
            'STCS' => 'rgba(255, 159, 64, 0.6)',  // Orange
            'SOE' => 'rgba(153, 102, 255, 0.6)',   // Violet
            'SCJE' => 'rgba(255, 99, 132, 0.6)', // Pink
            'SNHS' => 'rgba(75, 192, 192, 0.6)',  // Green
            'SME' => 'rgba(255, 206, 86, 0.6)',   // Yellow
            'SAS' => 'rgba(54, 162, 235, 0.6)', //Blue
            'STED' => 'rgba(199, 199, 199, 0.6)', //Gray
            'default' => '7C3AED' // Purple
        ];
        
        $bgColor = $colors[$this->department] ?? $colors['default'];
        
        return "https://ui-avatars.com/api/?" . http_build_query([
            'name' => $this->first_name . '+' . $this->last_name,
            'background' => $bgColor,
            'color' => 'FFFFFF',
            'size' => '256',
            'rounded' => 'true',
            'bold' => 'true',
            'format' => 'png'
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    public function getFullNameAttribute()
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    public function getDepartmentAttribute()
    {
        return $this->user->department;
    }

    public function getEmployeeIdAttribute()
    {
        return $this->user->employee_id;
    }
}