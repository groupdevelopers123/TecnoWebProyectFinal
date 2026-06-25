
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

        <div
    class="border-b border-slate-100 bg-gradient-to-r from-blue-700 via-blue-600 to-sky-500 px-6 py-8 text-white sm:px-10 sm:py-10"
>
    <div
        class="mx-auto flex max-w-5xl flex-col items-center gap-6 text-center sm:flex-row sm:items-center sm:gap-8 sm:text-left"
    >
        <!-- Logo -->
        <img
            src="{{ asset('img/logo_2.png') }}"
            alt="Logo Instituto Andrés Ibáñez"
            class="h-20 w-auto max-w-[180px] shrink-0 object-contain sm:h-24"
        >

        <!-- Información -->
        <div class="min-w-0">
            <p
                class="text-xs font-bold uppercase tracking-[0.25em] text-blue-100"
            >
                Instituto Andrés Ibáñez
            </p>

            <h1
                class="mt-2 text-3xl font-black tracking-tight sm:text-4xl"
            >
                Registro de alumno
            </h1>

            <p
                class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-blue-50 sm:mx-0 sm:text-base"
            >
                Crea tu cuenta para acceder al sistema e iniciar tu proceso
                de inscripción académica.
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
                                   data-validate="nombres"
                                   aria-describedby="nombres-error"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <p id="nombres-error" aria-live="polite" class="mt-1 text-sm text-red-600 {{ $errors->has('nombres') ? '' : 'hidden' }}">
                                {{ $errors->first('nombres') }}
                            </p>
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
                                   data-validate="apellidos"
                                   aria-describedby="apellidos-error"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <p id="apellidos-error" aria-live="polite" class="mt-1 text-sm text-red-600 {{ $errors->has('apellidos') ? '' : 'hidden' }}">
                                {{ $errors->first('apellidos') }}
                            </p>
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
                                   data-validate="ci"
                                   aria-describedby="ci-error"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <p id="ci-error" aria-live="polite" class="mt-1 text-sm text-red-600 {{ $errors->has('ci') ? '' : 'hidden' }}">
                                {{ $errors->first('ci') }}
                            </p>
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
                                   data-validate="fecha_nacimiento"
                                   aria-describedby="fecha_nacimiento-error"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <p id="fecha_nacimiento-error" aria-live="polite" class="mt-1 text-sm text-red-600 {{ $errors->has('fecha_nacimiento') ? '' : 'hidden' }}">
                                {{ $errors->first('fecha_nacimiento') }}
                            </p>
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
                                   data-validate="telefono"
                                   aria-describedby="telefono-error"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <p id="telefono-error" aria-live="polite" class="mt-1 text-sm text-red-600 {{ $errors->has('telefono') ? '' : 'hidden' }}">
                                {{ $errors->first('telefono') }}
                            </p>
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
                                   data-validate="email"
                                   aria-describedby="email-error"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <p id="email-error" aria-live="polite" class="mt-1 text-sm text-red-600 {{ $errors->has('email') ? '' : 'hidden' }}">
                                {{ $errors->first('email') }}
                            </p>
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
                                   data-validate="direccion"
                                   aria-describedby="direccion-error"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <p id="direccion-error" aria-live="polite" class="mt-1 text-sm text-red-600 {{ $errors->has('direccion') ? '' : 'hidden' }}">
                                {{ $errors->first('direccion') }}
                            </p>
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
                                   data-validate="colegio_origen"
                                   aria-describedby="colegio_origen-error"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <p id="colegio_origen-error" aria-live="polite" class="mt-1 text-sm text-red-600 {{ $errors->has('colegio_origen') ? '' : 'hidden' }}">
                                {{ $errors->first('colegio_origen') }}
                            </p>
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
                                   data-validate="anio_bachillerato"
                                   aria-describedby="anio_bachillerato-error"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                            <p id="anio_bachillerato-error" aria-live="polite" class="mt-1 text-sm text-red-600 {{ $errors->has('anio_bachillerato') ? '' : 'hidden' }}">
                                {{ $errors->first('anio_bachillerato') }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="estado_academico"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Estado académico
                            </label>

                            <select id="estado_academico"
                                    name="estado_academico"
                                    data-validate="estado_academico"
                                    aria-describedby="estado_academico-error"
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

                            <p id="estado_academico-error" aria-live="polite" class="mt-1 text-sm text-red-600 {{ $errors->has('estado_academico') ? '' : 'hidden' }}">
                                {{ $errors->first('estado_academico') }}
                            </p>
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
                                       data-validate="password"
                                       aria-describedby="password-error"
                                       class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                                <button type="button"
                                        data-password-target="password"
                                        class="btn-ver-password absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-600">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>

                            <p id="password-error" aria-live="polite" class="mt-1 text-sm text-red-600 {{ $errors->has('password') ? '' : 'hidden' }}">
                                {{ $errors->first('password') }}
                            </p>
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
                                       data-validate="password_confirmation"
                                       aria-describedby="password_confirmation-error"
                                       class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

                                <button type="button"
                                        data-password-target="password_confirmation"
                                        class="btn-ver-password absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-600">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>

                            <p id="password_confirmation-error" aria-live="polite" class="mt-1 text-sm text-red-600 hidden"></p>
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

            const form = document.querySelector('form[action="{{ route('register') }}"]');
            const validationRules = {
                nombres: {
                    required: 'Debe ingresar sus nombres.',
                    pattern: 'Los nombres solo pueden contener letras y espacios.',
                    maxLength: 100,
                },
                apellidos: {
                    required: 'Debe ingresar sus apellidos.',
                    pattern: 'Los apellidos solo pueden contener letras y espacios.',
                    maxLength: 100,
                },
                ci: {
                    required: 'Debe ingresar su Cédula de identidad.',
                    pattern: 'La cédula solo puede contener números y guiones.',
                    maxLength: 20,
                },
                fecha_nacimiento: {
                    invalid: 'Ingrese una fecha de nacimiento válida.',
                    past: 'La fecha de nacimiento no puede ser futura.',
                },
                telefono: {
                    pattern: 'El teléfono solo puede contener números, espacios, guiones o signos +.',
                    maxLength: 30,
                },
                email: {
                    required: 'Debe ingresar su correo electrónico.',
                    invalid: 'Debe ingresar un correo electrónico válido.',
                    maxLength: 150,
                },
                direccion: {
                    maxLength: 200,
                },
                colegio_origen: {
                    maxLength: 150,
                },
                anio_bachillerato: {
                    invalid: 'Ingrese un año válido de bachillerato.',
                    min: 'El año de bachillerato no puede ser anterior a 1950.',
                    max: 'El año de bachillerato no puede ser posterior a {{ now()->year }}.',
                },
                estado_academico: {
                    invalid: 'Debe seleccionar un estado académico válido.',
                },
                password: {
                    required: 'Debe ingresar una contraseña.',
                    minLength: 'La contraseña debe tener al menos 8 caracteres.',
                    pattern: 'La contraseña debe incluir mayúsculas, minúsculas, números y símbolos.',
                },
                password_confirmation: {
                    required: 'Debe confirmar su contraseña.',
                    mismatch: 'Las contraseñas no coinciden.',
                },
            };

            const getMessageElement = (fieldName) => document.getElementById(`${fieldName}-error`);
            const getField = (fieldName) => document.querySelector(`[name="${fieldName}"]`);

            const validators = {
                nombres(value) {
                    if (!value.trim()) return validationRules.nombres.required;
                    if (value.length > validationRules.nombres.maxLength) return `Los nombres no pueden tener más de ${validationRules.nombres.maxLength} caracteres.`;
                    if (!/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+$/.test(value)) return validationRules.nombres.pattern;
                    return '';
                },
                apellidos(value) {
                    if (!value.trim()) return validationRules.apellidos.required;
                    if (value.length > validationRules.apellidos.maxLength) return `Los apellidos no pueden tener más de ${validationRules.apellidos.maxLength} caracteres.`;
                    if (!/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+$/.test(value)) return validationRules.apellidos.pattern;
                    return '';
                },
                ci(value) {
                    if (!value.trim()) return validationRules.ci.required;
                    if (value.length > validationRules.ci.maxLength) return `La cédula no puede tener más de ${validationRules.ci.maxLength} caracteres.`;
                    if (!/^[0-9\-]+$/.test(value)) return validationRules.ci.pattern;
                    return '';
                },
                fecha_nacimiento(value) {
                    if (!value) return '';
                    const fecha = new Date(value);
                    const hoy = new Date();
                    if (Number.isNaN(fecha.getTime())) return validationRules.fecha_nacimiento.invalid;
                    if (fecha > hoy) return validationRules.fecha_nacimiento.past;
                    return '';
                },
                telefono(value) {
                    if (!value.trim()) return '';
                    if (value.length > validationRules.telefono.maxLength) return `El teléfono no puede tener más de ${validationRules.telefono.maxLength} caracteres.`;
                    if (!/^[0-9 +\-]+$/.test(value)) return validationRules.telefono.pattern;
                    return '';
                },
                email(value) {
                    if (!value.trim()) return validationRules.email.required;
                    if (value.length > validationRules.email.maxLength) return `El correo no puede tener más de ${validationRules.email.maxLength} caracteres.`;
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) return validationRules.email.invalid;
                    return '';
                },
                direccion(value) {
                    if (!value.trim()) return '';
                    if (value.length > validationRules.direccion.maxLength) return `La dirección no puede tener más de ${validationRules.direccion.maxLength} caracteres.`;
                    return '';
                },
                colegio_origen(value) {
                    if (!value.trim()) return '';
                    if (value.length > validationRules.colegio_origen.maxLength) return `El nombre del colegio no puede tener más de ${validationRules.colegio_origen.maxLength} caracteres.`;
                    return '';
                },
                anio_bachillerato(value) {
                    if (!value) return '';
                    const numberValue = Number(value);
                    if (Number.isNaN(numberValue)) return validationRules.anio_bachillerato.invalid;
                    if (numberValue < 1950) return validationRules.anio_bachillerato.min;
                    if (numberValue > {{ now()->year }}) return validationRules.anio_bachillerato.max;
                    return '';
                },
                estado_academico(value) {
                    const validOptions = ['nuevo', 'bachiller', 'universitario', 'profesional'];
                    if (!value || !validOptions.includes(value)) return validationRules.estado_academico.invalid;
                    return '';
                },
                password(value) {
                    if (!value) return validationRules.password.required;
                    if (value.length < 8) return validationRules.password.minLength;
                    if (!/[A-Z]/.test(value) || !/[a-z]/.test(value) || !/[0-9]/.test(value) || !/[^A-Za-z0-9]/.test(value)) return validationRules.password.pattern;
                    return '';
                },
                password_confirmation(value) {
                    const passwordField = getField('password');
                    if (!value) return validationRules.password_confirmation.required;
                    if (value !== (passwordField ? passwordField.value : '')) return validationRules.password_confirmation.mismatch;
                    return '';
                },
            };

            const validateField = (fieldName) => {
                const field = getField(fieldName);
                if (!field) return true;
                const message = validators[fieldName](field.value);
                const messageElement = getMessageElement(fieldName);

                if (messageElement) {
                    messageElement.textContent = message;
                    messageElement.classList.toggle('hidden', !message);
                }

                field.classList.toggle('border-red-500', !!message);
                field.classList.toggle('focus:ring-red-100', !!message);
                return !message;
            };

            const validateAll = () => {
                let isValid = true;
                Object.keys(validators).forEach((fieldName) => {
                    const ok = validateField(fieldName);
                    if (!ok) isValid = false;
                });
                return isValid;
            };

            const inputs = document.querySelectorAll('[data-validate]');
            inputs.forEach((input) => {
                const fieldName = input.getAttribute('data-validate');
                input.addEventListener('input', () => validateField(fieldName));
                input.addEventListener('blur', () => validateField(fieldName));
                input.addEventListener('change', () => validateField(fieldName));
            });

            if (form) {
                form.addEventListener('submit', function (event) {
                    if (!validateAll()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                });
            }
        });
    </script>

</body>
</html>
