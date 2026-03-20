<?php

namespace App\Console\Commands;

use App\Mail\WorkshopReminder;
use App\Models\Registration;
use App\Enums\RegistrationStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWorkshopReminders extends Command
{
    protected $signature = 'academy:remind';
    protected $description = 'Send reminder emails to participants of tomorrow\'s workshops';

    public function handle(): void
    {
        $tomorrow = now()->addDay();

        $registrations = Registration::with(['user', 'workshop'])
            ->where('status', RegistrationStatus::Confirmed)
            ->whereHas('workshop', function ($query) use ($tomorrow) {
                $query->whereDate('starts_at', $tomorrow->toDateString());
            })
            ->get();

        if ($registrations->isEmpty()) {
            $this->info('No workshops scheduled for tomorrow. No emails sent.');
            return;
        }

        foreach ($registrations as $registration) {
            $this->info("Sending reminder to: {$registration->user->email} for: {$registration->workshop->title}");
            Mail::to($registration->user->email)->send(new WorkshopReminder($registration->workshop));
        }

        $this->info("Done! {$registrations->count()} reminder(s) sent.");
    }
}
