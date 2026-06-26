<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AulaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo' => mb_strtoupper(
                trim((string) $this->input('codigo'))
            ),

            'nombre' => preg_replace(
                '/\s+/u',
                ' ',
                trim((string) $this->input('nombre'))
            ),

            'ubicacion' => preg_replace(
                '/\s+/u',
                ' ',
                trim((string) $this->input('ubicacion'))
            ),

            'piso' => trim((string) $this->input('piso')),

            'largo' => str_replace(
                ',',
                '.',
                trim((string) $this->input('largo'))
            ),

            'ancho' => str_replace(
                ',',
                '.',
                trim((string) $this->input('ancho'))
            ),
        ]);
    }

    public function rules(): array
    {
        $aula = $this->route('aula');

        $aulaId = is_object($aula)
            ? $aula->getKey()
            : $aula;

        return [
            'codigo' => [
                'bail',
                'required',
                'string',
                'min:3',
                'max:20',

                /*
                 * Debe contener al menos una letra.
                 * Solo permite letras mayúsculas, números y guiones.
                 * No permite espacios, guiones dobles ni guiones al inicio/final.
                 */
                'regex:/^(?=.*[A-Z])[A-Z0-9]+(?:-[A-Z0-9]+)*$/',

                Rule::unique('aulas', 'codigo')->ignore($aulaId),
            ],

            'nombre' => [
                'bail',
                'required',
                'string',
                'min:3',
                'max:80',

                /*
                 * Permite:
                 * Letras con y sin tilde
                 * Números
                 * Espacios
                 *
                 * Exige al menos una letra.
                 */
                'regex:/^(?=.*\p{L})[\p{L}\p{N} ]+$/u',
            ],

            'ubicacion' => [
                'bail',
                'required',
                'string',
                'min:3',
                'max:80',
                'regex:/^(?=.*\p{L})[\p{L}\p{N} ]+$/u',
            ],

            'piso' => [
                'bail',
                'required',
                Rule::in([
                    'Planta baja',
                    'Primer piso',
                    'Segundo piso',
                    'Tercer piso',
                    'Cuarto piso',
                    'Quinto piso',
                ]),
            ],

            'capacidad' => [
                'bail',
                'required',
                'integer',
                'between:1,500',
            ],

            'disponible' => [
                'bail',
                'required',
                Rule::in([0, 1, '0', '1']),
            ],

            'largo' => [
                'bail',
                'required',
                'numeric',
                'decimal:0,2',
                'between:1,100',
            ],

            'ancho' => [
                'bail',
                'required',
                'numeric',
                'decimal:0,2',
                'between:1,100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código del aula es obligatorio.',
            'codigo.min' => 'El código debe tener al menos 3 caracteres.',
            'codigo.max' => 'El código no puede superar los 20 caracteres.',
            'codigo.regex' => 'El código solo puede contener letras mayúsculas, números y guiones. Ejemplo: AULA-101.',
            'codigo.unique' => 'Ya existe un aula registrada con este código.',

            'nombre.required' => 'El nombre del aula es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre no puede superar los 80 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras, números y espacios.',

            'ubicacion.required' => 'La ubicación del aula es obligatoria.',
            'ubicacion.min' => 'La ubicación debe tener al menos 3 caracteres.',
            'ubicacion.max' => 'La ubicación no puede superar los 80 caracteres.',
            'ubicacion.regex' => 'La ubicación solo puede contener letras, números y espacios.',

            'piso.required' => 'Debe seleccionar un piso.',
            'piso.in' => 'El piso seleccionado no es válido.',

            'capacidad.required' => 'La capacidad del aula es obligatoria.',
            'capacidad.integer' => 'La capacidad debe ser un número entero.',
            'capacidad.between' => 'La capacidad debe estar entre 1 y 500 personas.',

            'disponible.required' => 'Debe seleccionar la disponibilidad del aula.',
            'disponible.in' => 'La disponibilidad seleccionada no es válida.',

            'largo.required' => 'El largo del aula es obligatorio.',
            'largo.numeric' => 'El largo debe ser un número válido.',
            'largo.decimal' => 'El largo puede tener como máximo 2 decimales.',
            'largo.between' => 'El largo debe estar entre 1 y 100 metros.',

            'ancho.required' => 'El ancho del aula es obligatorio.',
            'ancho.numeric' => 'El ancho debe ser un número válido.',
            'ancho.decimal' => 'El ancho puede tener como máximo 2 decimales.',
            'ancho.between' => 'El ancho debe estar entre 1 y 100 metros.',
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'nombre' => 'nombre',
            'ubicacion' => 'ubicación',
            'piso' => 'piso',
            'capacidad' => 'capacidad',
            'disponible' => 'disponibilidad',
            'largo' => 'largo',
            'ancho' => 'ancho',
        ];
    }
}