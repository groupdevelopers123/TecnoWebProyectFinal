<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        $id = $this->route('materia')?->id;

        return [
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('materias', 'codigo')->ignore($id),
            ],

            'nombre' => ['required', 'string', 'max:150'],
            'carga_horaria' => ['nullable', 'integer', 'min:1'],
            'docente_detalle_id' => ['nullable', 'exists:docente_detalles,id'],
            'estado' => ['required', 'boolean'],

            'carrera_id' => ['nullable', 'exists:carreras,id'],
            'periodo_numero' => ['nullable', 'integer', 'min:1', 'max:12'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código de la materia es obligatorio.',
            'codigo.unique' => 'El código de la materia ya se encuentra registrado.',
            'nombre.required' => 'El nombre de la materia es obligatorio.',
            'docente_detalle_id.exists' => 'El docente seleccionado no es válido.',
            'carrera_id.exists' => 'La carrera seleccionada no es válida.',
            'periodo_numero.integer' => 'El período debe ser un número válido.',
        ];
    }
}