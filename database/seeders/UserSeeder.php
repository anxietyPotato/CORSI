<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@academy.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]);

        User::create([
            'name' => 'Employee One',
            'email' => 'employee@academy.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Employee,
        ]);
    }
}
