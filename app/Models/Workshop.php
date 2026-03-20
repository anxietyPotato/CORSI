<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
        'capacity',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function confirmedRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class)
            ->where('status', RegistrationStatus::Confirmed);
    }

    public function waitingList(): HasMany
    {
        return $this->hasMany(Registration::class)
            ->where('status', RegistrationStatus::Waiting);
    }

    public function hasAvailableSeats(): bool
    {
        return $this->confirmedRegistrations()->count() < $this->capacity;
    }
}
