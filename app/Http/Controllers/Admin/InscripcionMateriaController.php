<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarreraMateria;
use App\Models\Inscripcion;
use App\Models\InscripcionMateria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InscripcionMateriaController extends Controller
{
    public function index(Inscripcion $inscripcion)
    {
        $inscripcion->load([
            'alumnoDetalle.user',
            'ofertaAcademica.carrera',
            'inscripcionMaterias.carreraMateria.materia',
        ]);

        return view('admin.inscripcion-materias.index', compact('inscripcion'));
    }

    public function create(Inscripcion $inscripcion)
    {
        $inscripcion->load([
            'alumnoDetalle.user',
            'ofertaAcademica.carrera',
            'inscripcionMaterias',
        ]);

        $materiasInscritasIds = $inscripcion->inscripcionMaterias
            ->pluck('carrera_materia_id');

        $carreraMaterias = CarreraMateria::query()
            ->with(['materia', 'carrera'])
            ->where('estado', true)
            ->where('carrera_id', $inscripcion->ofertaAcademica->carrera_id)
            ->whereNotIn('id', $materiasInscritasIds)
            ->orderBy('periodo_numero')
            ->get();

        $inscripcionMateria = new InscripcionMateria();

        return view('admin.inscripcion-materias.create', compact(
            'inscripcion',
            'carreraMaterias',
            'inscripcionMateria'
        ));
    }

    public function store(Request $request, Inscripcion $inscripcion)
    {
        $data = $request->validate([
            'carrera_materia_id' => [
                'required',
                'exists:carrera_materia,id',
                Rule::unique('inscripcion_materias', 'carrera_materia_id')
                    ->where('inscripcion_id', $inscripcion->id),
            ],
            'estado' => [
                'required',
                Rule::in([
                    'Cursando',
                    'Aprobada',
                    'Reprobada',
                    'Retirada',
                ]),
            ],
        ], [
            'carrera_materia_id.required' => 'Debe seleccionar una materia.',
            'carrera_materia_id.exists' => 'La materia seleccionada no existe.',
            'carrera_materia_id.unique' => 'Esta materia ya está registrada en la inscripción.',
            'estado.required' => 'Debe seleccionar el estado.',
        ]);

        $carreraMateria = CarreraMateria::findOrFail($data['carrera_materia_id']);

        if ($carreraMateria->carrera_id !== $inscripcion->ofertaAcademica->carrera_id) {
            return back()
                ->withErrors([
                    'carrera_materia_id' => 'La materia seleccionada no pertenece a la carrera de esta inscripción.',
                ])
                ->withInput();
        }

        InscripcionMateria::create([
            'inscripcion_id' => $inscripcion->id,
            'carrera_materia_id' => $data['carrera_materia_id'],
            'estado' => $data['estado'],
        ]);

        return redirect()
            ->route('admin.inscripciones.materias.index', $inscripcion)
            ->with('success', 'Materia inscrita correctamente.');
    }

    public function edit(Inscripcion $inscripcion, InscripcionMateria $inscripcionMateria)
    {
        if ($inscripcionMateria->inscripcion_id !== $inscripcion->id) {
            abort(404);
        }

        $inscripcion->load([
            'alumnoDetalle.user',
            'ofertaAcademica.carrera',
        ]);

        $inscripcionMateria->load('carreraMateria.materia');

        $carreraMaterias = CarreraMateria::query()
            ->with(['materia', 'carrera'])
            ->where('id', $inscripcionMateria->carrera_materia_id)
            ->get();

        return view('admin.inscripcion-materias.edit', compact(
            'inscripcion',
            'inscripcionMateria',
            'carreraMaterias'
        ));
    }

    public function update(Request $request, Inscripcion $inscripcion, InscripcionMateria $inscripcionMateria)
    {
        if ($inscripcionMateria->inscripcion_id !== $inscripcion->id) {
            abort(404);
        }

        $data = $request->validate([
            'estado' => [
                'required',
                Rule::in([
                    'Cursando',
                    'Aprobada',
                    'Reprobada',
                    'Retirada',
                ]),
            ],
        ], [
            'estado.required' => 'Debe seleccionar el estado.',
        ]);

        $inscripcionMateria->update([
            'estado' => $data['estado'],
        ]);

        return redirect()
            ->route('admin.inscripciones.materias.index', $inscripcion)
            ->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy(Inscripcion $inscripcion, InscripcionMateria $inscripcionMateria)
    {
        if ($inscripcionMateria->inscripcion_id !== $inscripcion->id) {
            abort(404);
        }

        $inscripcionMateria->delete();

        return redirect()
            ->route('admin.inscripciones.materias.index', $inscripcion)
            ->with('success', 'Materia retirada correctamente de la inscripción.');
    }
}