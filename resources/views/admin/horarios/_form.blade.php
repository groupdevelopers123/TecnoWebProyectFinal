<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Datos del horario</h3>
        <p class="mt-1 text-sm text-slate-500">
            Selecciona carrera-materia, periodo, aula, docente, día y horas.
        </p>
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Carrera y materia</label>
        <select name="carrera_materia_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione una asignación</option>
            @foreach ($carreraMaterias as $item)
                <option value="{{ $item->id }}" @selected(old('carrera_materia_id', $horario->carrera_materia_id ?? '') == $item->id)>
                    {{ $item->carrera->nombre }} - {{ $item->materia->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Periodo académico</label>
        <select name="periodo_academico_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un periodo</option>
            @foreach ($periodos as $periodo)
                <option value="{{ $periodo->id }}" @selected(old('periodo_academico_id', $horario->periodo_academico_id ?? '') == $periodo->id)>
                    {{ $periodo->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Aula</label>
        <select name="aula_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un aula</option>
            @foreach ($aulas as $aula)
                <option value="{{ $aula->id }}" @selected(old('aula_id', $horario->aula_id ?? '') == $aula->id)>
                    {{ $aula->nombre }} - {{ $aula->codigo }} / Capacidad: {{ $aula->capacidad }}
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
                <option value="{{ $docente->id }}" @selected(old('docente_detalle_id', $horario->docente_detalle_id ?? '') == $docente->id)>
                    {{ $docente->user->nombreCompleto() }} - {{ $docente->especialidad ?? 'Sin especialidad' }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="border-t border-slate-200 pt-6 md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Día y horario</h3>
        <p class="mt-1 text-sm text-slate-500">
            El sistema validará que no exista cruce de aula ni docente.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Día</label>
        <select name="dia"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un día</option>
            @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
                <option value="{{ $dia }}" @selected(old('dia', $horario->dia ?? '') === $dia)>
                    {{ $dia }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Turno</label>
        <select name="turno"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un turno</option>
            @foreach (['Mañana', 'Tarde', 'Noche'] as $turno)
                <option value="{{ $turno }}" @selected(old('turno', $horario->turno ?? '') === $turno)>
                    {{ $turno }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Hora inicio</label>
        <input type="time"
               name="hora_inicio"
               value="{{ old('hora_inicio', isset($horario) ? substr($horario->hora_inicio, 0, 5) : '') }}"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Hora fin</label>
        <input type="time"
               name="hora_fin"
               value="{{ old('hora_fin', isset($horario) ? substr($horario->hora_fin, 0, 5) : '') }}"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Estado</label>
        <select name="estado"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="1" @selected(old('estado', $horario->estado ?? 1) == 1)>Activo</option>
            <option value="0" @selected(old('estado', $horario->estado ?? 1) == 0)>Inactivo</option>
        </select>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar horario
        </button>

        <a href="{{ route('admin.horarios.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>