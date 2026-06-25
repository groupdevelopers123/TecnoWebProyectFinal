<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\AlumnoDetalle;
use App\Models\ConceptoPago;
use App\Models\Inscripcion;
use App\Models\OfertaAcademica;
use App\Models\PagoContado;
use App\Services\PagoFacilService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AlumnoInscripcionController extends Controller
{
    public function store(
        Request $request,
        PagoFacilService $pagoFacilService
    ): RedirectResponse {
        $datos = $request->validate([
            'oferta_academica_id' => [
                'required',
                'integer',
                'exists:ofertas_academicas,id',
            ],

            'metodo_pago' => [
                'required',
                'string',
                'in:QR,Tarjeta,PayPal',
            ],

            'tipo_pago' => [
                'required',
                'string',
                'in:matricula,carrera_completa',
            ],
        ], [
            'oferta_academica_id.required' =>
                'Debe seleccionar una oferta académica.',

            'oferta_academica_id.exists' =>
                'La oferta académica seleccionada no existe.',

            'metodo_pago.required' =>
                'Debe seleccionar un método de pago.',

            'metodo_pago.in' =>
                'El método de pago seleccionado no es válido.',

            'tipo_pago.required' =>
                'Debe seleccionar qué desea pagar.',

            'tipo_pago.in' =>
                'El tipo de pago seleccionado no es válido.',
        ]);


        $alumno = AlumnoDetalle::query()
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $alumno) {
            throw ValidationException::withMessages([
                'oferta_academica_id' =>
                    'El usuario autenticado no tiene un registro de alumno.',
            ]);
        }


        $concepto = $this->obtenerConceptoPago(
            $datos['tipo_pago']
        );

        if (! $concepto) {
            throw ValidationException::withMessages([
                'tipo_pago' =>
                    'No existe un concepto de pago activo para la opción seleccionada.',
            ]);
        }


        [$inscripcion, $pago] = DB::transaction(
            function () use ($datos, $alumno, $concepto, $request) {
                $oferta = OfertaAcademica::query()
                    ->with('carrera')
                    ->lockForUpdate()
                    ->findOrFail($datos['oferta_academica_id']);

                if (! $oferta->estado) {
                    throw ValidationException::withMessages([
                        'oferta_academica_id' =>
                            'La oferta académica ya no se encuentra activa.',
                    ]);
                }

                if ((int) $oferta->cupos_disponibles <= 0) {
                    throw ValidationException::withMessages([
                        'oferta_academica_id' =>
                            'La oferta académica ya no tiene cupos disponibles.',
                    ]);
                }

                $inscripcionExistente = Inscripcion::query()
                    ->where('alumno_detalle_id', $alumno->id)
                    ->where('oferta_academica_id', $oferta->id)
                    ->exists();

                if ($inscripcionExistente) {
                    throw ValidationException::withMessages([
                        'oferta_academica_id' =>
                            'Ya se encuentra inscrito en esta oferta académica.',
                    ]);
                }

    
                $inscripcion = Inscripcion::create([
                    'alumno_detalle_id' => $alumno->id,
                    'oferta_academica_id' => $oferta->id,
                    'periodo_numero' => 1,
                    'fecha_inscripcion' => now()->toDateString(),
                    'observacion' =>
                        'Inscripción realizada desde el portal del alumno.',
                    'user_id_registro' => $request->user()->id,
                ]);

                $monto = $datos['tipo_pago'] === 'matricula'
                    ? (float) $oferta->precio_matricula
                    : (float) $oferta->precio_carrera_completa;

                if ($monto <= 0) {
                    throw ValidationException::withMessages([
                        'tipo_pago' =>
                            'La oferta no tiene configurado el precio seleccionado.',
                    ]);
                }

                $pago = PagoContado::create([
                    'inscripcion_id' => $inscripcion->id,
                    'concepto_pago_id' => $concepto->id,
                    'monto_pagado' => $monto,
                    'fecha_pago' => now()->toDateString(),
                    'metodo_pago' => $datos['metodo_pago'],
                    'estado' => 'Pendiente',
                    'observacion' =>
                        'Pago iniciado desde el portal del alumno.',
                ]);

                $oferta->decrement('cupos_disponibles');

                return [$inscripcion, $pago];
            }
        );


        if ($datos['metodo_pago'] === 'QR') {
            try {
                $pagoFacilService->generarQr($pago);

                $pago->refresh();

                return back()->with([
                    'success' =>
                        'La inscripción fue registrada y el código QR fue generado.',

                    'pago_generado' => [
                        'id' => $pago->id,
                        'estado' => $pago->estado,
                        'metodo_pago' => $pago->metodo_pago,
                        'monto_pagado' => $pago->monto_pagado,
                        'qr_url' => $pago->qr_path
                            ? Storage::url($pago->qr_path)
                            : null,
                    ],
                ]);
            } catch (Throwable $error) {
                $pago->update([
                    'estado' => 'Fallido',
                    'observacion' => trim(
                        ($pago->observacion ?? '')
                        . "\nError PagoFácil: "
                        . $error->getMessage()
                    ),
                ]);

                return back()->with(
                    'error',
                    'La inscripción fue registrada, pero no se pudo generar el QR.'
                );
            }
        }


        return back()->with([
            'success' =>
                'La inscripción fue registrada. El pago quedó pendiente de procesamiento.',

            'pago_generado' => [
                'id' => $pago->id,
                'estado' => $pago->estado,
                'metodo_pago' => $pago->metodo_pago,
                'monto_pagado' => $pago->monto_pagado,
                'qr_url' => null,
            ],
        ]);
    }

    private function obtenerConceptoPago(
        string $tipoPago
    ): ?ConceptoPago {
        return ConceptoPago::query()
            ->where('estado', 'Activo')
            ->where(function ($query) use ($tipoPago) {
                if ($tipoPago === 'matricula') {
                    $query
                        ->where('nombre', 'ILIKE', '%matrícula%')
                        ->orWhere('nombre', 'ILIKE', '%matricula%');

                    return;
                }

                $query->where(
                    'nombre',
                    'ILIKE',
                    '%carrera%completa%'
                );
            })
            ->first();
    }
}