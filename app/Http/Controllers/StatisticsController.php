<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Workshop;
use App\Enums\RegistrationStatus;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class StatisticsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Statistics', [
            'stats' => $this->getStats(),
        ]);
    }

    public function live(): JsonResponse
    {
        return response()->json($this->getStats());
    }

    private function getStats(): array
    {
        $workshops = Workshop::withCount([
            'registrations as confirmed_count' => fn($q) => $q->where('status', RegistrationStatus::Confirmed),
            'registrations as waiting_count' => fn($q) => $q->where('status', RegistrationStatus::Waiting),
        ])->get();

        $mostPopular = $workshops->sortByDesc('confirmed_count')->first();

        return [
            'total_workshops' => $workshops->count(),
            'total_registrations' => Registration::where('status', RegistrationStatus::Confirmed)->count(),
            'most_popular' => $mostPopular ? [
                'title' => $mostPopular->title,
                'confirmed_count' => $mostPopular->confirmed_count,
                'capacity' => $mostPopular->capacity,
            ] : null,
            'workshops' => $workshops->map(fn($w) => [
                'id' => $w->id,
                'title' => $w->title,
                'confirmed_count' => $w->confirmed_count,
                'waiting_count' => $w->waiting_count,
                'capacity' => $w->capacity,
            ])->values(),
        ];
    }
}
