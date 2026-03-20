<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public home
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard - both roles
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Workshop index - both roles
    Route::get('/workshops', [WorkshopController::class, 'index'])->name('workshops.index');

    // Admin only - create, edit, delete (BEFORE wildcard route!)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/workshops/create', [WorkshopController::class, 'create'])->name('workshops.create');
        Route::post('/workshops', [WorkshopController::class, 'store'])->name('workshops.store');
        Route::get('/workshops/{workshop}/edit', [WorkshopController::class, 'edit'])->name('workshops.edit');
        Route::put('/workshops/{workshop}', [WorkshopController::class, 'update'])->name('workshops.update');
        Route::delete('/workshops/{workshop}', [WorkshopController::class, 'destroy'])->name('workshops.destroy');


            Route::post('/workshops', [WorkshopController::class, 'store'])->name('workshops.store');
            Route::get('/workshops/{workshop}/edit', [WorkshopController::class, 'edit'])->name('workshops.edit');
            Route::put('/workshops/{workshop}', [WorkshopController::class, 'update'])->name('workshops.update');
            Route::delete('/workshops/{workshop}', [WorkshopController::class, 'destroy'])->name('workshops.destroy');
            Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index'); // ADD THIS
            Route::get('/statistics/live', [StatisticsController::class, 'live'])->name('statistics.live'); // ADD THIS

    });

    // Workshop show - both roles (AFTER /create!)
    Route::get('/workshops/{workshop}', [WorkshopController::class, 'show'])->name('workshops.show');

    // Employee only - register/unregister
    Route::middleware(['role:employee'])->group(function () {
        Route::post('/workshops/{workshop}/register', [RegistrationController::class, 'store'])->name('workshops.register');
        Route::delete('/workshops/{workshop}/register', [RegistrationController::class, 'destroy'])->name('workshops.unregister');
    });

    // Profile - both roles
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
