<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    protected $fillable = [
        'carrera_materia_id',
        'periodo_academico_id',
        'aula_id',
        'docente_detalle_id',
        'dia',
        'hora_inicio',
        'hora_fin',
        'turno',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function carreraMateria(): BelongsTo
    {
        return $this->belongsTo(CarreraMateria::class);
    }

    public function periodoAcademico(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class);
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }

    public function docenteDetalle(): BelongsTo
    {
        return $this->belongsTo(DocenteDetalle::class);
    }

    public function estadoTexto(): string
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }
}