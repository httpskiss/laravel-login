<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $startDate = Carbon::now()->subMonth();
        $endDate = Carbon::now();
        
        foreach ($users as $user) {
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // Skip weekends (optional)
                if ($currentDate->isWeekend() && rand(0, 1)) {
                    $currentDate->addDay();
                    continue;
                }
                
                $status = $this->getRandomStatus();
                $timeIn = null;
                $timeOut = null;
                
                if ($status === 'present' || $status === 'late') {
                    $timeIn = $currentDate->copy()
                        ->setHour(rand(7, 10))
                        ->setMinute(rand(0, 59))
                        ->setSecond(0);
                    
                    if ($status === 'late' && $timeIn->hour >= 9) {
                        $timeIn->setHour(rand(9, 11));
                    }
                    
                    $timeOut = $timeIn->copy()
                        ->addHours(rand(6, 9))
                        ->addMinutes(rand(0, 59));
                }
                
                Attendance::create([
                    'user_id' => $user->id,
                    'date' => $currentDate->format('Y-m-d'),
                    'time_in' => $timeIn ? $timeIn->format('H:i:s') : null,
                    'time_out' => $timeOut ? $timeOut->format('H:i:s') : null,
                    'status' => $status,
                    'notes' => $status === 'on_leave' ? 'On leave' : null,
                ]);
                
                $currentDate->addDay();
            }
        }
    }
    
    private function getRandomStatus()
    {
        $rand = rand(1, 100);
        
        if ($rand <= 5) { // 5% chance
            return 'absent';
        } elseif ($rand <= 15) { // 10% chance
            return 'late';
        } elseif ($rand <= 20) { // 5% chance
            return 'on_leave';
        } else { // 80% chance
            return 'present';
        }
    }
}