<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreditoRequest;
use App\Models\ConceptoPago;
use App\Models\Credito;
use App\Models\Inscripcion;
use App\Notifications\CreditoHabilitado;
use App\Services\CreditoCuotaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditoController extends Controller
{
    public function index(Request $request)
    {
        $creditos = Credito::query()
            ->with([
                'inscripcion.alumnoDetalle.user',
                'inscripcion.ofertaAcademica.carrera',
                'inscripcion.ofertaAcademica.periodoAcademico',
                'conceptoPago',
                'pagoCuotas',
            ])
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('tipo_pago', 'ILIKE', "%{$buscar}%")
                        ->orWhere('estado', 'ILIKE', "%{$buscar}%")
                        ->orWhereHas('conceptoPago', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('inscripcion.alumnoDetalle.user', function ($sub) use ($buscar) {
                            $sub->where('nombres', 'ILIKE', "%{$buscar}%")
                                ->orWhere('apellidos', 'ILIKE', "%{$buscar}%")
                                ->orWhere('ci', 'ILIKE', "%{$buscar}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pagos.creditos.index', compact('creditos'));
    }

    public function create()
    {
        return view('admin.pagos.creditos.create', [
            ...$this->formData(),
            'credito' => new Credito([
                'tipo_pago' => 'CREDITO',
                'estado' => 'pendiente',
                'fecha_otorgamiento' => now(),
            ]),
        ]);
    }

    public function store(CreditoRequest $request, CreditoCuotaService $cuotaService)
    {
        DB::transaction(function () use ($request, $cuotaService) {
            $data = $request->validated();

            $data['saldo_pendiente'] = $data['saldo_pendiente'] ?? $data['monto_total'];
            $data['estado'] = 'activo';

            $credito = Credito::create($data);
            $cuotaService->generarCuotas($credito);

            $alumno = $credito->inscripcion->alumnoDetalle->user;
            if ($alumno) {
                $alumno->notify(new CreditoHabilitado($credito));
            }
        });

        return redirect()
            ->route('admin.creditos.index')
            ->with('success', 'Crédito registrado correctamente y cuotas generadas automáticamente.');
    }

    public function show(Credito $credito)
    {
        $credito->load([
            'inscripcion.alumnoDetalle.user',
            'inscripcion.ofertaAcademica.carrera',
            'inscripcion.ofertaAcademica.periodoAcademico',
            'conceptoPago',
            'pagoCuotas',
        ]);

        return view('admin.pagos.creditos.show', compact('credito'));
    }

    public function edit(Credito $credito)
    {
        return view('admin.pagos.creditos.edit', [
            ...$this->formData(),
            'credito' => $credito,
        ]);
    }

    public function update(CreditoRequest $request, Credito $credito, CreditoCuotaService $cuotaService)
    {
        DB::transaction(function () use ($request, $credito, $cuotaService) {
            $data = $request->validated();

            $data['saldo_pendiente'] = $data['saldo_pendiente'] ?? $data['monto_total'];

            $debeRegenerar = (
                (float) $credito->monto_total !== (float) $data['monto_total'] ||
                (int) $credito->cantidad_cuotas !== (int) $data['cantidad_cuotas'] ||
                optional($credito->fecha_otorgamiento)->format('Y-m-d') !== $data['fecha_otorgamiento'] ||
                optional($credito->fecha_vencimiento)->format('Y-m-d') !== $data['fecha_vencimiento']
            );

            if ($debeRegenerar && ! $cuotaService->puedeRegenerarCuotas($credito)) {
                abort(422, 'No se pueden modificar monto, fechas o cantidad de cuotas porque ya existen cuotas pagadas.');
            }

            $credito->update($data);

            if ($debeRegenerar) {
                $cuotaService->generarCuotas($credito);
            }

            $credito->recalcularSaldo();
        });

        return redirect()
            ->route('admin.creditos.index')
            ->with('success', 'Crédito actualizado correctamente.');
    }

    public function destroy(Credito $credito)
    {
        $credito->update([
            'estado' => $credito->estado === 'anulado' ? 'activo' : 'anulado',
        ]);

        return redirect()
            ->route('admin.creditos.index')
            ->with('success', 'Estado del crédito actualizado correctamente.');
    }

    private function formData(): array
    {
        return [
            'inscripciones' => Inscripcion::query()
                ->with([
                    'alumnoDetalle.user',
                    'ofertaAcademica.carrera',
                    'ofertaAcademica.periodoAcademico',
                ])
                ->latest()
                ->get(),

            'conceptos' => ConceptoPago::query()
                ->where('estado', 'Activo')
                ->orderBy('nombre')
                ->get(),
        ];
    }
}