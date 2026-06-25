<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Docente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100">

<div class="flex min-h-screen items-center justify-center px-5">
    <div class="w-full max-w-5xl animate-[fadeUp_.4s_ease-out] rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Panel del Docente</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Bienvenido, {{ auth()->user()->nombreCompleto() }}
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                    Salir
                </button>
            </form>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-3xl bg-blue-50 p-5">
                <p class="text-sm font-bold text-blue-600">Mis materias</p>
                <p class="mt-3 text-4xl font-black text-slate-900">0</p>
            </div>

            <div class="rounded-3xl bg-emerald-50 p-5">
                <p class="text-sm font-bold text-emerald-600">Mis horarios</p>
                <p class="mt-3 text-4xl font-black text-slate-900">0</p>
            </div>

            <div class="rounded-3xl bg-violet-50 p-5">
                <p class="text-sm font-bold text-violet-600">Seguimiento</p>
                <p class="mt-3 text-4xl font-black text-slate-900">0</p>
            </div>

            <div class="rounded-3xl bg-amber-50 p-5">
                <p class="text-sm font-bold text-amber-600">Alumnos</p>
                <p class="mt-3 text-4xl font-black text-slate-900">0</p>
            </div>
        </div>

    </div>
</div>

</body>
</html>