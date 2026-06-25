<?php

namespace App\Http\Requests\Admin;

use App\Models\Horario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdministrativo();
    }

    public function rules(): array
    {
        return [
            'carrera_materia_id' => ['required', 'exists:carrera_materia,id'],
            'periodo_academico_id' => ['required', 'exists:periodos_academicos,id'],
            'aula_id' => ['required', 'exists:aulas,id'],
            'docente_detalle_id' => ['required', 'exists:docente_detalles,id'],
            'dia' => ['required', Rule::in(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'])],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'turno' => ['required', Rule::in(['Mañana', 'Tarde', 'Noche'])],
            'estado' => ['required', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $horarioId = $this->route('horario')?->id;

            $cruceAula = Horario::query()
                ->where('aula_id', $this->aula_id)
                ->where('periodo_academico_id', $this->periodo_academico_id)
                ->where('dia', $this->dia)
                ->when($horarioId, fn ($q) => $q->where('id', '!=', $horarioId))
                ->where('estado', true)
                ->where(function ($query) {
                    $query->where('hora_inicio', '<', $this->hora_fin)
                        ->where('hora_fin', '>', $this->hora_inicio);
                })
                ->exists();

            if ($cruceAula) {
                $validator->errors()->add('aula_id', 'El aula ya está ocupada en ese día y rango de horas.');
            }

            $cruceDocente = Horario::query()
                ->where('docente_detalle_id', $this->docente_detalle_id)
                ->where('periodo_academico_id', $this->periodo_academico_id)
                ->where('dia', $this->dia)
                ->when($horarioId, fn ($q) => $q->where('id', '!=', $horarioId))
                ->where('estado', true)
                ->where(function ($query) {
                    $query->where('hora_inicio', '<', $this->hora_fin)
                        ->where('hora_fin', '>', $this->hora_inicio);
                })
                ->exists();

            if ($cruceDocente) {
                $validator->errors()->add('docente_detalle_id', 'El docente ya tiene una clase asignada en ese día y rango de horas.');
            }
        });
    }
}