<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PeriodoAcademicoRequest;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;

class PeriodoAcademicoController extends Controller
{
    public function index(Request $request)
    {
        $periodos = PeriodoAcademico::query()
            ->when($request->buscar, function ($query, $buscar) {
                $query->where('nombre', 'ILIKE', "%{$buscar}%")
                    ->orWhere('gestion', (int) $buscar);
            })
            ->orderByDesc('gestion')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
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

        return view('admin.periodos.index', compact('periodos'));
    }

    public function create()
    {
        return view('admin.periodos.create');
    }

    public function store(PeriodoAcademicoRequest $request)
    {
        PeriodoAcademico::create($request->validated());

        return redirect()->route('admin.periodos-academicos.index')->with('success', 'Periodo académico registrado correctamente.');
    }

    public function show(PeriodoAcademico $periodos_academico)
    {
        return view('admin.periodos.show', ['periodo' => $periodos_academico]);
    }

    public function edit(PeriodoAcademico $periodos_academico)
    {
        return view('admin.periodos.edit', ['periodo' => $periodos_academico]);
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