<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsuarioRequest;
use App\Http\Requests\Admin\UpdateUsuarioRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UsuarioService;
use Illuminate\Database\QueryException;
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

        if ($request->ajax()) {
            return response()->json([
                'data' => $usuarios->getCollection()->map(function ($usuario) {
                    return [
                        'id' => $usuario->id,
                        'ci' => $usuario->ci,
                        'nombres' => $usuario->nombres,
                        'apellidos' => $usuario->apellidos,
                        'email' => $usuario->email,
                        'role' => $usuario->role?->nombre,
                        'estado' => (bool) $usuario->estado,
                    ];
                })->values(),
                'pagination' => [
                    'current_page' => $usuarios->currentPage(),
                    'last_page' => $usuarios->lastPage(),
                    'per_page' => $usuarios->perPage(),
                    'total' => $usuarios->total(),
                    'prev_page_url' => $usuarios->previousPageUrl(),
                    'next_page_url' => $usuarios->nextPageUrl(),
                ],
            ]);
        }

        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    public function create()
    {
        $roles = Role::where('estado', true)->orderBy('nombre')->get();

        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(StoreUsuarioRequest $request)
    {
        try {
            $this->usuarioService->crear($request->validated());
        } catch (QueryException $exception) {
            if (($exception->errorInfo[0] ?? '') === '23505') {
                return back()
                    ->withInput()
                    ->with('error', 'No se pudo registrar el usuario: el código ya existe. Verifique el valor e intente de nuevo.');
            }

            throw $exception;
        }

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
        try {
            $this->usuarioService->actualizar($usuario, $request->validated());
        } catch (QueryException $exception) {
            if (($exception->errorInfo[0] ?? '') === '23505') {
                return back()
                    ->withInput()
                    ->with('error', 'No se pudo actualizar el usuario: el código ya existe. Verifique el valor e intente de nuevo.');
            }

            throw $exception;
        }

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