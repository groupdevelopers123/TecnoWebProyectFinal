<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

import HeaderDocente from "@/views/partials/headerDocente.vue";
import PageVisitCounter from "@/views/partials/PageVisitCounter.vue";

const page = usePage();

/* =========================================================
   INFORMACIÓN DEL DOCENTE
========================================================= */

const usuario = computed(() => {
    return page.props?.auth?.user ?? {};
});

const nombreDocente = computed(() => {
    const user = usuario.value;

    const nombreCompleto = [
        user.nombre,
        user.nombres,
        user.apellido,
        user.apellidos,
        user.apellido_paterno,
        user.apellido_materno,
    ]
        .filter(Boolean)
        .join(" ")
        .trim();

    return user.nombre_completo || user.name || nombreCompleto || "Docente";
});

const primerNombre = computed(() => {
    return nombreDocente.value.split(" ")[0] || "Docente";
});

const iniciales = computed(() => {
    return nombreDocente.value
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((palabra) => palabra.charAt(0).toUpperCase())
        .join("");
});

const saludo = computed(() => {
    const horaActual = new Date().getHours();

    if (horaActual < 12) {
        return "Buenos días";
    }

    if (horaActual < 19) {
        return "Buenas tardes";
    }

    return "Buenas noches";
});

/* =========================================================
   ESTADÍSTICAS
========================================================= */

const estadisticas = computed(() => {
    const datos = page.props?.estadisticas ?? {};

    return {
        materias: Number(datos.materias ?? page.props?.materias_count ?? 0),

        alumnos: Number(datos.alumnos ?? page.props?.alumnos_count ?? 0),

        notificaciones: Number(
            datos.notificaciones ?? page.props?.notificaciones_count ?? 0,
        ),
    };
});

const tarjetas = computed(() => [
    {
        titulo: "Materias asignadas",
        valor: estadisticas.value.materias,
        descripcion: "Materias activas del período",
        icono: "fa-solid fa-book-open",
        iconoClase: "accent-soft-bg accent-text",
    },
    {
        titulo: "Alumnos",
        valor: estadisticas.value.alumnos,
        descripcion: "Estudiantes registrados",
        icono: "fa-solid fa-user-graduate",
        iconoClase: "bg-emerald-50 text-emerald-700",
    },
    {
        titulo: "Notificaciones",
        valor: estadisticas.value.notificaciones,
        descripcion: "Avisos pendientes",
        icono: "fa-solid fa-bell",
        iconoClase: "bg-amber-50 text-amber-700",
    },
]);

/* =========================================================
   ACCESOS RÁPIDOS
========================================================= */

const accesosRapidos = [
    {
        titulo: "Mis materias",
        descripcion: "Consulta tus materias asignadas.",
        icono: "fa-solid fa-book",
        ruta: "/docente/materias",
    },
    {
        titulo: "Mi perfil",
        descripcion: "Revisa tu información personal.",
        icono: "fa-solid fa-user",
        ruta: "/perfil",
    },
    {
        titulo: "Configuraciones",
        descripcion: "Personaliza la apariencia del sistema.",
        icono: "fa-solid fa-sliders",
        ruta: "/configuraciones",
    },
];

const navegar = (ruta) => {
    router.visit(ruta);
};
</script>

<template>
    <Head title="Panel del Docente" />

    <div class="min-h-screen bg-slate-100">
        <HeaderDocente />

        <main
            class="relative isolate overflow-hidden px-5 pb-24 pt-20 sm:px-6 lg:px-8"
        >
            <!-- Desvanecido superior -->
            <div
                class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-72 bg-gradient-to-b from-[var(--accent-soft)] via-[var(--page-bg)] to-transparent opacity-70"
            ></div>

            <!-- Decoración suave izquierda -->
            <div
                class="pointer-events-none absolute -left-24 top-20 -z-10 h-72 w-72 rounded-full bg-[var(--accent-soft)] opacity-40 blur-3xl"
            ></div>

            <!-- Decoración suave derecha -->
            <div
                class="pointer-events-none absolute -right-24 top-40 -z-10 h-80 w-80 rounded-full bg-[var(--surface-tertiary)] opacity-50 blur-3xl"
            ></div>

            <div class="relative mx-auto max-w-7xl space-y-6">
                <!-- =====================================================
                     BIENVENIDA
                ====================================================== -->

                <Transition
                    appear
                    enter-active-class="transition duration-700 ease-out"
                    enter-from-class="translate-y-4 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                >
                    <section
                        class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
                    >
                        <!-- Desvanecido dentro de la tarjeta -->
                        <div
                            class="pointer-events-none absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-[var(--accent-soft)] to-transparent opacity-35"
                        ></div>

                        <!-- Círculo decorativo -->
                        <div
                            class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full bg-[var(--accent-soft)] opacity-60 blur-2xl"
                        ></div>

                        <div
                            class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
                        >
                            <div class="max-w-2xl">
                                <div
                                    class="accent-soft-bg inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-bold"
                                >
                                    <i class="fa-solid fa-graduation-cap"></i>

                                    Panel académico
                                </div>

                                <h1
                                    class="mt-5 text-3xl font-black text-slate-900 sm:text-4xl"
                                >
                                    {{ saludo }},

                                    <span class="accent-text">
                                        {{ primerNombre }}
                                    </span>
                                </h1>

                                <p
                                    class="mt-3 text-sm leading-7 text-slate-600 sm:text-base"
                                >
                                    Bienvenido a tu panel docente. Desde aquí
                                    puedes consultar tus materias y acceder a
                                    las principales funciones del sistema.
                                </p>

                                <div
                                    class="mt-6 flex flex-col gap-3 sm:flex-row"
                                >
                                    <button
                                        type="button"
                                        class="accent-bg inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3 text-sm font-bold shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md"
                                        @click="navegar('/docente/materias')"
                                    >
                                        <i class="fa-solid fa-book-open"></i>

                                        Ver mis materias
                                    </button>

                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3 text-sm font-bold text-slate-700 transition duration-200 hover:-translate-y-0.5 hover:border-blue-500 hover:bg-slate-100"
                                        @click="navegar('/perfil')"
                                    >
                                        <i class="fa-solid fa-user"></i>

                                        Ver mi perfil
                                    </button>
                                </div>
                            </div>

                            <!-- Información del docente -->
                            <div
                                class="flex items-center gap-4 rounded-3xl border border-slate-200 bg-slate-50 p-5 md:min-w-80"
                            >
                                <div
                                    class="accent-bg flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl text-xl font-black shadow-sm"
                                >
                                    {{ iniciales }}
                                </div>

                                <div class="min-w-0">
                                    <p
                                        class="text-xs font-bold uppercase tracking-wider text-slate-500"
                                    >
                                        Sesión iniciada como
                                    </p>

                                    <p
                                        class="mt-1 truncate text-lg font-black text-slate-900"
                                    >
                                        {{ nombreDocente }}
                                    </p>

                                    <div
                                        class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700"
                                    >
                                        <span
                                            class="h-2 w-2 rounded-full bg-emerald-500"
                                        ></span>

                                        Docente activo
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </Transition>

                <!-- =====================================================
                     ESTADÍSTICAS
                ====================================================== -->

                <Transition
                    appear
                    enter-active-class="transition delay-150 duration-700 ease-out"
                    enter-from-class="translate-y-4 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                >
                    <section>
                        <div>
                            <p
                                class="accent-text text-sm font-bold uppercase tracking-widest"
                            >
                                Resumen
                            </p>

                            <h2 class="mt-1 text-2xl font-black text-slate-900">
                                Actividad académica
                            </h2>
                        </div>

                        <div
                            class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
                        >
                            <article
                                v-for="tarjeta in tarjetas"
                                :key="tarjeta.titulo"
                                class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-blue-500 hover:shadow-lg"
                            >
                                <div
                                    class="flex items-start justify-between gap-4"
                                >
                                    <div
                                        :class="[
                                            'flex h-12 w-12 items-center justify-center rounded-2xl text-lg',
                                            tarjeta.iconoClase,
                                        ]"
                                    >
                                        <i :class="tarjeta.icono"></i>
                                    </div>

                                    <span
                                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500"
                                    >
                                        Actual
                                    </span>
                                </div>

                                <p
                                    class="mt-5 text-4xl font-black text-slate-900"
                                >
                                    {{ tarjeta.valor }}
                                </p>

                                <h3 class="mt-2 font-black text-slate-900">
                                    {{ tarjeta.titulo }}
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ tarjeta.descripcion }}
                                </p>
                            </article>
                        </div>
                    </section>
                </Transition>

                <!-- =====================================================
                     ACCESOS RÁPIDOS
                ====================================================== -->

                <Transition
                    appear
                    enter-active-class="transition delay-300 duration-700 ease-out"
                    enter-from-class="translate-y-4 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                >
                    <section
                        class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <div>
                            <p
                                class="accent-text text-sm font-bold uppercase tracking-widest"
                            >
                                Herramientas
                            </p>

                            <h2 class="mt-1 text-2xl font-black text-slate-900">
                                Accesos rápidos
                            </h2>

                            <p class="mt-2 text-sm text-slate-500">
                                Ingresa directamente a las secciones
                                principales.
                            </p>
                        </div>

                        <div
                            class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
                        >
                            <button
                                v-for="acceso in accesosRapidos"
                                :key="acceso.titulo"
                                type="button"
                                class="group flex items-center gap-4 rounded-3xl border border-slate-200 bg-slate-50 p-5 text-left transition duration-300 hover:-translate-y-1 hover:border-blue-500 hover:bg-slate-100 hover:shadow-md"
                                @click="navegar(acceso.ruta)"
                            >
                                <div
                                    class="accent-soft-bg accent-text flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-lg transition duration-300 group-hover:scale-105"
                                >
                                    <i :class="acceso.icono"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="font-black text-slate-900">
                                        {{ acceso.titulo }}
                                    </h3>

                                    <p
                                        class="mt-1 text-sm leading-5 text-slate-500"
                                    >
                                        {{ acceso.descripcion }}
                                    </p>
                                </div>

                                <i
                                    class="fa-solid fa-chevron-right accent-text text-sm transition duration-300 group-hover:translate-x-1"
                                ></i>
                            </button>
                        </div>
                    </section>
                </Transition>
            </div>
        </main>

        <footer
            class="fixed bottom-4 left-4 z-50 bg-transparent p-0 sm:bottom-6 sm:left-6"
        >
            <PageVisitCounter compact />
        </footer>
    </div>
</template>
