<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $propietario = Role::where('nombre', 'propietario')->first();
        $secretaria = Role::where('nombre', 'secretaria')->first();
        $docente = Role::where('nombre', 'docente')->first();
        $alumno = Role::where('nombre', 'alumno')->first();

        User::updateOrCreate(
            ['email' => 'propietario@instituto.com'],
            [
                'role_id' => $propietario->id,
                'ci' => '1000001',
                'nombres' => 'Usuario',
                'apellidos' => 'Propietario',
                'telefono' => '70000001',
                'direccion' => 'Santa Cruz',
                'password' => Hash::make('12345678'),
                'estado' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'secretaria@instituto.com'],
            [
                'role_id' => $secretaria->id,
                'ci' => '1000002',
                'nombres' => 'Usuario',
                'apellidos' => 'Secretaria',
                'telefono' => '70000002',
                'direccion' => 'Santa Cruz',
                'password' => Hash::make('12345678'),
                'estado' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'docente@instituto.com'],
            [
                'role_id' => $docente->id,
                'ci' => '1000003',
                'nombres' => 'Usuario',
                'apellidos' => 'Docente',
                'telefono' => '70000003',
                'direccion' => 'Santa Cruz',
                'password' => Hash::make('12345678'),
                'estado' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'alumno@instituto.com'],
            [
                'role_id' => $alumno->id,
                'ci' => '1000004',
                'nombres' => 'Usuario',
                'apellidos' => 'Alumno',
                'telefono' => '70000004',
                'direccion' => 'Santa Cruz',
                'password' => Hash::make('12345678'),
                'estado' => true,
            ]
        );
    }
}