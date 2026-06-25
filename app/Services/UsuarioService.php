<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioService
{
    public function crear(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $usuario = User::create([
                'role_id' => $data['role_id'],
                'ci' => $data['ci'],
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'email' => $data['email'],
                'telefono' => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
                'password' => Hash::make($data['password']),
                'estado' => $data['estado'],
            ]);

            $this->guardarDetallePorRol($usuario, $data);

            return $usuario;
        });
    }

    public function actualizar(User $usuario, array $data): User
    {
        return DB::transaction(function () use ($usuario, $data) {
            $usuario->update([
                'role_id' => $data['role_id'],
                'ci' => $data['ci'],
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'email' => $data['email'],
                'telefono' => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
                'estado' => $data['estado'],
            ]);

            if (! empty($data['password'])) {
                $usuario->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            $this->limpiarDetallesSiCambioRol($usuario);
            $this->guardarDetallePorRol($usuario, $data);

            return $usuario;
        });
    }

    public function cambiarEstado(User $usuario): void
    {
        $usuario->update([
            'estado' => ! $usuario->estado,
        ]);
    }

    private function guardarDetallePorRol(User $usuario, array $data): void
    {
        $usuario->load('role');

        match ($usuario->role->nombre) {
            'propietario' => $usuario->propietarioDetalle()->updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'codigo' => $data['codigo'],
                    'cargo' => $data['cargo'] ?? null,
                ]
            ),

            'secretaria' => $usuario->secretariaDetalle()->updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'codigo' => $data['codigo'],
                    'turno_trabajo' => $data['turno_trabajo'] ?? null,
                    'sueldo' => $data['sueldo'] ?? null,
                ]
            ),

            'docente' => $usuario->docenteDetalle()->updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'codigo' => $data['codigo'],
                    'especialidad' => $data['especialidad'] ?? null,
                    'titulo' => $data['titulo'] ?? null,
                    'registro_profesional' => $data['registro_profesional'] ?? null,
                ]
            ),

            'alumno' => $usuario->alumnoDetalle()->updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'codigo' => $data['codigo'],
                    'colegio_origen' => $data['colegio_origen'] ?? null,
                    'anio_bachillerato' => $data['anio_bachillerato'] ?? null,
                    'estado_academico' => $data['estado_academico'] ?? 'activo',
                ]
            ),

            default => null,
        };
    }

    private function limpiarDetallesSiCambioRol(User $usuario): void
    {
        $usuario->load('role');

        if ($usuario->role->nombre !== 'propietario') {
            $usuario->propietarioDetalle()->delete();
        }

        if ($usuario->role->nombre !== 'secretaria') {
            $usuario->secretariaDetalle()->delete();
        }

        if ($usuario->role->nombre !== 'docente') {
            $usuario->docenteDetalle()->delete();
        }

        if ($usuario->role->nombre !== 'alumno') {
            $usuario->alumnoDetalle()->delete();
        }
    }
}