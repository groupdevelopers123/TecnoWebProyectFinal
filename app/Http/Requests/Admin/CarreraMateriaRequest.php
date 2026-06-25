<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarreraMateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        $id = $this->route('carrera_materia')?->id;

        return [
            'carrera_id' => ['required', 'exists:carreras,id'],
            'materia_id' => [
                'required',
                'exists:materias,id',
                Rule::unique('carrera_materia', 'materia_id')
                    ->where('carrera_id', $this->carrera_id)
                    ->ignore($id),
            ],
            'periodo_numero' => ['nullable', 'integer', 'min:1', 'max:12'],
        ];
    }

    public function messages(): array
    {
        return [
            'materia_id.unique' => 'Esta materia ya está asignada a la carrera seleccionada.',
        ];
    }
}