<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarreraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        $id = $this->route('carrera')?->id;

        return [
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('carreras', 'codigo')->ignore($id),
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'duracion' => ['nullable', 'integer', 'min:1'],
            'regimen_academico' => ['nullable', Rule::in(['Semestral', 'Anual', 'Modular'])],
            'estado' => ['required', 'boolean'],
        ];
    }
}