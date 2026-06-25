<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropietarioDetalle extends Model
{
    protected $fillable = [
        'user_id',
        'codigo',
        'cargo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}