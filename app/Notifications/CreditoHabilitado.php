<?php

namespace App\Notifications;

use App\Models\Credito;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CreditoHabilitado extends Notification
{
    use Queueable;

    public function __construct(private Credito $credito)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $alumnoNombre = $this->credito->inscripcion->alumnoDetalle->user->nombre ?? 'Alumno';

        return [
            'titulo' => 'Crédito habilitado',
            'mensaje' => "Se habilitó un crédito por {$this->credito->conceptoPago->nombre}.",
            'descripcion' => "Tu crédito fue registrado y las cuotas ya están disponibles.",
            'credito_id' => $this->credito->id,
            'fecha' => now()->format('Y-m-d H:i:s'),
            'tipo' => 'credito_habilitado',
        ];
    }
}
