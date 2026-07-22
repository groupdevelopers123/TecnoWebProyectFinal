<template>
    <Head title="Crear cuenta" />
    <main class="min-h-screen bg-slate-100">
        <div
            class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(37,99,235,0.22),_transparent_34%),linear-gradient(135deg,_#eff6ff,_#f8fafc)] px-4 py-10"
        >
            <div
                class="mx-auto w-full max-w-5xl animate-[fadeUp_.45s_ease-out] overflow-hidden rounded-[2rem] border border-white/70 bg-white/90 shadow-2xl shadow-slate-900/10 backdrop-blur-xl"
            >
                <div
                    class="border-b border-slate-100 bg-gradient-to-r from-blue-700 via-blue-600 to-sky-500 px-6 py-8 text-white sm:px-10 sm:py-10"
                >
                    <div
                        class="mx-auto flex max-w-5xl flex-col items-center gap-6 text-center sm:flex-row sm:items-center sm:gap-8 sm:text-left"
                    >
                        <img
                            src="/img/logo_2.png"
                            alt="Logo Instituto Andrés Ibáñez"
                            class="h-20 w-auto max-w-[180px] shrink-0 object-contain sm:h-24"
                        />

                        <div class="min-w-0">
                            <p
                                class="text-xs font-bold uppercase tracking-[0.25em] text-blue-100"
                            >
                                Instituto Andrés Ibáñez
                            </p>

                            <h1
                                class="mt-2 text-3xl font-black tracking-tight sm:text-4xl"
                            >
                                Registro de alumno
                            </h1>

                            <p
                                class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-blue-50 sm:mx-0 sm:text-base"
                            >
                                Crea tu cuenta para acceder al sistema e iniciar
                                tu proceso de inscripción académica.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-10">
                    <form @submit.prevent="submit" class="space-y-8">
                        <div
                            v-if="info"
                            class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-bold text-blue-700"
                        >
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-info mt-0.5"></i>
                                <span>{{ info }}</span>
                            </div>
                        </div>

                        <div
                            v-if="activeErrorMessages.length"
                            class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-700"
                        >
                            <div class="flex items-start gap-3">
                                <i
                                    class="fa-solid fa-circle-exclamation mt-0.5"
                                ></i>
                                <div>
                                    <p class="font-bold">
                                        Revisa los siguientes errores:
                                    </p>
                                    <ul class="mt-2 list-inside list-disc">
                                        <li
                                            v-for="(
                                                msg, idx
                                            ) in activeErrorMessages"
                                            :key="idx"
                                        >
                                            {{ msg }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <section>
                            <div class="mb-5 flex items-center gap-3">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-700"
                                >
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <h2
                                        class="text-lg font-black text-slate-900"
                                    >
                                        Datos personales
                                    </h2>
                                    <p class="text-sm text-slate-500">
                                        Información principal del estudiante.
                                    </p>
                                </div>
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <Field
                                    v-model="form.nombres"
                                    label="Nombres"
                                    name="nombres"
                                    :error="
                                        validationErrors.nombres ||
                                        form.errors.nombres
                                    "
                                    required
                                />
                                <Field
                                    v-model="form.apellidos"
                                    label="Apellidos"
                                    name="apellidos"
                                    :error="
                                        validationErrors.apellidos ||
                                        form.errors.apellidos
                                    "
                                    required
                                />

                                <Field
                                    v-model="form.ci"
                                    label="Cédula de identidad"
                                    name="ci"
                                    :error="
                                        validationErrors.ci || form.errors.ci
                                    "
                                    required
                                />

                                <Field
                                    v-model="form.fecha_nacimiento"
                                    label="Fecha de nacimiento"
                                    name="fecha_nacimiento"
                                    type="date"
                                    :error="
                                        validationErrors.fecha_nacimiento ||
                                        form.errors.fecha_nacimiento
                                    "
                                />

                                <Field
                                    v-model="form.telefono"
                                    label="Teléfono"
                                    name="telefono"
                                    :error="
                                        validationErrors.telefono ||
                                        form.errors.telefono
                                    "
                                />
                                <Field
                                    v-model="form.email"
                                    label="Correo electrónico"
                                    name="email"
                                    type="email"
                                    :error="
                                        validationErrors.email ||
                                        form.errors.email
                                    "
                                    required
                                />

                                <Field
                                    v-model="form.direccion"
                                    label="Dirección"
                                    name="direccion"
                                    :error="
                                        validationErrors.direccion ||
                                        form.errors.direccion
                                    "
                                    class="md:col-span-2"
                                />

                                <Field
                                    v-model="form.colegio_origen"
                                    label="Colegio de origen"
                                    name="colegio_origen"
                                    :error="
                                        validationErrors.colegio_origen ||
                                        form.errors.colegio_origen
                                    "
                                />
                                <Field
                                    v-model="form.anio_bachillerato"
                                    label="Año de bachillerato"
                                    name="anio_bachillerato"
                                    type="number"
                                    :error="
                                        validationErrors.anio_bachillerato ||
                                        form.errors.anio_bachillerato
                                    "
                                />

                                <div class="md:col-span-2">
                                    <label
                                        for="estado_academico"
                                        class="mb-2 block text-sm font-bold text-slate-700"
                                        >Estado académico</label
                                    >
                                    <select
                                        id="estado_academico"
                                        v-model="form.estado_academico"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    >
                                        <option value="nuevo">Nuevo</option>
                                        <option value="bachiller">
                                            Bachiller
                                        </option>
                                        <option value="universitario">
                                            Universitario
                                        </option>
                                        <option value="profesional">
                                            Profesional
                                        </option>
                                    </select>
                                    <p
                                        class="mt-1 text-sm text-red-600"
                                        v-if="form.errors.estado_academico"
                                    >
                                        {{ form.errors.estado_academico }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section class="border-t border-slate-100 pt-8">
                            <div class="mb-5 flex items-center gap-3">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-700"
                                >
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <h2
                                        class="text-lg font-black text-slate-900"
                                    >
                                        Datos académicos
                                    </h2>
                                    <p class="text-sm text-slate-500">
                                        Información académica previa del
                                        estudiante.
                                    </p>
                                </div>
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <Field
                                    v-model="form.colegio_origen"
                                    label="Colegio de origen"
                                    name="colegio_origen"
                                    :error="
                                        validationErrors.colegio_origen ||
                                        form.errors.colegio_origen
                                    "
                                />
                                <Field
                                    v-model="form.anio_bachillerato"
                                    label="Año de bachillerato"
                                    name="anio_bachillerato"
                                    type="number"
                                    :error="
                                        validationErrors.anio_bachillerato ||
                                        form.errors.anio_bachillerato
                                    "
                                />
                            </div>
                        </section>

                        <section class="border-t border-slate-100 pt-8">
                            <div class="mb-5 flex items-center gap-3">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-green-50 text-green-700"
                                >
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div>
                                    <h2
                                        class="text-lg font-black text-slate-900"
                                    >
                                        Seguridad de la cuenta
                                    </h2>
                                    <p class="text-sm text-slate-500">
                                        Crea una contraseña segura para ingresar
                                        al sistema.
                                    </p>
                                </div>
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label
                                        for="password"
                                        class="mb-2 block text-sm font-bold text-slate-700"
                                        >Contraseña</label
                                    >
                                    <div class="relative">
                                        <input
                                            v-model="form.password"
                                            :type="passwordType"
                                            id="password"
                                            name="password"
                                            required
                                            autocomplete="new-password"
                                            placeholder="Ingrese una contraseña"
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                        />
                                        <button
                                            type="button"
                                            @click.prevent="togglePassword"
                                            class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-600"
                                        >
                                            <i
                                                :class="[
                                                    'fa-solid',
                                                    passwordIcon,
                                                ]"
                                            ></i>
                                        </button>
                                    </div>
                                    <p
                                        class="mt-1 text-sm text-red-600"
                                        v-if="
                                            validationErrors.password ||
                                            form.errors.password
                                        "
                                    >
                                        {{
                                            validationErrors.password ||
                                            form.errors.password
                                        }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="password_confirmation"
                                        class="mb-2 block text-sm font-bold text-slate-700"
                                        >Confirmar contraseña</label
                                    >
                                    <div class="relative">
                                        <input
                                            v-model="form.password_confirmation"
                                            :type="confirmType"
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            required
                                            autocomplete="new-password"
                                            placeholder="Repita la contraseña"
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                        />
                                        <button
                                            type="button"
                                            @click.prevent="toggleConfirm"
                                            class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-600"
                                        >
                                            <i
                                                :class="[
                                                    'fa-solid',
                                                    confirmIcon,
                                                ]"
                                            ></i>
                                        </button>
                                    </div>
                                    <p
                                        class="mt-1 text-sm text-red-600"
                                        v-if="
                                            validationErrors.password_confirmation ||
                                            form.errors.password_confirmation
                                        "
                                    >
                                        {{
                                            validationErrors.password_confirmation ||
                                            form.errors.password_confirmation
                                        }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <input
                            v-model="form.oferta_academica_id"
                            type="hidden"
                        />
                        <input v-model="form.role" type="hidden" />

                        <div
                            class="flex flex-col gap-4 border-t border-slate-100 pt-8 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div>
                                <p class="text-sm font-bold text-slate-700">
                                    ¿Ya tienes una cuenta?
                                </p>
                                <Link
                                    :href="route('login')"
                                    class="mt-1 inline-flex items-center gap-2 text-sm font-bold text-blue-700 transition hover:text-blue-800"
                                    >Iniciar sesión
                                    <i
                                        class="fa-solid fa-arrow-right text-xs"
                                    ></i
                                ></Link>
                            </div>

                            <button
                                type="submit"
                                :disabled="submitButtonDisabled"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-7 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/25 transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:opacity-60"
                            >
                                <i class="fa-solid fa-user-plus text-xs"></i>
                                {{
                                    form.processing
                                        ? "Registrando..."
                                        : "Crear cuenta de alumno"
                                }}
                            </button>
                        </div>
                    </form>

                    <a
                        :href="route('welcome')"
                        class="mt-8 inline-flex w-full items-center justify-center gap-2 text-sm font-bold text-slate-500 transition hover:text-blue-700"
                    >
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        Volver a la página principal
                    </a>
                </div>
            </div>
        </div>
    </main>
</template>

<script setup>
import { computed, reactive, watch, ref } from "vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import Field from "../components/FormField.vue";

const page = usePage();
const info = computed(() => page.props.flash?.info ?? null);

const props = defineProps({
    ofertaAcademicaId: { type: [String, Number], default: null },
    role: { type: String, default: "alumno" },
});

const form = useForm({
    nombres: "",
    apellidos: "",
    ci: "",
    email: "",
    telefono: "",
    direccion: "",
    fecha_nacimiento: "",
    colegio_origen: "",
    anio_bachillerato: "",
    estado_academico: "nuevo",
    oferta_academica_id: props.ofertaAcademicaId,
    role: props.role,
    password: "",
    password_confirmation: "",
});

const validationErrors = reactive({
    nombres: "",
    apellidos: "",
    ci: "",
    email: "",
    telefono: "",
    direccion: "",
    fecha_nacimiento: "",
    colegio_origen: "",
    anio_bachillerato: "",
    password: "",
    password_confirmation: "",
});

const currentYear = new Date().getFullYear();

const patterns = {
    nombres: /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+$/,
    ci: /^[0-9\-]+$/,
    email: /\S+@\S+\.\S+/,
    telefono: /^[0-9 +\-]+$/,
    colegio_origen: /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ0-9 .,#\-]+$/,
};

const validateField = (field) => {
    const value = String(form[field] ?? "").trim();

    switch (field) {
        case "nombres":
        case "apellidos":
            if (!value)
                validationErrors[field] =
                    `Debe ingresar sus ${field === "nombres" ? "nombres" : "apellidos"}.`;
            else if (!patterns.nombres.test(value))
                validationErrors[field] = "Solo se permiten letras y espacios.";
            else if (value.length > 100)
                validationErrors[field] = "No puede exceder 100 caracteres.";
            else validationErrors[field] = "";
            break;
        case "ci":
        case "email":
            if (!value) {
                validationErrors[field] =
                    field === "ci"
                        ? "Debe ingresar su Cédula de identidad."
                        : "Debe ingresar su correo electrónico.";
            } else if (field === "ci" && !patterns.ci.test(value))
                validationErrors.ci = "Solo se permiten números y guiones.";
            else if (field === "email" && !patterns.email.test(value))
                validationErrors.email =
                    "Ingrese un correo electrónico válido.";
            else if (field === "ci" && value.length > 20)
                validationErrors.ci = "No puede exceder 20 caracteres.";
            else if (field === "email" && value.length > 150)
                validationErrors.email = "No puede exceder 150 caracteres.";
            else validationErrors[field] = "";
            break;
        case "telefono":
            if (!value) validationErrors.telefono = "";
            else if (!patterns.telefono.test(value))
                validationErrors.telefono =
                    "Solo se permiten números, espacios, + y guiones.";
            else if (value.length > 30)
                validationErrors.telefono = "No puede exceder 30 caracteres.";
            else validationErrors.telefono = "";
            break;
        case "direccion":
            if (!value) validationErrors.direccion = "";
            else if (value.length > 200)
                validationErrors.direccion = "No puede exceder 200 caracteres.";
            else validationErrors.direccion = "";
            break;
        case "fecha_nacimiento":
            if (!value) validationErrors.fecha_nacimiento = "";
            else {
                const date = new Date(value);
                const today = new Date();
                if (Number.isNaN(date.getTime()))
                    validationErrors.fecha_nacimiento =
                        "Debe ingresar una fecha válida.";
                else if (date > today)
                    validationErrors.fecha_nacimiento =
                        "La fecha no puede ser futura.";
                else validationErrors.fecha_nacimiento = "";
            }
            break;
        case "colegio_origen":
            if (!value) validationErrors.colegio_origen = "";
            else if (!patterns.colegio_origen.test(value))
                validationErrors.colegio_origen =
                    "Solo se permiten letras, números y signos básicos.";
            else if (value.length > 150)
                validationErrors.colegio_origen =
                    "No puede exceder 150 caracteres.";
            else validationErrors.colegio_origen = "";
            break;
        case "anio_bachillerato":
            if (!value) validationErrors.anio_bachillerato = "";
            else {
                const year = Number(value);
                if (
                    !Number.isInteger(year) ||
                    String(year).length !== value.length
                )
                    validationErrors.anio_bachillerato =
                        "Debe ingresar un año válido.";
                else if (year < 1950 || year > currentYear)
                    validationErrors.anio_bachillerato = `Ingrese un año entre 1950 y ${currentYear}.`;
                else validationErrors.anio_bachillerato = "";
            }
            break;
        case "password":
            if (!value)
                validationErrors.password = "Debe ingresar una contraseña.";
            else if (value.length < 8)
                validationErrors.password = "Debe tener al menos 8 caracteres.";
            else if (!/[A-Z]/.test(value))
                validationErrors.password =
                    "Debe incluir al menos una mayúscula.";
            else if (!/[a-z]/.test(value))
                validationErrors.password =
                    "Debe incluir al menos una minúscula.";
            else if (!/[0-9]/.test(value))
                validationErrors.password = "Debe incluir al menos un número.";
            else if (!/[!@#$%^&*(),.?\":{}|<>_\-\[\];'`~+/=]/.test(value))
                validationErrors.password = "Debe incluir al menos un símbolo.";
            else validationErrors.password = "";
            if (form.password_confirmation)
                validateField("password_confirmation");
            break;
        case "password_confirmation":
            if (!value)
                validationErrors.password_confirmation =
                    "Debe confirmar la contraseña.";
            else if (value !== form.password)
                validationErrors.password_confirmation =
                    "La confirmación no coincide.";
            else validationErrors.password_confirmation = "";
            break;
    }
};

const requiredFields = [
    "nombres",
    "apellidos",
    "ci",
    "email",
    "password",
    "password_confirmation",
];

const hasValidationErrors = computed(() =>
    Object.values(validationErrors).some((value) => Boolean(value)),
);
const hasMissingRequiredFields = computed(() =>
    requiredFields.some((field) => !String(form[field] ?? "").trim()),
);
const submitButtonDisabled = computed(
    () =>
        form.processing ||
        hasValidationErrors.value ||
        hasMissingRequiredFields.value,
);
const activeErrorMessages = computed(() => {
    const localErrors = Object.values(validationErrors).filter(Boolean);
    const serverErrors = Object.values(form.errors).flatMap((error) =>
        Array.isArray(error) ? error : [error],
    );
    return [...localErrors, ...serverErrors];
});

watch(
    () => form.nombres,
    () => validateField("nombres"),
);
watch(
    () => form.apellidos,
    () => validateField("apellidos"),
);
watch(
    () => form.ci,
    () => validateField("ci"),
);
watch(
    () => form.email,
    () => validateField("email"),
);
watch(
    () => form.telefono,
    () => validateField("telefono"),
);
watch(
    () => form.direccion,
    () => validateField("direccion"),
);
watch(
    () => form.fecha_nacimiento,
    () => validateField("fecha_nacimiento"),
);
watch(
    () => form.colegio_origen,
    () => validateField("colegio_origen"),
);
watch(
    () => form.anio_bachillerato,
    () => validateField("anio_bachillerato"),
);
watch(
    () => form.password,
    () => validateField("password"),
);
watch(
    () => form.password_confirmation,
    () => validateField("password_confirmation"),
);

const submit = () => {
    Object.keys(validationErrors).forEach((field) => validateField(field));
    if (hasValidationErrors.value || hasMissingRequiredFields.value) return;
    form.post(route("register"));
};

// password toggles
const passwordVisible = ref(false);
const confirmVisible = ref(false);
const passwordType = computed(() =>
    passwordVisible.value ? "text" : "password",
);
const confirmType = computed(() =>
    confirmVisible.value ? "text" : "password",
);
const passwordIcon = computed(() =>
    passwordVisible.value ? "fa-eye-slash" : "fa-eye",
);
const confirmIcon = computed(() =>
    confirmVisible.value ? "fa-eye-slash" : "fa-eye",
);
function togglePassword() {
    passwordVisible.value = !passwordVisible.value;
}
function toggleConfirm() {
    confirmVisible.value = !confirmVisible.value;
}
</script>
