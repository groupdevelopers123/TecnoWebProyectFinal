<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoContado extends Model
{
    protected $table = 'pago_contados';

    protected $fillable = [
        'inscripcion_id',
        'concepto_pago_id',
        'monto_pagado',
        'fecha_pago',
        'metodo_pago',
        'estado',
        'codigo_transaccion',
        'correo_solicitante',
        'observacion',
        'payment_number',
        'qr_path',
        'fecha_confirmacion',
    ];

    protected function casts(): array
    {
        return [
            'monto_pagado' => 'decimal:2',
            'fecha_pago' => 'date',
            'fecha_confirmacion' => 'datetime',
        ];
    }

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function conceptoPago(): BelongsTo
    {
        return $this->belongsTo(ConceptoPago::class);
    }

    public function estaConfirmado(): bool
    {
        return $this->estado === 'Confirmado';
    }

    public function esQr(): bool
    {
        return $this->metodo_pago === 'QR';
    }
}