<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConceptoPagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        $conceptoId = $this->route('concepto_pago')?->id;

        return [
            'nombre' => [
                'required',
                'string',
                'max:120',
                Rule::unique('concepto_pagos', 'nombre')->ignore($conceptoId),
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'estado' => [
                'required',
                Rule::in([
                    'Activo',
                    'Inactivo',
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'Debe ingresar el nombre del concepto de pago.',
            'nombre.unique' => 'Ya existe un concepto de pago con este nombre.',
            'nombre.max' => 'El nombre no debe superar los 120 caracteres.',

            'descripcion.max' => 'La descripción no debe superar los 1000 caracteres.',

            'estado.required' => 'Debe seleccionar el estado.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ];
    }
}