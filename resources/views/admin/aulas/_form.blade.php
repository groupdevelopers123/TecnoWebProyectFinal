<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Datos del aula</h3>
        <p class="mt-1 text-sm text-slate-500">
            Registra la información necesaria para identificar y administrar el aula.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Código</label>
        <input type="text"
               name="codigo"
               value="{{ old('codigo', $aula->codigo ?? '') }}"
               placeholder="Ej: AULA-101"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Nombre</label>
        <input type="text"
               name="nombre"
               value="{{ old('nombre', $aula->nombre ?? '') }}"
               placeholder="Ej: Aula de Sistemas"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Ubicación</label>
        <input type="text"
               name="ubicacion"
               value="{{ old('ubicacion', $aula->ubicacion ?? '') }}"
               placeholder="Ej: Bloque A"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Piso</label>

        <select name="piso"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un piso</option>

            <option value="Planta baja" @selected(old('piso', $aula->piso ?? '') === 'Planta baja')>
                Planta baja
            </option>

            <option value="Primer piso" @selected(old('piso', $aula->piso ?? '') === 'Primer piso')>
                Primer piso
            </option>

            <option value="Segundo piso" @selected(old('piso', $aula->piso ?? '') === 'Segundo piso')>
                Segundo piso
            </option>

            <option value="Tercer piso" @selected(old('piso', $aula->piso ?? '') === 'Tercer piso')>
                Tercer piso
            </option>

            <option value="Cuarto piso" @selected(old('piso', $aula->piso ?? '') === 'Cuarto piso')>
                Cuarto piso
            </option>

            <option value="Quinto piso" @selected(old('piso', $aula->piso ?? '') === 'Quinto piso')>
                Quinto piso
            </option>
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Capacidad</label>
        <input type="number"
               name="capacidad"
               value="{{ old('capacidad', $aula->capacidad ?? '') }}"
               min="1"
               placeholder="Ej: 30"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Disponibilidad</label>
        <select name="disponible"
                required
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="1" @selected(old('disponible', $aula->disponible ?? 1) == 1)>Disponible</option>
            <option value="0" @selected(old('disponible', $aula->disponible ?? 1) == 0)>No disponible</option>
        </select>
    </div>

    <div class="border-t border-slate-200 pt-6 md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Dimensiones</h3>
        <p class="mt-1 text-sm text-slate-500">
            Estos datos ayudarán a describir mejor el espacio físico del aula.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Largo en metros</label>
        <input type="number"
               step="0.01"
               name="largo"
               value="{{ old('largo', $aula->largo ?? '') }}"
               placeholder="Ej: 8.50"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Ancho en metros</label>
        <input type="number"
               step="0.01"
               name="ancho"
               value="{{ old('ancho', $aula->ancho ?? '') }}"
               placeholder="Ej: 6.20"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar aula
        </button>

        <a href="{{ route('admin.aulas.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>