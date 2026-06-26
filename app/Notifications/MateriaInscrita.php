<?php

namespace App\Notifications;

use App\Models\InscripcionMateria;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MateriaInscrita extends Notification
{
    use Queueable;

    public function __construct(private InscripcionMateria $inscripcionMateria)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $materia = $this->inscripcionMateria->carreraMateria->materia;

        return [
            'titulo' => 'Materia inscrita',
            'mensaje' => "Se inscribió la materia {$materia->nombre} en tu inscripción.",
            'descripcion' => "La materia {$materia->nombre} fue agregada a tu plan académico.",
            'materia_id' => $materia->id,
            'materia_nombre' => $materia->nombre,
            'inscripcion_materia_id' => $this->inscripcionMateria->id,
            'fecha' => now()->format('Y-m-d H:i:s'),
            'tipo' => 'materia_inscrita',
        ];
    }
}
