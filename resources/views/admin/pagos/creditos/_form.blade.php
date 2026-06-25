@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-bold">Revisa los siguientes errores:</p>

        <ul class="mt-2 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Datos del crédito</h3>
        <p class="mt-1 text-sm text-slate-500">
            Registra el crédito asociado a una inscripción y a un concepto de pago.
        </p>
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Inscripción</label>
        <select name="inscripcion_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione una inscripción</option>

            @foreach ($inscripciones as $inscripcion)
                <option value="{{ $inscripcion->id }}"
                    @selected(old('inscripcion_id', $credito->inscripcion_id ?? '') == $inscripcion->id)>
                    {{ $inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
                    /
                    {{ $inscripcion->ofertaAcademica->carrera->nombre ?? 'Sin carrera' }}
                    /
                    {{ $inscripcion->ofertaAcademica->periodoAcademico->nombre ?? 'Sin periodo' }}
                    {{ $inscripcion->ofertaAcademica->periodoAcademico->gestion ?? '' }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Concepto de pago</label>
        <select name="concepto_pago_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un concepto</option>

            @foreach ($conceptos as $concepto)
                <option value="{{ $concepto->id }}"
                    @selected(old('concepto_pago_id', $credito->concepto_pago_id ?? '') == $concepto->id)>
                    {{ $concepto->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Tipo de pago</label>
        <input type="text"
               name="tipo_pago"
               value="{{ old('tipo_pago', $credito->tipo_pago ?? 'CREDITO') }}"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-bold uppercase outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Monto total</label>
        <input type="number"
               step="0.01"
               min="0.01"
               name="monto_total"
               id="monto-total"
               value="{{ old('monto_total', $credito->monto_total ?? '') }}"
               required
               placeholder="Ejemplo: 1200.00"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Saldo pendiente</label>
        <input type="number"
               step="0.01"
               min="0"
               name="saldo_pendiente"
               id="saldo-pendiente"
               value="{{ old('saldo_pendiente', $credito->saldo_pendiente ?? '') }}"
               placeholder="Si se deja vacío, será igual al monto total"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Cantidad de cuotas</label>
        <input type="number"
               min="1"
               max="120"
               name="cantidad_cuotas"
               value="{{ old('cantidad_cuotas', $credito->cantidad_cuotas ?? '') }}"
               required
               placeholder="Ejemplo: 6"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Fecha de otorgamiento</label>
        <input type="date"
            name="fecha_otorgamiento"
            value="{{ old('fecha_otorgamiento', isset($credito) && $credito->fecha_otorgamiento ? $credito->fecha_otorgamiento->format('Y-m-d') : now()->format('Y-m-d')) }}"
            required
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Fecha de vencimiento</label>
        <input type="date"
            name="fecha_vencimiento"
            value="{{ old('fecha_vencimiento', isset($credito) && $credito->fecha_vencimiento ? $credito->fecha_vencimiento->format('Y-m-d') : '') }}"
            required
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Estado</label>
        <select name="estado"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            @foreach (['pendiente', 'activo', 'pagado', 'anulado'] as $estado)
                <option value="{{ $estado }}"
                    @selected(old('estado', $credito->estado ?? 'pendiente') === $estado)>
                    {{ ucfirst($estado) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar crédito
        </button>

        <a href="{{ route('admin.creditos.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const montoTotal = document.getElementById('monto-total');
        const saldoPendiente = document.getElementById('saldo-pendiente');

        if (!montoTotal || !saldoPendiente) {
            return;
        }

        montoTotal.addEventListener('blur', function () {
            if (!saldoPendiente.value && montoTotal.value) {
                saldoPendiente.value = montoTotal.value;
            }
        });
    });
</script>