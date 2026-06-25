<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PeriodoAcademicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'gestion' => ['required', 'integer', 'min:2020', 'max:2100'],
            'tipo_periodo' => ['nullable', Rule::in(['Semestral', 'Anual', 'Modular'])],
            'numero_periodo' => ['nullable', 'integer', 'min:1', 'max:4'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['required', 'boolean'],
        ];
    }
}