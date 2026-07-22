<template>
    <div class="min-h-screen bg-slate-950 text-slate-900">
        <header
            class="sticky top-0 z-30 border-b border-white/10 bg-slate-950/90 backdrop-blur"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8"
            >
                <a
                    href="/"
                    class="flex items-center gap-3"
                    @click.prevent="irA('/')"
                >
                    <img
                        src="/img/logo_2.png"
                        alt="Instituto Andrés Ibáñez"
                        class="h-11 w-auto rounded-xl"
                    />
                    <div class="leading-tight">
                        <p class="text-sm font-semibold text-blue-300">
                            Instituto Andrés Ibáñez
                        </p>
                        <p class="text-xs text-slate-400">Formación integral</p>
                    </div>
                </a>

                <nav
                    class="hidden items-center gap-6 text-sm font-semibold text-slate-300 md:flex"
                >
                    <Link
                        :href="route('welcome')"
                        class="transition hover:text-white"
                    >
                        Inicio
                    </Link>
                    <a
                        href="/carreras"
                        class="transition hover:text-white"
                        @click.prevent="irA('/carreras')"
                    >
                        Carreras
                    </a>
                    <a
                        href="/ofertas-academicas"
                        class="transition hover:text-white"
                        @click.prevent="irA('/ofertas-academicas')"
                    >
                        Ofertas
                    </a>
                    <a
                        href="/docentes"
                        class="transition hover:text-white"
                        @click.prevent="irA('/docentes')"
                    >
                        Docentes
                    </a>
                </nav>

                <div class="flex items-center gap-2">
                    <template v-if="authUser">
                        <button
                            class="inline-flex rounded-full border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-300 transition hover:bg-emerald-500/20"
                            @click.prevent="irA(rutaPanel)"
                        >
                            Ir al panel
                        </button>
                        <button
                            class="inline-flex rounded-full border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-red-500 hover:text-white"
                            @click.prevent="cerrarSesion"
                        >
                            Cerrar sesión
                        </button>

                        <form
                            id="public-logout-form"
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
                    </template>
                    <template v-else>
                        <a
                            href="/login"
                            class="hidden rounded-full border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-blue-500 hover:text-white sm:inline-flex"
                            @click.prevent="irA('/login')"
                        >
                            Iniciar sesión
                        </a>
                        <a
                            href="/register"
                            class="inline-flex rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
                            @click.prevent="irA('/register')"
                        >
                            Registrarse
                        </a>
                    </template>
                </div>
            </div>
        </header>

        <slot />
    </div>
</template>

<script setup>
import { computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const page = usePage();
const authUser = computed(() => page.props.auth?.user ?? null);

const rutaPanel = computed(() => {
    const rol = authUser.value?.role?.nombre;

    switch (rol) {
        case "propietario":
        case "secretaria":
            return "/admin/dashboard";
        case "docente":
            return "/docente/inicio";
        case "alumno":
            return "/alumno/inicio";
        default:
            return "/";
    }
});

const csrfToken = computed(() => {
    return (
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || ""
    );
});

const irA = (ruta) => router.visit(ruta);
const cerrarSesion = () => {
    const form = document.getElementById("public-logout-form");
    if (form) {
        form.submit();
    }
};
</script>
