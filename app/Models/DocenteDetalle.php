<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocenteDetalle extends Model
{
    protected $fillable = [
        'user_id',
        'codigo',
        'especialidad',
        'titulo',
        'registro_profesional',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    public function seguimientosAcademicos(): HasMany
    {
        return $this->hasMany(SeguimientoAcademico::class);
    }

    public function ofertasAcademicas(): HasMany
    {
        return $this->hasMany(OfertaAcademica::class);
    }

    public function materias(): HasMany
    {
        return $this->hasMany(Materia::class);
    }
}