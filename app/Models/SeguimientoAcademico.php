<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeguimientoAcademico extends Model
{
    protected $table = 'seguimientos_academicos';

    protected $fillable = [
        'inscripcion_materia_id',
        'docente_detalle_id',
        'nota_final',
        'porcentaje_asistencia',
        'observacion',
        'estado_academico',
        'fecha_registro',
    ];

    protected function casts(): array
    {
        return [
            'nota_final' => 'decimal:2',
            'porcentaje_asistencia' => 'decimal:2',
            'fecha_registro' => 'date',
        ];
    }

    public function inscripcionMateria(): BelongsTo
    {
        return $this->belongsTo(InscripcionMateria::class);
    }

    public function docenteDetalle(): BelongsTo
    {
        return $this->belongsTo(DocenteDetalle::class);
    }
}