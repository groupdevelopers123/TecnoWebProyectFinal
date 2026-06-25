
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Registro de alumno - Instituto Andrés Ibáñez</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="min-h-screen bg-slate-100">

<div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(37,99,235,0.22),_transparent_34%),linear-gradient(135deg,_#eff6ff,_#f8fafc)] px-4 py-10">

    <div class="mx-auto w-full max-w-5xl animate-[fadeUp_.45s_ease-out] overflow-hidden rounded-[2rem] border border-white/70 bg-white/90 shadow-2xl shadow-slate-900/10 backdrop-blur-xl">

        <div class="border-b border-slate-100 bg-gradient-to-r from-blue-700 via-blue-600 to-sky-500 px-6 py-8 text-white sm:px-10">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                <a href="{{ route('welcome') }}"
                   class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-white/15 text-2xl font-black text-white ring-1 ring-white/20 transition hover:-translate-y-0.5 hover:bg-white/20">
                    IA
                </a>

                <div>
                    <h1 class="text-3xl font-black tracking-tight">
                        Registro de alumno
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-50">
                        Crea tu cuenta para acceder al sistema e iniciar tu proceso de inscripción académica.
                    </p>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-10">

            @if (session('info'))
                <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-bold text-blue-700">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-info mt-0.5"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-700">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>

                        <div>
                            <p class="font-bold">Revisa los siguientes errores:</p>

                            <ul class="mt-2 list-inside list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('register') }}"
                  class="space-y-8">
                @csrf

                @php
                    $ofertaSeleccionada = old(
                        'oferta_academica_id',
                        request('oferta_academica_id', $ofertaAcademicaId ?? null)
                    );
                @endphp

                @if ($ofertaSeleccionada)
                    <input type="hidden"
                           name="oferta_academica_id"
                           value="{{ $ofertaSeleccionada }}">

                    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                                <i class="fa-solid fa-book-open-reader"></i>
                            </div>

                            <div>
                                <p class="text-sm font-black text-blue-800">
                                    Oferta académica seleccionada
                                </p>

                                <p class="mt-1 text-sm leading-6 text-blue-700">
                                    Al completar tu registro podrás continuar el proceso de inscripción a la oferta seleccionada.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <section>
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div>
                            <h2 class="text-lg font-black text-slate-900">
                                Datos personales
                            </h2>

                            <p class="text-sm text-slate-500">
                                Información principal del estudiante.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">

                        <div>
                            <label for="nombres"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Nombres
                            </label>

                            <input type="text"
                                   id="nombres"
                                   name="nombres"
                                   value="{{ old('nombres') }}"
                                   placeholder="Ejemplo: Juan Carlos"
                                   required
                                   autofocus
                                   autocomplete="given-name"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="apellidos"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Apellidos
                            </label>

                            <input type="text"
                                   id="apellidos"
                                   name="apellidos"
                                   value="{{ old('apellidos') }}"
                                   placeholder="Ejemplo: Pérez Gómez"
                                   required
                                   autocomplete="family-name"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="ci"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Cédula de identidad
                            </label>

                            <input type="text"
                                   id="ci"
                                   name="ci"
                                   value="{{ old('ci') }}"
                                   placeholder="Ejemplo: 12345678"
                                   required
                                   maxlength="20"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="fecha_nacimiento"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Fecha de nacimiento
                            </label>

                            <input type="date"
                                   id="fecha_nacimiento"
                                   name="fecha_nacimiento"
                                   value="{{ old('fecha_nacimiento') }}"
                                   max="{{ now()->format('Y-m-d') }}"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="telefono"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Teléfono
                            </label>

                            <input type="text"
                                   id="telefono"
                                   name="telefono"
                                   value="{{ old('telefono') }}"
                                   placeholder="Ejemplo: 70000000"
                                   maxlength="30"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="email"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Correo electrónico
                            </label>

                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="alumno@correo.com"
                                   required
                                   autocomplete="email"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div class="md:col-span-2">
                            <label for="direccion"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Dirección
                            </label>

                            <input type="text"
                                   id="direccion"
                                   name="direccion"
                                   value="{{ old('direccion') }}"
                                   placeholder="Ejemplo: Av. Principal, calle y número"
                                   maxlength="200"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>

                    </div>
                </section>

                <section class="border-t border-slate-100 pt-8">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>

                        <div>
                            <h2 class="text-lg font-black text-slate-900">
                                Datos académicos
                            </h2>

                            <p class="text-sm text-slate-500">
                                Información académica previa del estudiante.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">

                        <div>
                            <label for="colegio_origen"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Colegio de origen
                            </label>

                            <input type="text"
                                   id="colegio_origen"
                                   name="colegio_origen"
                                   value="{{ old('colegio_origen') }}"
                                   placeholder="Nombre de la unidad educativa"
                                   maxlength="150"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="anio_bachillerato"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Año de bachillerato
                            </label>

                            <input type="number"
                                   id="anio_bachillerato"
                                   name="anio_bachillerato"
                                   value="{{ old('anio_bachillerato') }}"
                                   min="1950"
                                   max="{{ now()->year }}"
                                   placeholder="Ejemplo: {{ now()->year }}"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div class="md:col-span-2">
                            <label for="estado_academico"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Estado académico
                            </label>

                            <select id="estado_academico"
                                    name="estado_academico"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                <option value="nuevo"
                                    @selected(old('estado_academico', 'nuevo') === 'nuevo')>
                                    Nuevo
                                </option>

                                <option value="bachiller"
                                    @selected(old('estado_academico') === 'bachiller')>
                                    Bachiller
                                </option>

                                <option value="universitario"
                                    @selected(old('estado_academico') === 'universitario')>
                                    Universitario
                                </option>

                                <option value="profesional"
                                    @selected(old('estado_academico') === 'profesional')>
                                    Profesional
                                </option>
                            </select>
                        </div>

                    </div>
                </section>

                <section class="border-t border-slate-100 pt-8">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-green-50 text-green-700">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>

                        <div>
                            <h2 class="text-lg font-black text-slate-900">
                                Seguridad de la cuenta
                            </h2>

                            <p class="text-sm text-slate-500">
                                Crea una contraseña segura para ingresar al sistema.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">

                        <div>
                            <label for="password"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Contraseña
                            </label>

                            <div class="relative">
                                <input type="password"
                                       id="password"
                                       name="password"
                                       required
                                       autocomplete="new-password"
                                       placeholder="Ingrese una contraseña"
                                       class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                                <button type="button"
                                        data-password-target="password"
                                        class="btn-ver-password absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-600">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Confirmar contraseña
                            </label>

                            <div class="relative">
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       required
                                       autocomplete="new-password"
                                       placeholder="Repita la contraseña"
                                       class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                                <button type="button"
                                        data-password-target="password_confirmation"
                                        class="btn-ver-password absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-600">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </section>

                <div class="flex flex-col gap-4 border-t border-slate-100 pt-8 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-sm font-bold text-slate-700">
                            ¿Ya tienes una cuenta?
                        </p>

                        <a href="{{ route('login') }}"
                           class="mt-1 inline-flex items-center gap-2 text-sm font-bold text-blue-700 transition hover:text-blue-800">
                            Iniciar sesión
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-7 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/25 transition hover:-translate-y-0.5 hover:bg-blue-700">
                        <i class="fa-solid fa-user-plus text-xs"></i>
                        Crear cuenta de alumno
                    </button>
                </div>

            </form>

            <a href="{{ route('welcome') }}"
               class="mt-8 inline-flex w-full items-center justify-center gap-2 text-sm font-bold text-slate-500 transition hover:text-blue-700">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Volver a la página principal
            </a>

        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const botonesPassword = document.querySelectorAll('.btn-ver-password');

        botonesPassword.forEach(function (boton) {
            boton.addEventListener('click', function () {
                const idInput = boton.dataset.passwordTarget;
                const input = document.getElementById(idInput);
                const icono = boton.querySelector('i');

                if (!input || !icono) {
                    return;
                }

                const esPassword = input.type === 'password';

                input.type = esPassword ? 'text' : 'password';

                icono.classList.toggle('fa-eye', !esPassword);
                icono.classList.toggle('fa-eye-slash', esPassword);
            });
        });
    });
</script>

</body>
</html>

