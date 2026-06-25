@php
    $panelRoute = null;

    if (auth()->check()) {
        $panelRoute = match (auth()->user()->role->nombre ?? null) {
            'propietario', 'secretaria' => route('admin.dashboard'),
            'docente'                   => route('docente.home'),
            'alumno'                    => route('alumno.home'),
            default                     => route('welcome'),
        };
    }
@endphp

<header class="fixed left-0 top-0 z-50 w-full bg-white/92 shadow-sm backdrop-blur-xl" style="border-bottom: 0.5px solid rgba(15,23,42,0.10);">

    {{-- Barra principal --}}
    <div class="mx-auto flex h-[68px] max-w-7xl items-center justify-between gap-4 px-6 lg:px-8">

        {{-- Logo --}}
        <a href="{{ route('welcome') }}" class="flex flex-shrink-0 items-center gap-3">
            <div class="relative flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-[13px] bg-gradient-to-br from-blue-700 to-blue-500 text-sm font-extrabold tracking-wide text-white shadow-[0_2px_8px_rgba(37,99,235,0.28)]">
                IA
                <span class="pointer-events-none absolute inset-0 rounded-[13px] border border-white/20"></span>
            </div>
            <div>
                <p class="text-[15px] font-bold leading-tight tracking-tight text-slate-900">
                    Instituto Andrés Ibáñez
                </p>
                <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-500">
                    Sistema Académico Web
                </p>
            </div>
        </a>

        {{-- Nav desktop --}}
        <nav class="hidden flex-1 items-center justify-center gap-0.5 lg:flex">

            <a href="{{ route('welcome') }}"
               class="inline-flex items-center gap-1.5 rounded-[9px] px-4 py-2 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('welcome')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-500 hover:bg-slate-100 hover:text-blue-700' }}">
                <i class="fa-solid fa-house text-xs"></i>
                Inicio
            </a>

            <a href="{{ route('public.carreras.index') }}"
               class="inline-flex items-center gap-1.5 rounded-[9px] px-4 py-2 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('public.carreras.*')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-500 hover:bg-slate-100 hover:text-blue-700' }}">
                <i class="fa-solid fa-graduation-cap text-xs"></i>
                Carreras
            </a>

            <a href="{{ route('public.ofertas.index') }}"
               class="inline-flex items-center gap-1.5 rounded-[9px] px-4 py-2 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('public.ofertas.*')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-500 hover:bg-slate-100 hover:text-blue-700' }}">
                <i class="fa-solid fa-calendar-days text-xs"></i>
                Ofertas Académicas
            </a>

            <a href="{{ route('public.docentes.index') }}"
               class="inline-flex items-center gap-1.5 rounded-[9px] px-4 py-2 text-sm font-semibold transition-colors duration-150
               {{ request()->routeIs('public.docentes.*')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-500 hover:bg-slate-100 hover:text-blue-700' }}">
                <i class="fa-solid fa-chalkboard-user text-xs"></i>
                Docentes
            </a>

        </nav>

        {{-- Acciones --}}
        <div class="flex flex-shrink-0 items-center gap-2">
            @auth
                <a href="{{ $panelRoute }}"
                   class="hidden rounded-[9px] border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 sm:inline-flex">
                    Ir al panel
                </a>
                <a href="{{ $panelRoute }}"
                   class="inline-flex items-center gap-2 rounded-[9px] bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_1px_4px_rgba(37,99,235,0.25)] transition hover:bg-blue-700">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    Entrar
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 rounded-[9px] border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    Iniciar sesión
                </a>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 rounded-[9px] bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_1px_4px_rgba(37,99,235,0.25)] transition hover:bg-blue-700">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    Registrarse
                </a>
            @endauth
        </div>

    </div>

    {{-- Divisor con acento de gradiente --}}
    <div class="h-px bg-gradient-to-r from-transparent via-blue-200/60 to-transparent"></div>

    {{-- Nav mobile --}}
    <div class="border-t border-slate-100 bg-slate-50/80 px-4 py-2.5 lg:hidden">
        <div class="grid grid-cols-4 gap-1.5">

            <a href="{{ route('welcome') }}"
               class="flex flex-col items-center gap-1 rounded-[9px] border px-1 py-2 text-[10px] font-bold uppercase tracking-wide transition
               {{ request()->routeIs('welcome')
                    ? 'border-blue-200 bg-blue-50 text-blue-700'
                    : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-100' }}">
                <i class="fa-solid fa-house text-base"></i>
                Inicio
            </a>

            <a href="{{ route('public.carreras.index') }}"
               class="flex flex-col items-center gap-1 rounded-[9px] border px-1 py-2 text-[10px] font-bold uppercase tracking-wide transition
               {{ request()->routeIs('public.carreras.*')
                    ? 'border-blue-200 bg-blue-50 text-blue-700'
                    : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-100' }}">
                <i class="fa-solid fa-graduation-cap text-base"></i>
                Carreras
            </a>

            <a href="{{ route('public.ofertas.index') }}"
               class="flex flex-col items-center gap-1 rounded-[9px] border px-1 py-2 text-[10px] font-bold uppercase tracking-wide transition
               {{ request()->routeIs('public.ofertas.*')
                    ? 'border-blue-200 bg-blue-50 text-blue-700'
                    : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-100' }}">
                <i class="fa-solid fa-calendar-days text-base"></i>
                Ofertas
            </a>

            <a href="{{ route('public.docentes.index') }}"
               class="flex flex-col items-center gap-1 rounded-[9px] border px-1 py-2 text-[10px] font-bold uppercase tracking-wide transition
               {{ request()->routeIs('public.docentes.*')
                    ? 'border-blue-200 bg-blue-50 text-blue-700'
                    : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-100' }}">
                <i class="fa-solid fa-chalkboard-user text-base"></i>
                Docentes
            </a>

        </div>
    </div>

</header>