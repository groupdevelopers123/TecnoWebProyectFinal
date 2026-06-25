<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAulaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        $aulaId = $this->route('aula')->id;

        return [
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('aulas', 'codigo')->ignore($aulaId),
            ],
            'nombre' => ['required', 'string', 'max:100'],
            'ubicacion' => ['nullable', 'string', 'max:150'],
            'piso' => ['nullable', 'string', 'max:30'],
            'capacidad' => ['required', 'integer', 'min:1'],
            'largo' => ['nullable', 'numeric', 'min:0'],
            'ancho' => ['nullable', 'numeric', 'min:0'],
            'disponible' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código del aula es obligatorio.',
            'codigo.unique' => 'El código del aula ya se encuentra registrado.',
            'nombre.required' => 'El nombre del aula es obligatorio.',
            'capacidad.required' => 'La capacidad del aula es obligatoria.',
            'capacidad.min' => 'La capacidad debe ser mayor a cero.',
        ];
    }
}