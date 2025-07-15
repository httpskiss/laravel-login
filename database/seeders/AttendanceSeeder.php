<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users except admin
        $users = User::whereDoesntHave('roles', function($q) {
            $q->where('name', 'Super Admin');
        })->get();

        if ($users->isEmpty()) {
            $this->command->info('No users found to seed attendance data. Please run UserFactory first.');
            return;
        }

        // Seed attendance for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $this->command->info('Seeding attendance data from '.$startDate->format('Y-m-d').' to '.$endDate->format('Y-m-d'));

        $workingDays = $this->getWorkingDaysBetweenDates($startDate, $endDate);

        foreach ($workingDays as $date) {
            foreach ($users as $user) {
                // Skip weekends and random days to simulate absences
                if ($date->isWeekend() || rand(1, 20) === 1) {
                    continue;
                }

                $status = $this->determineRandomStatus();
                $timeIn = $this->generateTimeIn($status);
                $timeOut = $this->generateTimeOut($timeIn);

                Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->format('Y-m-d'),
                    'time_in' => $status !== 'absent' ? $timeIn->format('H:i:s') : null,
                    'time_out' => $status !== 'absent' ? $timeOut->format('H:i:s') : null,
                    'status' => $status,
                    'ip_address' => $this->fakerIp(),
                    'device_info' => $this->fakeDevice(),
                    'location' => 'Office',
                    'total_hours' => $status !== 'absent' ? $this->calculateHours($timeIn, $timeOut) : null,
                    'is_regularized' => rand(1, 20) === 1 ? false : true,
                    'regularization_reason' => rand(1, 20) === 1 ? 'Forgot to check in' : null,
                ]);
            }
        }

        $this->command->info('Successfully seeded attendance data for '.count($users).' users across '.count($workingDays).' working days.');
    }

    /**
     * Get all working days between two dates
     */
    private function getWorkingDaysBetweenDates(Carbon $startDate, Carbon $endDate): array
    {
        $dates = [];
        $current = clone $startDate;

        while ($current <= $endDate) {
            if (!$current->isWeekend()) {
                $dates[] = clone $current;
            }
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Determine random attendance status with weighted probabilities
     */
    private function determineRandomStatus(): string
    {
        $rand = rand(1, 100);
        
        if ($rand <= 70) { // 70% chance of being present
            return 'present';
        } elseif ($rand <= 90) { // 20% chance of being late (90-70)
            return 'late';
        } elseif ($rand <= 95) { // 5% chance of half day
            return 'half_day';
        } else { // 5% chance of absent
            return 'absent';
        }
    }

    /**
     * Generate time-in based on status
     */
    private function generateTimeIn(string $status): Carbon
    {
        $time = Carbon::today()->setHour(8)->setMinute(0); // Standard start time 8:00 AM

        switch ($status) {
            case 'late':
                // Late arrival between 8:15 AM to 10:00 AM
                return $time->addMinutes(rand(15, 120));
            case 'half_day':
                // Half day arrives late (after 12 PM)
                return $time->addHours(rand(4, 6));
            default:
                // Present arrives between 7:45 AM to 8:15 AM
                return $time->addMinutes(rand(-15, 15));
        }
    }

    /**
     * Generate time-out based on time-in
     */
    private function generateTimeOut(Carbon $timeIn): Carbon
    {
        $minHours = 4; // Minimum working hours (for half day)
        $maxHours = 9; // Maximum working hours
        
        // For half day, work 4-6 hours
        if (rand(1, 10) === 1) {
            $hours = rand($minHours, 6);
        } else {
            // Regular day work 7-9 hours
            $hours = rand(7, $maxHours);
        }
        
        return $timeIn->copy()->addHours($hours);
    }

    /**
     * Calculate total hours worked
     */
    private function calculateHours(Carbon $timeIn, Carbon $timeOut): float
    {
        return round($timeOut->diffInMinutes($timeIn) / 60, 2);
    }

    /**
     * Generate fake IP address
     */
    private function fakerIp(): string
    {
        return implode('.', [rand(1, 255), rand(0, 255), rand(0, 255), rand(0, 255)]);
    }

    /**
     * Generate fake device info
     */
    private function fakeDevice(): string
    {
        $devices = [
            'iPhone 13, iOS 15.4',
            'Samsung Galaxy S21, Android 12',
            'Windows 10, Chrome 98',
            'MacBook Pro, macOS Monterey, Safari 15',
            'Linux, Firefox 97'
        ];
        
        return $devices[array_rand($devices)];
    }
}