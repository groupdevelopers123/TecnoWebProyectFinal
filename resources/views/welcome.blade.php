<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Instituto Andrés Ibáñez | Sistema Académico Web</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Página pública del Instituto Andrés Ibáñez. Carreras, ofertas académicas, docentes e inscripción en línea.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-slate-50 text-slate-900">

@include('partials.header')

<main class="pt-28">

    <section class="relative overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-slate-950">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.25),transparent_35%)]"></div>
        <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-blue-300/20 blur-3xl"></div>
        <div class="absolute -right-24 top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-7xl gap-12 px-5 py-20 lg:grid-cols-2 lg:px-8 lg:py-28">

            <div class="flex flex-col justify-center">
                <span class="mb-5 inline-flex w-fit items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-wide text-blue-50 ring-1 ring-white/20">
                    <i class="fa-solid fa-school"></i>
                    Sistema Académico Web
                </span>

                <h1 class="text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Instituto Andrés Ibáñez
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-blue-50">
                    Explora nuestras carreras, revisa las ofertas académicas disponibles, conoce a los docentes y empieza tu proceso de inscripción en línea.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('public.ofertas.index') }}"
                       class="inline-flex items-center gap-2 rounded-2xl bg-white px-6 py-3 text-sm font-black text-blue-700 shadow-xl transition hover:-translate-y-0.5 hover:bg-blue-50">
                        <i class="fa-solid fa-list-check text-xs"></i>
                        Ver ofertas académicas
                    </a>

                    <a href="{{ route('public.carreras.index') }}"
                       class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-6 py-3 text-sm font-black text-white ring-1 ring-white/20 transition hover:-translate-y-0.5 hover:bg-white/20">
                        <i class="fa-solid fa-graduation-cap text-xs"></i>
                        Ver carreras
                    </a>
                </div>
            </div>

            <div class="hidden items-center justify-center lg:flex">
                <div class="relative h-[430px] w-[430px] rounded-[3rem] bg-white/10 p-8 shadow-2xl ring-1 ring-white/20 backdrop-blur">
                    <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/20"></div>
                    <div class="absolute -bottom-8 -left-8 h-40 w-40 rounded-full bg-blue-300/20"></div>

                    <div class="relative flex h-full flex-col justify-between rounded-[2rem] bg-white p-8">
                        <div>
                            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-3xl text-blue-700">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>

                            <h2 class="mt-6 text-2xl font-black text-slate-900">
                                Formación académica moderna
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-slate-500">
                                Información pública de carreras, docentes, ofertas académicas y registro de alumnos desde la web.
                            </p>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="rounded-2xl bg-blue-50 p-4 text-center">
                                <p class="text-2xl font-black text-blue-700">
                                    {{ $carreras->count() }}
                                </p>
                                <p class="text-xs font-bold text-slate-500">
                                    Carreras
                                </p>
                            </div>

                            <div class="rounded-2xl bg-green-50 p-4 text-center">
                                <p class="text-2xl font-black text-green-700">
                                    {{ $ofertas->count() }}
                                </p>
                                <p class="text-xs font-bold text-slate-500">
                                    Ofertas
                                </p>
                            </div>

                            <div class="rounded-2xl bg-amber-50 p-4 text-center">
                                <p class="text-2xl font-black text-amber-700">
                                    {{ $docentes->count() }}
                                </p>
                                <p class="text-xs font-bold text-slate-500">
                                    Docentes
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-2 text-sm font-black uppercase tracking-wide text-blue-600">
                    Carreras
                </p>
                <h2 class="text-3xl font-black text-slate-900">
                    Carreras destacadas
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Conoce algunas de las carreras registradas en el instituto.
                </p>
            </div>

            <a href="{{ route('public.carreras.index') }}"
               class="inline-flex w-fit items-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Ver todas
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($carreras as $carrera)
                @include('public.partials.carrera-card', ['carrera' => $carrera])
            @empty
                <div class="rounded-3xl border border-slate-200 bg-white p-6 text-sm font-bold text-slate-500">
                    No hay carreras registradas.
                </div>
            @endforelse
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="mb-2 text-sm font-black uppercase tracking-wide text-green-600">
                        Ofertas académicas
                    </p>
                    <h2 class="text-3xl font-black text-slate-900">
                        Ofertas disponibles
                    </h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Revisa las ofertas activas y regístrate para iniciar tu inscripción.
                    </p>
                </div>

                <a href="{{ route('public.ofertas.index') }}"
                   class="inline-flex w-fit items-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                    Ver todas
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($ofertas as $oferta)
                    @include('public.partials.oferta-card', ['oferta' => $oferta])
                @empty
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-sm font-bold text-slate-500">
                        No hay ofertas académicas registradas.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-2 text-sm font-black uppercase tracking-wide text-amber-600">
                    Docentes
                </p>
                <h2 class="text-3xl font-black text-slate-900">
                    Equipo docente
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Conoce parte del equipo docente del instituto.
                </p>
            </div>

            <a href="{{ route('public.docentes.index') }}"
               class="inline-flex w-fit items-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Ver todos
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @forelse ($docentes as $docente)
                @include('public.partials.docente-card', ['docente' => $docente])
            @empty
                <div class="rounded-3xl border border-slate-200 bg-white p-6 text-sm font-bold text-slate-500">
                    No hay docentes registrados.
                </div>
            @endforelse
        </div>
    </section>

    <section class="bg-slate-950">
        <div class="mx-auto grid max-w-7xl gap-8 px-5 py-16 lg:grid-cols-2 lg:px-8">
            <div>
                <p class="mb-3 text-sm font-black uppercase tracking-wide text-blue-300">
                    Inscripción en línea
                </p>

                <h2 class="text-3xl font-black text-white">
                    Regístrate para iniciar tu proceso académico
                </h2>

                <p class="mt-4 max-w-xl text-sm leading-6 text-slate-300">
                    Los estudiantes pueden crear su cuenta desde la página pública y luego continuar con el proceso de inscripción a una oferta académica.
                </p>
            </div>

            <div class="flex items-center gap-3 lg:justify-end">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    Crear cuenta
                </a>

                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-6 py-3 text-sm font-black text-white ring-1 ring-white/20 transition hover:-translate-y-0.5 hover:bg-white/20">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    Iniciar sesión
                </a>
            </div>
        </div>
    </section>

</main>

<footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-8 text-sm text-slate-500 lg:px-8">
        <p class="font-black text-slate-900">
            Instituto Andrés Ibáñez
        </p>
        <p>
            Sistema Académico Web para gestión de carreras, ofertas académicas, docentes, inscripciones y pagos.
        </p>
    </div>
</footer>

</body>
</html>