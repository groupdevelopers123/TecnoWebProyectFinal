<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OfertaAcademicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        return [
            'carrera_id' => [
                'required',
                'exists:carreras,id',
            ],

            'periodo_academico_id' => [
                'required',
                'exists:periodos_academicos,id',
            ],

            'docente_detalle_id' => [
                'nullable',
                'exists:docente_detalles,id',
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'cantidad_cupos' => [
                'required',
                'integer',
                'min:1',
            ],

            'cupos_disponibles' => [
                'required',
                'integer',
                'min:0',
                'lte:cantidad_cupos',
            ],

            'fecha_inicio' => [
                'required',
                'date',
            ],

            'fecha_fin' => [
                'required',
                'date',
                'after_or_equal:fecha_inicio',
            ],

            'precio_matricula' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'precio_mensualidad' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'precio_carrera_completa' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'estado' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'carrera_id.required' => 'Debe seleccionar una carrera.',
            'carrera_id.exists' => 'La carrera seleccionada no es válida.',

            'periodo_academico_id.required' =>
                'Debe seleccionar un periodo académico.',

            'periodo_academico_id.exists' =>
                'El periodo académico seleccionado no es válido.',

            'docente_detalle_id.exists' =>
                'El docente seleccionado no es válido.',

            'nombre.required' =>
                'Debe ingresar el nombre de la oferta académica.',

            'nombre.string' =>
                'El nombre de la oferta académica no es válido.',

            'nombre.max' =>
                'El nombre de la oferta académica no puede superar los 150 caracteres.',

            'cantidad_cupos.required' =>
                'Debe ingresar la cantidad de cupos.',

            'cantidad_cupos.integer' =>
                'La cantidad de cupos debe ser un número entero.',

            'cantidad_cupos.min' =>
                'La cantidad de cupos debe ser mayor a 0.',

            'cupos_disponibles.required' =>
                'Debe ingresar los cupos disponibles.',

            'cupos_disponibles.integer' =>
                'Los cupos disponibles deben ser un número entero.',

            'cupos_disponibles.min' =>
                'Los cupos disponibles no pueden ser negativos.',

            'cupos_disponibles.lte' =>
                'Los cupos disponibles no pueden ser mayores a la cantidad total de cupos.',

            'fecha_inicio.required' =>
                'Debe ingresar la fecha de inicio.',

            'fecha_inicio.date' =>
                'La fecha de inicio no es válida.',

            'fecha_fin.required' =>
                'Debe ingresar la fecha de fin.',

            'fecha_fin.date' =>
                'La fecha de fin no es válida.',

            'fecha_fin.after_or_equal' =>
                'La fecha de fin debe ser igual o posterior a la fecha de inicio.',

            'precio_matricula.required' =>
                'Debe ingresar el precio de la matrícula.',

            'precio_matricula.numeric' =>
                'El precio de la matrícula debe ser un valor numérico.',

            'precio_matricula.min' =>
                'El precio de la matrícula no puede ser negativo.',

            'precio_matricula.max' =>
                'El precio de la matrícula supera el máximo permitido.',

            'precio_mensualidad.required' =>
                'Debe ingresar el precio de la mensualidad.',

            'precio_mensualidad.numeric' =>
                'El precio de la mensualidad debe ser un valor numérico.',

            'precio_mensualidad.min' =>
                'El precio de la mensualidad no puede ser negativo.',

            'precio_mensualidad.max' =>
                'El precio de la mensualidad supera el máximo permitido.',

            'precio_carrera_completa.required' =>
                'Debe ingresar el precio de la carrera completa.',

            'precio_carrera_completa.numeric' =>
                'El precio de la carrera completa debe ser un valor numérico.',

            'precio_carrera_completa.min' =>
                'El precio de la carrera completa no puede ser negativo.',

            'precio_carrera_completa.max' =>
                'El precio de la carrera completa supera el máximo permitido.',

            'estado.required' =>
                'Debe seleccionar el estado.',

            'estado.boolean' =>
                'El estado seleccionado no es válido.',
        ];
    }
}