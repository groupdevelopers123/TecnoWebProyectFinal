<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlumnoDetalle extends Model
{
    protected $fillable = [
        'user_id',
        'codigo',
        'colegio_origen',
        'anio_bachillerato',
        'estado_academico',
    ];

    protected function casts(): array
    {
        return [
            'anio_bachillerato' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }
}