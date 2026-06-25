<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        return [
            'role_id' => ['required', 'exists:roles,id'],
            'ci' => ['required', 'string', 'max:20', 'unique:users,ci'],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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

    public function messages(): array
    {
        return [
            'role_id.required' => 'Debe seleccionar un rol.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'ci.required' => 'El CI es obligatorio.',
            'ci.unique' => 'El CI ya se encuentra registrado.',
            'nombres.required' => 'Los nombres son obligatorios.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.unique' => 'El correo electrónico ya se encuentra registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'codigo.required' => 'El código del usuario es obligatorio.',
        ];
    }
}