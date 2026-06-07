<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default programs
        $programs = [
            ['name' => 'Film Production', 'description' => 'Bachelor of Film Production'],
            ['name' => 'Digital Media', 'description' => 'Bachelor of Digital Media'],
            ['name' => 'Game Design', 'description' => 'Bachelor of Game Design'],
            ['name' => 'Audio Engineering', 'description' => 'Bachelor of Audio Engineering'],
        ];

        foreach ($programs as $p) {
            Program::firstOrCreate(['name' => $p['name']], $p);
        }

        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@saejordan.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_verified' => true,
            ]
        );

        // Create instructor
        User::firstOrCreate(
            ['email' => 'instructor@saejordan.com'],
            [
                'name' => 'Dr. Ahmad Khalidi',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'program' => 'Film Production',
                'is_verified' => true,
                'bio' => 'Experienced film educator with 15+ years in the industry.',
                'years_experience' => 15,
            ]
        );

        // Create student
        User::firstOrCreate(
            ['email' => 'student@saejordan.com'],
            [
                'name' => 'Alex Johnson',
                'password' => Hash::make('password'),
                'role' => 'student',
                'program' => 'Digital Media',
            ]
        );

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@saejordan.com / password');
        $this->command->info('Instructor: instructor@saejordan.com / password');
        $this->command->info('Student: student@saejordan.com / password');
    }
}
