<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->employee = User::factory()->create(['role' => UserRole::Employee]);
    }

    public function test_admin_can_create_workshop(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/workshops', [
                'title' => 'Test Workshop',
                'description' => 'Test Description',
                'starts_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'ends_at' => now()->addDays(2)->addHours(2)->format('Y-m-d H:i:s'),
                'capacity' => 10,
            ]);

        $response->assertRedirect('/workshops');
        $this->assertDatabaseHas('workshops', ['title' => 'Test Workshop']);
    }

    public function test_employee_cannot_create_workshop(): void
    {
        $response = $this->actingAs($this->employee)
            ->post('/workshops', [
                'title' => 'Test Workshop',
                'description' => 'Test Description',
                'starts_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'ends_at' => now()->addDays(2)->addHours(2)->format('Y-m-d H:i:s'),
                'capacity' => 10,
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_own_workshop(): void
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->admin->id]);

        $response = $this->actingAs($this->admin)
            ->delete("/workshops/{$workshop->id}");

        $response->assertRedirect('/workshops');
        $this->assertDatabaseMissing('workshops', ['id' => $workshop->id]);
    }

    public function test_admin_cannot_delete_another_admins_workshop(): void
    {
        $otherAdmin = User::factory()->create(['role' => UserRole::Admin]);
        $workshop = Workshop::factory()->create(['created_by' => $otherAdmin->id]);

        $response = $this->actingAs($this->admin)
            ->delete("/workshops/{$workshop->id}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_workshops(): void
    {
        $response = $this->get('/workshops');
        $response->assertRedirect('/login');
    }
}
