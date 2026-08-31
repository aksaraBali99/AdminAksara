<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Owner user
        User::updateOrCreate(
            ['email' => 'owner@aksaravirtual.com'],
            [
                'name' => 'Admin Owner',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        // Create Finance user
        User::updateOrCreate(
            ['email' => 'finance@aksaravirtual.com'],
            [
                'name' => 'Finance Staff',
                'password' => Hash::make('password'),
                'role' => 'finance',
            ]
        );
    }
}
