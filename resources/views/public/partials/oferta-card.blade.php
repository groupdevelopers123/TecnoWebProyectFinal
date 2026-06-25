@php
    $regimen = $oferta->carrera?->regimen_academico
        ?? 'No especificado';

    $duracion = $oferta->carrera?->duracion
        ? $oferta->carrera->duracion . ' años'
        : 'No especificada';

    $modalId = 'detalle-oferta-' . $oferta->id;
@endphp

<div class="group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">

    {{-- Encabezado de la tarjeta --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-violet-700 p-6 text-white">

        <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-12 -left-8 h-32 w-32 rounded-full bg-white/10"></div>

        <div class="relative">

            <div class="mb-4 flex items-start justify-between gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-xl backdrop-blur-sm">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>

                @if ($oferta->estado && $oferta->cupos_disponibles > 0)
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-400/20 px-3 py-1.5 text-xs font-black text-emerald-50 backdrop-blur-sm">
                        <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                        Cupos disponibles
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 rounded-full bg-red-400/20 px-3 py-1.5 text-xs font-black text-red-50 backdrop-blur-sm">
                        <span class="h-2 w-2 rounded-full bg-red-300"></span>
                        Sin cupos
                    </span>
                @endif

            </div>

            <h3 class="text-2xl font-black leading-tight">
                {{ $oferta->carrera?->nombre ?? 'Carrera no registrada' }}
            </h3>

            @if ($oferta->carrera?->codigo)
                <p class="mt-2 text-sm font-bold text-blue-100">
                    Código: {{ $oferta->carrera->codigo }}
                </p>
            @endif

        </div>
    </div>

    {{-- Información resumida --}}
    <div class="flex flex-1 flex-col p-6">

        <div>
            <p class="text-xs font-black uppercase tracking-wider text-slate-400">
                Oferta académica
            </p>

            <h4 class="mt-2 text-lg font-black text-slate-900">
                {{ $oferta->nombre ?: 'Oferta académica' }}
            </h4>

            <p class="mt-2 text-sm text-slate-500">
                {{ $oferta->periodoAcademico?->nombre ?? 'Periodo no registrado' }}

                @if ($oferta->periodoAcademico?->gestion)
                    · {{ $oferta->periodoAcademico->gestion }}
                @endif
            </p>
        </div>

        {{-- Régimen y duración --}}
        <div class="mt-5 grid grid-cols-2 gap-3">

            <div class="rounded-2xl bg-violet-50 p-4">

                <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-xl bg-violet-100 text-violet-700">
                    <i class="fa-solid fa-calendar-days text-sm"></i>
                </div>

                <p class="text-xs font-bold uppercase text-violet-500">
                    Régimen
                </p>

                <p class="mt-1 font-black text-violet-800">
                    {{ $regimen }}
                </p>

            </div>

            <div class="rounded-2xl bg-amber-50 p-4">

                <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <i class="fa-solid fa-hourglass-half text-sm"></i>
                </div>

                <p class="text-xs font-bold uppercase text-amber-600">
                    Duración
                </p>

                <p class="mt-1 font-black text-amber-800">
                    {{ $duracion }}
                </p>

            </div>

        </div>

        {{-- Botones de la tarjeta --}}
        <div class="mt-auto grid gap-3 pt-6 sm:grid-cols-2">

            <button type="button"
                    onclick="document.getElementById('{{ $modalId }}').showModal()"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-3 text-sm font-black text-blue-700 transition hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-100">

                <i class="fa-solid fa-eye text-xs"></i>
                Ver detalle
            </button>

            @if ($oferta->estado && $oferta->cupos_disponibles > 0)

                @auth
                    <a href="{{ route('public.ofertas.inscribirse', $oferta) }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">

                        <i class="fa-solid fa-user-plus text-xs"></i>
                        Inscribirme
                    </a>
                @else
                    <button type="button"
                            onclick="mostrarMensajeAutenticacion('{{ $modalId }}')"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">

                        <i class="fa-solid fa-user-plus text-xs"></i>
                        Inscribirme
                    </button>
                @endauth

            @else
                <button type="button"
                        disabled
                        class="inline-flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-2xl bg-slate-300 px-5 py-3 text-sm font-black text-slate-500">

                    <i class="fa-solid fa-ban text-xs"></i>
                    No disponible
                </button>
            @endif

        </div>

        {{-- Mensaje para usuario no autenticado --}}
        @guest
            <div id="mensaje-auth-{{ $modalId }}"
                 class="mt-4 hidden rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">

                <div class="flex items-start gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>

                    <div>
                        <p class="font-black">
                            Debes iniciar sesión
                        </p>

                        <p class="mt-1">
                            Para inscribirte en esta oferta académica debes
                            iniciar sesión o registrarte.
                        </p>
                    </div>

                </div>
            </div>
        @endguest

    </div>
</div>

{{-- Modal de detalle --}}
<dialog id="{{ $modalId }}"
        onclick="if (event.target === this) this.close()"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-4xl overflow-hidden rounded-3xl bg-white p-0 shadow-2xl backdrop:bg-slate-950/60 backdrop:backdrop-blur-sm md:w-1/2">

    <div class="flex max-h-[90vh] flex-col">

        {{-- Encabezado del modal --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-700 to-violet-700 px-6 py-6 text-white">

            <div class="absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>

            <div class="relative flex items-start justify-between gap-5">

                <div class="flex min-w-0 items-start gap-4">

                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-2xl backdrop-blur-sm">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>

                    <div class="min-w-0">

                        <p class="text-xs font-bold uppercase tracking-wider text-blue-100">
                            Detalle de la oferta académica
                        </p>

                        <h2 class="mt-1 text-2xl font-black leading-tight">
                            {{ $oferta->carrera?->nombre ?? 'Carrera no registrada' }}
                        </h2>

                        <p class="mt-2 text-sm font-bold text-blue-100">
                            {{ $oferta->nombre ?: 'Oferta académica' }}
                        </p>

                    </div>
                </div>

                <form method="dialog">
                    <button type="submit"
                            aria-label="Cerrar modal"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-white transition hover:rotate-90 hover:bg-white/25">

                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </form>

            </div>
        </div>

        {{-- Contenido del modal --}}
        <div class="overflow-y-auto p-6">

            {{-- Información general --}}
            <div>

                <h3 class="text-lg font-black text-slate-900">
                    Información general
                </h3>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">

                    <div class="rounded-2xl bg-slate-50 p-4">

                        <p class="text-xs font-bold uppercase text-slate-400">
                            Carrera
                        </p>

                        <p class="mt-1 font-black text-slate-800">

                            @if ($oferta->carrera?->codigo)
                                {{ $oferta->carrera->codigo }} -
                            @endif

                            {{ $oferta->carrera?->nombre ?? 'No registrada' }}
                        </p>

                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">

                        <p class="text-xs font-bold uppercase text-slate-400">
                            Oferta académica
                        </p>

                        <p class="mt-1 font-black text-slate-800">
                            {{ $oferta->nombre ?: 'No registrada' }}
                        </p>

                    </div>

                    <div class="rounded-2xl bg-violet-50 p-4">

                        <p class="text-xs font-bold uppercase text-violet-500">
                            Régimen académico
                        </p>

                        <p class="mt-1 font-black text-violet-800">
                            {{ $regimen }}
                        </p>

                    </div>

                    <div class="rounded-2xl bg-amber-50 p-4">

                        <p class="text-xs font-bold uppercase text-amber-600">
                            Duración
                        </p>

                        <p class="mt-1 font-black text-amber-800">
                            {{ $duracion }}
                        </p>

                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">

                        <p class="text-xs font-bold uppercase text-slate-400">
                            Periodo académico
                        </p>

                        <p class="mt-1 font-black text-slate-800">

                            {{ $oferta->periodoAcademico?->nombre ?? 'No registrado' }}

                            @if ($oferta->periodoAcademico?->gestion)
                                - {{ $oferta->periodoAcademico->gestion }}
                            @endif

                        </p>

                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">

                        <p class="text-xs font-bold uppercase text-slate-400">
                            Docente
                        </p>

                        <p class="mt-1 font-black text-slate-800">
                            {{ $oferta->docenteDetalle?->user?->nombreCompleto()
                                ?? 'No asignado'
                            }}
                        </p>

                    </div>

                </div>
            </div>

            {{-- Cupos y fechas --}}
            <div class="mt-7">

                <h3 class="text-lg font-black text-slate-900">
                    Cupos y fechas
                </h3>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">

                    <div class="rounded-2xl bg-blue-50 p-4">

                        <p class="text-xs font-bold uppercase text-blue-500">
                            Cupos totales
                        </p>

                        <p class="mt-1 text-xl font-black text-blue-800">
                            {{ $oferta->cantidad_cupos ?? 0 }}
                        </p>

                    </div>

                    <div class="rounded-2xl bg-emerald-50 p-4">

                        <p class="text-xs font-bold uppercase text-emerald-500">
                            Cupos disponibles
                        </p>

                        <p class="mt-1 text-xl font-black text-emerald-800">
                            {{ $oferta->cupos_disponibles ?? 0 }}
                        </p>

                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">

                        <p class="text-xs font-bold uppercase text-slate-400">
                            Fecha de inicio
                        </p>

                        <p class="mt-1 font-black text-slate-800">
                            {{ $oferta->fecha_inicio
                                ? $oferta->fecha_inicio->format('d/m/Y')
                                : 'No definida'
                            }}
                        </p>

                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">

                        <p class="text-xs font-bold uppercase text-slate-400">
                            Fecha de finalización
                        </p>

                        <p class="mt-1 font-black text-slate-800">
                            {{ $oferta->fecha_fin
                                ? $oferta->fecha_fin->format('d/m/Y')
                                : 'No definida'
                            }}
                        </p>

                    </div>

                </div>
            </div>

            {{-- Precios --}}
            <div class="mt-7">

                <h3 class="text-lg font-black text-slate-900">
                    Precios
                </h3>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">

                    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                                <i class="fa-solid fa-file-signature"></i>
                            </div>

                            <div>

                                <p class="text-xs font-bold uppercase text-blue-500">
                                    Matrícula
                                </p>

                                <p class="mt-1 text-lg font-black text-blue-800">
                                    Bs {{ number_format(
                                        (float) ($oferta->precio_matricula ?? 0),
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </p>

                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-100 text-cyan-700">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>

                            <div>

                                <p class="text-xs font-bold uppercase text-cyan-600">
                                    Mensualidad
                                </p>

                                <p class="mt-1 text-lg font-black text-cyan-800">
                                    Bs {{ number_format(
                                        (float) ($oferta->precio_mensualidad ?? 0),
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </p>

                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 sm:col-span-2">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                                <i class="fa-solid fa-money-check-dollar"></i>
                            </div>

                            <div>

                                <p class="text-xs font-bold uppercase text-amber-600">
                                    Carrera completa
                                </p>

                                <p class="mt-1 text-xl font-black text-amber-800">
                                    Bs {{ number_format(
                                        (float) ($oferta->precio_carrera_completa ?? 0),
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </p>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Estado --}}
            <div class="mt-7 rounded-2xl bg-slate-50 p-4">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="text-xs font-bold uppercase text-slate-400">
                            Estado de la oferta
                        </p>

                        @if ($oferta->estado)
                            <span class="mt-2 inline-flex items-center gap-2 rounded-full bg-green-100 px-4 py-2 text-sm font-black text-green-700">

                                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                Oferta activa
                            </span>
                        @else
                            <span class="mt-2 inline-flex items-center gap-2 rounded-full bg-red-100 px-4 py-2 text-sm font-black text-red-700">

                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                Oferta inactiva
                            </span>
                        @endif

                    </div>

                    <div class="text-right">

                        <p class="text-xs font-bold uppercase text-slate-400">
                            Disponibilidad
                        </p>

                        <p class="mt-1 font-black {{ $oferta->cupos_disponibles > 0 ? 'text-green-700' : 'text-red-700' }}">

                            {{ $oferta->cupos_disponibles > 0
                                ? $oferta->cupos_disponibles . ' cupos'
                                : 'Sin cupos'
                            }}
                        </p>

                    </div>

                </div>
            </div>

            {{-- Mensaje dentro del modal para invitados --}}
            @guest
                <div id="mensaje-modal-auth-{{ $modalId }}"
                     class="mt-5 hidden rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">

                    <div class="flex items-start gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>

                        <div>

                            <p class="font-black">
                                Debes iniciar sesión
                            </p>

                            <p class="mt-1">
                                Para inscribirte en esta oferta académica debes
                                iniciar sesión o registrarte.
                            </p>

                        </div>

                    </div>
                </div>
            @endguest

        </div>

        {{-- Pie del modal --}}
        <div class="border-t border-slate-200 bg-white p-5">

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <form method="dialog">

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-100 px-6 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-200 sm:w-auto">

                        Cerrar
                    </button>

                </form>

                @if ($oferta->estado && $oferta->cupos_disponibles > 0)

                    @auth
                        <a href="{{ route('public.ofertas.inscribirse', $oferta) }}"
                           class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 sm:w-auto">

                            <i class="fa-solid fa-user-plus text-xs"></i>
                            Inscribirme
                        </a>
                    @else
                        <button type="button"
                                onclick="mostrarMensajeModalAutenticacion('{{ $modalId }}')"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 sm:w-auto">

                            <i class="fa-solid fa-user-plus text-xs"></i>
                            Inscribirme
                        </button>
                    @endauth

                @else
                    <button type="button"
                            disabled
                            class="inline-flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-2xl bg-slate-300 px-6 py-3 text-sm font-black text-slate-500 sm:w-auto">

                        <i class="fa-solid fa-ban text-xs"></i>
                        Inscripción no disponible
                    </button>
                @endif

            </div>
        </div>

    </div>
</dialog>

@once
    <script>
        function mostrarMensajeAutenticacion(modalId) {
            const mensaje = document.getElementById(
                'mensaje-auth-' + modalId
            );

            if (!mensaje) {
                return;
            }

            mensaje.classList.remove('hidden');
            mensaje.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        function mostrarMensajeModalAutenticacion(modalId) {
            const mensaje = document.getElementById(
                'mensaje-modal-auth-' + modalId
            );

            if (!mensaje) {
                return;
            }

            mensaje.classList.remove('hidden');
            mensaje.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }
    </script>
@endonce