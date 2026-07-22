<?php

namespace Tests\Unit;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use PHPUnit\Framework\TestCase;

class AuthRedirectsTest extends TestCase
{
    public function test_login_controller_routes_admin_roles_to_dashboard(): void
    {
        $controller = new LoginController();

        $method = new \ReflectionMethod($controller, 'urlPorRol');
        $method->setAccessible(true);

        $this->assertSame('/admin/dashboard', $method->invoke($controller, 'Propietario'));
        $this->assertSame('/admin/dashboard', $method->invoke($controller, 'ADMIN'));
        $this->assertSame('/admin/dashboard', $method->invoke($controller, 'owner'));
    }

    public function test_registration_controller_normalizes_admin_roles(): void
    {
        $controller = new RegisteredUserController();

        $method = new \ReflectionMethod($controller, 'normalizarRol');
        $method->setAccessible(true);

        $this->assertSame('propietario', $method->invoke($controller, 'Admin'));
        $this->assertSame('propietario', $method->invoke($controller, 'Owner'));
        $this->assertSame('propietario', $method->invoke($controller, 'administrador'));
    }
}
