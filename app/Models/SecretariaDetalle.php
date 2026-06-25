<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecretariaDetalle extends Model
{
    protected $fillable = [
        'user_id',
        'codigo',
        'turno_trabajo',
        'sueldo',
    ];

    protected function casts(): array
    {
        return [
            'sueldo' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}