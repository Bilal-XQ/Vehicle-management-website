<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@vehiclemanagement.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create manager user
        User::create([
            'name' => 'Fleet Manager',
            'email' => 'manager@vehiclemanagement.com',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create viewer users
        $viewers = [
            ['name' => 'John Smith', 'email' => 'john@vehiclemanagement.com'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah@vehiclemanagement.com'],
            ['name' => 'Mike Davis', 'email' => 'mike@vehiclemanagement.com'],
            ['name' => 'Lisa Wilson', 'email' => 'lisa@vehiclemanagement.com'],
        ];

        foreach ($viewers as $viewer) {
            User::create([
                'name' => $viewer['name'],
                'email' => $viewer['email'],
                'password' => Hash::make('password123'),
                'role' => 'viewer',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}
