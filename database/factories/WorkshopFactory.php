<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkshopFactory extends Factory
{
    public function definition(): array
    {
        $start = now()->addDays(fake()->numberBetween(1, 30));
        $end = $start->copy()->addHours(2);

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'starts_at' => $start,
            'ends_at' => $end,
            'capacity' => fake()->numberBetween(5, 30),
            'created_by' => User::factory()->create(['role' => UserRole::Admin])->id,
        ];
    }
}
