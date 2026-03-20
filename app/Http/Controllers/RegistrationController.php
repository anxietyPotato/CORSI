<?php

namespace App\Http\Controllers;

use App\Enums\RegistrationStatus;
use App\Enums\UserRole;
use App\Models\Registration;
use App\Models\Workshop;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function store(Request $request, Workshop $workshop)
    {
        if ($request->user()->role !== UserRole::Employee) {
            abort(403);
        }

        $user = $request->user();

        // Check if already registered
        $existing = Registration::where('user_id', $user->id)
            ->where('workshop_id', $workshop->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You are already registered for this workshop.');
        }

        // Check for overlapping workshops
        $overlapping = Registration::where('user_id', $user->id)
            ->whereHas('workshop', function ($query) use ($workshop) {
                $query->where('starts_at', '<', $workshop->ends_at)
                    ->where('ends_at', '>', $workshop->starts_at);
            })
            ->where('status', RegistrationStatus::Confirmed)
            ->exists();

        if ($overlapping) {
            return back()->with('error', 'You are already registered for an overlapping workshop.');
        }

        // Confirm or waiting list
        $status = $workshop->hasAvailableSeats()
            ? RegistrationStatus::Confirmed
            : RegistrationStatus::Waiting;

        Registration::create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'status' => $status,
            'registered_at' => now(),
        ]);

        return back()->with('success', $status === RegistrationStatus::Confirmed
            ? 'Successfully registered!'
            : 'Workshop is full. You have been added to the waiting list.'
        );
    }

    public function destroy(Request $request, Workshop $workshop)
    {
        if ($request->user()->role !== UserRole::Employee) {
            abort(403);
        }

        $user = $request->user();

        $registration = Registration::where('user_id', $user->id)
            ->where('workshop_id', $workshop->id)
            ->firstOrFail();

        $wasConfirmed = $registration->status === RegistrationStatus::Confirmed;

        $registration->delete();

        // Promote first person on waiting list (FIFO)
        if ($wasConfirmed) {
            $next = Registration::where('workshop_id', $workshop->id)
                ->where('status', RegistrationStatus::Waiting)
                ->orderBy('registered_at')
                ->first();

            if ($next) {
                $next->update(['status' => RegistrationStatus::Confirmed]);
            }
        }

        return back()->with('success', 'Registration cancelled successfully.');
    }
}
