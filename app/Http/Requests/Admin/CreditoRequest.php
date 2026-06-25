<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreditoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        return [
            'inscripcion_id' => ['required', 'exists:inscripciones,id'],
            'concepto_pago_id' => ['required', 'exists:concepto_pagos,id'],

            'tipo_pago' => ['required', 'string', 'max:50'],

            'monto_total' => ['required', 'numeric', 'min:0.01'],
            'saldo_pendiente' => ['nullable', 'numeric', 'min:0', 'lte:monto_total'],

            'cantidad_cuotas' => ['required', 'integer', 'min:1', 'max:120'],

            'fecha_otorgamiento' => ['required', 'date'],
            'fecha_vencimiento' => ['required', 'date', 'after:fecha_otorgamiento'],

            'estado' => [
                'required',
                Rule::in([
                    'pendiente',
                    'activo',
                    'pagado',
                    'anulado',
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'inscripcion_id.required' => 'Debe seleccionar una inscripción.',
            'concepto_pago_id.required' => 'Debe seleccionar un concepto de pago.',
            'monto_total.required' => 'Debe ingresar el monto total.',
            'monto_total.min' => 'El monto total debe ser mayor a 0.',
            'cantidad_cuotas.required' => 'Debe ingresar la cantidad de cuotas.',
            'cantidad_cuotas.min' => 'Debe existir al menos una cuota.',
            'fecha_otorgamiento.required' => 'Debe ingresar la fecha de otorgamiento.',
            'fecha_vencimiento.required' => 'Debe ingresar la fecha de vencimiento.',
            'fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a la fecha de otorgamiento.',
        ];
    }
}