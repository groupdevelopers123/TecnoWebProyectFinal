<div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl text-blue-700">
        <i class="fa-solid fa-graduation-cap"></i>
    </div>

    <h3 class="text-xl font-black text-slate-900">
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
                <span class="font-black text-green-700">Activa</span>
            @else
                <span class="font-black text-red-700">Inactiva</span>
            @endif
        </div>
    </div>
</div>