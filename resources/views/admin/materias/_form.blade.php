<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Datos de la materia</h3>
        <p class="mt-1 text-sm text-slate-500">
            Registra la información principal de la materia académica.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Código</label>
        <input type="text"
               name="codigo"
               value="{{ old('codigo', $materia->codigo ?? '') }}"
               placeholder="Ej: WEB-101"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Nombre</label>
        <input type="text"
               name="nombre"
               value="{{ old('nombre', $materia->nombre ?? '') }}"
               placeholder="Ej: Tecnología Web"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Carga horaria</label>
        <input type="number"
               name="carga_horaria"
               value="{{ old('carga_horaria', $materia->carga_horaria ?? '') }}"
               placeholder="Ej: 80"
               min="1"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Docente (Opcional)</label>
        <select name="docente_detalle_id"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un docente (opcional)</option>
            @foreach ($docentes as $docente)
                <option value="{{ $docente->id }}"
                    @selected(old('docente_detalle_id', $materia->docente_detalle_id ?? '') == $docente->id)>
                    {{ $docente->codigo }} - {{ $docente->user->nombres }} {{ $docente->user->apellidos }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Estado</label>
        <select name="estado"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="1" @selected(old('estado', $materia->estado ?? 1) == 1)>Activa</option>
            <option value="0" @selected(old('estado', $materia->estado ?? 1) == 0)>Inactiva</option>
        </select>
    </div>



    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar materia
        </button>

        <a href="{{ route('admin.materias.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>