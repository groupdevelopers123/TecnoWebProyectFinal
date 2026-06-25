<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\CarreraMateria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CarreraMateriaController extends Controller
{
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