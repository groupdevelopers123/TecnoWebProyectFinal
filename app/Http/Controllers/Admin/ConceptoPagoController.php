<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConceptoPagoRequest;
use App\Models\ConceptoPago;
use Illuminate\Http\Request;

class ConceptoPagoController extends Controller
{
    public function index(Request $request)
    {
        $conceptos = ConceptoPago::query()
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'ILIKE', "%{$buscar}%")
                        ->orWhere('descripcion', 'ILIKE', "%{$buscar}%")
                        ->orWhere('estado', 'ILIKE', "%{$buscar}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'data' => $conceptos->getCollection()->map(function ($concepto) {
                    return [
                        'id' => $concepto->id,
                        'nombre' => $concepto->nombre,
                        'descripcion' => $concepto->descripcion,
                        'estado' => $concepto->estado,
                    ];
                })->values(),

                'pagination' => [
                    'current_page' => $conceptos->currentPage(),
                    'last_page' => $conceptos->lastPage(),
                    'per_page' => $conceptos->perPage(),
                    'total' => $conceptos->total(),
                    'prev_page_url' => $conceptos->previousPageUrl(),
                    'next_page_url' => $conceptos->nextPageUrl(),
                ],
            ]);
        }

        return view('admin.concepto-pagos.index', compact('conceptos'));
    }

    public function create()
    {
        return view('admin.concepto-pagos.create', [
            'concepto' => new ConceptoPago(),
        ]);
    }

    public function store(ConceptoPagoRequest $request)
    {
        ConceptoPago::create($request->validated());

        return redirect()
            ->route('admin.concepto-pagos.index')
            ->with('success', 'Concepto de pago registrado correctamente.');
    }

    public function show(ConceptoPago $concepto_pago)
    {
        return view('admin.concepto-pagos.show', [
            'concepto' => $concepto_pago,
        ]);
    }

    public function edit(ConceptoPago $concepto_pago)
    {
        return view('admin.concepto-pagos.edit', [
            'concepto' => $concepto_pago,
        ]);
    }

    public function update(ConceptoPagoRequest $request, ConceptoPago $concepto_pago)
    {
        $concepto_pago->update($request->validated());

        return redirect()
            ->route('admin.concepto-pagos.index')
            ->with('success', 'Concepto de pago actualizado correctamente.');
    }

    public function destroy(ConceptoPago $concepto_pago)
    {
        $concepto_pago->update([
            'estado' => $concepto_pago->estado === 'Activo' ? 'Inactivo' : 'Activo',
        ]);

        return redirect()
            ->route('admin.concepto-pagos.index')
            ->with('success', 'Estado del concepto de pago actualizado correctamente.');
    }
}