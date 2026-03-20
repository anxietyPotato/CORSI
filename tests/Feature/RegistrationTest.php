<?php

namespace Tests\Feature;

use App\Enums\RegistrationStatus;
use App\Enums\UserRole;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $employee;
    private Workshop $workshop;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->employee = User::factory()->create(['role' => UserRole::Employee]);
        $this->workshop = Workshop::factory()->create([
            'created_by' => $this->admin->id,
            'capacity' => 2,
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHours(2),
        ]);
    }

    public function test_employee_can_register_for_workshop(): void
    {
        $response = $this->actingAs($this->employee)
            ->post("/workshops/{$this->workshop->id}/register");

        $response->assertRedirect();
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
            'status' => RegistrationStatus::Confirmed->value,
        ]);
    }

    public function test_employee_cannot_register_twice(): void
    {
        Registration::create([
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
            'status' => RegistrationStatus::Confirmed,
            'registered_at' => now(),
        ]);

        $this->actingAs($this->employee)
            ->post("/workshops/{$this->workshop->id}/register");

        $this->assertEquals(1, Registration::where('user_id', $this->employee->id)->count());
    }

    public function test_employee_added_to_waiting_list_when_workshop_is_full(): void
    {
        $employee2 = User::factory()->create(['role' => UserRole::Employee]);
        $employee3 = User::factory()->create(['role' => UserRole::Employee]);

        Registration::create([
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
            'status' => RegistrationStatus::Confirmed,
            'registered_at' => now(),
        ]);

        Registration::create([
            'user_id' => $employee2->id,
            'workshop_id' => $this->workshop->id,
            'status' => RegistrationStatus::Confirmed,
            'registered_at' => now(),
        ]);

        $this->actingAs($employee3)
            ->post("/workshops/{$this->workshop->id}/register");

        $this->assertDatabaseHas('registrations', [
            'user_id' => $employee3->id,
            'workshop_id' => $this->workshop->id,
            'status' => RegistrationStatus::Waiting->value,
        ]);
    }

    public function test_waiting_list_user_promoted_when_confirmed_user_cancels(): void
    {
        $employee2 = User::factory()->create(['role' => UserRole::Employee]);
        $employee3 = User::factory()->create(['role' => UserRole::Employee]);

        Registration::create([
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
            'status' => RegistrationStatus::Confirmed,
            'registered_at' => now(),
        ]);

        Registration::create([
            'user_id' => $employee2->id,
            'workshop_id' => $this->workshop->id,
            'status' => RegistrationStatus::Confirmed,
            'registered_at' => now(),
        ]);

        Registration::create([
            'user_id' => $employee3->id,
            'workshop_id' => $this->workshop->id,
            'status' => RegistrationStatus::Waiting,
            'registered_at' => now(),
        ]);

        $this->actingAs($this->employee)
            ->delete("/workshops/{$this->workshop->id}/register");

        $this->assertDatabaseHas('registrations', [
            'user_id' => $employee3->id,
            'workshop_id' => $this->workshop->id,
            'status' => RegistrationStatus::Confirmed->value,
        ]);
    }

    public function test_employee_cannot_register_for_overlapping_workshops(): void
    {
        $overlappingWorkshop = Workshop::factory()->create([
            'created_by' => $this->admin->id,
            'capacity' => 10,
            'starts_at' => now()->addDays(2)->addHour(),
            'ends_at' => now()->addDays(2)->addHours(3),
        ]);

        Registration::create([
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
            'status' => RegistrationStatus::Confirmed,
            'registered_at' => now(),
        ]);

        $this->actingAs($this->employee)
            ->post("/workshops/{$overlappingWorkshop->id}/register");

        $this->assertDatabaseMissing('registrations', [
            'user_id' => $this->employee->id,
            'workshop_id' => $overlappingWorkshop->id,
        ]);
    }

    public function test_admin_cannot_register_for_workshop(): void
    {
        $response = $this->actingAs($this->admin)
            ->post("/workshops/{$this->workshop->id}/register");

        $response->assertStatus(403);
    }
}
