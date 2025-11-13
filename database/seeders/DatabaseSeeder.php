<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Chresthofer Andrew Noya',
            'email' => 'chrestho.lpkbpi@gmail.com',
            'phone' => '082248408245',
            'jabatan' => 'Designer Grafis',
            'password' => Hash::make('adminbpi123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Optional: Create sample regular users
        User::create([
            'name' => 'User Test 1',
            'email' => 'user1@example.com',
            'phone' => '081234567890',
            'jabatan' => 'Staff Operasional',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'User Test 2',
            'email' => 'user2@example.com',
            'phone' => '081234567891',
            'jabatan' => 'Staff Marketing',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}