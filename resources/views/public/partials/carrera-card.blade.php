<div class="group relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-100">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
    <div class="absolute top-0 left-0 h-1 w-0 bg-gradient-to-r from-blue-600 to-blue-400 transition-all duration-300 group-hover:w-full"></div>

    <div class="relative p-6">
        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl text-blue-700">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>

        <h3 class="text-xl font-black text-slate-900 transition-colors duration-300 group-hover:text-blue-700">
            {{ $carrera->nombre ?: 'Carrera sin nombre' }}
        </h3>

        <p class="mt-3 text-sm leading-6 text-slate-500">
            Carrera académica registrada en el Instituto Andrés Ibáñez.
        </p>

        <div class="mt-5 grid gap-3 text-sm">
            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                <span class="font-bold text-slate-500">Código</span>
                <span class="font-black text-slate-800">
                    {{ $carrera->codigo ?: '-' }}
                </span>
            </div>

            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                <span class="font-bold text-slate-500">Duración</span>
                <span class="font-black text-slate-800">
                    {{ $carrera->duracion ? $carrera->duracion . ' semestres' : '-' }}
                </span>
            </div>

            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                <span class="font-bold text-slate-500">Régimen</span>
                <span class="font-black text-slate-800">
                    {{ $carrera->regimen_academico ?: '-' }}
                </span>
            </div>

            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                <span class="font-bold text-slate-500">Estado</span>
                @if ($carrera->estado)
                    <span class="inline-flex items-center gap-2 font-black text-emerald-700">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        Activa
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 font-black text-red-700">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-50 text-red-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="12" r="2" />
                            </svg>
                        </span>
                        Inactiva
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>