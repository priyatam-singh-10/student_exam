<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure there is a default admin and a default student
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'password' => bcrypt('password123'),
                'role' => 'student',
            ]
        );
    }
}
