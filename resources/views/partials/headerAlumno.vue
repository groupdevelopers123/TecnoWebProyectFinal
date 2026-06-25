<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from "vue";

import { Link, router, usePage } from "@inertiajs/vue3";

const page = usePage();

const tokenCsrf =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") ?? "";

const ofertasActivas = computed(() => {
    return page.url.startsWith("/alumno/ofertas-academicas");
});
const carrerasActivas = computed(() => {
    return page.url.startsWith("/alumno/carreras-inscritas");
});
const materiasActivas = computed(() => {
    return page.url.startsWith("/alumno/materias-inscritas");
});
const headerRef = ref(null);
const panelAbierto = ref(null);
const menuMovilAbierto = ref(false);
const seccionActiva = ref("inicio");
const aviso = ref("");

let temporizadorAviso = null;

const usuario = computed(() => {
    return page.props.auth?.user ?? {};
});

const nombreCompleto = computed(() => {
    if (usuario.value.nombre_completo) {
        return usuario.value.nombre_completo;
    }

    const partesNombre = [
        usuario.value.nombre,
        usuario.value.apellido_paterno,
        usuario.value.apellido_materno,
    ].filter(Boolean);

    if (partesNombre.length > 0) {
        return partesNombre.join(" ");
    }

    return usuario.value.name ?? "Alumno";
});

const correoAlumno = computed(() => {
    return usuario.value.email ?? "Correo no registrado";
});

const iniciales = computed(() => {
    return nombreCompleto.value
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((palabra) => palabra.charAt(0).toUpperCase())
        .join("");
});

const fotoPerfil = computed(() => {
    return (
        usuario.value.foto_url ??
        usuario.value.foto_perfil_url ??
        usuario.value.foto ??
        null
    );
});

const cantidadMensajes = computed(() => {
    return Number(page.props.cantidad_mensajes ?? 0);
});

const cantidadNotificaciones = computed(() => {
    return Number(page.props.cantidad_notificaciones ?? 0);
});

const mostrarAviso = (mensaje) => {
    aviso.value = mensaje;

    if (temporizadorAviso) {
        clearTimeout(temporizadorAviso);
    }

    temporizadorAviso = setTimeout(() => {
        aviso.value = "";
    }, 2500);
};

const alternarPanel = (panel) => {
    panelAbierto.value = panelAbierto.value === panel ? null : panel;

    menuMovilAbierto.value = false;
};

const alternarMenuMovil = () => {
    menuMovilAbierto.value = !menuMovilAbierto.value;
    panelAbierto.value = null;
};

const seleccionarSeccion = (seccion, nombreVisible) => {
    seccionActiva.value = seccion;
    menuMovilAbierto.value = false;
    panelAbierto.value = null;

    mostrarAviso(`${nombreVisible}: la vista todavía no fue creada.`);
};

const seleccionarOpcionPerfil = (nombreOpcion) => {
    panelAbierto.value = null;

    mostrarAviso(`${nombreOpcion}: esta opción se habilitará próximamente.`);
};

const irAMisPagos = () => {
    cerrarMenus();
    router.visit("/alumno/mis-pagos");
};

const irAMisCreditos = () => {
    cerrarMenus();
    router.visit("/alumno/mis-creditos");
};

const irAPerfil = () => {
    cerrarMenus();
    router.visit("/perfil");
};

const irAConfiguracion = () => {
    cerrarMenus();
    router.visit("/configuraciones");
};

const cerrarMenus = () => {
    panelAbierto.value = null;
    menuMovilAbierto.value = false;
};

const cerrarAlPresionarFuera = (event) => {
    if (headerRef.value && !headerRef.value.contains(event.target)) {
        cerrarMenus();
    }
};

const cerrarConEscape = (event) => {
    if (event.key === "Escape") {
        cerrarMenus();
    }
};

onMounted(() => {
    document.addEventListener("click", cerrarAlPresionarFuera);

    document.addEventListener("keydown", cerrarConEscape);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", cerrarAlPresionarFuera);

    document.removeEventListener("keydown", cerrarConEscape);

    if (temporizadorAviso) {
        clearTimeout(temporizadorAviso);
    }
});
</script>

<template>
    <header
        ref="headerRef"
        class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur-xl"
    >
        <div
            class="mx-auto flex h-[72px] max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8"
        >
            <!-- Logo -->
            <button
                type="button"
                class="flex shrink-0 items-center gap-3 text-left"
                @click="seleccionarSeccion('inicio', 'Inicio')"
            >
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-700 to-blue-500 text-sm font-black tracking-wide text-white shadow-lg shadow-blue-200"
                >
                    IA
                </div>

                <div class="hidden sm:block">
                    <p
                        class="text-[15px] font-black leading-tight text-slate-900"
                    >
                        Instituto Andrés Ibáñez
                    </p>

                    <p
                        class="mt-0.5 text-[11px] font-bold uppercase tracking-widest text-slate-400"
                    >
                        Panel del Alumno
                    </p>
                </div>
            </button>

            <!-- Navegación central -->
            <nav
                class="hidden flex-1 items-center justify-center gap-1 lg:flex"
            >
                <!-- Mis Carreras -->
                <Link
                    href="/alumno/carreras-inscritas"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold transition"
                    :class="
                        carrerasActivas
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-500 hover:bg-slate-100 hover:text-blue-700'
                    "
                    @click="cerrarMenus"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M22 10 12 5 2 10l10 5 10-5Z" />
                        <path d="M6 12v5c3 2 9 2 12 0v-5" />
                    </svg>

                    Mis Carreras
                </Link>

                <!-- Mis Materias -->
                <Link
                    href="/alumno/materias-inscritas"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold transition"
                    :class="
                        materiasActivas
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-500 hover:bg-slate-100 hover:text-blue-700'
                    "
                    @click="cerrarMenus"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path
                            d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"
                        />
                    </svg>

                    Mis Materias
                </Link>

                <!-- Ofertas académicas -->
                <Link
                    href="/alumno/ofertas-academicas"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold transition"
                    :class="
                        ofertasActivas
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-500 hover:bg-slate-100 hover:text-blue-700'
                    "
                    @click="cerrarMenus"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <rect width="18" height="18" x="3" y="4" rx="2" />

                        <path d="M16 2v4M8 2v4M3 10h18" />
                    </svg>

                    Ofertas Académicas
                </Link>
            </nav>

            <!-- Acciones -->
            <div class="flex shrink-0 items-center gap-1 sm:gap-2">
                <!-- Mensajes -->
                <div class="relative">
                    <button
                        type="button"
                        title="Mensajes"
                        aria-label="Abrir mensajes"
                        class="relative flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-blue-50 hover:text-blue-700"
                        :class="{
                            'bg-blue-50 text-blue-700':
                                panelAbierto === 'mensajes',
                        }"
                        @click.stop="alternarPanel('mensajes')"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <rect width="20" height="16" x="2" y="4" rx="2" />

                            <path d="m22 7-10 6L2 7" />
                        </svg>

                        <span
                            v-if="cantidadMensajes > 0"
                            class="absolute -right-0.5 -top-0.5 flex min-h-4 min-w-4 items-center justify-center rounded-full bg-blue-600 px-1 text-[9px] font-black text-white ring-2 ring-white"
                        >
                            {{
                                cantidadMensajes > 99 ? "99+" : cantidadMensajes
                            }}
                        </span>
                    </button>

                    <!-- Panel mensajes -->
                    <Transition
                        enter-active-class="transition duration-150"
                        enter-from-class="-translate-y-2 opacity-0"
                        enter-to-class="translate-y-0 opacity-100"
                        leave-active-class="transition duration-100"
                        leave-from-class="translate-y-0 opacity-100"
                        leave-to-class="-translate-y-2 opacity-0"
                    >
                        <div
                            v-if="panelAbierto === 'mensajes'"
                            class="absolute right-0 top-full mt-3 w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
                        >
                            <div class="border-b border-slate-100 px-4 py-3">
                                <h3 class="text-sm font-black text-slate-900">
                                    Mensajes
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Bandeja del alumno
                                </p>
                            </div>

                            <div
                                class="flex flex-col items-center px-5 py-8 text-center"
                            >
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"
                                >
                                    <svg
                                        class="h-6 w-6"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <rect
                                            width="20"
                                            height="16"
                                            x="2"
                                            y="4"
                                            rx="2"
                                        />

                                        <path d="m22 7-10 6L2 7" />
                                    </svg>
                                </div>

                                <p
                                    class="mt-4 text-sm font-bold text-slate-700"
                                >
                                    Módulo pendiente
                                </p>

                                <p
                                    class="mt-1 text-xs leading-5 text-slate-500"
                                >
                                    La vista de mensajes todavía no fue creada.
                                </p>
                            </div>
                        </div>
                    </Transition>
                </div>

                <!-- Notificaciones -->
                <div class="relative">
                    <button
                        type="button"
                        title="Notificaciones"
                        aria-label="Abrir notificaciones"
                        class="relative flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-blue-50 hover:text-blue-700"
                        :class="{
                            'bg-blue-50 text-blue-700':
                                panelAbierto === 'notificaciones',
                        }"
                        @click.stop="alternarPanel('notificaciones')"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"
                            />

                            <path d="M13.7 21a2 2 0 0 1-3.4 0" />
                        </svg>

                        <span
                            v-if="cantidadNotificaciones > 0"
                            class="absolute -right-0.5 -top-0.5 flex min-h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-black text-white ring-2 ring-white"
                        >
                            {{
                                cantidadNotificaciones > 99
                                    ? "99+"
                                    : cantidadNotificaciones
                            }}
                        </span>
                    </button>

                    <!-- Panel notificaciones -->
                    <Transition
                        enter-active-class="transition duration-150"
                        enter-from-class="-translate-y-2 opacity-0"
                        enter-to-class="translate-y-0 opacity-100"
                        leave-active-class="transition duration-100"
                        leave-from-class="translate-y-0 opacity-100"
                        leave-to-class="-translate-y-2 opacity-0"
                    >
                        <div
                            v-if="panelAbierto === 'notificaciones'"
                            class="absolute right-0 top-full mt-3 w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
                        >
                            <div class="border-b border-slate-100 px-4 py-3">
                                <h3 class="text-sm font-black text-slate-900">
                                    Notificaciones
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Avisos académicos
                                </p>
                            </div>

                            <div
                                class="flex flex-col items-center px-5 py-8 text-center"
                            >
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600"
                                >
                                    <svg
                                        class="h-6 w-6"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"
                                        />

                                        <path d="M13.7 21a2 2 0 0 1-3.4 0" />
                                    </svg>
                                </div>

                                <p
                                    class="mt-4 text-sm font-bold text-slate-700"
                                >
                                    Sin notificaciones
                                </p>

                                <p
                                    class="mt-1 text-xs leading-5 text-slate-500"
                                >
                                    La vista de notificaciones todavía no fue
                                    creada.
                                </p>
                            </div>
                        </div>
                    </Transition>
                </div>

                <!-- Perfil -->
                <div class="relative">
                    <button
                        type="button"
                        aria-label="Abrir menú de perfil"
                        title="Perfil"
                        class="flex h-10 w-10 items-center justify-center rounded-full text-slate-500 transition hover:bg-blue-50 hover:text-blue-700"
                        :class="{
                            'bg-blue-50 text-blue-700':
                                panelAbierto === 'perfil',
                        }"
                        :aria-expanded="panelAbierto === 'perfil'"
                        @click.stop="alternarPanel('perfil')"
                    >
                        <svg
                            class="h-7 w-7"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M12 2a5 5 0 1 0 0 10 5 5 0 0 0 0-10ZM9 7a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3 7c-5.05 0-9 2.75-9 6a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1c0-3.25-3.95-6-9-6Zm-6.76 5c.73-1.53 3.31-3 6.76-3s6.03 1.47 6.76 3H5.24Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>

                    <!-- Menú de perfil -->
                    <Transition
                        enter-active-class="transition duration-150 ease-out"
                        enter-from-class="-translate-y-2 opacity-0"
                        enter-to-class="translate-y-0 opacity-100"
                        leave-active-class="transition duration-100 ease-in"
                        leave-from-class="translate-y-0 opacity-100"
                        leave-to-class="-translate-y-2 opacity-0"
                    >
                        <div
                            v-if="panelAbierto === 'perfil'"
                            class="absolute right-0 top-full mt-3 w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-300/40"
                        >
                            <!-- Información superior -->
                            <div
                                class="border-b border-slate-100 bg-slate-50 px-4 py-4"
                            >
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="fotoPerfil"
                                        :src="fotoPerfil"
                                        :alt="nombreCompleto"
                                        class="h-11 w-11 rounded-xl object-cover"
                                    />

                                    <span
                                        v-else
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-violet-600 text-sm font-black text-white"
                                    >
                                        {{ iniciales }}
                                    </span>

                                    <div class="min-w-0">
                                        <p
                                            class="truncate text-sm font-black text-slate-900"
                                        >
                                            {{ nombreCompleto }}
                                        </p>

                                        <p
                                            class="mt-1 truncate text-xs text-slate-500"
                                        >
                                            {{ correoAlumno }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Opciones -->
                            <div class="p-2">
                                <button
                                    type="button"
                                    class="opcion-perfil"
                                    @click="irAPerfil"
                                >
                                    <span
                                        class="icono-opcion bg-blue-50 text-blue-600"
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path d="M20 21a8 8 0 0 0-16 0" />

                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                    </span>

                                    Mi Perfil
                                </button>

                                <Link
                                    href="/alumno/materias-inscritas"
                                    class="opcion-perfil"
                                    @click="cerrarMenus"
                                >
                                    <span
                                        class="icono-opcion bg-emerald-50 text-emerald-600"
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"
                                            />

                                            <path
                                                d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"
                                            />
                                        </svg>
                                    </span>

                                    Mis Materias
                                </Link>

                                <Link
                                    href="/alumno/carreras-inscritas"
                                    class="opcion-perfil"
                                    @click="cerrarMenus"
                                >
                                    <span
                                        class="icono-opcion bg-violet-50 text-violet-600"
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                d="m22 10-10-5-10 5 10 5 10-5Z"
                                            />

                                            <path d="M6 12v5c3 2 9 2 12 0v-5" />
                                        </svg>
                                    </span>

                                    Mis Carreras
                                </Link>

                                <button
                                    type="button"
                                    class="opcion-perfil"
                                    @click="irAMisPagos"
                                >
                                    <span
                                        class="icono-opcion bg-cyan-50 text-cyan-600"
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <rect
                                                width="18"
                                                height="14"
                                                x="3"
                                                y="5"
                                                rx="2"
                                            />

                                            <path d="M3 10h18" />
                                        </svg>
                                    </span>

                                    Mis Pagos
                                </button>

                                <button
                                    type="button"
                                    class="opcion-perfil"
                                    @click="irAMisCreditos"
                                >
                                    <span
                                        class="icono-opcion bg-amber-50 text-amber-600"
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <circle cx="12" cy="12" r="9" />

                                            <path
                                                d="M16 8h-6a2 2 0 0 0 0 4h4a2 2 0 0 1 0 4H8"
                                            />

                                            <path d="M12 6v12" />
                                        </svg>
                                    </span>

                                    Mis Créditos
                                </button>

                                <button
                                    type="button"
                                    class="opcion-perfil"
                                    @click="irAConfiguracion"
                                >
                                    <span
                                        class="icono-opcion bg-slate-100 text-slate-600"
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <circle cx="12" cy="12" r="3" />

                                            <path
                                                d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1V21H9.6v-.09a1.7 1.7 0 0 0-1.1-1.51 1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1-.4H3V9.6h.09A1.7 1.7 0 0 0 4.6 8.5a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1V3h4v.09A1.7 1.7 0 0 0 15.5 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 9c.26.35.47.72.6 1 .12.3.2.65.2 1v1c0 .35-.08.7-.2 1-.13.28-.34.65-.6 1Z"
                                            />
                                        </svg>
                                    </span>

                                    Configuración
                                </button>
                            </div>

                            <!-- Cerrar sesión -->
                            <form
                                action="/logout"
                                method="POST"
                                class="border-t border-slate-100 p-2"
                                @submit="cerrarMenus"
                            >
                                <input
                                    type="hidden"
                                    name="_token"
                                    :value="tokenCsrf"
                                />

                                <button
                                    type="submit"
                                    class="flex w-full cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-bold text-red-600 transition hover:bg-red-50"
                                >
                                    <span
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            aria-hidden="true"
                                        >
                                            <path d="M10 17l5-5-5-5" />

                                            <path d="M15 12H3" />

                                            <path
                                                d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"
                                            />
                                        </svg>
                                    </span>

                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </Transition>
                </div>

                <!-- Botón móvil -->
                <button
                    type="button"
                    aria-label="Abrir menú móvil"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-blue-50 hover:text-blue-700 lg:hidden"
                    @click.stop="alternarMenuMovil"
                >
                    <svg
                        v-if="!menuMovilAbierto"
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>

                    <svg
                        v-else
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m6 6 12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú móvil -->
        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="-translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-2 opacity-0"
        >
            <div
                v-if="menuMovilAbierto"
                class="border-t border-slate-100 bg-white px-4 py-3 lg:hidden"
            >
                <div class="mx-auto grid max-w-7xl gap-2 sm:grid-cols-4">
                    <Link
                        href="/alumno/carreras-inscritas"
                        class="boton-movil"
                        @click="cerrarMenus"
                    >
                        Mis Carreras
                    </Link>

                    <Link
                        href="/alumno/materias-inscritas"
                        class="boton-movil"
                        :class="{
                            'border-blue-200 bg-blue-50 text-blue-700':
                                materiasActivas,
                        }"
                        @click="cerrarMenus"
                    >
                        Mis Materias
                    </Link>

                    <Link
                        href="/alumno/ofertas-academicas"
                        class="boton-movil"
                        :class="{
                            'border-blue-200 bg-blue-50 text-blue-700':
                                ofertasActivas,
                        }"
                        @click="cerrarMenus"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            aria-hidden="true"
                        >
                            <rect width="18" height="18" x="3" y="4" rx="2" />

                            <path d="M16 2v4M8 2v4M3 10h18" />
                        </svg>

                        Ofertas Académicas
                    </Link>

                    <Link
                        href="/configuraciones"
                        class="boton-movil"
                        @click="cerrarMenus"
                    >
                        Configuración
                    </Link>
                </div>
            </div>
        </Transition>

        <!-- Aviso temporal -->
        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="-translate-y-3 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-3 opacity-0"
        >
            <div
                v-if="aviso"
                class="fixed right-4 top-24 z-[70] max-w-sm rounded-2xl border border-blue-200 bg-white px-4 py-3 shadow-xl"
            >
                <div class="flex items-start gap-3">
                    <span
                        class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-sm font-black text-blue-600"
                    >
                        i
                    </span>

                    <p class="text-sm font-semibold leading-5 text-slate-600">
                        {{ aviso }}
                    </p>
                </div>
            </div>
        </Transition>
    </header>
</template>

<style scoped>
.opcion-perfil {
    display: flex;
    width: 100%;
    align-items: center;
    gap: 0.75rem;
    border-radius: 0.75rem;
    padding: 0.625rem 0.75rem;
    text-align: left;
    font-size: 0.875rem;
    font-weight: 600;
    color: rgb(71 85 105);
    transition: 150ms;
}

.opcion-perfil:hover {
    background: rgb(239 246 255);
    color: rgb(29 78 216);
}

.icono-opcion {
    display: flex;
    height: 2rem;
    width: 2rem;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
}

.icono-opcion svg {
    height: 1rem;
    width: 1rem;
}

.boton-movil {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    border: 1px solid rgb(226 232 240);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    background: white;
    font-size: 0.875rem;
    font-weight: 700;
    color: rgb(71 85 105);
    text-decoration: none;
    transition: 150ms;
}

.boton-movil:hover {
    border-color: rgb(191 219 254);
    background: rgb(239 246 255);
    color: rgb(29 78 216);
}
</style>
