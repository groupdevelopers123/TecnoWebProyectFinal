<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión — Instituto Andrés Ibáñez</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-slate-100">

    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">

        {{-- Elementos decorativos del fondo --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">

            <div class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-blue-200/50 blur-3xl"></div>

            <div class="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-violet-200/50 blur-3xl"></div>

        </div>

        {{-- Contenedor del login --}}
        <div class="relative z-10 w-full max-w-md">

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-300/40">

                {{-- Encabezado --}}
                <div class="bg-gradient-to-br from-blue-700 via-blue-600 to-violet-700 px-8 py-8 text-center text-white">

                    
                    <img
                        src="{{ asset('img/logo_2.png') }}"
                        alt="Logo Instituto Andrés Ibáñez"
                        class="mx-auto h-20 w-auto object-contain"
                    >
                    
                    <h1 class="mt-5 text-2xl font-black">
                        Iniciar sesión
                    </h1>

                    <p class="mt-2 text-sm text-blue-100">
                        Ingresa tus credenciales para acceder al sistema
                    </p>

                </div>

                {{-- Contenido del formulario --}}
                <div class="px-7 py-8 sm:px-9">

                    {{-- Mensaje de sesión --}}
                    @if (session('status'))
                        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Mensaje informativo --}}
                    @if (session('info'))
                        <div class="mb-5 flex items-start gap-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">

                            <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>

                            <p>
                                {{ session('info') }}
                            </p>

                        </div>
                    @endif

                    {{-- Errores de validación --}}
                    @if ($errors->any())
                        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">

                            <div class="flex items-start gap-3">

                                <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-red-500"></i>

                                <div>
                                    <p class="font-black">
                                        No se pudo iniciar sesión
                                    </p>

                                    <ul class="mt-2 list-inside list-disc space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>

                            </div>

                        </div>
                    @endif

                    {{-- Formulario --}}
                    <form method="POST"
                          action="{{ route('login.store') }}"
                          class="space-y-5">

                        @csrf

                        {{-- Correo --}}
                        <div>

                            <label for="email"
                                   class="mb-2 block text-sm font-bold text-slate-700">

                                Correo electrónico
                            </label>

                            <div class="relative">

                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <i class="fa-solid fa-envelope text-sm"></i>
                                </span>

                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                       autocomplete="email"
                                       placeholder="ejemplo@instituto.com"
                                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">

                            </div>

                        </div>

                        {{-- Contraseña --}}
                        <div>

                            <label for="password"
                                   class="mb-2 block text-sm font-bold text-slate-700">

                                Contraseña
                            </label>

                            <div class="relative">

                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <i class="fa-solid fa-lock text-sm"></i>
                                </span>

                                <input type="password"
                                       id="password"
                                       name="password"
                                       required
                                       autocomplete="current-password"
                                       placeholder="Ingresa tu contraseña"
                                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">

                                <button type="button"
                                        id="toggle-password"
                                        aria-label="Mostrar contraseña"
                                        class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-600">

                                    <i id="password-icon"
                                       class="fa-solid fa-eye text-sm"></i>

                                </button>

                            </div>

                        </div>

                        {{-- Recordar sesión y recuperar contraseña --}}
                        <div class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:justify-between">

                            <label class="inline-flex cursor-pointer items-center gap-2 font-semibold text-slate-600">

                                <input type="checkbox"
                                       name="remember"
                                       value="1"
                                       @checked(old('remember'))
                                       class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">

                                Recordar sesión
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="font-bold text-blue-600 transition hover:text-blue-800">

                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif

                        </div>

                        {{-- Botón de inicio --}}
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-blue-600/25 transition hover:-translate-y-0.5 hover:bg-blue-700">

                            <i class="fa-solid fa-right-to-bracket text-xs"></i>

                            Iniciar sesión
                        </button>

                    </form>

                    {{-- Registro --}}
                    @if (Route::has('register'))

                        <div class="my-6 flex items-center gap-3">

                            <div class="h-px flex-1 bg-slate-200"></div>

                            <span class="text-xs font-bold text-slate-400">
                                ¿No tienes una cuenta?
                            </span>

                            <div class="h-px flex-1 bg-slate-200"></div>

                        </div>

                        <a href="{{ route('register') }}"
                           class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-6 py-3 text-sm font-black text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">

                            <i class="fa-solid fa-user-plus text-xs"></i>

                            Crear una cuenta
                        </a>

                    @endif

                </div>
            </div>

        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('toggle-password');
            const passwordIcon = document.getElementById('password-icon');

            if (!passwordInput || !toggleButton || !passwordIcon) {
                return;
            }

            toggleButton.addEventListener('click', function () {
                const isHidden = passwordInput.type === 'password';

                passwordInput.type = isHidden ? 'text' : 'password';

                passwordIcon.classList.toggle('fa-eye', !isHidden);
                passwordIcon.classList.toggle('fa-eye-slash', isHidden);

                toggleButton.setAttribute(
                    'aria-label',
                    isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña'
                );
            });
        });
    </script>

</body>

</html>