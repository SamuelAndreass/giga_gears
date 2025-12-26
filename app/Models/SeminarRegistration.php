<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeminarRegistration extends Model
{
    protected $fillable = ['user_id', 'seminar_id', 'status', 'seat_number'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seminar(): BelongsTo
    {
        return $this->belongsTo(Seminar::class);
    }
}
