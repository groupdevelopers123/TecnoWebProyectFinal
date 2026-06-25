<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'ci' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9]+$/',
                Rule::unique('users', 'ci')->ignore($this->user()->id),
            ],
            'nombres' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\p{L}\s]+$/u',
            ],
            'apellidos' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\p{L}\s]+$/u',
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^[0-9()+\s\-]+$/',
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\p{L}0-9\s\-#,\.]+$/u',
            ],
            'fecha_nacimiento' => ['nullable', 'date'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'codigo' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[\p{L}0-9\s\-]+$/u',
            ],
            'cargo' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}\s]+$/u',
            ],
            'turno_trabajo' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[\p{L}\s]+$/u',
            ],
            'sueldo' => ['nullable', 'numeric', 'min:0'],
            'especialidad' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}\s]+$/u',
            ],
            'titulo' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}0-9\s\.\-]+$/u',
            ],
            'registro_profesional' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}0-9\s\-]+$/u',
            ],
            'colegio_origen' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^[\p{L}0-9\s\-\.,]+$/u',
            ],
            'anio_bachillerato' => [
                'nullable',
                'integer',
                'min:1950',
                'max:' . date('Y'),
            ],
            'estado_academico' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[\p{L}\s]+$/u',
            ],
        ];
    }
}
