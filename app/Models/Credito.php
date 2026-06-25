<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credito extends Model
{
    protected $table = 'creditos';

    protected $fillable = [
        'inscripcion_id',
        'concepto_pago_id',
        'tipo_pago',
        'monto_total',
        'saldo_pendiente',
        'cantidad_cuotas',
        'fecha_otorgamiento',
        'fecha_vencimiento',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'monto_total' => 'decimal:2',
            'saldo_pendiente' => 'decimal:2',
            'cantidad_cuotas' => 'integer',
            'fecha_otorgamiento' => 'date',
            'fecha_vencimiento' => 'date',
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

    public function pagoCuotas(): HasMany
    {
        return $this->hasMany(PagoCuota::class);
    }

    public function recalcularSaldo(): void
    {
        $totalPagado = $this->pagoCuotas()
            ->where('estado_cuota', 'pagado')
            ->sum('monto');

        $saldo = max(0, (float) $this->monto_total - (float) $totalPagado);

        $this->update([
            'saldo_pendiente' => $saldo,
            'estado' => $saldo <= 0 ? 'pagado' : 'activo',
        ]);
    }
}