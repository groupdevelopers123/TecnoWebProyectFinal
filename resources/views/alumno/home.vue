<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

import HeaderAlumno from "@/views/partials/headerAlumno.vue";
import PageVisitCounter from "@/views/partials/PageVisitCounter.vue";

const page = usePage();

/* =========================================================
   DATOS DEL ALUMNO
========================================================= */

const usuario = computed(() => {
    return page.props?.auth?.user ?? {};
});

const alumno = computed(() => {
    return page.props?.alumno ?? usuario.value?.alumno ?? {};
});

const nombreCompleto = computed(() => {
    const user = usuario.value;

    const nombreConstruido = [
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

    return user.nombre_completo || user.name || nombreConstruido || "Alumno";
});

const primerNombre = computed(() => {
    return nombreCompleto.value.split(" ")[0] || "Alumno";
});

const iniciales = computed(() => {
    return nombreCompleto.value
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

const fechaActual = computed(() => {
    const fecha = new Intl.DateTimeFormat("es-BO", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
    }).format(new Date());

    return fecha.charAt(0).toUpperCase() + fecha.slice(1);
});

/* =========================================================
   INFORMACIÓN ACADÉMICA
========================================================= */

const resumenAcademico = computed(() => {
    const resumen = page.props?.resumen_academico ?? {};
    const datosAlumno = alumno.value;

    return {
        carrera:
            resumen.carrera ??
            datosAlumno.carrera?.nombre ??
            datosAlumno.carrera_nombre ??
            "Sin carrera asignada",

        periodo:
            resumen.periodo ??
            page.props?.periodo_actual?.nombre ??
            page.props?.periodo_actual ??
            "Sin período activo",

        materias: Number(
            resumen.materias ??
                page.props?.materias_count ??
                page.props?.cantidad_materias ??
                0,
        ),

        estado: resumen.estado ?? datosAlumno.estado ?? "Activo",
    };
});

const proximasActividades = computed(() => {
    const actividades =
        page.props?.proximas_actividades ?? page.props?.proximas_clases ?? [];

    return Array.isArray(actividades) ? actividades.slice(0, 3) : [];
});

const avisos = computed(() => {
    const datos = page.props?.avisos ?? page.props?.notificaciones ?? [];

    return Array.isArray(datos) ? datos.slice(0, 3) : [];
});
</script>

<template>
    <Head title="Panel del Alumno" />

    <div class="min-h-screen bg-slate-100">
        <HeaderAlumno />

        <main
            class="relative isolate overflow-hidden px-5 pb-24 pt-20 sm:px-6 lg:px-8"
        >
            <!-- Desvanecido superior según el tema -->
            <div
                class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-72 bg-gradient-to-b from-[var(--accent-soft)] via-[var(--page-bg)] to-transparent opacity-70"
            ></div>

            <div
                class="pointer-events-none absolute -left-24 top-20 -z-10 h-72 w-72 rounded-full bg-[var(--accent-soft)] opacity-40 blur-3xl"
            ></div>

            <div
                class="pointer-events-none absolute -right-24 top-36 -z-10 h-80 w-80 rounded-full bg-[var(--surface-tertiary)] opacity-50 blur-3xl"
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
                        <div
                            class="pointer-events-none absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-[var(--accent-soft)] to-transparent opacity-35"
                        ></div>

                        <div
                            class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full bg-[var(--accent-soft)] opacity-60 blur-2xl"
                        ></div>

                        <div
                            class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
                        >
                            <div class="max-w-3xl">
                                <div
                                    class="accent-soft-bg inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-bold"
                                >
                                    <i class="fa-solid fa-user-graduate"></i>

                                    Panel académico
                                </div>

                                <p
                                    class="accent-text mt-5 text-xs font-bold uppercase tracking-[0.18em]"
                                >
                                    {{ fechaActual }}
                                </p>

                                <h1
                                    class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl"
                                >
                                    {{ saludo }},

                                    <span class="accent-text">
                                        {{ primerNombre }}
                                    </span>
                                </h1>

                                <p
                                    class="mt-3 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base"
                                >
                                    Bienvenido a tu espacio académico. Aquí
                                    podrás mantenerte informado sobre tu
                                    carrera, período actual y próximas
                                    actividades.
                                </p>
                            </div>

                            <!-- Identidad del alumno -->
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
                                        {{ nombreCompleto }}
                                    </p>

                                    <div
                                        class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700"
                                    >
                                        <span
                                            class="h-2 w-2 rounded-full bg-emerald-500"
                                        ></span>

                                        Alumno activo
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </Transition>

                <!-- =====================================================
                     RESUMEN ACADÉMICO
                ====================================================== -->

                <Transition
                    appear
                    enter-active-class="transition delay-150 duration-700 ease-out"
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
                                Tu información
                            </p>

                            <h2 class="mt-1 text-2xl font-black text-slate-900">
                                Resumen académico
                            </h2>

                            <p class="mt-2 text-sm text-slate-500">
                                Información principal de tu inscripción actual.
                            </p>
                        </div>

                        <div
                            class="mt-6 grid divide-y divide-slate-200 overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 sm:grid-cols-2 sm:divide-x sm:divide-y-0 lg:grid-cols-4"
                        >
                            <div class="p-5">
                                <div
                                    class="accent-soft-bg accent-text flex h-11 w-11 items-center justify-center rounded-2xl"
                                >
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>

                                <p
                                    class="mt-4 text-xs font-bold uppercase tracking-wider text-slate-500"
                                >
                                    Carrera
                                </p>

                                <p class="mt-1 font-black text-slate-900">
                                    {{ resumenAcademico.carrera }}
                                </p>
                            </div>

                            <div class="p-5">
                                <div
                                    class="accent-soft-bg accent-text flex h-11 w-11 items-center justify-center rounded-2xl"
                                >
                                    <i class="fa-solid fa-calendar-days"></i>
                                </div>

                                <p
                                    class="mt-4 text-xs font-bold uppercase tracking-wider text-slate-500"
                                >
                                    Período actual
                                </p>

                                <p class="mt-1 font-black text-slate-900">
                                    {{ resumenAcademico.periodo }}
                                </p>
                            </div>

                            <div class="p-5">
                                <div
                                    class="accent-soft-bg accent-text flex h-11 w-11 items-center justify-center rounded-2xl"
                                >
                                    <i class="fa-solid fa-book-open"></i>
                                </div>

                                <p
                                    class="mt-4 text-xs font-bold uppercase tracking-wider text-slate-500"
                                >
                                    Materias inscritas
                                </p>

                                <p
                                    class="mt-1 text-2xl font-black text-slate-900"
                                >
                                    {{ resumenAcademico.materias }}
                                </p>
                            </div>

                            <div class="p-5">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700"
                                >
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>

                                <p
                                    class="mt-4 text-xs font-bold uppercase tracking-wider text-slate-500"
                                >
                                    Estado
                                </p>

                                <p class="mt-1 font-black text-emerald-700">
                                    {{ resumenAcademico.estado }}
                                </p>
                            </div>
                        </div>
                    </section>
                </Transition>

                <!-- =====================================================
                     ACTIVIDADES Y AVISOS
                ====================================================== -->

                <Transition
                    appear
                    enter-active-class="transition delay-300 duration-700 ease-out"
                    enter-from-class="translate-y-4 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                >
                    <section class="grid gap-6 lg:grid-cols-2">
                        <!-- Próximas actividades -->
                        <div
                            class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p
                                        class="accent-text text-sm font-bold uppercase tracking-widest"
                                    >
                                        Agenda
                                    </p>

                                    <h2
                                        class="mt-1 text-xl font-black text-slate-900"
                                    >
                                        Próximas actividades
                                    </h2>
                                </div>

                                <div
                                    class="accent-soft-bg accent-text flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl"
                                >
                                    <i class="fa-regular fa-calendar"></i>
                                </div>
                            </div>

                            <div
                                v-if="proximasActividades.length"
                                class="mt-5 space-y-3"
                            >
                                <article
                                    v-for="actividad in proximasActividades"
                                    :key="
                                        actividad.id ??
                                        `${actividad.titulo}-${actividad.fecha}`
                                    "
                                    class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4"
                                >
                                    <div
                                        class="accent-soft-bg accent-text flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl"
                                    >
                                        <i class="fa-solid fa-clock"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <h3
                                            class="truncate font-black text-slate-900"
                                        >
                                            {{
                                                actividad.titulo ??
                                                actividad.materia ??
                                                "Actividad académica"
                                            }}
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{
                                                actividad.fecha ??
                                                actividad.hora ??
                                                "Horario por confirmar"
                                            }}
                                        </p>
                                    </div>
                                </article>
                            </div>

                            <div
                                v-else
                                class="mt-5 rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-5 py-10 text-center"
                            >
                                <div
                                    class="accent-soft-bg accent-text mx-auto flex h-14 w-14 items-center justify-center rounded-2xl text-xl"
                                >
                                    <i class="fa-regular fa-calendar-check"></i>
                                </div>

                                <h3 class="mt-4 font-black text-slate-900">
                                    Sin actividades próximas
                                </h3>

                                <p
                                    class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500"
                                >
                                    Por el momento no tienes actividades
                                    registradas para los próximos días.
                                </p>
                            </div>
                        </div>

                        <!-- Avisos -->
                        <div
                            class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p
                                        class="accent-text text-sm font-bold uppercase tracking-widest"
                                    >
                                        Información
                                    </p>

                                    <h2
                                        class="mt-1 text-xl font-black text-slate-900"
                                    >
                                        Avisos importantes
                                    </h2>
                                </div>

                                <div
                                    class="accent-soft-bg accent-text flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl"
                                >
                                    <i class="fa-solid fa-bell"></i>
                                </div>
                            </div>

                            <div v-if="avisos.length" class="mt-5 space-y-3">
                                <article
                                    v-for="aviso in avisos"
                                    :key="
                                        aviso.id ??
                                        `${aviso.titulo}-${aviso.fecha}`
                                    "
                                    class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
                                >
                                    <div class="flex items-start gap-3">
                                        <span
                                            class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[var(--accent)]"
                                        ></span>

                                        <div>
                                            <h3
                                                class="font-black text-slate-900"
                                            >
                                                {{
                                                    aviso.titulo ??
                                                    "Aviso académico"
                                                }}
                                            </h3>

                                            <p
                                                class="mt-1 text-sm leading-6 text-slate-500"
                                            >
                                                {{
                                                    aviso.mensaje ??
                                                    aviso.descripcion ??
                                                    "Tienes una nueva notificación."
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            </div>

                            <div
                                v-else
                                class="mt-5 rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-5 py-10 text-center"
                            >
                                <div
                                    class="accent-soft-bg accent-text mx-auto flex h-14 w-14 items-center justify-center rounded-2xl text-xl"
                                >
                                    <i class="fa-regular fa-bell-slash"></i>
                                </div>

                                <h3 class="mt-4 font-black text-slate-900">
                                    Todo está al día
                                </h3>

                                <p
                                    class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500"
                                >
                                    No tienes avisos pendientes en este momento.
                                </p>
                            </div>
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
