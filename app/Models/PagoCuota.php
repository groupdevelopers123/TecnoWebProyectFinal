<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoCuota extends Model
{
    protected $table = 'pago_cuotas';

    protected $fillable = [
        'credito_id',
        'monto',
        'numero_cuota',
        'fecha_vencimiento',
        'fecha_pago',
        'estado_cuota',
        'metodo_pago',
        'observacion',
        'codigo_transaccion',
        'correo_solicitante',
        'payment_number',
        'qr_path',
        'fecha_confirmacion',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'numero_cuota' => 'integer',
            'fecha_vencimiento' => 'date',
            'fecha_pago' => 'date',
            'fecha_confirmacion' => 'datetime',
        ];
    }

    public function credito(): BelongsTo
    {
        return $this->belongsTo(Credito::class);
    }

    public function estaPagada(): bool
    {
        return $this->estado_cuota === 'pagado';
    }

    public function estaPendiente(): bool
    {
        return $this->estado_cuota === 'pendiente';
    }

    public function esQr(): bool
    {
        return $this->metodo_pago === 'QR';
    }
}