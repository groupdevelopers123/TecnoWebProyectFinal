<div class="group relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-100">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
    <div class="absolute top-0 left-0 h-1 w-0 bg-gradient-to-r from-blue-600 to-blue-400 transition-all duration-300 group-hover:w-full"></div>

    <div class="relative p-6 text-center">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 text-3xl text-blue-700">
            <i class="fa-solid fa-chalkboard-user"></i>
        </div>

        <h3 class="mt-5 text-lg font-black text-slate-900 transition-colors duration-300 group-hover:text-blue-700">
            {{ $docente->user?->nombreCompleto() ?? 'Docente sin usuario' }}
        </h3>

        <p class="mt-1 text-sm font-bold text-blue-700">
            {{ $docente->especialidad ?? 'Especialidad no registrada' }}
        </p>

        <div class="mt-4 space-y-2 text-sm text-slate-500">
            

            @if (isset($docente->estado))
                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-left">
                    <span class="font-bold text-slate-500">Estado</span>
                    @if ($docente->estado)
                        <span class="inline-flex items-center gap-2 font-black text-emerald-700">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </span>
                            Activo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 font-black text-red-700">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-50 text-red-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="2" />
                                </svg>
                            </span>
                            Inactivo
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-500">
            {{ $docente->biografia ?? 'Sin biografía registrada.' }}
        </p>
    </div>
</div>