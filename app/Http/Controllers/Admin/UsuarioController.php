<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsuarioRequest;
use App\Http\Requests\Admin\UpdateUsuarioRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UsuarioService;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(
        private readonly UsuarioService $usuarioService
    ) {
    }

    public function index(Request $request)
    {
        $usuarios = User::query()
            ->with('role')
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombres', 'ILIKE', "%{$buscar}%")
                        ->orWhere('apellidos', 'ILIKE', "%{$buscar}%")
                        ->orWhere('ci', 'ILIKE', "%{$buscar}%")
                        ->orWhere('email', 'ILIKE', "%{$buscar}%");
                });
            })
            ->when($request->role_id, function ($query, $roleId) {
                $query->where('role_id', $roleId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = Role::where('estado', true)->orderBy('nombre')->get();

        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    public function create()
    {
        $roles = Role::where('estado', true)->orderBy('nombre')->get();

        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(StoreUsuarioRequest $request)
    {
        $this->usuarioService->crear($request->validated());

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    public function show(User $usuario)
    {
        $usuario->load([
            'role',
            'propietarioDetalle',
            'secretariaDetalle',
            'docenteDetalle',
            'alumnoDetalle',
        ]);

        return view('admin.usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $usuario->load([
            'role',
            'propietarioDetalle',
            'secretariaDetalle',
            'docenteDetalle',
            'alumnoDetalle',
        ]);

        $roles = Role::where('estado', true)->orderBy('nombre')->get();

        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(UpdateUsuarioRequest $request, User $usuario)
    {
        $this->usuarioService->actualizar($usuario, $request->validated());

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        $this->usuarioService->cambiarEstado($usuario);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Estado del usuario actualizado correctamente.');
    }
}