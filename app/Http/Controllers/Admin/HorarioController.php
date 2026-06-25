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

class HorarioController extends Controller
{
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

        return view('admin.horarios.index', compact('horarios'));
    }

    public function create()
    {
        return view('admin.horarios.create', $this->formData());
    }

    public function store(HorarioRequest $request)
    {
        Horario::create($request->validated());

        return redirect()->route('admin.horarios.index')->with('success', 'Horario registrado correctamente.');
    }

    public function show(Horario $horario)
    {
        $horario->load([
            'carreraMateria.carrera',
            'carreraMateria.materia',
            'periodoAcademico',
            'aula',
            'docenteDetalle.user',
        ]);

        return view('admin.horarios.show', compact('horario'));
    }

    public function edit(Horario $horario)
    {
        return view('admin.horarios.edit', [
            'horario' => $horario,
            ...$this->formData(),
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
                ->get(),

            'periodos' => PeriodoAcademico::query()
                ->where('estado', true)
                ->orderByDesc('gestion')
                ->orderBy('nombre')
                ->get(),

            'aulas' => Aula::query()
                ->where('disponible', true)
                ->orderBy('codigo')
                ->get(),

            'docentes' => DocenteDetalle::query()
                ->with('user.role')
                ->whereHas('user', function ($query) {
                    $query->where('estado', true)
                        ->whereHas('role', function ($roleQuery) {
                            $roleQuery->where('nombre', 'docente');
                        });
                })
                ->orderBy('codigo')
                ->get(),
        ];
    }
}