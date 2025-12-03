<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\CscLeaveComputationService;
use App\Models\LeaveCreditEarning;
use Carbon\Carbon;

class ComputeLeaveEarnings extends Command
{
    protected $signature = 'leave:compute-earnings 
                            {--user= : Specific user ID}
                            {--month= : Month to compute (default: previous month)}
                            {--year= : Year to compute (default: current year)}';
    
    protected $description = 'Compute leave earnings for employees based on CSC rules';

    public function handle()
    {
        $month = $this->option('month') ?? now()->subMonth()->month;
        $year = $this->option('year') ?? now()->year;
        
        $fromDate = Carbon::create($year, $month, 1);
        $toDate = $fromDate->copy()->endOfMonth();
        
        $computationService = new CscLeaveComputationService();
        
        // Get users
        $query = User::query();
        
        if ($this->option('user')) {
            $query->where('id', $this->option('user'));
        }
        
        $users = $query->get();
        
        $this->info("Computing leave earnings for {$users->count()} users for {$fromDate->format('F Y')}");
        
        $bar = $this->output->createProgressBar($users->count());
        
        foreach ($users as $user) {
            try {
                $earnings = $computationService->computeLeaveEarnings($user, $fromDate, $toDate);
                
                // Record the earning
                $this->recordLeaveEarning($user, $earnings, $fromDate);
                
                // Update user's last computation date
                $user->last_leave_computation_date = now();
                $user->save();
                
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("Error computing for user {$user->id}: {$e->getMessage()}");
            }
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Leave earnings computation completed.');
    }
    
    private function recordLeaveEarning(User $user, array $earnings, Carbon $earningDate): void
    {
        foreach ($earnings as $type => $days) {
            if ($days > 0) {
                LeaveCreditEarning::create([
                    'user_id' => $user->id,
                    'earning_date' => $earningDate,
                    'credit_type' => $this->mapEarningType($type),
                    'days_earned' => $days,
                    'description' => $this->getEarningDescription($type, $earningDate),
                    'computation_details' => $earnings,
                ]);
            }
        }
    }
    
    private function mapEarningType(string $type): string
    {
        $mapping = [
            'vacation_leave' => LeaveCreditEarning::TYPE_VACATION,
            'sick_leave' => LeaveCreditEarning::TYPE_SICK,
            'vacation_service_credits' => LeaveCreditEarning::TYPE_VACATION_SERVICE,
            'proportional_vacation_pay' => LeaveCreditEarning::TYPE_VACATION,
        ];
        
        return $mapping[$type] ?? $type;
    }
    
    private function getEarningDescription(string $type, Carbon $date): string
    {
        $descriptions = [
            'vacation_leave' => "Vacation Leave earned for {$date->format('F Y')}",
            'sick_leave' => "Sick Leave earned for {$date->format('F Y')}",
            'vacation_service_credits' => "Vacation Service Credits earned for {$date->format('F Y')}",
            'proportional_vacation_pay' => "Proportional Vacation Pay for school year",
        ];
        
        return $descriptions[$type] ?? "Leave credits earned for {$date->format('F Y')}";
    }
}