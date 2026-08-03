<template>
    <Head title="Iniciar sesión" />
    <main
        class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-100 px-4 py-10"
    >
        <div
            class="relative z-10 w-full max-w-md overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl"
        >
            <div
                class="bg-gradient-to-br from-blue-700 via-blue-600 to-violet-700 px-8 py-8 text-center text-white"
            >
                <img
                    src="/img/logo_2.png"
                    alt="Logo Instituto Andrés Ibáñez"
                    class="mx-auto h-20 w-auto object-contain"
                />
                <h1 class="mt-5 text-2xl font-black">Iniciar sesión</h1>
                <p class="mt-2 text-sm text-blue-100">
                    Ingresa tus credenciales para acceder al sistema
                </p>
            </div>
            <form class="space-y-5 px-7 py-8 sm:px-9" @submit.prevent="submit">
                <div
                    v-if="status"
                    class="mb-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                >
                    {{ status }}
                </div>

                <div
                    v-if="info"
                    class="mb-2 flex items-start gap-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700"
                >
                    <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
                    <p>{{ info }}</p>
                </div>

                <div
                    v-if="errorList.length"
                    class="mb-2 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
                >
                    <div class="flex items-start gap-3">
                        <i
                            class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-red-500"
                        ></i>
                        <div>
                            <p class="font-black">No se pudo iniciar sesión</p>
                            <ul class="mt-2 list-inside list-disc space-y-1">
                                <li v-for="(err, i) in errorList" :key="i">
                                    {{ err }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <label class="block text-sm font-bold text-slate-700">
                    Correo electrónico
                    <div class="relative mt-2">
                        <span
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                        >
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </span>

                        <input
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            autofocus
                            required
                            placeholder="Ingresa tu correo electrónico"
                            class="mt-0 w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm font-normal outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
                        />
                    </div>
                </label>

                <label class="block text-sm font-bold text-slate-700">
                    Contraseña
                    <div class="relative mt-2">
                        <span
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                        >
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>

                        <input
                            v-model="form.password"
                            :type="passwordType"
                            id="password"
                            required
                            autocomplete="current-password"
                            placeholder="Ingresa tu contraseña"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-12 text-sm font-normal text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
                        />

                        <button
                            type="button"
                            @click.prevent="togglePassword"
                            :aria-label="
                                passwordVisible
                                    ? 'Ocultar contraseña'
                                    : 'Mostrar contraseña'
                            "
                            class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-600"
                        >
                            <i :class="['fa-solid', passwordIcon]"></i>
                        </button>
                    </div>
                </label>

                <label
                    class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600"
                >
                    <input
                        v-model="form.remember"
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-blue-600"
                    />
                    Recordar sesión
                </label>

                <button
                    :disabled="form.processing"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-black text-white transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:opacity-60"
                >
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    {{ form.processing ? "Ingresando..." : "Iniciar sesión" }}
                </button>

                <Link
                    :href="route('register')"
                    class="block text-center text-sm font-bold text-blue-600 hover:text-blue-800"
                    >Crear una cuenta</Link
                >
            </form>
        </div>
    </main>
</template>

<script setup>
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";

const page = usePage();
const flash = computed(() => page.props.flash || {});
const status = computed(() => flash.value.status ?? null);
const info = computed(() => flash.value.info ?? null);

const form = useForm({ email: "", password: "", remember: false });

const passwordVisible = ref(false);
const passwordType = computed(() =>
    passwordVisible.value ? "text" : "password",
);
const passwordIcon = computed(() =>
    passwordVisible.value ? "fa-eye-slash" : "fa-eye",
);

function togglePassword() {
    passwordVisible.value = !passwordVisible.value;
}

const submit = () => form.post(route("login.store"));

const errorList = computed(() => {
    const vals = Object.values(form.errors || {});
    return vals.flat ? vals.flat() : vals.reduce((acc, v) => acc.concat(v), []);
});

onMounted(() => {
    console.log("Login.vue mounted", {
        flash: page.props.flash || {},
        errors: form.errors,
    });
});
</script>
