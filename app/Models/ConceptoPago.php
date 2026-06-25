<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConceptoPago extends Model
{
    protected $table = 'concepto_pagos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    public function estaActivo(): bool
    {
        return $this->estado === 'Activo';
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