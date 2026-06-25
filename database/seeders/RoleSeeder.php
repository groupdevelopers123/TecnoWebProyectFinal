<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'propietario',
                'descripcion' => 'Administra todo el sistema académico y administrativo.',
            ],
            [
                'nombre' => 'secretaria',
                'descripcion' => 'Gestiona usuarios, alumnos, inscripciones, pagos, horarios y oferta académica.',
            ],
            [
                'nombre' => 'docente',
                'descripcion' => 'Consulta sus horarios, materias asignadas y registra seguimiento académico.',
            ],
            [
                'nombre' => 'alumno',
                'descripcion' => 'Consulta su inscripción, pagos, horarios y seguimiento académico.',
            ],
        ];

        foreach ($roles as $rol) {
            Role::updateOrCreate(
                ['nombre' => $rol['nombre']],
                $rol
            );
        }
    }
}