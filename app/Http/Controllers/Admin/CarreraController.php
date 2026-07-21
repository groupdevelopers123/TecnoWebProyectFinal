<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarreraRequest;
use App\Models\Carrera;
use App\Models\Materia;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarreraController extends Controller
{
    private function carreraPayload(Carrera $carrera): array
    {
        $carrera->loadMissing('carreraMaterias.materia');

        return [
            'id' => $carrera->id,
            'codigo' => $carrera->codigo,
            'nombre' => $carrera->nombre,
            'duracion' => $carrera->duracion,
            'regimen_academico' => $carrera->regimen_academico,
            'estado' => (bool) $carrera->estado,
            'materias_asignadas' => $carrera->carreraMaterias->map(function ($asignacion) {
                return [
                    'id' => $asignacion->id,
                    'materia_id' => $asignacion->materia_id,
                    'periodo_numero' => $asignacion->periodo_numero,
                    'estado' => (bool) $asignacion->estado,
                    'materia' => [
                        'id' => $asignacion->materia->id,
                        'codigo' => $asignacion->materia->codigo,
                        'nombre' => $asignacion->materia->nombre,
                        'carga_horaria' => $asignacion->materia->carga_horaria,
                    ],
                ];
            })->values(),
        ];
    }

    public function index(Request $request)
    {
        $carreras = Carrera::query()
            ->with('carreraMaterias.materia')
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('codigo', 'ILIKE', "%{$buscar}%")
                        ->orWhere('nombre', 'ILIKE', "%{$buscar}%")
                        ->orWhere('regimen_academico', 'ILIKE', "%{$buscar}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $materias = Materia::where('estado', true)
            ->orderBy('nombre')
            ->get();

        if ($request->ajax() && ! $request->header('X-Inertia')) {
            return response()->json([
                'data' => $carreras->getCollection()->map(function ($carrera) {
                    return [
                        'id' => $carrera->id,
                        'codigo' => $carrera->codigo,
                        'nombre' => $carrera->nombre,
                        'duracion' => $carrera->duracion,
                        'regimen_academico' => $carrera->regimen_academico,
                        'estado' => (bool) $carrera->estado,
                        'materias_asignadas' => $carrera->carreraMaterias->map(function ($asignacion) {
                            return [
                                'id' => $asignacion->id,
                                'materia_id' => $asignacion->materia_id,
                                'periodo_numero' => $asignacion->periodo_numero,
                                'materia' => [
                                    'id' => $asignacion->materia->id,
                                    'codigo' => $asignacion->materia->codigo,
                                    'nombre' => $asignacion->materia->nombre,
                                    'carga_horaria' => $asignacion->materia->carga_horaria,
                                ],
                            ];
                        })->values(),
                    ];
                })->values(),

                'materias' => $materias->map(function ($materia) {
                    return [
                        'id' => $materia->id,
                        'codigo' => $materia->codigo,
                        'nombre' => $materia->nombre,
                        'carga_horaria' => $materia->carga_horaria,
                    ];
                })->values(),

                'pagination' => [
                    'current_page' => $carreras->currentPage(),
                    'last_page' => $carreras->lastPage(),
                    'per_page' => $carreras->perPage(),
                    'total' => $carreras->total(),
                    'prev_page_url' => $carreras->previousPageUrl(),
                    'next_page_url' => $carreras->nextPageUrl(),
                ],
            ]);
        }

        return Inertia::render('admin/carreras/Index', [
            'carreras' => [
                'data' => $carreras->getCollection()->map(fn ($carrera) => $this->carreraPayload($carrera))->values(),
                'pagination' => [
                    'current_page' => $carreras->currentPage(),
                    'last_page' => $carreras->lastPage(),
                    'per_page' => $carreras->perPage(),
                    'total' => $carreras->total(),
                    'prev_page_url' => $carreras->previousPageUrl(),
                    'next_page_url' => $carreras->nextPageUrl(),
                ],
            ],
            'materias' => $materias->map(function ($materia) {
                return [
                    'id' => $materia->id,
                    'codigo' => $materia->codigo,
                    'nombre' => $materia->nombre,
                    'carga_horaria' => $materia->carga_horaria,
                ];
            })->values(),
            'request' => [
                'buscar' => $request->buscar,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/carreras/Create', [
            'action' => route('admin.carreras.store'),
            'cancelUrl' => route('admin.carreras.index'),
        ]);
    }

    public function store(CarreraRequest $request)
    {
        Carrera::create([
            ...$request->validated(),
            'user_id_registro' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.carreras.index')
            ->with('success', 'Carrera registrada correctamente.');
    }

    public function show(Carrera $carrera)
    {
        return Inertia::render('admin/carreras/Show', [
            'carrera' => $this->carreraPayload($carrera),
        ]);
    }

    public function edit(Carrera $carrera)
    {
        return Inertia::render('admin/carreras/Edit', [
            'carrera' => $this->carreraPayload($carrera),
            'action' => route('admin.carreras.update', $carrera),
            'cancelUrl' => route('admin.carreras.index'),
        ]);
    }

    public function update(CarreraRequest $request, Carrera $carrera)
    {
        $carrera->update($request->validated());

        return redirect()
            ->route('admin.carreras.index')
            ->with('success', 'Carrera actualizada correctamente.');
    }

    public function destroy(Carrera $carrera)
    {
        $carrera->update([
            'estado' => ! $carrera->estado,
        ]);

        return redirect()
            ->route('admin.carreras.index')
            ->with('success', 'Estado de la carrera actualizado.');
    }
}