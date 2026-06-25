<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario')->id;

        return [
            'role_id' => ['required', 'exists:roles,id'],
            'ci' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'ci')->ignore($usuarioId),
            ],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($usuarioId),
            ],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'estado' => ['required', 'boolean'],

            'codigo' => ['required', 'string', 'max:50'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'turno_trabajo' => ['nullable', 'string', 'max:50'],
            'sueldo' => ['nullable', 'numeric', 'min:0'],
            'especialidad' => ['nullable', 'string', 'max:100'],
            'titulo' => ['nullable', 'string', 'max:100'],
            'registro_profesional' => ['nullable', 'string', 'max:100'],
            'colegio_origen' => ['nullable', 'string', 'max:150'],
            'anio_bachillerato' => ['nullable', 'integer', 'min:1950', 'max:' . date('Y')],
            'estado_academico' => ['nullable', 'string', 'max:50'],
        ];
    }
}