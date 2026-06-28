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
use Illuminate\Database\QueryException;
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
            'nombres' => ['required', 'string', 'max:100', 'regex:/^[\p{L} ]+$/u'],
            'apellidos' => ['required', 'string', 'max:100', 'regex:/^[\p{L} ]+$/u'],

            'ci' => ['required', 'string', 'max:20', 'regex:/^[0-9\-]+$/', 'unique:users,ci'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],

            'telefono' => ['nullable', 'string', 'max:30', 'regex:/^[0-9 +\-]+$/'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],

            'colegio_origen' => ['nullable', 'string', 'max:150'],
            'anio_bachillerato' => ['nullable', 'integer', 'min:1950', 'max:' . now()->year],
            'estado_academico' => ['nullable', 'string', 'in:nuevo,bachiller,universitario,profesional'],

            'oferta_academica_id' => ['nullable', 'exists:oferta_academicas,id'],

            'password' => ['required', 'confirmed', Rules\Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'nombres.required' => 'Debe ingresar sus nombres.',
            'nombres.regex' => 'Los nombres solo pueden contener letras y espacios.',
            'nombres.max' => 'Los nombres no pueden tener más de 100 caracteres.',
            'apellidos.required' => 'Debe ingresar sus apellidos.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
            'apellidos.max' => 'Los apellidos no pueden tener más de 100 caracteres.',
            'ci.required' => 'Debe ingresar su Cédula de identidad.',
            'ci.regex' => 'La cédula solo puede contener números y guiones.',
            'ci.unique' => 'Este CI ya está registrado.',
            'ci.max' => 'La cédula no puede tener más de 20 caracteres.',
            'email.required' => 'Debe ingresar su correo electrónico.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'email.max' => 'El correo no puede tener más de 150 caracteres.',
            'telefono.regex' => 'El teléfono solo puede contener números, espacios, guiones o +.',
            'telefono.max' => 'El teléfono no puede tener más de 30 caracteres.',
            'direccion.max' => 'La dirección no puede tener más de 200 caracteres.',
            'fecha_nacimiento.date' => 'Debe ingresar una fecha de nacimiento válida.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'colegio_origen.max' => 'El nombre del colegio no puede tener más de 150 caracteres.',
            'anio_bachillerato.integer' => 'El año de bachillerato debe ser un número válido.',
            'anio_bachillerato.min' => 'El año de bachillerato no puede ser anterior a 1950.',
            'anio_bachillerato.max' => 'El año de bachillerato no puede ser posterior a ' . now()->year . '.',
            'estado_academico.in' => 'Debe seleccionar un estado académico válido.',
            'password.required' => 'Debe ingresar una contraseña.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixedCase' => 'La contraseña debe incluir mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
            'password.symbols' => 'La contraseña debe incluir al menos un símbolo.',
            'oferta_academica_id.exists' => 'La oferta académica seleccionada no existe.',
        ]);
        try {
            $user = DB::transaction(function () use ($request) {
                $rolAlumno = Role::where('nombre', 'alumno')->first();

                if (! $rolAlumno) {
                    $rolAlumno = Role::create([
                        'nombre' => 'alumno',
                        'descripcion' => 'Usuario alumno registrado desde la página pública',
                        'estado' => true,
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
                    'estado' => $this->normalizarEstadoBoolean(true),
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
        } catch (QueryException $exception) {
            if (($exception->errorInfo[0] ?? '') === '23505') {
                return back()
                    ->withInput()
                    ->with('error', 'No se pudo completar el registro: el código de alumno generado ya existe. Intenta de nuevo.');
            }

            throw $exception;
        }

        event(new Registered($user));

        Auth::login($user);

        if (session('oferta_academica_id')) {
            return redirect()
                ->route('public.ofertas.index')
                ->with('info', 'Registro completado correctamente. Ahora puedes continuar tu inscripción a la oferta académica seleccionada.');
        }

        return redirect()
            ->route('alumno.home')
            ->with('info', 'Registro completado correctamente.');
    }

    private function generarCodigoAlumno(User $user): string
    {
        $anio = now()->format('Y');

        $numero = str_pad((string) $user->id, 5, '0', STR_PAD_LEFT);

        return 'ALU-' . $anio . '-' . $numero;
    }

    private function normalizarEstadoBoolean(bool $estado): bool
    {
        return $estado;
    }
}