<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class WhapifySettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('whapify_secret', '');
        Setting::set('whapify_account', '');
    }
}
