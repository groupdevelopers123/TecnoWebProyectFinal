@extends('layouts.admin')

@section('title', 'Configuraciones')
@section('page-title', 'Configuraciones')
@section('page-subtitle', 'Ajusta tu experiencia visual')

@section('content')
<div class="settings-page min-h-screen bg-slate-100">
    <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">
                    Preferencias personales
                </p>

                <h1 class="mt-2 text-3xl font-black text-slate-900">
                    Ajusta tu experiencia visual
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Elige tema, modo oscuro, tamaño de letra y contraste. También puedes cambiar tu contraseña desde aquí.
                </p>
            </div>

            <div class="inline-flex items-center gap-3 rounded-3xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 shadow-sm">
                <i class="fa-solid fa-sliders-h"></i>
                Configuración global activa
            </div>
        </div>
    </section>

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

    <div class="mx-auto max-w-5xl">
        <form method="POST" action="{{ route('configuraciones.update') }}">
            @csrf
            @method('PUT')

            <input type="hidden" name="theme" id="input-theme" value="{{ old('theme', $preferences['theme'] ?? '') }}">
            <input type="hidden" name="mode" id="input-mode" value="{{ old('mode', $preferences['mode'] ?? 'light') }}">
            <input type="hidden" name="font_size" id="input-font-size" value="{{ old('font_size', $preferences['font_size'] ?? 16) }}">
            <input type="hidden" name="contrast" id="input-contrast" value="{{ old('contrast', $preferences['contrast'] ?? 'normal') }}">

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="grid gap-4 md:grid-cols-2">
                    <article class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                        <div class="flex items-center gap-3 text-slate-900">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-indigo-700">
                                <i class="fa-solid fa-palette text-lg"></i>
                            </div>

                            <div>
                                <p class="font-semibold">Tema</p>
                                <p class="mt-1 text-sm text-slate-500">Elige el estilo de colores y fondo.</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3" id="theme-options">
                            @php
                                $selectedTheme = old('theme', $preferences['theme'] ?? '');
                            @endphp

                            @foreach ([
                                ['value' => 'ninos', 'label' => 'Niños', 'description' => 'Diseño entretenido y cálido.', 'icon' => 'fa-solid fa-child'],
                                ['value' => 'adultos', 'label' => 'Adultos', 'description' => 'Estilo serio y profesional.', 'icon' => 'fa-solid fa-user-tie'],
                                ['value' => 'jovenes', 'label' => 'Jóvenes', 'description' => 'Opción fresca y moderna.', 'icon' => 'fa-solid fa-graduation-cap'],
                            ] as $option)
                                <button
                                    type="button"
                                    class="visual-option flex items-start gap-3 rounded-3xl border p-4 text-left text-sm transition duration-200 hover:border-slate-300 hover:bg-white"
                                    data-field="theme"
                                    data-value="{{ $option['value'] }}"
                                    aria-pressed="{{ $selectedTheme === $option['value'] ? 'true' : 'false' }}"
                                >
                                    <div class="visual-option__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-lg">
                                        <i class="{{ $option['icon'] }}"></i>
                                    </div>

                                    <div>
                                        <p class="font-semibold">{{ $option['label'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $option['description'] }}</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </article>

                    <article class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                        <div class="flex items-center gap-3 text-slate-900">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                                <i class="fa-solid fa-circle-half-stroke text-lg"></i>
                            </div>

                            <div>
                                <p class="font-semibold">Modo</p>
                                <p class="mt-1 text-sm text-slate-500">Cambia entre luz suave y estilo oscuro.</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2" id="mode-options">
                            @php
                                $selectedMode = old('mode', $preferences['mode'] ?? 'light');
                            @endphp

                            @foreach ([
                                ['value' => 'light', 'label' => 'Claro', 'description' => 'Mejor para entornos iluminados.', 'icon' => 'fa-solid fa-sun'],
                                ['value' => 'dark', 'label' => 'Oscuro', 'description' => 'Ideal para lectura nocturna.', 'icon' => 'fa-solid fa-moon'],
                            ] as $option)
                                <button
                                    type="button"
                                    class="visual-option flex items-start gap-3 rounded-3xl border p-4 text-left text-sm transition duration-200 hover:border-slate-300 hover:bg-white"
                                    data-field="mode"
                                    data-value="{{ $option['value'] }}"
                                    aria-pressed="{{ $selectedMode === $option['value'] ? 'true' : 'false' }}"
                                >
                                    <div class="visual-option__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-lg">
                                        <i class="{{ $option['icon'] }}"></i>
                                    </div>

                                    <div>
                                        <p class="font-semibold">{{ $option['label'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $option['description'] }}</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </article>

                    <article class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                        <div class="flex items-center gap-3 text-slate-900">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                                <i class="fa-solid fa-text-height text-lg"></i>
                            </div>

                            <div>
                                <p class="font-semibold">Tamaño de letra</p>
                                <p class="mt-1 text-sm text-slate-500">Ajusta la lectura sin afectar el contenido.</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="flex items-center justify-between gap-3 text-sm text-slate-500">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-font"></i>
                                    <span id="font-size-value" class="font-semibold text-slate-700">{{ old('font_size', $preferences['font_size'] ?? 16) }}px</span>
                                </div>
                                <span>Tamaño actual</span>
                            </div>

                            <input
                                type="range"
                                id="font-size-range"
                                min="12"
                                max="36"
                                step="1"
                                class="mt-3 w-full rounded-full accent-blue-600"
                                aria-label="Tamaño de letra"
                                value="{{ old('font_size', $preferences['font_size'] ?? 16) }}"
                            />

                            <div class="mt-2 flex items-center justify-between text-sm text-slate-500">
                                <span>Pequeño</span>
                                <span>Grande</span>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                        <div class="flex items-center gap-3 text-slate-900">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                                <i class="fa-solid fa-eye-low-vision text-lg"></i>
                            </div>

                            <div>
                                <p class="font-semibold">Contraste</p>
                                <p class="mt-1 text-sm text-slate-500">Elige la visibilidad que te sea más cómoda.</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2" id="contrast-options">
                            @php
                                $selectedContrast = old('contrast', $preferences['contrast'] ?? 'normal');
                            @endphp

                            @foreach ([
                                ['value' => 'normal', 'label' => 'Normal', 'description' => 'Contraste estándar y agradable.', 'icon' => 'fa-solid fa-eye'],
                                ['value' => 'high', 'label' => 'Alto contraste', 'description' => 'Más legibilidad en entornos brillantes.', 'icon' => 'fa-solid fa-eye-low-vision'],
                            ] as $option)
                                <button
                                    type="button"
                                    class="visual-option flex items-start gap-3 rounded-3xl border p-4 text-left text-sm transition duration-200 hover:border-slate-300 hover:bg-white"
                                    data-field="contrast"
                                    data-value="{{ $option['value'] }}"
                                    aria-pressed="{{ $selectedContrast === $option['value'] ? 'true' : 'false' }}"
                                >
                                    <div class="visual-option__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-lg">
                                        <i class="{{ $option['icon'] }}"></i>
                                    </div>

                                    <div>
                                        <p class="font-semibold">{{ $option['label'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $option['description'] }}</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </article>
                </div>

                <article class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-700">
                            <i class="fa-solid fa-lock text-xl"></i>
                        </div>

                        <div>
                            <p class="text-lg font-semibold">Seguridad</p>
                            <p class="mt-1 text-sm text-slate-500">Cambia tu contraseña de forma segura desde el modal.</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-3xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700"
                            id="open-password-modal"
                        >
                            <i class="fa-solid fa-key"></i>
                            Cambiar contraseña
                        </button>
                    </div>
                </article>

                <div class="mt-6 flex justify-end">
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-3xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            id="reset-preferences"
                        >
                            <i class="fa-solid fa-rotate-left"></i>
                            Restablecer valores
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-3xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-blue-700"
                        >
                            <i class="fa-solid fa-check"></i>
                            Guardar configuración
                        </button>
                    </div>
                </div>
            </section>
        </form>
    </div>

    <div id="password-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm">
        <div class="w-full max-w-[55vw] min-w-[min(320px,100%)] rounded-[32px] border border-slate-700 bg-slate-950 text-slate-100 shadow-2xl ring-1 ring-slate-800">
            <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-5 text-white">
                <div class="flex items-start justify-between gap-5">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-200">Cambiar contraseña</p>
                        <h2 class="mt-2 text-2xl font-black">Actualiza tu contraseña</h2>
                    </div>
                    <button
                        type="button"
                        aria-label="Cerrar modal"
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-white transition hover:bg-white/20"
                        id="close-password-modal"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="grid gap-6 bg-slate-950 p-6 sm:p-8">
                <div class="grid gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Contraseña actual</label>
                        <input
                            type="password"
                            name="current_password"
                            class="mt-2 w-full rounded-3xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 placeholder:text-slate-500"
                        />
                        @if ($errors->has('current_password'))
                            <p class="mt-2 text-sm text-rose-400">{{ $errors->first('current_password') }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Nueva contraseña</label>
                        <input
                            type="password"
                            name="new_password"
                            class="mt-2 w-full rounded-3xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 placeholder:text-slate-500"
                        />
                        @if ($errors->has('new_password'))
                            <p class="mt-2 text-sm text-rose-400">{{ $errors->first('new_password') }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-100">Confirmar contraseña</label>
                        <input
                            type="password"
                            name="new_password_confirmation"
                            class="mt-2 w-full rounded-3xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 placeholder:text-slate-500"
                        />
                        @if ($errors->has('new_password_confirmation'))
                            <p class="mt-2 text-sm text-rose-400">{{ $errors->first('new_password_confirmation') }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-3xl border border-slate-700 bg-slate-900 px-5 py-3 text-sm font-bold text-slate-100 transition hover:border-slate-500 hover:bg-slate-800"
                        id="cancel-password-modal"
                    >
                        Cerrar
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-3xl bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700"
                    >
                        Guardar contraseña
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const optionButtons = document.querySelectorAll('[data-field]');
        const themeInput = document.getElementById('input-theme');
        const modeInput = document.getElementById('input-mode');
        const contrastInput = document.getElementById('input-contrast');
        const fontSizeInput = document.getElementById('font-size-range');
        const hiddenFontSizeInput = document.getElementById('input-font-size');
        const fontSizeValue = document.getElementById('font-size-value');
        const resetButton = document.getElementById('reset-preferences');
        const openModalButton = document.getElementById('open-password-modal');
        const closeModalButton = document.getElementById('close-password-modal');
        const cancelModalButton = document.getElementById('cancel-password-modal');
        const passwordModal = document.getElementById('password-modal');

        function updateSelectedButtons(field, value) {
            const buttons = document.querySelectorAll(`[data-field='${field}']`);

            buttons.forEach(button => {
                const isSelected = button.dataset.value === value;
                button.classList.toggle('visual-option--selected', isSelected);
                button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
            });
        }

        function getFormPreferences() {
            return {
                theme: themeInput.value || null,
                mode: modeInput.value,
                contrast: contrastInput.value,
                font_size: Number(hiddenFontSizeInput.value) || 16,
            };
        }

        function applyPreferencesToDocument(preferences) {
            const root = document.documentElement;

            if (preferences.theme) {
                root.setAttribute('data-theme', preferences.theme);
            } else {
                root.removeAttribute('data-theme');
            }

            root.setAttribute('data-mode', preferences.mode);
            root.setAttribute('data-contrast', preferences.contrast);
            root.classList.toggle('high-contrast', preferences.contrast === 'high');
            root.style.setProperty('--base-font-size', `${preferences.font_size}px`);
        }

        function setPreference(field, value) {
            if (field === 'theme') {
                themeInput.value = value || '';
            }
            if (field === 'mode') {
                modeInput.value = value;
            }
            if (field === 'contrast') {
                contrastInput.value = value;
            }
            updateSelectedButtons(field, value);
            applyPreferencesToDocument(getFormPreferences());
        }

        optionButtons.forEach(button => {
            button.addEventListener('click', function () {
                const field = this.dataset.field;
                const value = this.dataset.value;
                setPreference(field, value);
            });
        });

        if (themeInput.value) {
            updateSelectedButtons('theme', themeInput.value);
        }
        updateSelectedButtons('mode', modeInput.value);
        updateSelectedButtons('contrast', contrastInput.value);
        applyPreferencesToDocument(getFormPreferences());

        function updateFontSizeDisplay(value) {
            fontSizeValue.textContent = `${value}px`;
            hiddenFontSizeInput.value = value;
        }

        updateFontSizeDisplay(fontSizeInput.value);

        fontSizeInput.addEventListener('input', function () {
            updateFontSizeDisplay(this.value);
            applyPreferencesToDocument(getFormPreferences());
        });

        resetButton.addEventListener('click', function () {
            setPreference('theme', '');
            setPreference('mode', 'light');
            setPreference('contrast', 'normal');
            fontSizeInput.value = 16;
            updateFontSizeDisplay(16);
            applyPreferencesToDocument(getFormPreferences());
        });

        function showPasswordModal() {
            passwordModal.classList.remove('hidden');
            passwordModal.classList.add('flex');
        }

        function hidePasswordModal() {
            passwordModal.classList.add('hidden');
            passwordModal.classList.remove('flex');
        }

        openModalButton.addEventListener('click', showPasswordModal);
        closeModalButton.addEventListener('click', hidePasswordModal);
        cancelModalButton.addEventListener('click', hidePasswordModal);

        passwordModal.addEventListener('click', function (event) {
            if (event.target === passwordModal) {
                hidePasswordModal();
            }
        });
    });
</script>
@endpush
