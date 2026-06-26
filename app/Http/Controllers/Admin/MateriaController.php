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

class MateriaController extends Controller
{
    public function index(Request $request)
    {
        $materias = Materia::query()
            ->with(['docenteDetalle.user'])
            ->when($request->buscar, function ($query, $buscar) {
                $query->where('codigo', 'ILIKE', "%{$buscar}%")
                    ->orWhere('nombre', 'ILIKE', "%{$buscar}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
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

        return view('admin.materias.index', compact('materias'));
    }

    public function create()
    {
        $carreras = Carrera::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $docentes = DocenteDetalle::query()
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('estado', true);
            })
            ->orderBy('codigo')
            ->get();

        return view('admin.materias.create', [
            'carreras' => $carreras,
            'docentes' => $docentes,
            'carreraSeleccionada' => null,
        ]);
    }

    public function createDesdeCarrera(Carrera $carrera)
    {
        $docentes = DocenteDetalle::query()
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('estado', true);
            })
            ->orderBy('codigo')
            ->get();

        return view('admin.materias.create', [
            'carreras' => collect([$carrera]),
            'docentes' => $docentes,
            'carreraSeleccionada' => $carrera,
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
        $materia->load(['carreraMaterias.carrera', 'docenteDetalle.user']);

        return view('admin.materias.show', compact('materia'));
    }

    public function edit(Materia $materia)
    {
        $carreras = Carrera::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $docentes = DocenteDetalle::query()
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('estado', true);
            })
            ->orderBy('codigo')
            ->get();

        return view('admin.materias.edit', compact('materia', 'carreras', 'docentes'));
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