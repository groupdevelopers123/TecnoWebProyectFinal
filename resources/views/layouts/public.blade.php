<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Instituto Andrés Ibáñez')
    </title>

    {{-- CSS y JavaScript de las vistas Blade --}}
    @vite('resources/js/app.js')

    {{-- Font Awesome, solo si ya lo usabas mediante CDN --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">
    @include('partials.headerPublico')

    <main>
        @yield('content')
    </main>
</body>
</html>