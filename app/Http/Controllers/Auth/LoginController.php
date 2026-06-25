<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
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
                'email' => 'Las credenciales ingresadas no son correctas.',
            ]);
        }

        $request->session()->regenerate();

        $usuario = Auth::user();

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

        $ruta = $this->rutaPorRol($usuario->role->nombre);

        if (!$ruta) {
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

        return redirect()->route($ruta);
    }

    private function rutaPorRol(string $rol): ?string
    {
        $rol = strtolower(trim($rol));

        return match ($rol) {
            'propietario', 'secretaria' => 'admin.dashboard',
            'docente' => 'docente.home',
            'alumno' => 'alumno.home',
            default => null,
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