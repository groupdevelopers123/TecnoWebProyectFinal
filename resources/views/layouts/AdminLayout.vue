<template>
    <div v-if="!showShell" class="min-h-screen bg-slate-100 text-slate-800">
        <slot />
    </div>

    <div v-else class="flex min-h-screen bg-slate-100 text-slate-800">
        <aside
            class="fixed left-0 top-0 hidden h-screen w-72 flex-col overflow-y-auto bg-slate-950 px-5 py-6 text-white shadow-2xl lg:flex"
        >
            <div class="mb-8">
                <img
                    src="/img/logo_2.png"
                    alt="Logo Instituto Andrés Ibáñez"
                    class="mx-auto h-20 w-auto object-contain"
                />

                <h2 class="mt-4 text-xl font-bold">Instituto Andrés Ibáñez</h2>
                <p class="mt-1 text-sm text-slate-400">Gestión académica web</p>
            </div>

            <nav class="flex flex-1 flex-col gap-2">
                <Link
                    :href="route('admin.dashboard')"
                    class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                    :class="
                        isActive('/admin') || currentPath === '/admin'
                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                            : 'text-slate-300'
                    "
                >
                    <i class="fa-solid fa-house w-5 text-center"></i>
                    <span>Dashboard</span>
                </Link>

                <div v-if="canSeeUsuarios" class="space-y-2">
                    <Link
                        :href="route('admin.usuarios.index')"
                        class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                        :class="
                            isActive('/admin/usuarios')
                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                : 'text-slate-300'
                        "
                    >
                        <i class="fa-solid fa-users w-5 text-center"></i>
                        <span>Usuarios</span>
                    </Link>
                </div>

                <div v-if="canSeeAulas" class="space-y-2">
                    <Link
                        :href="route('admin.aulas.index')"
                        class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                        :class="
                            isActive('/admin/aulas')
                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                : 'text-slate-300'
                        "
                    >
                        <i class="fa-solid fa-school w-5 text-center"></i>
                        <span>Aulas</span>
                    </Link>
                </div>

                <div v-if="canSeeHorarios" class="space-y-2">
                    <button
                        type="button"
                        class="group flex w-full items-center justify-between gap-3 rounded-2xl bg-slate-900/80 px-4 py-3 text-left text-sm font-bold text-white transition duration-200 hover:bg-white/10"
                        @click="toggleHorarios"
                        :aria-expanded="horariosOpen ? 'true' : 'false'"
                    >
                        <span class="inline-flex items-center gap-3">
                            <i
                                class="fa-solid fa-calendar-week w-5 text-center"
                            ></i>
                            Horarios
                        </span>
                        <i
                            class="fa-solid fa-chevron-down text-slate-300 transition-transform duration-200"
                            :class="horariosOpen ? 'rotate-180' : ''"
                            aria-hidden="true"
                        ></i>
                    </button>

                    <div v-show="horariosOpen" class="space-y-2 pl-6">
                        <Link
                            :href="route('admin.carreras.index')"
                            class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                            :class="
                                isActive('/admin/carreras')
                                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                    : 'text-slate-300'
                            "
                        >
                            <i
                                class="fa-solid fa-graduation-cap w-5 text-center"
                            ></i>
                            <span>Carreras</span>
                        </Link>

                        <Link
                            :href="route('admin.materias.index')"
                            class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                            :class="
                                isActive('/admin/materias')
                                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                    : 'text-slate-300'
                            "
                        >
                            <i
                                class="fa-solid fa-book-open w-5 text-center"
                            ></i>
                            <span>Materias</span>
                        </Link>

                        <Link
                            :href="route('admin.periodos-academicos.index')"
                            class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                            :class="
                                isActive('/admin/periodos-academicos')
                                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                    : 'text-slate-300'
                            "
                        >
                            <i
                                class="fa-solid fa-calendar-days w-5 text-center"
                            ></i>
                            <span>Periodos</span>
                        </Link>

                        <Link
                            :href="route('admin.horarios.index')"
                            class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                            :class="
                                isActive('/admin/horarios')
                                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                    : 'text-slate-300'
                            "
                        >
                            <i class="fa-solid fa-clock w-5 text-center"></i>
                            <span>Horarios</span>
                        </Link>
                    </div>
                </div>

                <div v-if="canSeeOfertas" class="space-y-2">
                    <Link
                        :href="route('admin.ofertas-academicas.index')"
                        class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                        :class="
                            isActive('/admin/ofertas-academicas')
                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                : 'text-slate-300'
                        "
                    >
                        <i class="fa-solid fa-bookmark w-5 text-center"></i>
                        <span>Ofertas académicas</span>
                    </Link>
                </div>

                <div v-if="canSeeInscripciones" class="space-y-2">
                    <Link
                        :href="route('admin.inscripciones.index')"
                        class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                        :class="
                            isActive('/admin/inscripciones')
                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                : 'text-slate-300'
                        "
                    >
                        <i
                            class="fa-solid fa-clipboard-list w-5 text-center"
                        ></i>
                        <span>Inscripciones</span>
                    </Link>
                </div>

                <div v-if="canSeeSeguimiento" class="space-y-2">
                    <Link
                        :href="route('admin.seguimientos-academicos.index')"
                        class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                        :class="
                            isActive('/admin/seguimientos-academicos')
                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                : 'text-slate-300'
                        "
                    >
                        <i class="fa-solid fa-chart-line w-5 text-center"></i>
                        <span>Seguimiento académico</span>
                    </Link>
                </div>

                <div v-if="canSeePagos" class="space-y-2">
                    <Link
                        :href="route('admin.pago-contados.index')"
                        class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                        :class="
                            isActive('/admin/pago-contados')
                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                : 'text-slate-300'
                        "
                    >
                        <i
                            class="fa-solid fa-file-invoice-dollar w-5 text-center"
                        ></i>
                        <span>Pagos</span>
                    </Link>
                </div>

                <div v-if="canSeeReportes" class="space-y-2">
                    <Link
                        :href="route('admin.reportes.index')"
                        class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                        :class="
                            isActive('/admin/reportes')
                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                : 'text-slate-300'
                        "
                    >
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Reportes</span>
                    </Link>
                </div>

                <div v-if="canSeeBitacora" class="space-y-2">
                    <Link
                        :href="route('admin.bitacora.index')"
                        class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition duration-200 hover:translate-x-1 hover:bg-white/10"
                        :class="
                            isActive('/admin/bitacora')
                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                : 'text-slate-300'
                        "
                    >
                        <i class="fa-solid fa-book-open w-5 text-center"></i>
                        <span>Bitácora</span>
                    </Link>
                </div>

                <div class="mt-auto pt-6">
                    <div class="space-y-2 border-t border-slate-800 pt-4">
                        <Link
                            :href="route('configuraciones.show')"
                            class="group flex items-center gap-3 rounded-2xl bg-slate-900/80 px-4 py-3 text-sm font-medium text-white transition duration-200 hover:bg-white/10 hover:text-white"
                        >
                            <i class="fa-solid fa-gear w-5 text-center"></i>
                            <span>Configuración</span>
                        </Link>

                        <button
                            type="button"
                            @click="logout"
                            class="inline-flex w-full items-center justify-center gap-3 rounded-2xl bg-slate-800 px-4 py-3 text-sm font-medium text-white transition duration-200 hover:bg-red-600"
                        >
                            <i
                                class="fa-solid fa-right-from-bracket w-5 text-center"
                            ></i>
                            <span>Cerrar sesión</span>
                        </button>

                        <form
                            id="admin-logout-form"
                            method="POST"
                            :action="route('logout')"
                            class="hidden"
                        >
                            <input
                                type="hidden"
                                name="_token"
                                :value="csrfToken"
                            />
                        </form>
                    </div>
                </div>
            </nav>
        </aside>

        <main class="w-full lg:ml-72">
            <header
                class="sticky top-0 z-30 border-b border-slate-200 bg-white/80 px-5 py-4 backdrop-blur-xl lg:px-8"
            >
                <div
                    class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                >
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-slate-900"
                        >
                            {{ pageTitle }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-500">
                            Gestión académica y administrativa del instituto
                        </p>
                    </div>
                </div>
            </header>

            <section class="animate-[fadeUp_.35s_ease-out] p-5 lg:p-8">
                <div
                    v-if="flashSuccess"
                    class="mb-5 flex items-center gap-3 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-700 shadow-sm"
                >
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ flashSuccess }}</span>
                </div>

                <div
                    v-if="flashError"
                    class="mb-5 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700 shadow-sm"
                >
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ flashError }}</span>
                </div>

                <div
                    v-if="hasErrors"
                    class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700 shadow-sm"
                >
                    <div class="flex items-center gap-2 font-bold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Corrige los siguientes errores:</span>
                    </div>
                    <ul class="mt-2 list-inside list-disc">
                        <li v-for="(message, key) in errors" :key="key">
                            {{ message }}
                        </li>
                    </ul>
                </div>

                <slot />
            </section>

            <div
                class="fixed bottom-4 right-4 z-50 bg-transparent p-0 sm:bottom-6 sm:right-6"
            >
                <PageVisitCounter compact />
            </div>
        </main>
    </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import PageVisitCounter from "../partials/PageVisitCounter.vue";

const page = usePage();
const roleName = computed(() => page.props.auth?.user?.role?.nombre ?? "");
const currentPath = computed(() => page.url || "/");
const flashSuccess = computed(() => page.props.flash?.success ?? "");
const flashError = computed(() => page.props.flash?.error ?? "");
const errors = computed(() => page.props.errors ?? {});
const hasErrors = computed(() => Object.keys(errors.value).length > 0);

const showShell = computed(() => {
    const component = page.component || "";
    const isAdminRole = ["propietario", "secretaria"].includes(
        String(roleName.value).toLowerCase(),
    );
    const isSettingsPage =
        component.toLowerCase().includes("settings") ||
        page.props?.isFromAdmin === true;

    return component.startsWith("admin/") || (isSettingsPage && isAdminRole);
});

const pageTitle = computed(() => {
    if (page.props.title) {
        return page.props.title;
    }

    return "Panel Administrativo";
});

const canSeeUsuarios = computed(() =>
    ["propietario", "secretaria"].includes(roleName.value),
);
const canSeeAulas = computed(() =>
    ["propietario", "secretaria"].includes(roleName.value),
);
const canSeeHorarios = computed(() =>
    ["propietario", "secretaria"].includes(roleName.value),
);
const canSeeOfertas = computed(() =>
    ["propietario", "secretaria"].includes(roleName.value),
);
const canSeeInscripciones = computed(() =>
    ["propietario", "secretaria"].includes(roleName.value),
);
const canSeeSeguimiento = computed(() =>
    ["propietario", "secretaria"].includes(roleName.value),
);
const canSeePagos = computed(() =>
    ["propietario", "secretaria", "docente", "alumno"].includes(roleName.value),
);
const canSeeReportes = computed(() =>
    ["propietario", "secretaria"].includes(roleName.value),
);
const canSeeBitacora = computed(() => roleName.value === "propietario");

const horariosOpen = ref(false);
const horariosActive = computed(() =>
    [
        "/admin/carreras",
        "/admin/materias",
        "/admin/periodos-academicos",
        "/admin/horarios",
    ].some((prefix) => currentPath.value.startsWith(prefix)),
);

function isActive(path) {
    return (
        currentPath.value === path || currentPath.value.startsWith(`${path}/`)
    );
}

function toggleHorarios() {
    horariosOpen.value = !horariosOpen.value;
}

watch(
    horariosActive,
    (value) => {
        if (value) {
            horariosOpen.value = true;
        }
    },
    { immediate: true },
);

const csrfToken = computed(() => {
    return (
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || ""
    );
});

function logout() {
    const form = document.getElementById("admin-logout-form");
    if (form) {
        form.submit();
    }
}
</script>
