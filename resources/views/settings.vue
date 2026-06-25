<template>
    <Head title="Configuraciones" />

    <div class="min-h-screen bg-slate-100">
        <component :is="getHeaderComponent" />

        <main class="px-5 pb-10 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div
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
                </div>

                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-2"
                >
                    <div
                        v-if="showSuccess"
                        class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-900 shadow-sm"
                    >
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700"
                            >
                                <i class="fa-solid fa-circle-check text-lg"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">
                                    Configuración aplicada exitosamente
                                </p>
                                <p class="mt-1 text-sm text-slate-600">
                                    {{ successMessage }}
                                </p>
                            </div>
                        </div>
                    </div>
                </Transition>

                <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                    <div
                        class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <div class="grid gap-4 md:grid-cols-2">
                            <div
                                class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm"
                            >
                                <div
                                    class="flex items-center gap-3 text-slate-900"
                                >
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700"
                                    >
                                        <i
                                            class="fa-solid fa-palette text-lg"
                                        ></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Tema</p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            Selecciona un estilo visual para tu
                                            panel.
                                        </p>
                                    </div>
                                </div>
                                <select
                                    v-model="form.theme"
                                    class="mt-4 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                                    <option value="ninos">Niños</option>
                                    <option value="adultos">Adultos</option>
                                    <option value="jovenes">Jóvenes</option>
                                </select>
                            </div>
                            <div
                                class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm"
                            >
                                <div
                                    class="flex items-center gap-3 text-slate-900"
                                >
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700"
                                    >
                                        <i class="fa-solid fa-moon text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Modo</p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            Activa modo oscuro o claro para
                                            todas tus vistas.
                                        </p>
                                    </div>
                                </div>
                                <select
                                    v-model="form.mode"
                                    class="mt-4 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                                    <option value="light">Claro</option>
                                    <option value="dark">Oscuro</option>
                                </select>
                            </div>
                            <div
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
                                    <input
                                        type="range"
                                        v-model.number="form.font_size"
                                        min="12"
                                        max="36"
                                        class="w-full"
                                    />
                                    <div
                                        class="mt-2 flex items-center justify-between text-sm text-slate-500"
                                    >
                                        <span>Pequeño</span>
                                        <span>{{ form.font_size }}px</span>
                                        <span>Grande</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm"
                            >
                                <div
                                    class="flex items-center gap-3 text-slate-900"
                                >
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700"
                                    >
                                        <i
                                            class="fa-solid fa-eye-low-vision text-lg"
                                        ></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Contraste</p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            Mejora la legibilidad si necesitas
                                            un contraste mayor.
                                        </p>
                                    </div>
                                </div>
                                <select
                                    v-model="form.contrast"
                                    class="mt-4 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                                    <option value="normal">Normal</option>
                                    <option value="high">Alto contraste</option>
                                </select>
                            </div>
                        </div>

                        <div
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
                                        Actualiza tu contraseña con seguridad y
                                        estilo.
                                    </p>
                                </div>
                            </div>
                            <div class="mt-6 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-slate-700"
                                        >Contraseña actual</label
                                    >
                                    <input
                                        type="password"
                                        v-model="form.current_password"
                                        class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-slate-700"
                                        >Nueva contraseña</label
                                    >
                                    <input
                                        type="password"
                                        v-model="form.new_password"
                                        class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    />
                                </div>
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-semibold text-slate-700"
                                        >Confirmar nueva contraseña</label
                                    >
                                    <input
                                        type="password"
                                        v-model="form.new_password_confirmation"
                                        class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside
                        class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <div
                            class="rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-600 p-6 text-white shadow-xl shadow-blue-500/20"
                        >
                            <div class="flex items-start gap-4">
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-3xl bg-white/10 text-white"
                                >
                                    <i
                                        class="fa-solid fa-lightbulb text-xl"
                                    ></i>
                                </div>
                                <div>
                                    <p
                                        class="text-sm uppercase tracking-[0.2em]"
                                    >
                                        Consejo rápido
                                    </p>
                                    <p class="mt-3 text-lg font-semibold">
                                        Activa el modo oscuro para cuidar tu
                                        vista en ambientes con poca luz.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="rounded-3xl border border-slate-200 bg-slate-50 p-5"
                        >
                            <div class="flex items-center gap-3 text-slate-900">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700"
                                >
                                    <i class="fa-solid fa-circle-info"></i>
                                </div>
                                <div>
                                    <p class="font-semibold">
                                        Guarda cualquier cambio
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        El mensaje aparecerá cuando la
                                        configuración se haya aplicado
                                        correctamente.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        type="button"
                        @click="guardar"
                        class="inline-flex items-center gap-2 rounded-3xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-blue-700"
                    >
                        <i class="fa-solid fa-check"></i>
                        Guardar configuración
                    </button>
                </div>
            </div>
        </main>

        <footer
            class="fixed left-4 bottom-4 z-50 bg-transparent p-0 sm:left-6 sm:bottom-6"
        >
            <PageVisitCounter compact />
        </footer>
    </div>
</template>

<script setup>
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { Transition } from "vue";
import { computed, ref, watch } from "vue";
import HeaderAlumno from "@/views/partials/headerAlumno.vue";
import HeaderDocente from "@/views/partials/headerDocente.vue";
import PageVisitCounter from "@/views/partials/PageVisitCounter.vue";

const page = usePage();
const prefs = page.props.value?.auth?.user?.preferences ?? {};

const getHeaderComponent = computed(() => {
    const userRole = page.props?.auth?.user?.role?.nombre
        ? String(page.props.auth.user.role.nombre).toLowerCase()
        : "alumno";

    const headerComponents = {
        alumno: HeaderAlumno,
        docente: HeaderDocente,
    };

    return headerComponents[userRole] || HeaderAlumno;
});

const form = useForm({
    theme: prefs.theme ?? "adultos",
    mode: prefs.mode ?? "light",
    font_size: prefs.font_size ?? 16,
    contrast: prefs.contrast ?? "normal",
    current_password: "",
    new_password: "",
    new_password_confirmation: "",
});

const showSuccess = ref(false);
const successMessage = ref("Tu configuración se guardó correctamente.");

const applyPreferences = (preferences) => {
    const root = document.documentElement;

    if (preferences?.theme) {
        root.setAttribute("data-theme", preferences.theme);
    }

    if (preferences?.mode) {
        root.setAttribute("data-mode", preferences.mode);
    }

    if (preferences?.font_size) {
        root.style.setProperty(
            "--base-font-size",
            `${preferences.font_size}px`,
        );
        document.body.style.fontSize = `${preferences.font_size}px`;
    }

    if (preferences?.contrast === "high") {
        root.classList.add("high-contrast");
    } else {
        root.classList.remove("high-contrast");
    }
};

const getModifiedCount = () => {
    const original = {
        theme: prefs.theme ?? "adultos",
        mode: prefs.mode ?? "light",
        font_size: prefs.font_size ?? 16,
        contrast: prefs.contrast ?? "normal",
    };

    return ["theme", "mode", "font_size", "contrast"].reduce(
        (count, key) => (form[key] !== original[key] ? count + 1 : count),
        0,
    );
};

function guardar() {
    const modifiedCount = getModifiedCount();

    if (modifiedCount <= 1) {
        successMessage.value = "Configuración aplicada exitosamente";
    } else {
        successMessage.value = "Configuración aplicada exitosamente";
    }

    form.put("/configuraciones", {
        preserveState: false,
        onSuccess: (page) => {
            const preferences = page.props?.auth?.user?.preferences ?? {};
            applyPreferences(preferences);
            showSuccess.value = true;

            window.setTimeout(() => {
                showSuccess.value = false;
            }, 3600);
        },
    });
}

watch(showSuccess, (value) => {
    if (value) {
        document.documentElement.classList.add("settings-saved");
    } else {
        document.documentElement.classList.remove("settings-saved");
    }
});

defineExpose({ form, guardar, applyPreferences, showSuccess, successMessage });
</script>

<style scoped></style>
