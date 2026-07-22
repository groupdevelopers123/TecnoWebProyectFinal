<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $usuario = Auth::user()->loadMissing('role');

            return redirect($this->urlPorRol(optional($usuario->role)->nombre ?? ''));
        }

        return Inertia::render('auth/Login');
    }

    public function login(Request $request): RedirectResponse|Response
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $recordar = $request->boolean('remember');

        if (!Auth::attempt($credenciales, $recordar)) {
            Bitacora::create([
                'tipo' => 'login',
                'estado' => 'fallado',
                'recurso' => 'login',
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'email' => 'Las credenciales ingresadas no son correctas. Verifique su correo y contraseña.',
            ]);
        }

        $request->session()->regenerate();

        $usuario = Auth::user()->loadMissing('role');

        if (!$usuario->estado) {
            $this->cerrarSesion($request);

            throw ValidationException::withMessages([
                'email' => 'El usuario se encuentra desactivado.',
            ]);
        }

        if (!$usuario->role) {
            $this->cerrarSesion($request);

            throw ValidationException::withMessages([
                'email' => 'El usuario no tiene un rol asignado.',
            ]);
        }

        $ruta = $this->urlPorRol($usuario->role->nombre);

        if ($ruta === '/') {
            Bitacora::create([
                'user_id' => $usuario->id,
                'tipo' => 'login',
                'estado' => 'fallado',
                'recurso' => 'login',
                'email' => $usuario->email,
                'role' => $usuario->role?->nombre,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $this->cerrarSesion($request);

            throw ValidationException::withMessages([
                'email' => 'El usuario no tiene un rol autorizado.',
            ]);
        }

        Bitacora::create([
            'user_id' => $usuario->id,
            'tipo' => 'login',
            'estado' => 'aceptado',
            'recurso' => 'login',
            'email' => $usuario->email,
            'role' => $usuario->role?->nombre,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $rutaDestino = $this->urlPorRol(optional($usuario->role)->nombre ?? '');

        if ($request->header('X-Inertia')) {
            return Inertia::location($rutaDestino);
        }

        return redirect($rutaDestino);
    }

    private function urlPorRol(string $rol): string
    {
        $rol = strtolower(trim($rol));

        return match ($rol) {
            'propietario', 'secretaria' => '/admin/dashboard',
            'docente' => '/docente/inicio',
            'alumno' => '/alumno/inicio',
            default => '/',
        };
    }

    private function cerrarSesion(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function logout(Request $request)
    {
        $this->cerrarSesion($request);

        return redirect()->route('welcome');
    }
}