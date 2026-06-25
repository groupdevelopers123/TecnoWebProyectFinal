<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'carga_horaria',
        'docente_detalle_id',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'carga_horaria' => 'integer',
            'estado' => 'boolean',
        ];
    }

    public function carreraMaterias(): HasMany
    {
        return $this->hasMany(CarreraMateria::class);
    }

    public function docenteDetalle(): BelongsTo
    {
        return $this->belongsTo(DocenteDetalle::class);
    }
}