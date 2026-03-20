<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@academy.com')->first();

        Workshop::create([
            'title' => 'Introduction to Laravel',
            'description' => 'Learn the basics of Laravel framework from scratch.',
            'starts_at' => now()->addDays(1)->setTime(10, 0),
            'ends_at' => now()->addDays(1)->setTime(12, 0),
            'capacity' => 10,
            'created_by' => $admin->id,
        ]);

        Workshop::create([
            'title' => 'Vue.js for Beginners',
            'description' => 'Get started with Vue.js and build reactive interfaces.',
            'starts_at' => now()->addDays(2)->setTime(14, 0),
            'ends_at' => now()->addDays(2)->setTime(16, 0),
            'capacity' => 5,
            'created_by' => $admin->id,
        ]);

        Workshop::create([
            'title' => 'Clean Code Principles',
            'description' => 'Write better, more maintainable code using SOLID principles.',
            'starts_at' => now()->addDays(3)->setTime(9, 0),
            'ends_at' => now()->addDays(3)->setTime(11, 0),
            'capacity' => 15,
            'created_by' => $admin->id,
        ]);

        Workshop::create([
            'title' => 'Git & Team Workflows',
            'description' => 'Master Git branching strategies and team collaboration.',
            'starts_at' => now()->addDays(4)->setTime(15, 0),
            'ends_at' => now()->addDays(4)->setTime(17, 0),
            'capacity' => 2,
            'created_by' => $admin->id,
        ]);
    }
}
