<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeguimientoAcademicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        $seguimientoId = $this->route('seguimiento')?->id;

        return [
            'inscripcion_materia_id' => [
                'required',
                'exists:inscripcion_materias,id',
                Rule::unique('seguimientos_academicos', 'inscripcion_materia_id')
                    ->ignore($seguimientoId),
            ],

            'docente_detalle_id' => [
                'required',
                'exists:docente_detalles,id',
            ],

            'nota_final' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'porcentaje_asistencia' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'observacion' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'estado_academico' => [
                'required',
                Rule::in([
                    'Cursando',
                    'Aprobado',
                    'Reprobado',
                    'Retirado',
                ]),
            ],

            'fecha_registro' => [
                'required',
                'date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'inscripcion_materia_id.required' => 'Debe seleccionar una inscripción de materia.',
            'inscripcion_materia_id.exists' => 'La inscripción de materia seleccionada no existe.',
            'inscripcion_materia_id.unique' => 'Esta materia inscrita ya tiene seguimiento académico registrado.',

            'docente_detalle_id.required' => 'Debe seleccionar un docente.',
            'docente_detalle_id.exists' => 'El docente seleccionado no existe.',

            'nota_final.numeric' => 'La nota final debe ser un número válido.',
            'nota_final.min' => 'La nota final no puede ser menor a 0.',
            'nota_final.max' => 'La nota final no puede ser mayor a 100.',

            'porcentaje_asistencia.numeric' => 'El porcentaje de asistencia debe ser un número válido.',
            'porcentaje_asistencia.min' => 'La asistencia no puede ser menor a 0.',
            'porcentaje_asistencia.max' => 'La asistencia no puede ser mayor a 100.',

            'estado_academico.required' => 'Debe seleccionar el estado académico.',
            'fecha_registro.required' => 'Debe ingresar la fecha de registro.',
            'fecha_registro.date' => 'La fecha de registro no es válida.',

            'observacion.max' => 'La observación no debe superar los 1000 caracteres.',
        ];
    }
}