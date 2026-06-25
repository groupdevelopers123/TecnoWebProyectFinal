<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscripcion extends Model
{
    protected $table = 'inscripciones';

    protected $fillable = [
        'alumno_detalle_id',
        'oferta_academica_id',
        'user_id_registro',
        'periodo_numero',
        'fecha_inscripcion',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'periodo_numero' => 'integer',
            'fecha_inscripcion' => 'date',
        ];
    }

    public function alumnoDetalle(): BelongsTo
    {
        return $this->belongsTo(AlumnoDetalle::class);
    }

    public function ofertaAcademica(): BelongsTo
    {
        return $this->belongsTo(OfertaAcademica::class);
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_registro');
    }

    public function inscripcionMaterias(): HasMany
    {
        return $this->hasMany(InscripcionMateria::class);
    }

    public function pagoContados(): HasMany
    {
        return $this->hasMany(PagoContado::class);
    }

    public function creditos(): HasMany
    {
        return $this->hasMany(Credito::class);
    }
}