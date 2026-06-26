<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel Administrativo')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

<div class="flex min-h-screen">

    <aside class="fixed left-0 top-0 hidden h-screen w-72 flex-col overflow-y-auto bg-slate-950 px-5 py-6 text-white shadow-2xl lg:flex">
        <div class="mb-8">
            <img
                        src="{{ asset('img/logo_2.png') }}"
                        alt="Logo Instituto Andrés Ibáñez"
                        class="mx-auto h-20 w-auto object-contain"
                    >

            <h2 class="mt-4 text-xl font-bold">
                Instituto Andrés Ibáñez
            </h2>

            <p class="mt-1 text-sm text-slate-400">
                Gestión académica web
            </p>
        </div>

        <nav class="flex flex-1 flex-col gap-2">

            <a href="{{ route('admin.dashboard') }}"
               class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
               {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                <i class="fa-solid fa-house w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            <div class="space-y-2">
                <button
                    type="button"
                    class="group flex w-full items-center justify-between gap-3 rounded-2xl bg-slate-900/80 px-4 py-3 text-left text-sm font-bold text-white transition duration-200 hover:bg-white/10"
                    data-sidebar-toggle
                    data-target="group-usuarios"
                    aria-expanded="{{ request()->routeIs('admin.usuarios.*') ? 'true' : 'false' }}"
                >
                    <span class="inline-flex items-center gap-3">
                        <i class="fa-solid fa-users w-5 text-center"></i>
                        Usuarios
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-300 transition-transform duration-200" aria-hidden="true"></i>
                </button>

                <div id="group-usuarios" class="space-y-2 pl-6 {{ request()->routeIs('admin.usuarios.*') ? '' : 'hidden' }}">
                    <a href="{{ route('admin.usuarios.index') }}"
                       class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                       {{ request()->routeIs('admin.usuarios.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                        <i class="fa-solid fa-user w-5 text-center"></i>
                        <span>Usuarios</span>
                    </a>
                </div>
            </div>

            <div class="space-y-2">
                <button
                    type="button"
                    class="group flex w-full items-center justify-between gap-3 rounded-2xl bg-slate-900/80 px-4 py-3 text-left text-sm font-bold text-white transition duration-200 hover:bg-white/10"
                    data-sidebar-toggle
                    data-target="group-aulas"
                    aria-expanded="{{ request()->routeIs('admin.aulas.*') ? 'true' : 'false' }}"
                >
                    <span class="inline-flex items-center gap-3">
                        <i class="fa-solid fa-school w-5 text-center"></i>
                        Aulas
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-300 transition-transform duration-200" aria-hidden="true"></i>
                </button>

                <div id="group-aulas" class="space-y-2 pl-6 {{ request()->routeIs('admin.aulas.*') ? '' : 'hidden' }}">
                    <a href="{{ route('admin.aulas.index') }}"
                       class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                       {{ request()->routeIs('admin.aulas.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                        <i class="fa-solid fa-door-open w-5 text-center"></i>
                        <span>Aulas</span>
                    </a>
                </div>
            </div>

            <div class="space-y-2">
                <button
                    type="button"
                    class="group flex w-full items-center justify-between gap-3 rounded-2xl bg-slate-900/80 px-4 py-3 text-left text-sm font-bold text-white transition duration-200 hover:bg-white/10"
                    data-sidebar-toggle
                    data-target="group-horarios"
                    aria-expanded="{{ request()->routeIs('admin.carreras.*') || request()->routeIs('admin.materias.*') || request()->routeIs('admin.periodos-academicos.*') || request()->routeIs('admin.horarios.*') ? 'true' : 'false' }}"
                >
                    <span class="inline-flex items-center gap-3">
                        <i class="fa-solid fa-calendar-week w-5 text-center"></i>
                        Horarios
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-300 transition-transform duration-200" aria-hidden="true"></i>
                </button>

                <div id="group-horarios" class="space-y-2 pl-6 {{ request()->routeIs('admin.carreras.*') || request()->routeIs('admin.materias.*') || request()->routeIs('admin.periodos-academicos.*') || request()->routeIs('admin.horarios.*') ? '' : 'hidden' }}">
                    <a href="{{ route('admin.carreras.index') }}"
                       class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                       {{ request()->routeIs('admin.carreras.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                        <i class="fa-solid fa-graduation-cap w-5 text-center"></i>
                        <span>Carreras</span>
                    </a>

                    <a href="{{ route('admin.materias.index') }}"
                       class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                       {{ request()->routeIs('admin.materias.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                        <i class="fa-solid fa-book-open w-5 text-center"></i>
                        <span>Materias</span>
                    </a>

                    <a href="{{ route('admin.periodos-academicos.index') }}"
                       class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                       {{ request()->routeIs('admin.periodos-academicos.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                        <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                        <span>Periodos</span>
                    </a>

                    <a href="{{ route('admin.horarios.index') }}"
                       class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                       {{ request()->routeIs('admin.horarios.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                        <i class="fa-solid fa-clock w-5 text-center"></i>
                        <span>Horarios</span>
                    </a>
                </div>
            </div>

            <div class="space-y-2">
                <button
                    type="button"
                    class="group flex w-full items-center justify-between gap-3 rounded-2xl bg-slate-900/80 px-4 py-3 text-left text-sm font-bold text-white transition duration-200 hover:bg-white/10"
                    data-sidebar-toggle
                    data-target="group-ofertas"
                    aria-expanded="{{ request()->routeIs('admin.ofertas-academicas.*') ? 'true' : 'false' }}"
                >
                    <span class="inline-flex items-center gap-3">
                        <i class="fa-solid fa-bookmark w-5 text-center"></i>
                        Ofertas académicas
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-300 transition-transform duration-200" aria-hidden="true"></i>
                </button>

                <div id="group-ofertas" class="space-y-2 pl-6 {{ request()->routeIs('admin.ofertas-academicas.*') ? '' : 'hidden' }}">
                    <a href="{{ route('admin.ofertas-academicas.index') }}"
                       class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                       {{ request()->routeIs('admin.ofertas-academicas.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                        <i class="fa-solid fa-bookmark w-5 text-center"></i>
                        <span>Ofertas académicas</span>
                    </a>
                </div>
            </div>

            <div class="space-y-2">
                <button
                    type="button"
                    class="group flex w-full items-center justify-between gap-3 rounded-2xl bg-slate-900/80 px-4 py-3 text-left text-sm font-bold text-white transition duration-200 hover:bg-white/10"
                    data-sidebar-toggle
                    data-target="group-inscripciones"
                    aria-expanded="{{ request()->routeIs('admin.inscripciones.*') ? 'true' : 'false' }}"
                >
                    <span class="inline-flex items-center gap-3">
                        <i class="fa-solid fa-clipboard-list w-5 text-center"></i>
                        Inscripciones
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-300 transition-transform duration-200" aria-hidden="true"></i>
                </button>

                <div id="group-inscripciones" class="space-y-2 pl-6 {{ request()->routeIs('admin.inscripciones.*') ? '' : 'hidden' }}">
                    <a href="{{ route('admin.inscripciones.index') }}"
                       class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                       {{ request()->routeIs('admin.inscripciones.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                        <i class="fa-solid fa-clipboard-list w-5 text-center"></i>
                        <span>Inscripciones</span>
                    </a>
                </div>
            </div>

            <div class="space-y-2">
                <button
                    type="button"
                    class="group flex w-full items-center justify-between gap-3 rounded-2xl bg-slate-900/80 px-4 py-3 text-left text-sm font-bold text-white transition duration-200 hover:bg-white/10"
                    data-sidebar-toggle
                    data-target="group-seguimiento"
                    aria-expanded="{{ request()->routeIs('admin.seguimientos-academicos.*') ? 'true' : 'false' }}"
                >
                    <span class="inline-flex items-center gap-3">
                        <i class="fa-solid fa-chart-line w-5 text-center"></i>
                        Seguimiento académico
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-300 transition-transform duration-200" aria-hidden="true"></i>
                </button>

                <div id="group-seguimiento" class="space-y-2 pl-6 {{ request()->routeIs('admin.seguimientos-academicos.*') ? '' : 'hidden' }}">
                    <a href="{{ route('admin.seguimientos-academicos.index') }}"
                       class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                       {{ request()->routeIs('admin.seguimientos-academicos.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                        <i class="fa-solid fa-chart-line w-5 text-center"></i>
                        <span>Seguimiento</span>
                    </a>
                </div>
            </div>

            <div class="space-y-2">
                <button
                    type="button"
                    class="group flex w-full items-center justify-between gap-3 rounded-2xl bg-slate-900/80 px-4 py-3 text-left text-sm font-bold text-white transition duration-200 hover:bg-white/10"
                    data-sidebar-toggle
                    data-target="group-pagos"
                    aria-expanded="{{ request()->routeIs('admin.pago-contados.*') || request()->routeIs('admin.concepto-pagos.*') ? 'true' : 'false' }}"
                >
                    <span class="inline-flex items-center gap-3">
                        <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
                        Pagos
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-300 transition-transform duration-200" aria-hidden="true"></i>
                </button>

                <div id="group-pagos" class="space-y-2 pl-6 {{ request()->routeIs('admin.pago-contados.*') || request()->routeIs('admin.concepto-pagos.*') ? '' : 'hidden' }}">
                    <a href="{{ route('admin.pago-contados.index') }}"
                       class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                       {{ request()->routeIs('admin.pago-contados.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                        <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
                        <span>Pagos</span>
                    </a>
                </div>
            </div>

            <div class="space-y-2">
                <button
                    type="button"
                    class="group flex w-full items-center justify-between gap-3 rounded-2xl bg-slate-900/80 px-4 py-3 text-left text-sm font-bold text-white transition duration-200 hover:bg-white/10"
                    data-sidebar-toggle
                    data-target="group-reportes"
                    aria-expanded="{{ request()->routeIs('admin.reportes.*') ? 'true' : 'false' }}"
                >
                    <span class="inline-flex items-center gap-3">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        Reportes
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-300 transition-transform duration-200" aria-hidden="true"></i>
                </button>

                <div id="group-reportes" class="space-y-2 pl-6 {{ request()->routeIs('admin.reportes.*') ? '' : 'hidden' }}">
                    <a href="{{ route('admin.reportes.index') }}"
                       class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                       {{ request()->routeIs('admin.reportes.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Reportes</span>
                    </a>
                </div>
            </div>

            <div class="space-y-2">
                <a href="{{ route('admin.bitacora.index') }}"
                   class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10
                   {{ request()->routeIs('admin.bitacora.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300' }}">
                    <i class="fa-solid fa-book-open w-5 text-center"></i>
                    <span>Bitácora</span>
                </a>
            </div>

            <div class="mt-auto pt-6">
                <div class="space-y-2 border-t border-slate-800 pt-4">
                    <a href="{{ route('configuraciones.show') }}"
                       class="group flex items-center gap-3 rounded-2xl bg-slate-900/80 px-4 py-3 text-sm font-medium text-white transition duration-200 hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-gear w-5 text-center"></i>
                        <span>Configuración</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-3 rounded-2xl bg-slate-800 px-4 py-3 text-sm font-medium text-white transition duration-200 hover:bg-red-600">
                            <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                            <span>Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </aside>

    <main class="w-full lg:ml-72">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/80 px-5 py-4 backdrop-blur-xl lg:px-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                        @yield('page-title', 'Panel Administrativo')
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        @yield('page-subtitle', 'Gestión académica y administrativa del instituto')
                    </p>
                </div>

                
            </div>
        </header>

        <section class="animate-[fadeUp_.35s_ease-out] p-5 lg:p-8">

            @if (session('success'))
                <div class="mb-5 flex items-center gap-3 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-700 shadow-sm">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700 shadow-sm">
                    <div class="flex items-center gap-2 font-bold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Corrige los siguientes errores:</span>
                    </div>

                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </section>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggles = document.querySelectorAll('[data-sidebar-toggle]');

        toggles.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const targetId = this.dataset.target;
                const target = targetId ? document.getElementById(targetId) : null;
                if (!target) {
                    return;
                }

                const isCollapsed = target.classList.contains('hidden');
                target.classList.toggle('hidden');
                this.setAttribute('aria-expanded', isCollapsed ? 'true' : 'false');

                const chevron = this.querySelector('.fa-chevron-down');
                if (chevron) {
                    chevron.classList.toggle('rotate-180', isCollapsed);
                }
            });
        });
    });
</script>
@stack('scripts')
</body>
</html>