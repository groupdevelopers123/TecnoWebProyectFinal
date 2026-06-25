<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Asignación carrera - materia</h3>
        <p class="mt-1 text-sm text-slate-500">
            Selecciona una carrera y una materia para habilitarla en horarios.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Carrera</label>
        <select id="carrera_select"
                name="carrera_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione una carrera</option>
            @foreach ($carreras as $carrera)
                <option value="{{ $carrera->id }}" 
                        data-regimen="{{ $carrera->regimen_academico }}"
                        @selected(old('carrera_id', $asignacion->carrera_id ?? '') == $carrera->id)>
                    {{ $carrera->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Materia</label>
        <select name="materia_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione una materia</option>
            @foreach ($materias as $materia)
                <option value="{{ $materia->id }}" @selected(old('materia_id', $asignacion->materia_id ?? '') == $materia->id)>
                    {{ $materia->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        @php
            $carreraSeleccionada = $asignacion->carrera ?? null;
            $regimen = $carreraSeleccionada?->regimen_academico ?? 'semestral';
            $labelMapa = [
                'semestral' => 'Semestre',
                'anual' => 'Año',
                'modular' => 'Módulo',
            ];
            $labelActual = $labelMapa[$regimen] ?? 'Período';
        @endphp
        <label class="mb-2 block text-sm font-bold text-slate-700">
            <span id="periodo_label">{{ $labelActual }}</span>
        </label>
        <select name="periodo_numero"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione</option>
            @foreach ([1,2,3,4,5,6,7,8,9,10,11,12] as $periodo)
                <option value="{{ $periodo }}" @selected(old('periodo_numero', $asignacion->periodo_numero ?? '') == $periodo)>
                    {{ $periodo }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar asignación
        </button>

        <a href="{{ route('admin.carrera-materias.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>

<script>
(function() {
    const labelMapa = {
        'semestral': 'Semestre',
        'anual': 'Año',
        'modular': 'Módulo',
    };

    const carreraSelect = document.getElementById('carrera_select');
    const periodoLabel = document.getElementById('periodo_label');

    function actualizarLabel() {
        if (carreraSelect && periodoLabel) {
            const selectedOption = carreraSelect.options[carreraSelect.selectedIndex];
            const regimen = selectedOption.getAttribute('data-regimen') || 'semestral';
            periodoLabel.textContent = labelMapa[regimen] || 'Período';
        }
    }

    // Ejecutar inmediatamente si el DOM ya está listo
    setTimeout(actualizarLabel, 0);

    // Ejecutar cuando cambia la selección
    if (carreraSelect) {
        carreraSelect.addEventListener('change', actualizarLabel);
    }
})();
</script>