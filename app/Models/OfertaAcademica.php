<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfertaAcademica extends Model
{
    protected $table = 'ofertas_academicas';

    protected $fillable = [
        'carrera_id',
        'periodo_academico_id',
        'docente_detalle_id',
        'nombre',
        'cantidad_cupos',
        'cupos_disponibles',
        'fecha_inicio',
        'fecha_fin',
        'precio_matricula',
        'precio_mensualidad',
        'precio_carrera_completa',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'cantidad_cupos' => 'integer',
            'cupos_disponibles' => 'integer',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'precio_matricula' => 'decimal:2',
            'precio_mensualidad' => 'decimal:2',
            'precio_carrera_completa' => 'decimal:2',
            'estado' => 'boolean',
        ];
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function periodoAcademico(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class);
    }

    public function docenteDetalle(): BelongsTo
    {
        return $this->belongsTo(DocenteDetalle::class);
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }
}