<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarreraMateria extends Model
{
    protected $table = 'carrera_materia';

    protected $fillable = [
        'carrera_id',
        'materia_id',
        'periodo_numero',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'periodo_numero' => 'integer',
            'estado' => 'boolean',
        ];
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    public function nombreCompleto(): string
    {
        $carrera = $this->carrera?->nombre ?? 'Sin carrera';
        $materia = $this->materia?->nombre ?? 'Sin materia';

        return $carrera . ' - ' . $materia;
    }

    public function inscripcionMaterias(): HasMany
    {
        return $this->hasMany(InscripcionMateria::class);
    }
}