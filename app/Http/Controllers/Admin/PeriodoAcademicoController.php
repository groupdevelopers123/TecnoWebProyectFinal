<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PeriodoAcademicoRequest;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PeriodoAcademicoController extends Controller
{
    private function periodoPayload(PeriodoAcademico $periodo): array
    {
        return [
            'id' => $periodo->id,
            'nombre' => $periodo->nombre,
            'gestion' => $periodo->gestion,
            'tipo_periodo' => $periodo->tipo_periodo,
            'numero_periodo' => $periodo->numero_periodo,
            'fecha_inicio' => $periodo->fecha_inicio?->format('Y-m-d'),
            'fecha_fin' => $periodo->fecha_fin?->format('Y-m-d'),
            'estado' => (bool) $periodo->estado,
        ];
    }

    public function index(Request $request)
    {
        $periodos = PeriodoAcademico::query()
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($subQuery) use ($buscar) {
                    $subQuery->where('nombre', 'ILIKE', "%{$buscar}%")
                        ->orWhere('gestion', (int) $buscar);
                });
            })
            ->orderByDesc('gestion')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax() && ! $request->header('X-Inertia')) {
            return response()->json([
                'data' => $periodos->getCollection()->map(function ($periodo) {
                    return [
                        'id' => $periodo->id,
                        'nombre' => $periodo->nombre,
                        'gestion' => $periodo->gestion,
                        'tipo_periodo' => $periodo->tipo_periodo,
                        'fecha_inicio' => $periodo->fecha_inicio?->format('d/m/Y'),
                        'fecha_fin' => $periodo->fecha_fin?->format('d/m/Y'),
                        'estado' => (bool) $periodo->estado,
                    ];
                })->values(),
                'pagination' => [
                    'current_page' => $periodos->currentPage(),
                    'last_page' => $periodos->lastPage(),
                    'per_page' => $periodos->perPage(),
                    'total' => $periodos->total(),
                    'prev_page_url' => $periodos->previousPageUrl(),
                    'next_page_url' => $periodos->nextPageUrl(),
                ],
            ]);
        }

        return Inertia::render('admin/periodos/Index', [
            'periodos' => [
                'data' => $periodos->getCollection()->map(fn ($periodo) => $this->periodoPayload($periodo))->values(),
                'pagination' => [
                    'current_page' => $periodos->currentPage(),
                    'last_page' => $periodos->lastPage(),
                    'per_page' => $periodos->perPage(),
                    'total' => $periodos->total(),
                    'prev_page_url' => $periodos->previousPageUrl(),
                    'next_page_url' => $periodos->nextPageUrl(),
                ],
            ],
            'request' => [
                'buscar' => $request->buscar,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/periodos/Create', [
            'periodo' => [],
            'action' => route('admin.periodos-academicos.store'),
            'cancelUrl' => route('admin.periodos-academicos.index'),
        ]);
    }

    public function store(PeriodoAcademicoRequest $request)
    {
        PeriodoAcademico::create($request->validated());

        return redirect()->route('admin.periodos-academicos.index')->with('success', 'Periodo académico registrado correctamente.');
    }

    public function show(PeriodoAcademico $periodos_academico)
    {
        return Inertia::render('admin/periodos/Show', [
            'periodo' => $this->periodoPayload($periodos_academico),
        ]);
    }

    public function edit(PeriodoAcademico $periodos_academico)
    {
        return Inertia::render('admin/periodos/Edit', [
            'periodo' => $this->periodoPayload($periodos_academico),
            'action' => route('admin.periodos-academicos.update', $periodos_academico),
            'cancelUrl' => route('admin.periodos-academicos.index'),
        ]);
    }

    public function update(PeriodoAcademicoRequest $request, PeriodoAcademico $periodos_academico)
    {
        $periodos_academico->update($request->validated());

        return redirect()->route('admin.periodos-academicos.index')->with('success', 'Periodo académico actualizado correctamente.');
    }

    public function destroy(PeriodoAcademico $periodos_academico)
    {
        $periodos_academico->update(['estado' => ! $periodos_academico->estado]);

        return redirect()->route('admin.periodos-academicos.index')->with('success', 'Estado del periodo actualizado.');
    }
}