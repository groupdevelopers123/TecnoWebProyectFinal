<?php

namespace App\Http\Requests\Admin;

use App\Models\Inscripcion;
use App\Models\OfertaAcademica;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InscripcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        $inscripcionId = $this->route('inscripcion')?->id;

        return [
            'alumno_detalle_id' => [
                'required',
                'exists:alumno_detalles,id',
            ],

            'oferta_academica_id' => [
                'required',
                'exists:ofertas_academicas,id',
                Rule::unique('inscripciones', 'oferta_academica_id')
                    ->where('alumno_detalle_id', $this->alumno_detalle_id)
                    ->ignore($inscripcionId),
            ],

            'periodo_numero' => [
                'required',
                'integer',
                'min:1',
                'max:20',
            ],

            'fecha_inscripcion' => [
                'required',
                'date',
            ],

            'observacion' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $inscripcionActual = $this->route('inscripcion');
            $oferta = OfertaAcademica::find($this->oferta_academica_id);

            if (! $oferta) {
                return;
            }

            if (! $oferta->estado) {
                $validator->errors()->add(
                    'oferta_academica_id',
                    'La oferta académica seleccionada está inactiva.'
                );
            }

            $cambioOferta = ! $inscripcionActual
                || (int) $inscripcionActual->oferta_academica_id !== (int) $this->oferta_academica_id;

            if ($cambioOferta && $oferta->cupos_disponibles <= 0) {
                $validator->errors()->add(
                    'oferta_academica_id',
                    'La oferta académica seleccionada no tiene cupos disponibles.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'alumno_detalle_id.required' => 'Debe seleccionar un alumno.',
            'alumno_detalle_id.exists' => 'El alumno seleccionado no existe.',

            'oferta_academica_id.required' => 'Debe seleccionar una oferta académica.',
            'oferta_academica_id.exists' => 'La oferta académica seleccionada no existe.',
            'oferta_academica_id.unique' => 'Este alumno ya está inscrito en la oferta académica seleccionada.',

            'periodo_numero.required' => 'Debe ingresar el número de periodo.',
            'periodo_numero.integer' => 'El periodo debe ser un número válido.',
            'periodo_numero.min' => 'El periodo debe ser mayor o igual a 1.',

            'fecha_inscripcion.required' => 'Debe ingresar la fecha de inscripción.',
            'fecha_inscripcion.date' => 'La fecha de inscripción no es válida.',

            'observacion.max' => 'La observación no debe superar los 1000 caracteres.',
        ];
    }
}