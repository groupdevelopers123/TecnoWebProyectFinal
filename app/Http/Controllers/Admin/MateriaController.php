<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MateriaRequest;
use App\Models\Carrera;
use App\Models\CarreraMateria;
use App\Models\DocenteDetalle;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MateriaController extends Controller
{
    private function materiaPayload(Materia $materia): array
    {
        $materia->loadMissing(['docenteDetalle.user', 'carreraMaterias.carrera']);

        return [
            'id' => $materia->id,
            'codigo' => $materia->codigo,
            'nombre' => $materia->nombre,
            'carga_horaria' => $materia->carga_horaria,
            'docente_detalle_id' => $materia->docente_detalle_id,
            'estado' => (bool) $materia->estado,
            'docente' => $materia->docenteDetalle?->user
                ? trim($materia->docenteDetalle->user->nombres . ' ' . $materia->docenteDetalle->user->apellidos)
                : null,
            'docente_detalle' => $materia->docenteDetalle ? [
                'id' => $materia->docenteDetalle->id,
                'codigo' => $materia->docenteDetalle->codigo,
                'especialidad' => $materia->docenteDetalle->especialidad,
                'titulo' => $materia->docenteDetalle->titulo,
                'registro_profesional' => $materia->docenteDetalle->registro_profesional,
                'user' => [
                    'id' => $materia->docenteDetalle->user?->id,
                    'nombres' => $materia->docenteDetalle->user?->nombres,
                    'apellidos' => $materia->docenteDetalle->user?->apellidos,
                    'email' => $materia->docenteDetalle->user?->email,
                ],
            ] : null,
            'carreras_asignadas' => $materia->carreraMaterias->map(function ($asignacion) {
                return [
                    'id' => $asignacion->id,
                    'periodo_numero' => $asignacion->periodo_numero,
                    'estado' => (bool) $asignacion->estado,
                    'carrera' => [
                        'id' => $asignacion->carrera?->id,
                        'codigo' => $asignacion->carrera?->codigo,
                        'nombre' => $asignacion->carrera?->nombre,
                        'regimen_academico' => $asignacion->carrera?->regimen_academico,
                    ],
                ];
            })->values(),
        ];
    }

    private function carrerasActivas()
    {
        return Carrera::where('estado', true)
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

    private function docentesActivos()
    {
        return DocenteDetalle::query()
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('estado', true);
            })
            ->orderBy('codigo')
            ->get()
            ->map(function ($docente) {
                return [
                    'id' => $docente->id,
                    'codigo' => $docente->codigo,
                    'nombre' => trim(($docente->user?->nombres ?? '') . ' ' . ($docente->user?->apellidos ?? '')),
                ];
            })
            ->values();
    }

    public function index(Request $request)
    {
        $materias = Materia::query()
            ->with(['docenteDetalle.user'])
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($subQuery) use ($buscar) {
                    $subQuery->where('codigo', 'ILIKE', "%{$buscar}%")
                        ->orWhere('nombre', 'ILIKE', "%{$buscar}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax() && ! $request->header('X-Inertia')) {
            return response()->json([
                'data' => $materias->getCollection()->map(function ($materia) {
                    return [
                        'id' => $materia->id,
                        'codigo' => $materia->codigo,
                        'nombre' => $materia->nombre,
                        'carga_horaria' => $materia->carga_horaria,
                        'estado' => (bool) $materia->estado,
                        'docente' => $materia->docenteDetalle?->user ? trim($materia->docenteDetalle->user->nombres.' '.$materia->docenteDetalle->user->apellidos) : null,
                    ];
                })->values(),
                'pagination' => [
                    'current_page' => $materias->currentPage(),
                    'last_page' => $materias->lastPage(),
                    'per_page' => $materias->perPage(),
                    'total' => $materias->total(),
                    'prev_page_url' => $materias->previousPageUrl(),
                    'next_page_url' => $materias->nextPageUrl(),
                ],
            ]);
        }

        return Inertia::render('admin/materias/Index', [
            'materias' => [
                'data' => $materias->getCollection()->map(fn ($materia) => $this->materiaPayload($materia))->values(),
                'pagination' => [
                    'current_page' => $materias->currentPage(),
                    'last_page' => $materias->lastPage(),
                    'per_page' => $materias->perPage(),
                    'total' => $materias->total(),
                    'prev_page_url' => $materias->previousPageUrl(),
                    'next_page_url' => $materias->nextPageUrl(),
                ],
            ],
            'request' => [
                'buscar' => $request->buscar,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/materias/Create', [
            'carreras' => $this->carrerasActivas(),
            'docentes' => $this->docentesActivos(),
            'carreraSeleccionada' => null,
            'action' => route('admin.materias.store'),
            'cancelUrl' => route('admin.materias.index'),
        ]);
    }

    public function createDesdeCarrera(Carrera $carrera)
    {
        return Inertia::render('admin/materias/Create', [
            'carreras' => collect([$carrera])->map(fn ($item) => [
                'id' => $item->id,
                'codigo' => $item->codigo,
                'nombre' => $item->nombre,
                'regimen_academico' => $item->regimen_academico,
            ])->values(),
            'docentes' => $this->docentesActivos(),
            'carreraSeleccionada' => [
                'id' => $carrera->id,
                'codigo' => $carrera->codigo,
                'nombre' => $carrera->nombre,
                'regimen_academico' => $carrera->regimen_academico,
            ],
            'action' => route('admin.materias.store'),
            'cancelUrl' => route('admin.materias.index'),
        ]);
    }

    public function store(MateriaRequest $request)
    {
        DB::transaction(function () use ($request) {
            $materia = Materia::create([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'carga_horaria' => $request->carga_horaria,
                'docente_detalle_id' => $request->docente_detalle_id,
                'estado' => $request->estado,
            ]);

            if ($request->filled('carrera_id')) {
                CarreraMateria::create([
                    'carrera_id' => $request->carrera_id,
                    'materia_id' => $materia->id,
                    'periodo_numero' => $request->periodo_numero,
                ]);
            }
        });

        return redirect()
            ->route('admin.materias.index')
            ->with('success', 'Materia registrada correctamente.');
    }

    public function show(Materia $materia)
    {
        return Inertia::render('admin/materias/Show', [
            'materia' => $this->materiaPayload($materia),
        ]);
    }

    public function edit(Materia $materia)
    {
        return Inertia::render('admin/materias/Edit', [
            'materia' => $this->materiaPayload($materia),
            'carreras' => $this->carrerasActivas(),
            'docentes' => $this->docentesActivos(),
            'action' => route('admin.materias.update', $materia),
            'cancelUrl' => route('admin.materias.index'),
        ]);
    }

    public function update(MateriaRequest $request, Materia $materia)
    {
        $materia->update($request->only([
            'codigo',
            'nombre',
            'carga_horaria',
            'docente_detalle_id',
            'estado',
        ]));

        return redirect()
            ->route('admin.materias.index')
            ->with('success', 'Materia actualizada correctamente.');
    }

    public function destroy(Materia $materia)
    {
        $materia->update([
            'estado' => ! $materia->estado,
        ]);

        return redirect()
            ->route('admin.materias.index')
            ->with('success', 'Estado de la materia actualizado correctamente.');
    }
}