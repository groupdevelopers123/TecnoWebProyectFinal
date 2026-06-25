<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InscripcionMateria extends Model
{
    protected $table = 'inscripcion_materias';

    protected $fillable = [
        'inscripcion_id',
        'carrera_materia_id',
        'estado',
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function carreraMateria(): BelongsTo
    {
        return $this->belongsTo(CarreraMateria::class);
    }

    public function seguimientoAcademico(): HasOne
    {
        return $this->hasOne(SeguimientoAcademico::class);
    }
}