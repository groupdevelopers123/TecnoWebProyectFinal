<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        $usuario = $request->user()->load([
            'role',
            'propietarioDetalle',
            'secretariaDetalle',
            'docenteDetalle',
            'alumnoDetalle',
        ]);

        return Inertia::render('profile', [
            'user' => [
                'id' => $usuario->id,
                'ci' => $usuario->ci,
                'nombres' => $usuario->nombres,
                'apellidos' => $usuario->apellidos,
                'email' => $usuario->email,
                'telefono' => $usuario->telefono,
                'direccion' => $usuario->direccion,
                'fecha_nacimiento' => $usuario->fecha_nacimiento?->format('Y-m-d'),
                'estado' => $usuario->estado,
                'role' => [
                    'id' => $usuario->role?->id,
                    'nombre' => $usuario->role?->nombre,
                ],
                'propietario_detalle' => $usuario->propietarioDetalle ? [
                    'codigo' => $usuario->propietarioDetalle->codigo,
                    'cargo' => $usuario->propietarioDetalle->cargo,
                ] : null,
                'secretaria_detalle' => $usuario->secretariaDetalle ? [
                    'codigo' => $usuario->secretariaDetalle->codigo,
                    'turno_trabajo' => $usuario->secretariaDetalle->turno_trabajo,
                    'sueldo' => $usuario->secretariaDetalle->sueldo,
                ] : null,
                'docente_detalle' => $usuario->docenteDetalle ? [
                    'codigo' => $usuario->docenteDetalle->codigo,
                    'especialidad' => $usuario->docenteDetalle->especialidad,
                    'titulo' => $usuario->docenteDetalle->titulo,
                    'registro_profesional' => $usuario->docenteDetalle->registro_profesional,
                ] : null,
                'alumno_detalle' => $usuario->alumnoDetalle ? [
                    'codigo' => $usuario->alumnoDetalle->codigo,
                    'colegio_origen' => $usuario->alumnoDetalle->colegio_origen,
                    'anio_bachillerato' => $usuario->alumnoDetalle->anio_bachillerato,
                    'estado_academico' => $usuario->alumnoDetalle->estado_academico,
                ] : null,
            ],
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $usuario = $request->user();
        $datos = $request->validated();

        $usuario->update([
            'ci' => $datos['ci'],
            'nombres' => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
            'password' => ! empty($datos['password']) ? Hash::make($datos['password']) : $usuario->password,
        ]);

        match ($usuario->role?->nombre) {
            'propietario' => $usuario->propietarioDetalle()->updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'codigo' => $datos['codigo'],
                    'cargo' => $datos['cargo'] ?? null,
                ]
            ),
            'secretaria' => $usuario->secretariaDetalle()->updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'codigo' => $datos['codigo'],
                    'turno_trabajo' => $datos['turno_trabajo'] ?? null,
                    'sueldo' => $datos['sueldo'] ?? null,
                ]
            ),
            'docente' => $usuario->docenteDetalle()->updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'codigo' => $datos['codigo'],
                    'especialidad' => $datos['especialidad'] ?? null,
                    'titulo' => $datos['titulo'] ?? null,
                    'registro_profesional' => $datos['registro_profesional'] ?? null,
                ]
            ),
            'alumno' => $usuario->alumnoDetalle()->updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'codigo' => $datos['codigo'],
                    'colegio_origen' => $datos['colegio_origen'] ?? null,
                    'anio_bachillerato' => $datos['anio_bachillerato'] ?? null,
                    'estado_academico' => $datos['estado_academico'] ?? null,
                ]
            ),
            default => null,
        };

        return redirect()
            ->route('perfil.show')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}
