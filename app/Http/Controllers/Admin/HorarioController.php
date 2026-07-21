<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HorarioRequest;
use App\Models\Aula;
use App\Models\CarreraMateria;
use App\Models\DocenteDetalle;
use App\Models\Horario;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HorarioController extends Controller
{
    private function horarioPayload(Horario $horario): array
    {
        $horario->loadMissing([
            'carreraMateria.carrera',
            'carreraMateria.materia',
            'periodoAcademico',
            'aula',
            'docenteDetalle.user',
        ]);

        return [
            'id' => $horario->id,
            'carrera_materia_id' => $horario->carrera_materia_id,
            'periodo_academico_id' => $horario->periodo_academico_id,
            'aula_id' => $horario->aula_id,
            'docente_detalle_id' => $horario->docente_detalle_id,
            'dia' => $horario->dia,
            'turno' => $horario->turno,
            'hora_inicio' => substr((string) $horario->hora_inicio, 0, 5),
            'hora_fin' => substr((string) $horario->hora_fin, 0, 5),
            'estado' => (bool) $horario->estado,
            'aula_codigo' => $horario->aula?->codigo,
            'aula_nombre' => $horario->aula?->nombre,
            'aula_capacidad' => $horario->aula?->capacidad,
            'carrera_nombre' => $horario->carreraMateria?->carrera?->nombre,
            'materia_nombre' => $horario->carreraMateria?->materia?->nombre,
            'periodo_nombre' => $horario->periodoAcademico?->nombre,
            'docente_nombre' => $horario->docenteDetalle?->user
                ? trim($horario->docenteDetalle->user->nombres . ' ' . $horario->docenteDetalle->user->apellidos)
                : null,
            'periodo_academico' => $horario->periodoAcademico ? [
                'id' => $horario->periodoAcademico->id,
                'nombre' => $horario->periodoAcademico->nombre,
                'gestion' => $horario->periodoAcademico->gestion,
            ] : null,
        ];
    }

    private function formData(): array
    {
        return [
            'carreraMaterias' => CarreraMateria::query()
                ->with(['carrera', 'materia'])
                ->where('estado', true)
                ->whereHas('carrera', function ($query) {
                    $query->where('estado', true);
                })
                ->whereHas('materia', function ($query) {
                    $query->where('estado', true);
                })
                ->orderBy('carrera_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'carrera' => [
                            'id' => $item->carrera?->id,
                            'nombre' => $item->carrera?->nombre,
                        ],
                        'materia' => [
                            'id' => $item->materia?->id,
                            'nombre' => $item->materia?->nombre,
                        ],
                    ];
                })
                ->values(),

            'periodos' => PeriodoAcademico::query()
                ->where('estado', true)
                ->orderByDesc('gestion')
                ->orderBy('nombre')
                ->get()
                ->map(function ($periodo) {
                    return [
                        'id' => $periodo->id,
                        'nombre' => $periodo->nombre,
                    ];
                })
                ->values(),

            'aulas' => Aula::query()
                ->where('disponible', true)
                ->orderBy('codigo')
                ->get()
                ->map(function ($aula) {
                    return [
                        'id' => $aula->id,
                        'nombre' => $aula->nombre,
                        'codigo' => $aula->codigo,
                        'capacidad' => $aula->capacidad,
                    ];
                })
                ->values(),

            'docentes' => DocenteDetalle::query()
                ->with('user.role')
                ->whereHas('user', function ($query) {
                    $query->where('estado', true)
                        ->whereHas('role', function ($roleQuery) {
                            $roleQuery->where('nombre', 'docente');
                        });
                })
                ->orderBy('codigo')
                ->get()
                ->map(function ($docente) {
                    return [
                        'id' => $docente->id,
                        'codigo' => $docente->codigo,
                        'especialidad' => $docente->especialidad,
                        'nombre' => trim(($docente->user?->nombres ?? '') . ' ' . ($docente->user?->apellidos ?? '')),
                    ];
                })
                ->values(),
        ];
    }

    public function index(Request $request)
    {
        $horarios = Horario::query()
            ->with([
                'carreraMateria.carrera',
                'carreraMateria.materia',
                'periodoAcademico',
                'aula',
                'docenteDetalle.user',
            ])
            ->when($request->buscar, function ($query, $buscar) {
                $query->whereHas('aula', fn ($q) => $q->where('nombre', 'ILIKE', "%{$buscar}%")->orWhere('codigo', 'ILIKE', "%{$buscar}%"))
                    ->orWhereHas('carreraMateria.carrera', fn ($q) => $q->where('nombre', 'ILIKE', "%{$buscar}%"))
                    ->orWhereHas('carreraMateria.materia', fn ($q) => $q->where('nombre', 'ILIKE', "%{$buscar}%"))
                    ->orWhereHas('docenteDetalle.user', fn ($q) => $q->where('nombres', 'ILIKE', "%{$buscar}%")->orWhere('apellidos', 'ILIKE', "%{$buscar}%"));
            })
            ->when($request->dia, fn ($q, $dia) => $q->where('dia', $dia))
            ->when($request->turno, fn ($q, $turno) => $q->where('turno', $turno))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax() && ! $request->header('X-Inertia')) {
            return response()->json([
                'data' => $horarios->getCollection()->map(function ($horario) {
                    return [
                        'id' => $horario->id,
                        'dia' => $horario->dia,
                        'turno' => $horario->turno,
                        'aula_codigo' => $horario->aula?->codigo,
                        'aula_nombre' => $horario->aula?->nombre,
                        'carrera_nombre' => $horario->carreraMateria?->carrera?->nombre,
                        'materia_nombre' => $horario->carreraMateria?->materia?->nombre,
                        'periodo_nombre' => $horario->periodoAcademico?->nombre,
                        'docente_nombre' => $horario->docenteDetalle?->user ? trim($horario->docenteDetalle->user->nombres.' '.$horario->docenteDetalle->user->apellidos) : null,
                        'estado' => (bool) $horario->estado,
                    ];
                })->values(),
                'pagination' => [
                    'current_page' => $horarios->currentPage(),
                    'last_page' => $horarios->lastPage(),
                    'per_page' => $horarios->perPage(),
                    'total' => $horarios->total(),
                    'prev_page_url' => $horarios->previousPageUrl(),
                    'next_page_url' => $horarios->nextPageUrl(),
                ],
            ]);
        }

        return Inertia::render('admin/horarios/Index', [
            'horarios' => [
                'data' => $horarios->getCollection()->map(fn ($horario) => $this->horarioPayload($horario))->values(),
                'pagination' => [
                    'current_page' => $horarios->currentPage(),
                    'last_page' => $horarios->lastPage(),
                    'per_page' => $horarios->perPage(),
                    'total' => $horarios->total(),
                    'prev_page_url' => $horarios->previousPageUrl(),
                    'next_page_url' => $horarios->nextPageUrl(),
                ],
            ],
            'request' => [
                'buscar' => $request->buscar,
                'dia' => $request->dia,
                'turno' => $request->turno,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/horarios/Create', [
            ...$this->formData(),
            'horario' => [],
            'action' => route('admin.horarios.store'),
            'cancelUrl' => route('admin.horarios.index'),
        ]);
    }

    public function store(HorarioRequest $request)
    {
        Horario::create($request->validated());

        return redirect()->route('admin.horarios.index')->with('success', 'Horario registrado correctamente.');
    }

    public function show(Horario $horario)
    {
        return Inertia::render('admin/horarios/Show', [
            'horario' => $this->horarioPayload($horario),
        ]);
    }

    public function edit(Horario $horario)
    {
        return Inertia::render('admin/horarios/Edit', [
            'horario' => $this->horarioPayload($horario),
            ...$this->formData(),
            'action' => route('admin.horarios.update', $horario),
            'cancelUrl' => route('admin.horarios.index'),
        ]);
    }

    public function update(HorarioRequest $request, Horario $horario)
    {
        $horario->update($request->validated());

        return redirect()->route('admin.horarios.index')->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(Horario $horario)
    {
        $horario->update(['estado' => ! $horario->estado]);

        return redirect()->route('admin.horarios.index')->with('success', 'Estado del horario actualizado.');
    }
}