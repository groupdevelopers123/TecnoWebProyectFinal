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
        'cantidad_notificaciones' => 0,
    ];
}
}