<div class="grid gap-6 md:grid-cols-2">
    <div>
        <label class="mb-2 block font-bold">Código</label>
        <input name="codigo" value="{{ old('codigo', $carrera->codigo ?? '') }}" required class="w-full rounded-2xl border px-4 py-3">
    </div>

    <div>
        <label class="mb-2 block font-bold">Nombre</label>
        <input name="nombre" value="{{ old('nombre', $carrera->nombre ?? '') }}" required class="w-full rounded-2xl border px-4 py-3">
    </div>

    <div>
        <label class="mb-2 block font-bold">Duración</label>
        <input type="number" name="duracion" value="{{ old('duracion', $carrera->duracion ?? '') }}" class="w-full rounded-2xl border px-4 py-3">
    </div>

    <div>
        <label class="mb-2 block font-bold">Régimen académico</label>
        <select name="regimen_academico" class="w-full rounded-2xl border px-4 py-3">
            <option value="">Seleccione</option>
            @foreach (['Semestral', 'Anual', 'Modular'] as $regimen)
                <option value="{{ $regimen }}" @selected(old('regimen_academico', $carrera->regimen_academico ?? '') === $regimen)>{{ $regimen }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block font-bold">Estado</label>
        <select name="estado" class="w-full rounded-2xl border px-4 py-3">
            <option value="1" @selected(old('estado', $carrera->estado ?? 1) == 1)>Activo</option>
            <option value="0" @selected(old('estado', $carrera->estado ?? 1) == 0)>Inactivo</option>
        </select>
    </div>

    <div class="md:col-span-2">
        <button class="rounded-2xl bg-blue-600 px-6 py-3 font-bold text-white">Guardar</button>
        <a href="{{ route('admin.carreras.index') }}" class="ml-2 rounded-2xl bg-slate-100 px-6 py-3 font-bold">Cancelar</a>
    </div>
</div>