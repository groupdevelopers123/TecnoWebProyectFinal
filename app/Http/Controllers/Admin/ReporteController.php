<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GenericViewExport;
use App\Http\Controllers\Controller;
use App\Models\Credito;
use App\Models\Inscripcion;
use App\Models\PagoContado;
use App\Models\PagoCuota;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        [$inicio, $fin] = $this->obtenerPeriodo($request);

        return view('admin.reportes.index', [
            'inicio' => $inicio->format('Y-m-d'),
            'fin' => $fin->format('Y-m-d'),

            'estadisticas' => $this->estadisticasGenerales($inicio, $fin),

            'pagosContados' => $this->pagosContadosMensuales($inicio, $fin),
            'pagosCreditos' => $this->pagosCreditosMensuales($inicio, $fin),
            'pagosContadosCantidad' => $this->pagosContadosCantidadMensuales($inicio, $fin),
            'pagosCreditosCantidad' => $this->pagosCreditosCantidadMensuales($inicio, $fin),
            'inscripciones' => $this->inscripcionesMensuales($inicio, $fin),
            'pagosPorConcepto' => $this->pagosPorConcepto($inicio, $fin),
        ]);
    }

    public function inscripcionesPdf(Request $request)
    {
        [$inicio, $fin] = $this->obtenerPeriodo($request);

        $data = $this->reporteInscripciones($inicio, $fin);

        $pdf = Pdf::loadView('admin.reportes.pdf.generic', [
            ...$data,
            'inicio' => $inicio,
            'fin' => $fin,
        ])->setPaper('letter', 'landscape');

        return $pdf->download('reporte-inscripciones.pdf');
    }

    public function inscripcionesExcel(Request $request)
    {
        [$inicio, $fin] = $this->obtenerPeriodo($request);

        $data = $this->reporteInscripciones($inicio, $fin);

        return Excel::download(
            new GenericViewExport('admin.reportes.excel.generic', [
                ...$data,
                'inicio' => $inicio,
                'fin' => $fin,
            ]),
            'reporte-inscripciones.xlsx'
        );
    }

    public function pagosPdf(Request $request)
    {
        [$inicio, $fin] = $this->obtenerPeriodo($request);

        $data = $this->reportePagos($inicio, $fin);

        $pdf = Pdf::loadView('admin.reportes.pdf.generic', [
            ...$data,
            'inicio' => $inicio,
            'fin' => $fin,
        ])->setPaper('letter', 'landscape');

        return $pdf->download('reporte-pagos.pdf');
    }

    public function pagosExcel(Request $request)
    {
        [$inicio, $fin] = $this->obtenerPeriodo($request);

        $data = $this->reportePagos($inicio, $fin);

        return Excel::download(
            new GenericViewExport('admin.reportes.excel.generic', [
                ...$data,
                'inicio' => $inicio,
                'fin' => $fin,
            ]),
            'reporte-pagos.xlsx'
        );
    }

    public function creditosPdf(Request $request)
    {
        [$inicio, $fin] = $this->obtenerPeriodo($request);

        $data = $this->reporteCreditos($inicio, $fin);

        $pdf = Pdf::loadView('admin.reportes.pdf.generic', [
            ...$data,
            'inicio' => $inicio,
            'fin' => $fin,
        ])->setPaper('letter', 'landscape');

        return $pdf->download('reporte-creditos.pdf');
    }

    public function creditosExcel(Request $request)
    {
        [$inicio, $fin] = $this->obtenerPeriodo($request);

        $data = $this->reporteCreditos($inicio, $fin);

        return Excel::download(
            new GenericViewExport('admin.reportes.excel.generic', [
                ...$data,
                'inicio' => $inicio,
                'fin' => $fin,
            ]),
            'reporte-creditos.xlsx'
        );
    }

    private function obtenerPeriodo(Request $request): array
    {
        $inicio = $request->filled('inicio')
            ? Carbon::parse($request->inicio)->startOfDay()
            : now()->startOfYear();

        $fin = $request->filled('fin')
            ? Carbon::parse($request->fin)->endOfDay()
            : now()->endOfDay();

        return [$inicio, $fin];
    }

    private function estadisticasGenerales(Carbon $inicio, Carbon $fin): array
    {
        $totalInscripciones = Inscripcion::query()
            ->whereBetween('created_at', [$inicio, $fin])
            ->count();

        $aprobados = 0;

        if (Schema::hasColumn('inscripciones', 'estado')) {
            $aprobados = Inscripcion::query()
                ->whereBetween('created_at', [$inicio, $fin])
                ->where('estado', 'ILIKE', '%aprob%')
                ->count();
        }

        $pagosContado = PagoContado::query()
            ->where('estado', 'Confirmado')
            ->whereBetween('fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->sum('monto_pagado');

        $pagosCuotas = PagoCuota::query()
            ->where('estado_cuota', 'pagado')
            ->whereBetween('fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->sum('monto');

        $creditosActivos = Credito::query()
            ->where('estado', 'activo')
            ->count();

        $saldoPendiente = Credito::query()
            ->whereIn('estado', ['pendiente', 'activo'])
            ->sum('saldo_pendiente');

        $cuotasPendientes = PagoCuota::query()
            ->where('estado_cuota', 'pendiente')
            ->count();

        $cuotasPagadas = PagoCuota::query()
            ->where('estado_cuota', 'pagado')
            ->count();

        return [
            'total_inscripciones' => $totalInscripciones,
            'aprobados' => $aprobados,
            'total_pagos' => round((float) $pagosContado + (float) $pagosCuotas, 2),
            'pagos_contado' => round((float) $pagosContado, 2),
            'pagos_cuotas' => round((float) $pagosCuotas, 2),
            'creditos_activos' => $creditosActivos,
            'saldo_pendiente' => round((float) $saldoPendiente, 2),
            'cuotas_pendientes' => $cuotasPendientes,
            'cuotas_pagadas' => $cuotasPagadas,
        ];
    }

    private function inscripcionesMensuales(Carbon $inicio, Carbon $fin): array
    {
        return Inscripcion::query()
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as mes")
            ->selectRaw("COUNT(*) as total")
            ->whereBetween('created_at', [$inicio, $fin])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn ($item) => [
                'mes' => $item->mes,
                'total' => (int) $item->total,
            ])
            ->values()
            ->toArray();
    }

    private function pagosMensuales(Carbon $inicio, Carbon $fin): array
    {
        $contado = PagoContado::query()
            ->selectRaw("TO_CHAR(fecha_pago, 'YYYY-MM') as mes")
            ->selectRaw("SUM(monto_pagado) as total")
            ->where('estado', 'Confirmado')
            ->whereBetween('fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $cuotas = PagoCuota::query()
            ->selectRaw("TO_CHAR(fecha_pago, 'YYYY-MM') as mes")
            ->selectRaw("SUM(monto) as total")
            ->where('estado_cuota', 'pagado')
            ->whereBetween('fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $meses = collect($contado->keys())
            ->merge($cuotas->keys())
            ->unique()
            ->sort()
            ->values();

        return $meses->map(function ($mes) use ($contado, $cuotas) {
            return [
                'mes' => $mes,
                'contado' => round((float) ($contado[$mes] ?? 0), 2),
                'cuotas' => round((float) ($cuotas[$mes] ?? 0), 2),
                'total' => round((float) ($contado[$mes] ?? 0) + (float) ($cuotas[$mes] ?? 0), 2),
            ];
        })->toArray();
    }

    private function pagosContadosMensuales(Carbon $inicio, Carbon $fin): array
    {
        return PagoContado::query()
            ->selectRaw("TO_CHAR(fecha_pago, 'YYYY-MM') as mes")
            ->selectRaw("SUM(monto_pagado) as total")
            ->where('estado', 'Confirmado')
            ->whereBetween('fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn ($item) => [
                'mes' => $item->mes,
                'total' => round((float) $item->total, 2),
            ])
            ->values()
            ->toArray();
    }

    private function pagosCreditosMensuales(Carbon $inicio, Carbon $fin): array
    {
        return PagoCuota::query()
            ->selectRaw("TO_CHAR(fecha_pago, 'YYYY-MM') as mes")
            ->selectRaw("SUM(monto) as total")
            ->where('estado_cuota', 'pagado')
            ->whereBetween('fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn ($item) => [
                'mes' => $item->mes,
                'total' => round((float) $item->total, 2),
            ])
            ->values()
            ->toArray();
    }

    private function pagosContadosCantidadMensuales(Carbon $inicio, Carbon $fin): array
    {
        return PagoContado::query()
            ->selectRaw("TO_CHAR(fecha_pago, 'YYYY-MM') as mes")
            ->selectRaw("COUNT(*) as total")
            ->where('estado', 'Confirmado')
            ->whereBetween('fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn ($item) => [
                'mes' => $item->mes,
                'total' => (int) $item->total,
            ])
            ->values()
            ->toArray();
    }

    private function pagosCreditosCantidadMensuales(Carbon $inicio, Carbon $fin): array
    {
        return PagoCuota::query()
            ->selectRaw("TO_CHAR(fecha_pago, 'YYYY-MM') as mes")
            ->selectRaw("COUNT(*) as total")
            ->where('estado_cuota', 'pagado')
            ->whereBetween('fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn ($item) => [
                'mes' => $item->mes,
                'total' => (int) $item->total,
            ])
            ->values()
            ->toArray();
    }

    private function pagosPorConcepto(Carbon $inicio, Carbon $fin): array
    {
        $contado = PagoContado::query()
            ->select('concepto_pagos.nombre as concepto')
            ->selectRaw('SUM(pago_contados.monto_pagado) as total')
            ->join('concepto_pagos', 'pago_contados.concepto_pago_id', '=', 'concepto_pagos.id')
            ->where('pago_contados.estado', 'Confirmado')
            ->whereBetween('pago_contados.fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->groupBy('concepto_pagos.nombre');

        $creditos = PagoCuota::query()
            ->select('concepto_pagos.nombre as concepto')
            ->selectRaw('SUM(pago_cuotas.monto) as total')
            ->join('creditos', 'pago_cuotas.credito_id', '=', 'creditos.id')
            ->join('concepto_pagos', 'creditos.concepto_pago_id', '=', 'concepto_pagos.id')
            ->where('pago_cuotas.estado_cuota', 'pagado')
            ->whereBetween('pago_cuotas.fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->groupBy('concepto_pagos.nombre');

        $merged = collect($contado->get()->map(fn ($item) => [
            'concepto' => $item->concepto,
            'total' => (float) $item->total,
        ]))->merge($creditos->get()->map(fn ($item) => [
            'concepto' => $item->concepto,
            'total' => (float) $item->total,
        ]));

        return $merged
            ->groupBy('concepto')
            ->map(fn ($items, $concepto) => [
                'concepto' => $concepto,
                'total' => round($items->sum('total'), 2),
            ])
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }

    private function creditosPorEstado(Carbon $inicio, Carbon $fin): array
    {
        return Credito::query()
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$inicio, $fin])
            ->groupBy('estado')
            ->orderBy('estado')
            ->get()
            ->map(fn ($item) => [
                'estado' => ucfirst($item->estado),
                'total' => (int) $item->total,
            ])
            ->values()
            ->toArray();
    }

    private function cuotasPorEstado(Carbon $inicio, Carbon $fin): array
    {
        return PagoCuota::query()
            ->select('estado_cuota', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$inicio, $fin])
            ->groupBy('estado_cuota')
            ->orderBy('estado_cuota')
            ->get()
            ->map(fn ($item) => [
                'estado' => ucfirst($item->estado_cuota),
                'total' => (int) $item->total,
            ])
            ->values()
            ->toArray();
    }

    private function ojivaInscripciones(Carbon $inicio, Carbon $fin): array
    {
        $datos = $this->inscripcionesMensuales($inicio, $fin);

        $acumulado = 0;

        return collect($datos)->map(function ($item) use (&$acumulado) {
            $acumulado += $item['total'];

            return [
                'mes' => $item['mes'],
                'total' => $item['total'],
                'acumulado' => $acumulado,
            ];
        })->values()->toArray();
    }

    private function reporteInscripciones(Carbon $inicio, Carbon $fin): array
    {
        $inscripciones = Inscripcion::query()
            ->with([
                'alumnoDetalle.user',
                'ofertaAcademica.carrera',
                'ofertaAcademica.periodoAcademico',
            ])
            ->whereBetween('created_at', [$inicio, $fin])
            ->latest()
            ->get();

        return [
            'titulo' => 'Reporte de Inscripciones',
            'columnas' => [
                'ID',
                'Alumno',
                'CI',
                'Carrera',
                'Periodo',
                'Fecha registro',
            ],
            'filas' => $inscripciones->map(function ($inscripcion) {
                return [
                    $inscripcion->id,
                    $inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Sin alumno',
                    $inscripcion->alumnoDetalle->user?->ci ?? '-',
                    $inscripcion->ofertaAcademica->carrera->nombre ?? '-',
                    trim(($inscripcion->ofertaAcademica->periodoAcademico->nombre ?? '') . ' ' . ($inscripcion->ofertaAcademica->periodoAcademico->gestion ?? '')),
                    $inscripcion->created_at?->format('d/m/Y'),
                ];
            })->toArray(),
        ];
    }

    private function reportePagos(Carbon $inicio, Carbon $fin): array
    {
        $contado = PagoContado::query()
            ->with([
                'inscripcion.alumnoDetalle.user',
                'inscripcion.ofertaAcademica.carrera',
                'conceptoPago',
            ])
            ->whereBetween('fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->get()
            ->map(function ($pago) {
                return [
                    'Contado',
                    $pago->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Sin alumno',
                    $pago->conceptoPago->nombre ?? '-',
                    'Bs ' . number_format($pago->monto_pagado, 2),
                    $pago->metodo_pago,
                    $pago->estado,
                    $pago->fecha_pago?->format('d/m/Y'),
                    $pago->codigo_transaccion ?? '-',
                ];
            });

        $cuotas = PagoCuota::query()
            ->with([
                'credito.inscripcion.alumnoDetalle.user',
                'credito.conceptoPago',
            ])
            ->whereNotNull('metodo_pago')
            ->whereBetween('fecha_pago', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->get()
            ->map(function ($cuota) {
                return [
                    'Cuota #' . $cuota->numero_cuota,
                    $cuota->credito->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Sin alumno',
                    $cuota->credito->conceptoPago->nombre ?? '-',
                    'Bs ' . number_format($cuota->monto, 2),
                    $cuota->metodo_pago ?? '-',
                    $cuota->estado_cuota,
                    $cuota->fecha_pago?->format('d/m/Y'),
                    $cuota->codigo_transaccion ?? '-',
                ];
            });

        return [
            'titulo' => 'Reporte de Pagos',
            'columnas' => [
                'Tipo',
                'Alumno',
                'Concepto',
                'Monto',
                'Método',
                'Estado',
                'Fecha pago',
                'Transacción',
            ],
            'filas' => $contado->merge($cuotas)->values()->toArray(),
        ];
    }

    private function reporteCreditos(Carbon $inicio, Carbon $fin): array
    {
        $creditos = Credito::query()
            ->with([
                'inscripcion.alumnoDetalle.user',
                'conceptoPago',
                'pagoCuotas',
            ])
            ->whereBetween('created_at', [$inicio, $fin])
            ->latest()
            ->get();

        return [
            'titulo' => 'Reporte de Créditos',
            'columnas' => [
                'ID',
                'Alumno',
                'Concepto',
                'Monto total',
                'Saldo pendiente',
                'Cuotas',
                'Pagadas',
                'Estado',
                'Otorgamiento',
                'Vencimiento',
            ],
            'filas' => $creditos->map(function ($credito) {
                return [
                    $credito->id,
                    $credito->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Sin alumno',
                    $credito->conceptoPago->nombre ?? '-',
                    'Bs ' . number_format($credito->monto_total, 2),
                    'Bs ' . number_format($credito->saldo_pendiente, 2),
                    $credito->cantidad_cuotas,
                    $credito->pagoCuotas->where('estado_cuota', 'pagado')->count(),
                    ucfirst($credito->estado),
                    $credito->fecha_otorgamiento?->format('d/m/Y'),
                    $credito->fecha_vencimiento?->format('d/m/Y'),
                ];
            })->toArray(),
        ];
    }
}