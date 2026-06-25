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
        <h3 class="text-lg font-black text-slate-900">Datos del concepto de pago</h3>
        <p class="mt-1 text-sm text-slate-500">
            Registra el nombre, descripción y estado del concepto.
        </p>
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Nombre</label>
        <input type="text"
               name="nombre"
               value="{{ old('nombre', $concepto->nombre ?? '') }}"
               required
               placeholder="Ejemplo: Matrícula, Mensualidad, Certificado, Examen"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Descripción</label>
        <textarea name="descripcion"
                  rows="4"
                  placeholder="Descripción del concepto de pago"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('descripcion', $concepto->descripcion ?? '') }}</textarea>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Estado</label>
        <select name="estado"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="Activo" @selected(old('estado', $concepto->estado ?? 'Activo') === 'Activo')>
                Activo
            </option>
            <option value="Inactivo" @selected(old('estado', $concepto->estado ?? 'Activo') === 'Inactivo')>
                Inactivo
            </option>
        </select>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar concepto
        </button>

        <a href="{{ route('admin.concepto-pagos.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>