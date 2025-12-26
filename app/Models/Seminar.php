<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seminar extends Model
{
    protected $fillable = [
        'title', 'description', 'start_date', 'end_date', 'location', 
        'image_url', 'capacity', 'instructor', 'requirements', 'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(SeminarRegistration::class);
    }

    public function getRegisteredCountAttribute(): int
    {
        return $this->registrations()->where('status', 'registered')->count();
    }

    public function getRemainingCapacityAttribute(): int
    {
        return max(0, $this->capacity - $this->registered_count);
    }
}
