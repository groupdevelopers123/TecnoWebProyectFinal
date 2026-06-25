<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>@yield('title', 'Instituto Andrés Ibáñez')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-slate-50 text-slate-900">

@include('partials.header')

<main class="pt-28">
    @yield('content')
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