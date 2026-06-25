<template>
    <Head title="Configuraciones" />

    <div class="settings-page min-h-screen bg-slate-100">
        <component :is="getHeaderComponent" />

        <main class="px-5 pb-10 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <!-- Encabezado -->
                <section
                    class="mb-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div
                        class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
                    >
                        <div>
                            <p
                                class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600"
                            >
                                Preferencias personales
                            </p>

                            <h1 class="mt-2 text-3xl font-black text-slate-900">
                                Ajusta tu experiencia visual
                            </h1>

                            <p
                                class="mt-2 max-w-2xl text-sm leading-6 text-slate-500"
                            >
                                Elige tema, modo oscuro, tamaño de letra y
                                contraste. También puedes cambiar tu contraseña
                                desde aquí.
                            </p>
                        </div>

                        <div
                            class="inline-flex items-center gap-3 rounded-3xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 shadow-sm"
                        >
                            <i class="fa-solid fa-sliders-h"></i>
                            Configuración global activa
                        </div>
                    </div>
                </section>

                <!-- Notificación -->
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="translate-y-2 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="translate-y-0 opacity-100"
                    leave-to-class="translate-y-2 opacity-0"
                >
                    <div
                        v-if="showNotice"
                        :class="[
                            'mb-6 rounded-3xl border p-5 shadow-sm',
                            noticeType === 'success'
                                ? 'border-emerald-200 bg-emerald-50'
                                : 'border-red-200 bg-red-50',
                        ]"
                    >
                        <div class="flex items-start gap-3">
                            <div
                                :class="[
                                    'mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl',
                                    noticeType === 'success'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-red-100 text-red-700',
                                ]"
                            >
                                <i
                                    :class="[
                                        'fa-solid text-lg',
                                        noticeType === 'success'
                                            ? 'fa-circle-check'
                                            : 'fa-circle-exclamation',
                                    ]"
                                ></i>
                            </div>

                            <div>
                                <p class="font-bold text-slate-900">
                                    {{
                                        noticeType === "success"
                                            ? "Configuración aplicada exitosamente"
                                            : "No se pudo completar la operación"
                                    }}
                                </p>

                                <p class="mt-1 text-sm text-slate-600">
                                    {{ noticeMessage }}
                                </p>
                            </div>
                        </div>
                    </div>
                </Transition>

                <div class="mx-auto max-w-5xl">
                    <section
                        class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <div class="grid gap-4 md:grid-cols-2">
                            <!-- Tema -->
                            <article
                                class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm"
                            >
                                <div
                                    class="flex items-center gap-3 text-slate-900"
                                >
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-indigo-700"
                                    >
                                        <i
                                            class="fa-solid fa-palette text-lg"
                                        ></i>
                                    </div>

                                    <div>
                                        <p class="font-semibold">Tema</p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Elige el estilo de colores y fondo.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                    <button
                                        v-for="option in themeOptions"
                                        :key="option.value"
                                        type="button"
                                        class="visual-option flex items-start gap-3 rounded-3xl border p-4 text-left text-sm transition"
                                        :class="{
                                            'visual-option--selected':
                                                form.theme === option.value,
                                        }"
                                        :aria-pressed="
                                            form.theme === option.value
                                        "
                                        @click="form.theme = option.value"
                                    >
                                        <div
                                            class="visual-option__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-lg"
                                        >
                                            <i :class="option.icon"></i>
                                        </div>

                                        <div>
                                            <p class="font-semibold">
                                                {{ option.label }}
                                            </p>

                                            <p
                                                class="mt-1 text-xs text-slate-500"
                                            >
                                                {{ option.description }}
                                            </p>
                                        </div>
                                    </button>
                                </div>
                            </article>

                            <!-- Modo -->
                            <article
                                class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm"
                            >
                                <div
                                    class="flex items-center gap-3 text-slate-900"
                                >
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700"
                                    >
                                        <i
                                            class="fa-solid fa-circle-half-stroke text-lg"
                                        ></i>
                                    </div>

                                    <div>
                                        <p class="font-semibold">Modo</p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Cambia entre luz suave y estilo
                                            oscuro.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <button
                                        v-for="option in modeOptions"
                                        :key="option.value"
                                        type="button"
                                        class="visual-option flex items-start gap-3 rounded-3xl border p-4 text-left text-sm transition"
                                        :class="{
                                            'visual-option--selected':
                                                form.mode === option.value,
                                        }"
                                        :aria-pressed="
                                            form.mode === option.value
                                        "
                                        @click="form.mode = option.value"
                                    >
                                        <div
                                            class="visual-option__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-lg"
                                        >
                                            <i :class="option.icon"></i>
                                        </div>

                                        <div>
                                            <p class="font-semibold">
                                                {{ option.label }}
                                            </p>

                                            <p
                                                class="mt-1 text-xs text-slate-500"
                                            >
                                                {{ option.description }}
                                            </p>
                                        </div>
                                    </button>
                                </div>
                            </article>

                            <!-- Tamaño de letra -->
                            <article
                                class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm"
                            >
                                <div
                                    class="flex items-center gap-3 text-slate-900"
                                >
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700"
                                    >
                                        <i
                                            class="fa-solid fa-text-height text-lg"
                                        ></i>
                                    </div>

                                    <div>
                                        <p class="font-semibold">
                                            Tamaño de letra
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Ajusta la lectura sin afectar el
                                            contenido.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div
                                        class="flex items-center justify-between gap-3 text-sm text-slate-500"
                                    >
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-font"></i>

                                            <span
                                                class="font-semibold text-slate-700"
                                            >
                                                {{ form.font_size }}px
                                            </span>
                                        </div>

                                        <span>Tamaño actual</span>
                                    </div>

                                    <input
                                        v-model.number="form.font_size"
                                        type="range"
                                        min="12"
                                        max="36"
                                        step="1"
                                        class="mt-3 w-full rounded-full accent-blue-600"
                                        aria-label="Tamaño de letra"
                                    />

                                    <div
                                        class="mt-2 flex items-center justify-between text-sm text-slate-500"
                                    >
                                        <span>Pequeño</span>
                                        <span>Grande</span>
                                    </div>
                                </div>
                            </article>

                            <!-- Contraste -->
                            <article
                                class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm"
                            >
                                <div
                                    class="flex items-center gap-3 text-slate-900"
                                >
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700"
                                    >
                                        <i
                                            class="fa-solid fa-eye-low-vision text-lg"
                                        ></i>
                                    </div>

                                    <div>
                                        <p class="font-semibold">Contraste</p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Elige la visibilidad que te sea más
                                            cómoda.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <button
                                        v-for="option in contrastOptions"
                                        :key="option.value"
                                        type="button"
                                        class="visual-option flex items-start gap-3 rounded-3xl border p-4 text-left text-sm transition"
                                        :class="{
                                            'visual-option--selected':
                                                form.contrast === option.value,
                                        }"
                                        :aria-pressed="
                                            form.contrast === option.value
                                        "
                                        @click="form.contrast = option.value"
                                    >
                                        <div
                                            class="visual-option__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-lg"
                                        >
                                            <i :class="option.icon"></i>
                                        </div>

                                        <div>
                                            <p class="font-semibold">
                                                {{ option.label }}
                                            </p>

                                            <p
                                                class="mt-1 text-xs text-slate-500"
                                            >
                                                {{ option.description }}
                                            </p>
                                        </div>
                                    </button>
                                </div>
                            </article>
                        </div>

                        <!-- Seguridad -->
                        <article
                            class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-700"
                                >
                                    <i class="fa-solid fa-lock text-xl"></i>
                                </div>

                                <div>
                                    <p class="text-lg font-semibold">
                                        Seguridad
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Cambia tu contraseña de forma segura
                                        desde el modal.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-3xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700"
                                    @click="showChangePasswordModal = true"
                                >
                                    <i class="fa-solid fa-key"></i>
                                    Cambiar contraseña
                                </button>
                            </div>
                        </article>
                    </section>
                </div>

                <!-- Acciones -->
                <div class="mt-6 flex justify-end">
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-3xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            :disabled="form.processing"
                            @click="resetPreferences"
                        >
                            <i class="fa-solid fa-rotate-left"></i>

                            Restablecer valores
                        </button>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-3xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-blue-700"
                            :disabled="form.processing"
                            @click="guardar"
                        >
                            <i
                                v-if="form.processing"
                                class="fa-solid fa-spinner animate-spin"
                            ></i>

                            <i v-else class="fa-solid fa-check"></i>

                            {{
                                form.processing
                                    ? "Guardando..."
                                    : "Guardar configuración"
                            }}
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <ChangePasswordModal
            :mostrar="showChangePasswordModal"
            @cerrar="showChangePasswordModal = false"
        />

        <footer
            class="fixed bottom-4 left-4 z-50 bg-transparent p-0 sm:bottom-6 sm:left-6"
        >
            <PageVisitCounter compact />
        </footer>
    </div>
</template>

<script setup>
import { Head, useForm, usePage } from "@inertiajs/vue3";

import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";

import HeaderAlumno from "@/views/partials/headerAlumno.vue";
import HeaderDocente from "@/views/partials/headerDocente.vue";
import PageVisitCounter from "@/views/partials/PageVisitCounter.vue";
import ChangePasswordModal from "@/views/components/ChangePasswordModal.vue";

import {
    applyVisualPreferences,
    getDefaultPreferences,
    getServerPreferences,
    normalizePreferences,
    preferencesAreEqual,
    resolvePreferences,
    writeStoredPreferences,
} from "@/js/preferences";

const page = usePage();

const showNotice = ref(false);
const noticeType = ref("success");
const noticeMessage = ref("");
const showChangePasswordModal = ref(false);

let noticeTimer = null;

/* =========================================================
   OPCIONES
========================================================= */

const themeOptions = [
    {
        value: "ninos",
        label: "Niños",
        description: "Diseño entretenido y cálido.",
        icon: "fa-solid fa-child",
    },
    {
        value: "adultos",
        label: "Adultos",
        description: "Estilo serio y profesional.",
        icon: "fa-solid fa-user-tie",
    },
    {
        value: "jovenes",
        label: "Jóvenes",
        description: "Opción fresca y moderna.",
        icon: "fa-solid fa-graduation-cap",
    },
];

const modeOptions = [
    {
        value: "light",
        label: "Claro",
        description: "Mejor para entornos iluminados.",
        icon: "fa-solid fa-sun",
    },
    {
        value: "dark",
        label: "Oscuro",
        description: "Ideal para lectura nocturna.",
        icon: "fa-solid fa-moon",
    },
];

const contrastOptions = [
    {
        value: "normal",
        label: "Normal",
        description: "Contraste estándar y agradable.",
        icon: "fa-solid fa-eye",
    },
    {
        value: "high",
        label: "Alto contraste",
        description: "Más legibilidad en entornos brillantes.",
        icon: "fa-solid fa-eye-low-vision",
    },
];

/* =========================================================
   USUARIO Y HEADER
========================================================= */

const currentUserId = computed(() => {
    return page.props?.auth?.user?.id ?? null;
});

const getHeaderComponent = computed(() => {
    const roleName = page.props?.auth?.user?.role?.nombre ?? "alumno";

    const normalizedRole = String(roleName).toLowerCase();

    const headerComponents = {
        alumno: HeaderAlumno,
        docente: HeaderDocente,
    };

    return headerComponents[normalizedRole] ?? HeaderAlumno;
});

/* =========================================================
   CONFIGURACIÓN INICIAL
========================================================= */

const serverPreferences = getServerPreferences(page.props);

const initialPreferences = resolvePreferences(
    serverPreferences,
    currentUserId.value,
);

const savedPreferences = ref({
    ...initialPreferences,
});

const form = useForm({
    theme: initialPreferences.theme,
    mode: initialPreferences.mode,
    font_size: initialPreferences.font_size,
    contrast: initialPreferences.contrast,
});

/* =========================================================
   FUNCIONES AUXILIARES
========================================================= */
function getFormPreferences() {
    return normalizePreferences({
        theme: form.theme,
        mode: form.mode,
        font_size: form.font_size,
        contrast: form.contrast,
    });
}

function assignFormPreferences(preferences) {
    const normalized = normalizePreferences(preferences);

    form.theme = normalized.theme;
    form.mode = normalized.mode;
    form.font_size = normalized.font_size;
    form.contrast = normalized.contrast;
}

function displayNotice(message, type = "success") {
    if (noticeTimer) {
        window.clearTimeout(noticeTimer);
    }

    noticeMessage.value = message;
    noticeType.value = type;
    showNotice.value = true;

    noticeTimer = window.setTimeout(() => {
        showNotice.value = false;
    }, 4000);
}

function markPreferencesAsSaved(preferences) {
    const normalized = normalizePreferences(preferences);

    savedPreferences.value = {
        ...normalized,
    };

    assignFormPreferences(normalized);

    form.defaults({
        theme: normalized.theme,
        mode: normalized.mode,
        font_size: normalized.font_size,
        contrast: normalized.contrast,
    });

    applyVisualPreferences(normalized);

    writeStoredPreferences(normalized, currentUserId.value);

    return normalized;
}

/* =========================================================
   PREVISUALIZACIÓN EN TIEMPO REAL
========================================================= */

watch(
    () => [form.theme, form.mode, form.font_size, form.contrast],
    () => {
        applyVisualPreferences(getFormPreferences());
    },
    {
        immediate: true,
    },
);

/* =========================================================
   GUARDAR
========================================================= */

function submitPreferences({
    isReset = false,
    previousPreferences = null,
} = {}) {
    if (form.processing) {
        return;
    }

    const desiredPreferences = getFormPreferences();

    /*
     * Se aplican inmediatamente para que la interfaz responda
     * sin esperar al servidor.
     */
    applyVisualPreferences(desiredPreferences);

    form.clearErrors();

    form.transform(() => ({
        theme: desiredPreferences.theme,
        mode: desiredPreferences.mode,
        font_size: desiredPreferences.font_size,
        contrast: desiredPreferences.contrast,
    }));

    form.put("/configuraciones", {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            markPreferencesAsSaved(desiredPreferences);

            displayNotice(
                isReset
                    ? "Los valores predeterminados fueron restablecidos y guardados."
                    : "Tus preferencias visuales fueron guardadas correctamente.",
                "success",
            );
        },

        onError: () => {
            /*
             * Si falló un restablecimiento, se vuelve a la configuración
             * que estaba guardada anteriormente.
             */
            if (isReset && previousPreferences) {
                assignFormPreferences(previousPreferences);

                applyVisualPreferences(previousPreferences);
            }

            displayNotice(
                "No se pudieron guardar las preferencias. Revisa los datos e intenta nuevamente.",
                "error",
            );
        },
    });
}

function guardar() {
    const currentPreferences = getFormPreferences();

    if (preferencesAreEqual(currentPreferences, savedPreferences.value)) {
        applyVisualPreferences(currentPreferences);

        writeStoredPreferences(currentPreferences, currentUserId.value);

        displayNotice(
            "No había cambios nuevos. Tu configuración ya estaba guardada.",
            "success",
        );

        return;
    }

    submitPreferences();
}

/* =========================================================
   RESTABLECER
========================================================= */

function resetPreferences() {
    if (form.processing) {
        return;
    }

    const previousPreferences = {
        ...savedPreferences.value,
    };

    const defaults = {
        theme: null,
        mode: "light",
        font_size: 16,
        contrast: "normal",
    };

    /*
     * Se asigna null explícitamente para que ninguno
     * de los tres temas aparezca seleccionado.
     */
    form.theme = null;
    form.mode = "light";
    form.font_size = 16;
    form.contrast = "normal";

    /*
     * Esto elimina data-theme del elemento html.
     */
    applyVisualPreferences(defaults);

    submitPreferences({
        isReset: true,
        previousPreferences,
    });
}

/* =========================================================
   CICLO DE VIDA
========================================================= */

onMounted(() => {
    applyVisualPreferences(getFormPreferences());
});

onBeforeUnmount(() => {
    if (noticeTimer) {
        window.clearTimeout(noticeTimer);
    }
});

defineExpose({
    form,
    guardar,
    resetPreferences,
    applyVisualPreferences,
    showChangePasswordModal,
});
</script>
