@if (session('success'))
    <div class="mb-5 rounded-2xl border border-green-100 bg-green-50 p-4 text-sm font-bold text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div>
        <h3 class="text-lg font-black text-slate-900">
            Materias inscritas
        </h3>
        <p class="mt-1 text-sm text-slate-500">
            {{ $inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
            —
            {{ $inscripcion->ofertaAcademica->carrera->nombre }}
        </p>
    </div>

    <a href="{{ route('admin.inscripciones.materias.create', $inscripcion) }}"
       data-modal-link
       class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
        <i class="fa-solid fa-plus text-xs"></i>
        Agregar materia
    </a>
</div>

<div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                        Materia
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                        Periodo
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                        Estado
                    </th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-500">
                        Acciones
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($inscripcion->inscripcionMaterias as $detalle)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ $detalle->carreraMateria->materia->nombre }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $detalle->carreraMateria->materia->codigo }}
                            </p>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $detalle->carreraMateria->periodo_numero ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1
                                @if ($detalle->estado === 'Cursando')
                                    bg-blue-50 text-blue-700 ring-blue-100
                                @elseif ($detalle->estado === 'Aprobada')
                                    bg-green-50 text-green-700 ring-green-100
                                @elseif ($detalle->estado === 'Reprobada')
                                    bg-red-50 text-red-700 ring-red-100
                                @else
                                    bg-yellow-50 text-yellow-700 ring-yellow-100
                                @endif">
                                {{ $detalle->estado }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.inscripciones.materias.edit', [$inscripcion, $detalle]) }}"
                                   data-modal-link
                                   title="Editar materia inscrita"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.inscripciones.materias.destroy', [$inscripcion, $detalle]) }}"
                                      data-modal-form
                                      onsubmit="return confirm('¿Está seguro de retirar esta materia de la inscripción?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="Eliminar materia inscrita"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                            Esta inscripción todavía no tiene materias registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>