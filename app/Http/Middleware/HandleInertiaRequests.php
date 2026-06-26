<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * Vista Blade donde se montará Vue + Inertia.
     */
    protected $rootView = 'inertia';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
{
    $usuario = $request->user();

    return [
        ...parent::share($request),

        'auth' => [
            'user' => $usuario
                ? [
                    'id' => $usuario->id,
                    'nombres' => $usuario->nombres,
                    'apellidos' => $usuario->apellidos,
                    'email' => $usuario->email,
                    'role' => $usuario->role
                        ? [
                            'id' => $usuario->role->id,
                            'nombre' => $usuario->role->nombre,
                        ]
                        : null,
                ]
                : null,
        ],

        'flash' => [
            'success' => fn () =>
                $request->session()->get('success'),

            'error' => fn () =>
                $request->session()->get('error'),

            'pago_generado' => fn () =>
                $request->session()->get('pago_generado'),
        ],

        'cantidad_mensajes' => 0,
        'cantidad_notificaciones' => $usuario ? $usuario->unreadNotifications()->count() : 0,
        'notificaciones' => $usuario
            ? $usuario->unreadNotifications()
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($notification) => [
                    'id' => $notification->id,
                    'titulo' => $notification->data['titulo'] ?? null,
                    'mensaje' => $notification->data['mensaje'] ?? null,
                    'descripcion' => $notification->data['descripcion'] ?? null,
                    'tipo' => $notification->data['tipo'] ?? null,
                    'materia_id' => $notification->data['materia_id'] ?? null,
                    'materia_nombre' => $notification->data['materia_nombre'] ?? null,
                    'inscripcion_materia_id' => $notification->data['inscripcion_materia_id'] ?? null,
                    'seguimiento_id' => $notification->data['seguimiento_id'] ?? null,
                    'fecha' => $notification->data['fecha'] ?? optional($notification->created_at)->format('Y-m-d H:i:s'),
                    'read_at' => $notification->read_at,
                ])
            : [],
        'notificaciones_count' => $usuario ? $usuario->unreadNotifications()->count() : 0,
        'avisos' => $usuario
            ? $usuario->unreadNotifications()
                ->latest()
                ->take(3)
                ->get()
                ->map(fn ($notification) => [
                    'id' => $notification->id,
                    'titulo' => $notification->data['titulo'] ?? null,
                    'mensaje' => $notification->data['mensaje'] ?? null,
                    'descripcion' => $notification->data['descripcion'] ?? null,
                    'tipo' => $notification->data['tipo'] ?? null,
                    'fecha' => $notification->data['fecha'] ?? optional($notification->created_at)->format('Y-m-d H:i:s'),
                ])
            : [],
    ];
}
}