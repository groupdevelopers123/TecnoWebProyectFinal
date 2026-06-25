@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-bold">Revisa los siguientes errores:</p>

        <ul class="mt-2 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-5">
    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Materia</label>

        @if (isset($soloLecturaMateria) && $soloLecturaMateria)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700">
                {{ $inscripcionMateria->carreraMateria->materia->codigo }}
                -
                {{ $inscripcionMateria->carreraMateria->materia->nombre }}
            </div>
        @else
            <select name="carrera_materia_id"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                <option value="">Seleccione una materia</option>

                @foreach ($carreraMaterias as $item)
                    <option value="{{ $item->id }}"
                        @selected(old('carrera_materia_id', $inscripcionMateria->carrera_materia_id ?? '') == $item->id)>
                        {{ $item->materia->codigo }}
                        -
                        {{ $item->materia->nombre }}
                        @if ($item->periodo_numero)
                            / Periodo {{ $item->periodo_numero }}
                        @endif
                    </option>
                @endforeach
            </select>
        @endif
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Estado</label>
        <select name="estado"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            @foreach (['Cursando', 'Aprobada', 'Reprobada', 'Retirada'] as $estado)
                <option value="{{ $estado }}"
                    @selected(old('estado', $inscripcionMateria->estado ?? 'Cursando') === $estado)>
                    {{ $estado }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">
        <a href="{{ route('admin.inscripciones.materias.index', $inscripcion) }}"
           data-modal-link
           class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar
        </button>
    </div>
</div>