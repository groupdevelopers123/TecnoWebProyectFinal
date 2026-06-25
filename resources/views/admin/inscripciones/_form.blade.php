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
        <h3 class="text-lg font-black text-slate-900">Datos de la inscripción</h3>
        <p class="mt-1 text-sm text-slate-500">
            Selecciona alumno, oferta académica, periodo y fecha de inscripción.
        </p>
    </div>

    @if ($alumnos->isEmpty())
        <div class="md:col-span-2 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 text-sm text-yellow-800">
            <p class="font-bold">No hay alumnos disponibles</p>
            <p class="mt-1">
                Primero registra un usuario con rol alumno y completa sus datos de alumno.
            </p>
        </div>
    @endif

    @if ($ofertas->isEmpty())
        <div class="md:col-span-2 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 text-sm text-yellow-800">
            <p class="font-bold">No hay ofertas académicas disponibles</p>
            <p class="mt-1">
                Primero registra una oferta académica activa con cupos disponibles.
            </p>
        </div>
    @endif

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Alumno</label>
        <select name="alumno_detalle_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un alumno</option>

            @foreach ($alumnos as $alumno)
                <option value="{{ $alumno->id }}"
                    @selected(old('alumno_detalle_id', $inscripcion->alumno_detalle_id ?? '') == $alumno->id)>
                    {{ $alumno->codigo ?? 'SIN-COD' }}
                    -
                    {{ $alumno->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
                    @if ($alumno->user?->ci)
                        / CI: {{ $alumno->user->ci }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Oferta académica</label>
        <select name="oferta_academica_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione una oferta académica</option>

            @foreach ($ofertas as $oferta)
                <option value="{{ $oferta->id }}"
                    @selected(old('oferta_academica_id', $inscripcion->oferta_academica_id ?? '') == $oferta->id)>
                    {{ $oferta->nombre }}
                    -
                    {{ $oferta->carrera->codigo }} {{ $oferta->carrera->nombre }}
                    /
                    {{ $oferta->periodoAcademico->nombre }} {{ $oferta->periodoAcademico->gestion }}
                    /
                    Cupos: {{ $oferta->cupos_disponibles }}/{{ $oferta->cantidad_cupos }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Periodo número</label>
        <input type="number"
               name="periodo_numero"
               value="{{ old('periodo_numero', $inscripcion->periodo_numero ?? '') }}"
               min="1"
               max="20"
               required
               placeholder="Ejemplo: 1"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Fecha de inscripción</label>
        <input type="date"
               name="fecha_inscripcion"
               value="{{ old('fecha_inscripcion', isset($inscripcion) && $inscripcion->fecha_inscripcion ? $inscripcion->fecha_inscripcion->format('Y-m-d') : now()->format('Y-m-d')) }}"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Observación</label>
        <textarea name="observacion"
                  rows="4"
                  placeholder="Observaciones adicionales de la inscripción"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('observacion', $inscripcion->observacion ?? '') }}</textarea>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar inscripción
        </button>

        <a href="{{ route('admin.inscripciones.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>