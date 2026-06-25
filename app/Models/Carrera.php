<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrera extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'duracion',
        'regimen_academico',
        'estado',
        'user_id_registro',
    ];

    protected function casts(): array
    {
        return [
            'duracion' => 'integer',
            'estado' => 'boolean',
        ];
    }

    public function carreraMaterias(): HasMany
    {
        return $this->hasMany(CarreraMateria::class);
    }

    public function ofertasAcademicas(): HasMany
    {
        return $this->hasMany(OfertaAcademica::class);
    }
}