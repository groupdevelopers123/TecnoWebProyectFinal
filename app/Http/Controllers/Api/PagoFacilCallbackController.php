<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PagoContado;
use App\Models\PagoCuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagoFacilCallbackController extends Controller
{
    public function __invoke(Request $request)
    {
        Log::info('Callback PagoFácil recibido', [
            'body' => $request->all(),
        ]);

        $pedidoId = $request->input('PedidoID');
        $fecha = $request->input('Fecha');
        $hora = $request->input('Hora');
        $metodoPago = $request->input('MetodoPago');
        $estado = $request->input('Estado');

        if (! $pedidoId) {
            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => 'PedidoID no recibido.',
                'values' => false,
            ], 400);
        }

        $estadoNormalizado = $this->normalizarEstado($estado);

        $pagoContado = PagoContado::where('payment_number', $pedidoId)->first();

        if ($pagoContado) {
            $pagoContado->update([
                'estado' => $estadoNormalizado,
                'fecha_confirmacion' => $estadoNormalizado === 'Confirmado' ? now() : $pagoContado->fecha_confirmacion,
                'observacion' => trim(($pagoContado->observacion ?? '') . "\nCallback PagoFácil: {$fecha} {$hora} - Método: {$metodoPago} - Estado: {$estado}"),
            ]);

            return $this->respuestaOk();
        }

        $pagoCuota = PagoCuota::where('payment_number', $pedidoId)->first();

        if ($pagoCuota) {
            $estadoCuota = $estadoNormalizado === 'Confirmado' ? 'pagado' : 'pendiente';

            $pagoCuota->update([
                'estado_cuota' => $estadoCuota,
                'fecha_pago' => $estadoCuota === 'pagado' ? now()->format('Y-m-d') : $pagoCuota->fecha_pago,
                'fecha_confirmacion' => $estadoCuota === 'pagado' ? now() : $pagoCuota->fecha_confirmacion,
                'observacion' => trim(($pagoCuota->observacion ?? '') . "\nCallback PagoFácil: {$fecha} {$hora} - Método: {$metodoPago} - Estado: {$estado}"),
            ]);

            $pagoCuota->credito->recalcularSaldo();

            return $this->respuestaOk();
        }

        return response()->json([
            'error' => 1,
            'status' => 0,
            'message' => 'Pago no encontrado.',
            'values' => false,
        ], 404);
    }

    private function respuestaOk()
    {
        return response()->json([
            'error' => 0,
            'status' => 1,
            'message' => 'Callback recibido correctamente.',
            'values' => true,
        ]);
    }

    private function normalizarEstado(mixed $estado): string
    {
        $estadoTexto = mb_strtolower(trim((string) $estado));

        return match (true) {
            $estadoTexto === '2',
            str_contains($estadoTexto, 'confirm'),
            str_contains($estadoTexto, 'pagado'),
            str_contains($estadoTexto, 'aprob') => 'Confirmado',

            str_contains($estadoTexto, 'fall'),
            str_contains($estadoTexto, 'rechaz'),
            str_contains($estadoTexto, 'anulad') => 'Fallido',

            default => 'Pendiente',
        };
    }
}