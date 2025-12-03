<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateLeaveCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:update-credits';

    public function handle()
    {
        $users = User::all();
        
        foreach ($users as $user) {
            $cscService = new CscLeaveService();
            $credits = $cscService->calculateLeaveCredits($user);
            
            LeaveBalance::create([
                'user_id' => $user->id,
                'as_of_date' => now(),
                'vacation_leave' => $credits['vacation_leave'],
                'sick_leave' => $credits['sick_leave'],
                'last_computed_at' => now(),
            ]);
        }
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
}
