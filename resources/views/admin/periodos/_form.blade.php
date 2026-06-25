<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Datos del periodo académico</h3>
        <p class="mt-1 text-sm text-slate-500">
            Define la gestión, tipo de periodo y fechas académicas.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Nombre</label>
        <input type="text"
               name="nombre"
               value="{{ old('nombre', $periodo->nombre ?? '') }}"
               placeholder="Ej: Periodo 1-2026"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Gestión</label>
        <input type="number"
               name="gestion"
               value="{{ old('gestion', $periodo->gestion ?? date('Y')) }}"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Tipo de periodo</label>
        <select name="tipo_periodo"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione</option>
            @foreach (['Semestral', 'Anual', 'Modular'] as $tipo)
                <option value="{{ $tipo }}" @selected(old('tipo_periodo', $periodo->tipo_periodo ?? '') === $tipo)>
                    {{ $tipo }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Número de periodo</label>
        <select name="numero_periodo"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione</option>
            @foreach ([1, 2, 3, 4] as $numero)
                <option value="{{ $numero }}" @selected(old('numero_periodo', $periodo->numero_periodo ?? '') == $numero)>
                    {{ $numero }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Fecha inicio</label>
        <input type="date"
               name="fecha_inicio"
               value="{{ old('fecha_inicio', isset($periodo) && $periodo->fecha_inicio ? $periodo->fecha_inicio->format('Y-m-d') : '') }}"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Fecha fin</label>
        <input type="date"
               name="fecha_fin"
               value="{{ old('fecha_fin', isset($periodo) && $periodo->fecha_fin ? $periodo->fecha_fin->format('Y-m-d') : '') }}"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Estado</label>
        <select name="estado"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="1" @selected(old('estado', $periodo->estado ?? 1) == 1)>Activo</option>
            <option value="0" @selected(old('estado', $periodo->estado ?? 1) == 0)>Inactivo</option>
        </select>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar periodo
        </button>

        <a href="{{ route('admin.periodos-academicos.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>