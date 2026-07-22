<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_shows_validation_error_for_invalid_credentials(): void
    {
        Role::create(['nombre' => 'alumno', 'descripcion' => 'Alumno', 'estado' => true]);
        User::create([
            'role_id' => Role::where('nombre', 'alumno')->first()->id,
            'nombres' => 'Ana',
            'apellidos' => 'Pérez',
            'ci' => '1234567',
            'email' => 'ana@example.com',
            'password' => 'password123',
            'estado' => true,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'ana@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertRedirect('/login');
    }

    public function test_registration_rejects_names_with_numbers(): void
    {
        $response = $this->from('/register')->post('/register', [
            'nombres' => 'Juan123',
            'apellidos' => 'Pérez',
            'ci' => '1234567',
            'email' => 'juan@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('nombres');
        $response->assertRedirect('/register');
    }
}
