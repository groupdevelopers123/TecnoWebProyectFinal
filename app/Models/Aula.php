<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aula extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'ubicacion',
        'piso',
        'capacidad',
        'largo',
        'ancho',
        'disponible',
        'user_id_registro',
    ];

    protected function casts(): array
    {
        return [
            'capacidad' => 'integer',
            'largo' => 'decimal:2',
            'ancho' => 'decimal:2',
            'disponible' => 'boolean',
        ];
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_registro');
    }

    public function area(): ?float
    {
        if (! $this->largo || ! $this->ancho) {
            return null;
        }

        return (float) $this->largo * (float) $this->ancho;
    }

    public function estadoTexto(): string
    {
        return $this->disponible ? 'Disponible' : 'No disponible';
    }
}