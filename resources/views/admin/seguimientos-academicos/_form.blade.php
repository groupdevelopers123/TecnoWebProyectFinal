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
        <h3 class="text-lg font-black text-slate-900">Datos del seguimiento académico</h3>
        <p class="mt-1 text-sm text-slate-500">
            Registra nota final, asistencia, docente responsable y estado académico.
        </p>
    </div>

    @if ($inscripcionMaterias->isEmpty())
        <div class="md:col-span-2 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 text-sm text-yellow-800">
            <p class="font-bold">No hay materias inscritas disponibles</p>
            <p class="mt-1">
                Primero registra una inscripción de materia que todavía no tenga seguimiento académico.
            </p>
        </div>
    @endif

    @if ($docentes->isEmpty())
        <div class="md:col-span-2 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 text-sm text-yellow-800">
            <p class="font-bold">No hay docentes disponibles</p>
            <p class="mt-1">
                Primero registra un usuario con rol docente y su detalle correspondiente.
            </p>
        </div>
    @endif

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Inscripción de materia</label>
        <select name="inscripcion_materia_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione una materia inscrita</option>

            @foreach ($inscripcionMaterias as $item)
                <option value="{{ $item->id }}"
                    @selected(old('inscripcion_materia_id', $seguimiento->inscripcion_materia_id ?? '') == $item->id)>
                    {{ $item->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
                    /
                    {{ $item->inscripcion->ofertaAcademica->carrera->nombre }}
                    /
                    {{ $item->carreraMateria->materia->codigo }} - {{ $item->carreraMateria->materia->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Docente</label>
        <select name="docente_detalle_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un docente</option>

            @foreach ($docentes as $docente)
                <option value="{{ $docente->id }}"
                    @selected(old('docente_detalle_id', $seguimiento->docente_detalle_id ?? '') == $docente->id)>
                    {{ $docente->codigo ?? 'SIN-COD' }}
                    -
                    {{ $docente->user?->nombreCompleto() ?? 'Docente sin usuario' }}
                    -
                    {{ $docente->especialidad ?? 'Sin especialidad' }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Nota final</label>
        <input type="number"
               step="0.01"
               min="0"
               max="100"
               name="nota_final"
               value="{{ old('nota_final', $seguimiento->nota_final ?? '') }}"
               placeholder="Ejemplo: 85.50"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Porcentaje de asistencia</label>
        <input type="number"
               step="0.01"
               min="0"
               max="100"
               name="porcentaje_asistencia"
               value="{{ old('porcentaje_asistencia', $seguimiento->porcentaje_asistencia ?? '') }}"
               placeholder="Ejemplo: 90.00"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Estado académico</label>
        <select name="estado_academico"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            @foreach (['Cursando', 'Aprobado', 'Reprobado', 'Retirado'] as $estado)
                <option value="{{ $estado }}"
                    @selected(old('estado_academico', $seguimiento->estado_academico ?? 'Cursando') === $estado)>
                    {{ $estado }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Fecha de registro</label>
        <input type="date"
               name="fecha_registro"
               value="{{ old('fecha_registro', isset($seguimiento) && $seguimiento->fecha_registro ? $seguimiento->fecha_registro->format('Y-m-d') : now()->format('Y-m-d')) }}"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Observación</label>
        <textarea name="observacion"
                  rows="4"
                  placeholder="Observaciones académicas del estudiante"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('observacion', $seguimiento->observacion ?? '') }}</textarea>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar seguimiento
        </button>

        <a href="{{ route('admin.seguimientos-academicos.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>