<?php

namespace App\Notifications;

use App\Models\SeguimientoAcademico;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SeguimientoAcademicoActualizado extends Notification
{
    use Queueable;

    public function __construct(private SeguimientoAcademico $seguimiento)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $materia = $this->seguimiento->inscripcionMateria->carreraMateria->materia;

        return [
            'titulo' => 'Seguimiento académico actualizado',
            'mensaje' => "El seguimiento de la materia {$materia->nombre} fue actualizado.",
            'descripcion' => "Tu docente actualizó la información de seguimiento para {$materia->nombre}.",
            'materia_id' => $materia->id,
            'materia_nombre' => $materia->nombre,
            'inscripcion_materia_id' => $this->seguimiento->inscripcion_materia_id,
            'seguimiento_id' => $this->seguimiento->id,
            'fecha' => now()->format('Y-m-d H:i:s'),
            'tipo' => 'seguimiento_actualizado',
        ];
    }
}
