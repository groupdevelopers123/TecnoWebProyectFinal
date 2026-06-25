<div class="mb-6 rounded-3xl bg-slate-50 p-5">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <p class="text-xs font-bold uppercase text-slate-400">Alumno</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $credito->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
            </p>
        </div>

        <div>
            <p class="text-xs font-bold uppercase text-slate-400">Concepto</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $credito->conceptoPago->nombre ?? 'Sin concepto' }}
            </p>
        </div>

        <div>
            <p class="text-xs font-bold uppercase text-slate-400">Monto total</p>
            <p class="mt-1 font-bold text-blue-700">
                Bs {{ number_format($credito->monto_total, 2) }}
            </p>
        </div>

        <div>
            <p class="text-xs font-bold uppercase text-slate-400">Saldo pendiente</p>
            <p class="mt-1 font-bold text-amber-700">
                Bs {{ number_format($credito->saldo_pendiente, 2) }}
            </p>
        </div>
    </div>
</div>

<div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Nro.</th>
                    <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Monto</th>
                    <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Vencimiento</th>
                    <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Pago</th>
                    <th class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                    <th class="px-5 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($credito->pagoCuotas as $cuota)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-5 py-4 text-sm font-black text-slate-800">
                            #{{ $cuota->numero_cuota }}
                        </td>

                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100">
                                Bs {{ number_format($cuota->monto, 2) }}
                            </span>
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            {{ $cuota->fecha_vencimiento?->format('d/m/Y') ?? '-' }}
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            {{ $cuota->fecha_pago?->format('d/m/Y') ?? '-' }}
                        </td>

                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1
                                @if ($cuota->estado_cuota === 'pagado')
                                    bg-green-50 text-green-700 ring-green-100
                                @elseif ($cuota->estado_cuota === 'anulado' || $cuota->estado_cuota === 'fallido')
                                    bg-red-50 text-red-700 ring-red-100
                                @else
                                    bg-yellow-50 text-yellow-700 ring-yellow-100
                                @endif">
                                {{ ucfirst($cuota->estado_cuota) }}
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.pago-cuotas.show', $cuota) }}"
                                   title="Ver detalle"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                @if ($cuota->estado_cuota !== 'pagado')
                                    <a href="{{ route('admin.pago-cuotas.edit', $cuota) }}"
                                       title="Pagar cuota"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition hover:-translate-y-0.5 hover:bg-emerald-100">
                                        <i class="fa-solid fa-money-bill-wave text-sm"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                            Este crédito todavía no tiene cuotas generadas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>