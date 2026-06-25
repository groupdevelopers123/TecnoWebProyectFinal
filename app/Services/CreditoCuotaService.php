<?php

namespace App\Services;

use App\Models\Credito;
use Carbon\Carbon;

class CreditoCuotaService
{
    public function generarCuotas(Credito $credito): void
    {
        $credito->pagoCuotas()->delete();

        $cantidadCuotas = (int) $credito->cantidad_cuotas;
        $montoTotal = round((float) $credito->monto_total, 2);

        $fechaInicio = Carbon::parse($credito->fecha_otorgamiento);
        $fechaFin = Carbon::parse($credito->fecha_vencimiento);

        $diasTotales = max(1, $fechaInicio->diffInDays($fechaFin));

        $montoBase = floor(($montoTotal / $cantidadCuotas) * 100) / 100;
        $montoAcumulado = 0;

        for ($i = 1; $i <= $cantidadCuotas; $i++) {
            if ($i === $cantidadCuotas) {
                $monto = round($montoTotal - $montoAcumulado, 2);
            } else {
                $monto = $montoBase;
                $montoAcumulado += $monto;
            }

            $diasParaCuota = (int) round(($diasTotales / $cantidadCuotas) * $i);

            $fechaVencimiento = $i === $cantidadCuotas
                ? $fechaFin->copy()
                : $fechaInicio->copy()->addDays($diasParaCuota);

            $credito->pagoCuotas()->create([
                'monto' => $monto,
                'numero_cuota' => $i,
                'fecha_vencimiento' => $fechaVencimiento->format('Y-m-d'),
                'fecha_pago' => null,
                'estado_cuota' => 'pendiente',
                'metodo_pago' => null,
                'observacion' => null,
                'codigo_transaccion' => null,
                'correo_solicitante' => null,
                'payment_number' => null,
                'qr_path' => null,
                'fecha_confirmacion' => null,
            ]);
        }
    }

    public function puedeRegenerarCuotas(Credito $credito): bool
    {
        return ! $credito->pagoCuotas()
            ->whereIn('estado_cuota', ['pagado', 'confirmado'])
            ->exists();
    }
}