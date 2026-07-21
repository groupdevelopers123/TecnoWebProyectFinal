<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\CarreraMateria;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CarreraMateriaController extends Controller
{
    private function periodoLabel(?string $regimenAcademico): string
    {
        return match (mb_strtolower((string) $regimenAcademico)) {
            'anual' => 'Año',
            'modular' => 'Módulo',
            default => 'Semestre',
        };
    }

    private function asignacionPayload(CarreraMateria $asignacion): array
    {
        $asignacion->loadMissing(['carrera', 'materia']);

        return [
            'id' => $asignacion->id,
            'carrera_id' => $asignacion->carrera_id,
            'materia_id' => $asignacion->materia_id,
            'periodo_numero' => $asignacion->periodo_numero,
            'estado' => (bool) $asignacion->estado,
            'carrera' => [
                'id' => $asignacion->carrera?->id,
                'codigo' => $asignacion->carrera?->codigo,
                'nombre' => $asignacion->carrera?->nombre,
                'regimen_academico' => $asignacion->carrera?->regimen_academico,
            ],
            'materia' => [
                'id' => $asignacion->materia?->id,
                'codigo' => $asignacion->materia?->codigo,
                'nombre' => $asignacion->materia?->nombre,
                'carga_horaria' => $asignacion->materia?->carga_horaria,
            ],
        ];
    }

    private function carrerasDisponibles()
    {
        return Carrera::query()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($carrera) {
                return [
                    'id' => $carrera->id,
                    'codigo' => $carrera->codigo,
                    'nombre' => $carrera->nombre,
                    'regimen_academico' => $carrera->regimen_academico,
                ];
            })
            ->values();
    }

    private function materiasDisponibles()
    {
        return Materia::query()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($materia) {
                return [
                    'id' => $materia->id,
                    'codigo' => $materia->codigo,
                    'nombre' => $materia->nombre,
                    'carga_horaria' => $materia->carga_horaria,
                ];
            })
            ->values();
    }

    public function index(Request $request)
    {
        $asignaciones = CarreraMateria::query()
            ->with(['carrera', 'materia'])
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($subQuery) use ($buscar) {
                    $subQuery->whereHas('carrera', function ($q) use ($buscar) {
                        $q->where('nombre', 'ILIKE', "%{$buscar}%")
                            ->orWhere('codigo', 'ILIKE', "%{$buscar}%");
                    })->orWhereHas('materia', function ($q) use ($buscar) {
                        $q->where('nombre', 'ILIKE', "%{$buscar}%")
                            ->orWhere('codigo', 'ILIKE', "%{$buscar}%");
                    });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/carrera_materias/Index', [
            'asignaciones' => [
                'data' => $asignaciones->getCollection()->map(fn (CarreraMateria $asignacion) => $this->asignacionPayload($asignacion))->values(),
                'pagination' => [
                    'current_page' => $asignaciones->currentPage(),
                    'last_page' => $asignaciones->lastPage(),
                    'per_page' => $asignaciones->perPage(),
                    'total' => $asignaciones->total(),
                    'prev_page_url' => $asignaciones->previousPageUrl(),
                    'next_page_url' => $asignaciones->nextPageUrl(),
                ],
            ],
            'request' => [
                'buscar' => $request->buscar,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/carrera_materias/Create', [
            'carreras' => $this->carrerasDisponibles(),
            'materias' => $this->materiasDisponibles(),
            'action' => route('admin.carrera-materias.store'),
            'cancelUrl' => route('admin.carrera-materias.index'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        CarreraMateria::create([
            'carrera_id' => $data['carrera_id'],
            'materia_id' => $data['materia_id'],
            'periodo_numero' => $data['periodo_numero'] ?? null,
            'estado' => true,
        ]);

        return redirect()
            ->route('admin.carrera-materias.index')
            ->with('success', 'Materia asignada correctamente a la carrera.');
    }

    public function show(CarreraMateria $asignacion)
    {
        return Inertia::render('admin/carrera_materias/Show', [
            'asignacion' => $this->asignacionPayload($asignacion),
            'periodLabel' => $this->periodoLabel($asignacion->carrera?->regimen_academico),
        ]);
    }

    public function edit(CarreraMateria $asignacion)
    {
        $asignacion->loadMissing(['carrera', 'materia']);

        return Inertia::render('admin/carrera_materias/Edit', [
            'asignacion' => $this->asignacionPayload($asignacion),
            'carreras' => $this->carrerasDisponibles(),
            'materias' => $this->materiasDisponibles(),
            'action' => route('admin.carrera-materias.update', $asignacion),
            'cancelUrl' => route('admin.carrera-materias.index'),
        ]);
    }

    public function update(Request $request, CarreraMateria $asignacion)
    {
        $data = $this->validatedData($request, $asignacion);

        $asignacion->update([
            'carrera_id' => $data['carrera_id'],
            'materia_id' => $data['materia_id'],
            'periodo_numero' => $data['periodo_numero'] ?? null,
            'estado' => $data['estado'] ?? $asignacion->estado,
        ]);

        return redirect()
            ->route('admin.carrera-materias.index')
            ->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroy(CarreraMateria $asignacion)
    {
        $asignacion->update([
            'estado' => ! $asignacion->estado,
        ]);

        return redirect()
            ->route('admin.carrera-materias.index')
            ->with('success', 'Estado de la asignación actualizado correctamente.');
    }

    private function validatedData(Request $request, ?CarreraMateria $asignacion = null): array
    {
        $carreraId = $request->input('carrera_id');

        return $request->validate([
            'carrera_id' => ['required', 'exists:carreras,id'],
            'materia_id' => [
                'required',
                'exists:materias,id',
                Rule::unique('carrera_materia', 'materia_id')
                    ->where('carrera_id', $carreraId)
                    ->ignore($asignacion?->id),
            ],
            'periodo_numero' => ['nullable', 'integer', 'min:1', 'max:12'],
            'estado' => ['nullable', 'boolean'],
        ], [
            'carrera_id.required' => 'Debe seleccionar una carrera.',
            'carrera_id.exists' => 'La carrera seleccionada no existe.',
            'materia_id.required' => 'Debe seleccionar una materia.',
            'materia_id.exists' => 'La materia seleccionada no existe.',
            'materia_id.unique' => 'Esta materia ya está asignada a esa carrera.',
            'periodo_numero.integer' => 'El periodo debe ser un número válido.',
        ]);
    }

    public function storeDesdeModal(Request $request, Carrera $carrera)
    {
        $data = $request->validate([
            'materia_id' => [
                'required',
                'exists:materias,id',
                Rule::unique('carrera_materia', 'materia_id')
                    ->where('carrera_id', $carrera->id),
            ],
            'periodo_numero' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
            ],
        ], [
            'materia_id.required' => 'Debe seleccionar una materia.',
            'materia_id.exists' => 'La materia seleccionada no existe.',
            'materia_id.unique' => 'Esta materia ya está asignada a esta carrera.',
            'periodo_numero.integer' => 'El periodo debe ser un número válido.',
        ]);

        CarreraMateria::create([
            'carrera_id' => $carrera->id,
            'materia_id' => $data['materia_id'],
            'periodo_numero' => $data['periodo_numero'] ?? null,
            'estado' => true,
        ]);

        return redirect()
            ->route('admin.carreras.index')
            ->with('success', 'Materia asignada correctamente a la carrera.');
    }

    public function updateDesdeModal(Request $request, Carrera $carrera, CarreraMateria $asignacion)
    {
        if ($asignacion->carrera_id !== $carrera->id) {
            abort(404);
        }

        $data = $request->validate([
            'periodo_numero' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
            ],
            'estado' => [
                'nullable',
                'boolean',
            ],
        ], [
            'periodo_numero.integer' => 'El periodo debe ser un número válido.',
        ]);

        $asignacion->update([
            'periodo_numero' => $data['periodo_numero'] ?? null,
            'estado' => $data['estado'] ?? $asignacion->estado,
        ]);

        return redirect()
            ->route('admin.carreras.index')
            ->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroyDesdeModal(Carrera $carrera, CarreraMateria $asignacion)
    {
        if ($asignacion->carrera_id !== $carrera->id) {
            abort(404);
        }

        $asignacion->update([
            'estado' => ! $asignacion->estado,
        ]);

        return redirect()
            ->route('admin.carreras.index')
            ->with('success', 'Estado de la materia en la carrera actualizado correctamente.');
    }
}