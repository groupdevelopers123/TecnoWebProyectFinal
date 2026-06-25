<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PagoCuotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        return [
            'metodo_pago' => [
                'required',
                Rule::in([
                    'Efectivo',
                    'Transferencia',
                    'QR',
                ]),
            ],

            'fecha_pago' => [
                'nullable',
                'date',
            ],

            'estado_cuota' => [
                'required',
                Rule::in([
                    'pendiente',
                    'pagado',
                    'anulado',
                    'fallido',
                ]),
            ],

            'correo_solicitante' => [
                'nullable',
                'email',
                'max:150',
            ],

            'codigo_transaccion' => [
                'nullable',
                'string',
                'max:150',
            ],

            'observacion' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}