<div class="rounded-3xl border border-slate-200 bg-white p-6 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 text-3xl text-blue-700">
        <i class="fa-solid fa-chalkboard-user"></i>
    </div>

    <h3 class="mt-5 text-lg font-black text-slate-900">
        {{ $docente->user?->nombreCompleto() ?? 'Docente sin usuario' }}
    </h3>

    <p class="mt-1 text-sm font-bold text-blue-700">
        {{ $docente->especialidad ?? 'Especialidad no registrada' }}
    </p>

    <div class="mt-4 space-y-2 text-sm text-slate-500">
        <p>
            <span class="font-bold text-slate-700">Grado:</span>
            {{ $docente->grado_academico ?? '-' }}
        </p>

        <p>
            <span class="font-bold text-slate-700">Experiencia:</span>
            {{ $docente->experiencia ?? '-' }}
        </p>

        @if (isset($docente->estado))
            <p>
                <span class="font-bold text-slate-700">Estado:</span>
                {{ $docente->estado ? 'Activo' : 'Inactivo' }}
            </p>
        @endif
    </div>

    <p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-500">
        {{ $docente->biografia ?? 'Sin biografía registrada.' }}
    </p>
</div>