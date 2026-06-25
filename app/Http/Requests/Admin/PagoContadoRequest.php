<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PagoContadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        return [
            'inscripcion_id' => [
                'required',
                'exists:inscripciones,id',
            ],

            'concepto_pago_id' => [
                'required',
                'exists:concepto_pagos,id',
            ],

            'monto_pagado' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'fecha_pago' => [
                'required',
                'date',
            ],

            'metodo_pago' => [
                'required',
                Rule::in([
                    'Efectivo',
                    'Transferencia',
                    'QR',
                ]),
            ],

            'estado' => [
                'required',
                Rule::in([
                    'Pendiente',
                    'Confirmado',
                    'Anulado',
                    'Fallido',
                ]),
            ],

            'codigo_transaccion' => [
                'nullable',
                'string',
                'max:150',
            ],

            'correo_solicitante' => [
                'nullable',
                'email',
                'max:150',
            ],

            'observacion' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'inscripcion_id.required' => 'Debe seleccionar una inscripción.',
            'concepto_pago_id.required' => 'Debe seleccionar un concepto de pago.',
            'monto_pagado.required' => 'Debe ingresar el monto pagado.',
            'monto_pagado.min' => 'El monto debe ser mayor a 0.',
            'fecha_pago.required' => 'Debe ingresar la fecha de pago.',
            'metodo_pago.required' => 'Debe seleccionar el método de pago.',
            'estado.required' => 'Debe seleccionar el estado.',
            'correo_solicitante.email' => 'El correo solicitante debe ser válido.',
        ];
    }
}