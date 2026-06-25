<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AlumnoDetalle;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.register', [
            'ofertaAcademicaId' => $request->query('oferta_academica_id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],

            'ci' => ['required', 'string', 'max:20', 'unique:users,ci'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],

            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'fecha_nacimiento' => ['nullable', 'date'],

            'colegio_origen' => ['nullable', 'string', 'max:150'],
            'anio_bachillerato' => ['nullable', 'integer', 'min:1950', 'max:' . now()->year],
            'estado_academico' => ['nullable', 'string', 'max:50'],

            'oferta_academica_id' => ['nullable', 'exists:oferta_academicas,id'],

            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'nombres.required' => 'Debe ingresar sus nombres.',
            'apellidos.required' => 'Debe ingresar sus apellidos.',
            'ci.required' => 'Debe ingresar su CI.',
            'ci.unique' => 'Este CI ya está registrado.',
            'email.required' => 'Debe ingresar su correo electrónico.',
            'email.email' => 'Debe ingresar un correo válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.required' => 'Debe ingresar una contraseña.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'oferta_academica_id.exists' => 'La oferta académica seleccionada no existe.',
        ]);

        $user = DB::transaction(function () use ($request) {
            $rolAlumno = Role::where('nombre', 'alumno')->first();

            if (! $rolAlumno) {
                $rolAlumno = Role::create([
                    'nombre' => 'alumno',
                    'descripcion' => 'Usuario alumno registrado desde la página pública',
                    'estado' => 'Activo',
                ]);
            }

            $user = User::create([
                'role_id' => $rolAlumno->id,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'ci' => $request->ci,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'password' => Hash::make($request->password),
                'estado' => 'activo',
            ]);

            AlumnoDetalle::create([
                'user_id' => $user->id,
                'codigo' => $this->generarCodigoAlumno($user),
                'colegio_origen' => $request->colegio_origen,
                'anio_bachillerato' => $request->anio_bachillerato,
                'estado_academico' => $request->estado_academico ?: 'nuevo',
            ]);

            if ($request->filled('oferta_academica_id')) {
                session([
                    'oferta_academica_id' => $request->oferta_academica_id,
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        if (session('oferta_academica_id')) {
            return redirect()
                ->route('public.ofertas.index')
                ->with('info', 'Registro completado correctamente. Ahora puedes continuar tu inscripción a la oferta académica seleccionada.');
        }

        return redirect()
            ->route('public.inicio')
            ->with('info', 'Registro completado correctamente.');
    }

    private function generarCodigoAlumno(User $user): string
    {
        $anio = now()->format('Y');

        $numero = str_pad((string) $user->id, 5, '0', STR_PAD_LEFT);

        return 'ALU-' . $anio . '-' . $numero;
    }
}