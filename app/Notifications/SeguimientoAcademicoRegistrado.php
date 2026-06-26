<?php

namespace App\Notifications;

use App\Models\SeguimientoAcademico;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SeguimientoAcademicoRegistrado extends Notification
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
        $alumno = $this->seguimiento->inscripcionMateria->inscripcion->alumnoDetalle->user;
        $materia = $this->seguimiento->inscripcionMateria->carreraMateria->materia;

        return [
            'titulo' => 'Nuevo seguimiento académico',
            'mensaje' => "Se registró un seguimiento para la materia {$materia->nombre}.",
            'descripcion' => "Tu docente ha registrado un seguimiento académico para {$materia->nombre}.",
            'materia_id' => $materia->id,
            'materia_nombre' => $materia->nombre,
            'inscripcion_materia_id' => $this->seguimiento->inscripcion_materia_id,
            'seguimiento_id' => $this->seguimiento->id,
            'fecha' => now()->format('Y-m-d H:i:s'),
            'tipo' => 'seguimiento_registrado',
        ];
    }
}
