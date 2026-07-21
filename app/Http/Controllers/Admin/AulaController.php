<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAulaRequest;
use App\Http\Requests\Admin\UpdateAulaRequest;
use App\Models\Aula;
use Illuminate\Http\Request;
use Inertia\Inertia;


class AulaController extends Controller
{
    public function index(Request $request)
    {
        $aulas = Aula::query()
            ->with('usuarioRegistro')
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('codigo', 'ILIKE', "%{$buscar}%")
                        ->orWhere('nombre', 'ILIKE', "%{$buscar}%")
                        ->orWhere('ubicacion', 'ILIKE', "%{$buscar}%")
                        ->orWhere('piso', 'ILIKE', "%{$buscar}%");
                });
            })
            ->when($request->filled('disponible'), function ($query) use ($request) {
                $query->where('disponible', $request->boolean('disponible'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // If it's an AJAX request coming from the client-side list fetch (not an Inertia visit),
        // return plain JSON. Inertia requests include the `X-Inertia` header and must receive
        // an Inertia response instead of plain JSON.
        if ($request->ajax() && ! $request->header('X-Inertia')) {
            return response()->json([
                'data' => $aulas->getCollection()->map(function ($aula) {
                    return [
                        'id' => $aula->id,
                        'codigo' => $aula->codigo,
                        'nombre' => $aula->nombre,
                        'ubicacion' => $aula->ubicacion,
                        'piso' => $aula->piso,
                        'capacidad' => $aula->capacidad,
                        'largo' => $aula->largo,
                        'ancho' => $aula->ancho,
                        'disponible' => (bool) $aula->disponible,
                    ];
                })->values(),
                'pagination' => [
                    'current_page' => $aulas->currentPage(),
                    'last_page' => $aulas->lastPage(),
                    'per_page' => $aulas->perPage(),
                    'total' => $aulas->total(),
                    'prev_page_url' => $aulas->previousPageUrl(),
                    'next_page_url' => $aulas->nextPageUrl(),
                ],
            ]);
        }

        return Inertia::render('admin/aulas/Index', [
            'aulas' => [
                'data' => $aulas->getCollection()->map(function ($aula) {
                    return [
                        'id' => $aula->id,
                        'codigo' => $aula->codigo,
                        'nombre' => $aula->nombre,
                        'ubicacion' => $aula->ubicacion,
                        'piso' => $aula->piso,
                        'capacidad' => $aula->capacidad,
                        'largo' => $aula->largo,
                        'ancho' => $aula->ancho,
                        'disponible' => (bool) $aula->disponible,
                    ];
                })->values(),
                'pagination' => [
                    'current_page' => $aulas->currentPage(),
                    'last_page' => $aulas->lastPage(),
                    'per_page' => $aulas->perPage(),
                    'total' => $aulas->total(),
                    'prev_page_url' => $aulas->previousPageUrl(),
                    'next_page_url' => $aulas->nextPageUrl(),
                ],
            ],
            'request' => [
                'buscar' => $request->buscar,
                'disponible' => $request->disponible,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/aulas/Create', [
            'action' => route('admin.aulas.store'),
            'method' => 'post',
            'cancelUrl' => route('admin.aulas.index'),
        ]);
    }

    public function store(StoreAulaRequest $request)
    {
        Aula::create([
            ...$request->validated(),
            'user_id_registro' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.aulas.index')
            ->with('success', 'Aula registrada correctamente.');
    }

    public function show(Aula $aula)
    {
        $aula->load('usuarioRegistro');

        // Prepare minimal computed fields used in the Vue page (server-side helpers)
        $aulaData = $aula->toArray();
        $aulaData['estadoTexto'] = $aula->estadoTexto();
        $aulaData['area'] = method_exists($aula, 'area') ? $aula->area() : null;

        return Inertia::render('admin/aulas/Show', [
            'aula' => $aulaData,
        ]);
    }

    public function edit(Aula $aula)
    {
        return Inertia::render('admin/aulas/Edit', [
            'aula' => $aula,
            'action' => route('admin.aulas.update', $aula),
            'method' => 'put',
            'cancelUrl' => route('admin.aulas.index'),
        ]);
    }

    public function update(UpdateAulaRequest $request, Aula $aula)
    {
        $aula->update($request->validated());

        return redirect()
            ->route('admin.aulas.index')
            ->with('success', 'Aula actualizada correctamente.');
    }

    public function destroy(Aula $aula)
    {
        $aula->update([
            'disponible' => ! $aula->disponible,
        ]);

        return redirect()
            ->route('admin.aulas.index')
            ->with('success', 'Disponibilidad del aula actualizada correctamente.');
    }
}