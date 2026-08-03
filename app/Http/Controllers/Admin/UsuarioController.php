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
use Inertia\Inertia;

class UsuarioController extends Controller
{
    public function __construct(
        private readonly UsuarioService $usuarioService
    ) {
    }

    private function rolesPermitidos()
    {
        $query = Role::where('estado', true);

        if (auth()->user()?->tieneRol('secretaria')) {
            $query->whereIn('nombre', ['alumno', 'docente']);
        }

        return $query->orderBy('nombre')->get();
    }

    private function puedeGestionarUsuario(?User $usuario = null): bool
    {
        if (! auth()->user()?->esAdministrativo()) {
            return false;
        }

        if (! auth()->user()?->tieneRol('secretaria')) {
            return true;
        }

        if ($usuario === null) {
            return true;
        }

        return in_array($usuario->role?->nombre, ['alumno', 'docente'], true);
    }

    private function usuarioPayload(User $usuario): array
    {
        $usuario->loadMissing([
            'role',
            'propietarioDetalle',
            'secretariaDetalle',
            'docenteDetalle',
            'alumnoDetalle',
        ]);

        return [
            'id' => $usuario->id,
            'role_id' => $usuario->role_id,
            'ci' => $usuario->ci,
            'nombres' => $usuario->nombres,
            'apellidos' => $usuario->apellidos,
            'email' => $usuario->email,
            'telefono' => $usuario->telefono,
            'direccion' => $usuario->direccion,
            'fecha_nacimiento' => $usuario->fecha_nacimiento?->format('Y-m-d'),
            'estado' => (bool) $usuario->estado,
            'nombre_completo' => trim(($usuario->nombres ?? '') . ' ' . ($usuario->apellidos ?? '')),
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
        ];
    }

    public function index(Request $request)
    {
        $highlightUserId = $request->filled('highlight_user') ? (int) $request->input('highlight_user') : null;

        $usuarios = User::query()
            ->with('role')
            ->when($highlightUserId, function ($query) use ($highlightUserId) {
                $query->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$highlightUserId]);
            })
            ->when(auth()->user()?->tieneRol('secretaria'), function ($query) {
                $query->whereHas('role', function ($q) {
                    $q->whereIn('nombre', ['alumno', 'docente']);
                });
            })
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombres', 'ILIKE', "%{$buscar}%")
                        ->orWhere('apellidos', 'ILIKE', "%{$buscar}%")
                        ->orWhere('ci', 'ILIKE', "%{$buscar}%")
                        ->orWhere('email', 'ILIKE', "%{$buscar}%");
                });
            })
            ->when($request->filled('role_id'), function ($query) use ($request) {
                $roleId = $request->input('role_id');
                $allowedRoleIds = $this->rolesPermitidos()->pluck('id');

                if ($allowedRoleIds->contains($roleId)) {
                    $query->where('role_id', $roleId);
                }
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = $this->rolesPermitidos();

        if ($request->ajax() && ! $request->header('X-Inertia')) {
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

        return Inertia::render('admin/usuarios/Index', [
            'usuarios' => [
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
            ],
            'roles' => $roles,
            'request' => [
                'buscar' => $request->buscar,
                'role_id' => $request->role_id,
                'highlight_user' => $highlightUserId,
            ],
        ]);
    }

    public function create()
    {
        $roles = $this->rolesPermitidos();

        return Inertia::render('admin/usuarios/Create', [
            'roles' => $roles,
            'action' => route('admin.usuarios.store'),
            'cancelUrl' => route('admin.usuarios.index'),
        ]);
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
        abort_unless($this->puedeGestionarUsuario($usuario), 403);

        return Inertia::render('admin/usuarios/Show', [
            'usuario' => $this->usuarioPayload($usuario),
        ]);
    }

    public function edit(User $usuario)
    {
        abort_unless($this->puedeGestionarUsuario($usuario), 403);

        $roles = $this->rolesPermitidos();

        return Inertia::render('admin/usuarios/Edit', [
            'usuario' => $this->usuarioPayload($usuario),
            'roles' => $roles,
            'action' => route('admin.usuarios.update', $usuario),
            'cancelUrl' => route('admin.usuarios.index'),
        ]);
    }

    public function update(UpdateUsuarioRequest $request, User $usuario)
    {
        abort_unless($this->puedeGestionarUsuario($usuario), 403);

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
        abort_unless($this->puedeGestionarUsuario($usuario), 403);

        $this->usuarioService->cambiarEstado($usuario);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Estado del usuario actualizado correctamente.');
    }
}