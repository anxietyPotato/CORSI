<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkshopRequest;
use App\Http\Requests\UpdateWorkshopRequest;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkshopController extends Controller
{
    public function index(): Response
    {
        $workshops = Workshop::with([
            'confirmedRegistrations:id,workshop_id,user_id,status',
            'waitingList:id,workshop_id,user_id,status',
        ])
            ->orderBy('starts_at')
            ->get();

        return Inertia::render('Workshops/Index', [
            'workshops' => $workshops,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Workshops/Create');
    }

    public function store(StoreWorkshopRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        Workshop::create($validated);

        return redirect()->route('workshops.index')
            ->with('success', 'Workshop created successfully!');
    }

    public function show(Workshop $workshop): Response
    {
        $workshop->load([
            'confirmedRegistrations:id,workshop_id,user_id,status',
            'waitingList:id,workshop_id,user_id,status',
        ]);

        return Inertia::render('Workshops/Show', [
            'workshop' => $workshop,
        ]);
    }

    public function edit(Request $request, Workshop $workshop): Response
    {
        if ($request->user()->id !== $workshop->created_by) {
            abort(403, 'You can only edit your own workshops.');
        }

        return Inertia::render('Workshops/Edit', [
            'workshop' => $workshop,
        ]);
    }

    public function update(UpdateWorkshopRequest $request, Workshop $workshop)
    {
        if ($request->user()->id !== $workshop->created_by) {
            abort(403, 'You can only update your own workshops.');
        }

        $workshop->update($request->validated());

        return redirect()->route('workshops.index')
            ->with('success', 'Workshop updated successfully!');
    }

    public function destroy(Request $request, Workshop $workshop)
    {
        if ($request->user()->id !== $workshop->created_by) {
            abort(403, 'You can only delete your own workshops.');
        }

        $workshop->delete();

        return redirect()->route('workshops.index')
            ->with('success', 'Workshop deleted successfully!');
    }
}
