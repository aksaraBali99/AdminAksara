<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'exchange_rate_usd'],
            ['value' => '15800', 'group' => 'finance']
        );

        \App\Models\Setting::updateOrCreate(
            ['key' => 'exchange_rate_aud'],
            ['value' => '10500', 'group' => 'finance']
        );
    }
}
