<?php

namespace App\Http\Middleware;

use App\Models\Bitacora;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrarAccesoMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $user = $request->user();
            $routeName = optional($request->route())->getName();

            if ($user && $routeName && str_starts_with($routeName, 'admin.')) {
                Bitacora::create([
                    'user_id' => $user->id,
                    'role' => $user->role?->nombre,
                    'tipo' => 'recurso',
                    'estado' => 'acceso',
                    'recurso' => $routeName,
                    'accion' => $request->method(),
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        } catch (\Throwable $e) {
            // Evitar que el registro de bitácora bloquee la aplicación.
        }

        return $response;
    }
}
