<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAulaRequest;
use App\Http\Requests\Admin\UpdateAulaRequest;
use App\Models\Aula;
use Illuminate\Http\Request;


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

        if ($request->ajax()) {
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

        return view('admin.aulas.index', compact('aulas'));
    }

    public function create()
    {
        return view('admin.aulas.create');
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

        return view('admin.aulas.show', compact('aula'));
    }

    public function edit(Aula $aula)
    {
        return view('admin.aulas.edit', compact('aula'));
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