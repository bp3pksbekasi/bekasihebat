<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all user passwords to default';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting passwords...');
        
        $newPassword = Hash::make('PKS123!');
        $count = User::query()->update(['password' => $newPassword]);
        
        $this->info("Successfully updated {$count} users to use the default password 'PKS123!'.");
    }
}
