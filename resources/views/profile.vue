<script setup>
import { computed, ref } from "vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import HeaderAlumno from "@/views/partials/headerAlumno.vue";
import HeaderDocente from "@/views/partials/headerDocente.vue";
import PageVisitCounter from "@/views/partials/PageVisitCounter.vue";

const props = defineProps({
    user: Object,
});

const page = usePage();

const editar = ref(false);

const form = useForm({
    ci: props.user.ci ?? "",
    nombres: props.user.nombres ?? "",
    apellidos: props.user.apellidos ?? "",
    email: props.user.email ?? "",
    telefono: props.user.telefono ?? "",
    direccion: props.user.direccion ?? "",
    fecha_nacimiento: props.user.fecha_nacimiento ?? "",
    password: "",
    password_confirmation: "",
    codigo:
        props.user.propietario_detalle?.codigo ??
        props.user.secretaria_detalle?.codigo ??
        props.user.docente_detalle?.codigo ??
        props.user.alumno_detalle?.codigo ??
        "",
    cargo: props.user.propietario_detalle?.cargo ?? "",
    turno_trabajo: props.user.secretaria_detalle?.turno_trabajo ?? "",
    sueldo: props.user.secretaria_detalle?.sueldo ?? "",
    especialidad: props.user.docente_detalle?.especialidad ?? "",
    titulo: props.user.docente_detalle?.titulo ?? "",
    registro_profesional:
        props.user.docente_detalle?.registro_profesional ?? "",
    colegio_origen: props.user.alumno_detalle?.colegio_origen ?? "",
    anio_bachillerato: props.user.alumno_detalle?.anio_bachillerato ?? "",
    estado_academico: props.user.alumno_detalle?.estado_academico ?? "",
});

const roleName = computed(() => {
    return props.user.role?.nombre
        ? String(props.user.role.nombre).toUpperCase()
        : "USUARIO";
});

const getHeaderComponent = computed(() => {
    const userRole = props.user.role?.nombre
        ? String(props.user.role.nombre).toLowerCase()
        : "alumno";

    const headerComponents = {
        alumno: HeaderAlumno,
        docente: HeaderDocente,
    };

    return headerComponents[userRole] || HeaderAlumno;
});

const fullName = computed(() => {
    return `${props.user.nombres ?? ""} ${props.user.apellidos ?? ""}`.trim();
});

const flashSuccess = computed(() => page.props.flash?.success ?? "");
const clientErrors = ref({});

const patterns = {
    ci: /^[0-9]+$/,
    nombres: /^[\p{L}\s]+$/u,
    apellidos: /^[\p{L}\s]+$/u,
    telefono: /^[0-9()+\s\-]+$/,
    direccion: /^[\p{L}0-9\s\-#,\.]+$/u,
    codigo: /^[\p{L}0-9\s\-]+$/u,
    cargo: /^[\p{L}\s]+$/u,
    turno_trabajo: /^[\p{L}\s]+$/u,
    especialidad: /^[\p{L}\s]+$/u,
    titulo: /^[\p{L}0-9\s\.\-]+$/u,
    registro_profesional: /^[\p{L}0-9\s\-]+$/u,
    colegio_origen: /^[\p{L}0-9\s\-\.,]+$/u,
    estado_academico: /^[\p{L}\s]+$/u,
};

const comenzarEdicion = () => {
    editar.value = true;
};

const cancelarEdicion = () => {
    editar.value = false;
    form.reset({
        ci: props.user.ci ?? "",
        nombres: props.user.nombres ?? "",
        apellidos: props.user.apellidos ?? "",
        email: props.user.email ?? "",
        telefono: props.user.telefono ?? "",
        direccion: props.user.direccion ?? "",
        fecha_nacimiento: props.user.fecha_nacimiento ?? "",
        password: "",
        password_confirmation: "",
        codigo:
            props.user.propietario_detalle?.codigo ??
            props.user.secretaria_detalle?.codigo ??
            props.user.docente_detalle?.codigo ??
            props.user.alumno_detalle?.codigo ??
            "",
        cargo: props.user.propietario_detalle?.cargo ?? "",
        turno_trabajo: props.user.secretaria_detalle?.turno_trabajo ?? "",
        sueldo: props.user.secretaria_detalle?.sueldo ?? "",
        especialidad: props.user.docente_detalle?.especialidad ?? "",
        titulo: props.user.docente_detalle?.titulo ?? "",
        registro_profesional:
            props.user.docente_detalle?.registro_profesional ?? "",
        colegio_origen: props.user.alumno_detalle?.colegio_origen ?? "",
        anio_bachillerato: props.user.alumno_detalle?.anio_bachillerato ?? "",
        estado_academico: props.user.alumno_detalle?.estado_academico ?? "",
    });
};

const validarPerfil = () => {
    clientErrors.value = {};
    form.clearErrors();

    if (!form.ci || !form.ci.trim()) {
        clientErrors.value.ci = "CI es obligatorio.";
    } else if (form.ci.length > 20) {
        clientErrors.value.ci = "CI no puede tener más de 20 caracteres.";
    } else if (!patterns.ci.test(form.ci)) {
        clientErrors.value.ci = "CI solo puede contener números.";
    }

    if (!form.nombres || !form.nombres.trim()) {
        clientErrors.value.nombres = "El nombre es obligatorio.";
    } else if (form.nombres.length > 100) {
        clientErrors.value.nombres =
            "El nombre no puede tener más de 100 caracteres.";
    } else if (!patterns.nombres.test(form.nombres)) {
        clientErrors.value.nombres =
            "El nombre solo puede contener letras y espacios.";
    }

    if (!form.apellidos || !form.apellidos.trim()) {
        clientErrors.value.apellidos = "El apellido es obligatorio.";
    } else if (form.apellidos.length > 100) {
        clientErrors.value.apellidos =
            "El apellido no puede tener más de 100 caracteres.";
    } else if (!patterns.apellidos.test(form.apellidos)) {
        clientErrors.value.apellidos =
            "El apellido solo puede contener letras y espacios.";
    }

    if (!form.email || !form.email.trim()) {
        clientErrors.value.email = "El correo es obligatorio.";
    } else if (form.email.length > 150) {
        clientErrors.value.email =
            "El correo no puede tener más de 150 caracteres.";
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        clientErrors.value.email = "El correo no tiene un formato válido.";
    }

    if (form.telefono && !patterns.telefono.test(form.telefono)) {
        clientErrors.value.telefono =
            "El teléfono solo puede contener números, espacios, paréntesis, + y guiones.";
    }

    if (form.direccion && !patterns.direccion.test(form.direccion)) {
        clientErrors.value.direccion =
            "La dirección contiene caracteres inválidos.";
    }

    if (
        form.fecha_nacimiento &&
        !/^\d{4}-\d{2}-\d{2}$/.test(form.fecha_nacimiento)
    ) {
        clientErrors.value.fecha_nacimiento =
            "La fecha de nacimiento debe tener el formato YYYY-MM-DD.";
    }

    if (form.password && form.password.length < 8) {
        clientErrors.value.password =
            "La contraseña debe tener al menos 8 caracteres.";
    }

    if (form.password && form.password !== form.password_confirmation) {
        clientErrors.value.password_confirmation =
            "La confirmación de contraseña no coincide.";
    }

    if (form.codigo && !patterns.codigo.test(form.codigo)) {
        clientErrors.value.codigo =
            "El código solo puede contener letras, números, espacios y guiones.";
    }

    if (form.cargo && !patterns.cargo.test(form.cargo)) {
        clientErrors.value.cargo =
            "El cargo solo puede contener letras y espacios.";
    }

    if (
        form.turno_trabajo &&
        !patterns.turno_trabajo.test(form.turno_trabajo)
    ) {
        clientErrors.value.turno_trabajo =
            "El turno solo puede contener letras y espacios.";
    }

    if (form.sueldo && Number(form.sueldo) < 0) {
        clientErrors.value.sueldo =
            "El sueldo debe ser un número mayor o igual a 0.";
    }

    if (form.especialidad && !patterns.especialidad.test(form.especialidad)) {
        clientErrors.value.especialidad =
            "La especialidad solo puede contener letras y espacios.";
    }

    if (form.titulo && !patterns.titulo.test(form.titulo)) {
        clientErrors.value.titulo =
            "El título solo puede contener letras, números, puntos y guiones.";
    }

    if (
        form.registro_profesional &&
        !patterns.registro_profesional.test(form.registro_profesional)
    ) {
        clientErrors.value.registro_profesional =
            "El registro profesional contiene caracteres inválidos.";
    }

    if (
        form.colegio_origen &&
        !patterns.colegio_origen.test(form.colegio_origen)
    ) {
        clientErrors.value.colegio_origen =
            "El colegio de origen contiene caracteres inválidos.";
    }

    if (
        form.anio_bachillerato &&
        (!Number.isInteger(Number(form.anio_bachillerato)) ||
            Number(form.anio_bachillerato) < 1950 ||
            Number(form.anio_bachillerato) > new Date().getFullYear())
    ) {
        clientErrors.value.anio_bachillerato =
            "El año de bachillerato debe ser un número válido entre 1950 y el año actual.";
    }

    if (
        form.estado_academico &&
        !patterns.estado_academico.test(form.estado_academico)
    ) {
        clientErrors.value.estado_academico =
            "El estado académico solo puede contener letras y espacios.";
    }

    return Object.keys(clientErrors.value).length === 0;
};

const guardarPerfil = () => {
    if (!validarPerfil()) {
        form.setErrors(clientErrors.value);
        return;
    }

    form.put("/perfil", {
        preserveState: true,
        onSuccess: () => {
            editar.value = false;
        },
    });
};

const regresar = () => {
    router.back();
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-900">
        <Head title="Mi Perfil" />
        <component :is="getHeaderComponent" />

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div
                class="rounded-[2rem] bg-white p-6 shadow-xl shadow-slate-200/50 sm:p-8"
            >
                <section>
                    <div
                        class="mb-8 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h1
                                class="text-3xl font-black tracking-tight text-slate-900"
                            >
                                {{ fullName || "Mi Perfil" }}
                            </h1>
                            <p
                                class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base"
                            >
                                Revisa y actualiza tus datos personales. Este
                                perfil es accesible para todos los roles
                                autenticados.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <button
                                type="button"
                                class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                                @click="comenzarEdicion"
                                v-if="!editar"
                            >
                                Editar perfil
                            </button>

                            <button
                                type="button"
                                class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                                @click="regresar"
                            >
                                Volver
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="flashSuccess"
                        class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm font-semibold text-emerald-700"
                    >
                        {{ flashSuccess }}
                    </div>

                    <div class="mt-8 grid gap-6 lg:grid-cols-3">
                        <div
                            class="rounded-3xl border border-slate-200 bg-slate-50 p-6"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-3xl bg-blue-100 text-2xl font-black text-blue-700"
                                >
                                    {{
                                        (props.user.nombres ?? "U")
                                            .charAt(0)
                                            .toUpperCase()
                                    }}
                                </div>

                                <div>
                                    <p
                                        class="text-sm font-semibold text-slate-500"
                                    >
                                        Rol
                                    </p>
                                    <p
                                        class="mt-1 text-xl font-black text-slate-900"
                                    >
                                        {{ roleName }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 space-y-4 text-sm text-slate-600">
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        CI
                                    </p>
                                    <p>
                                        {{ props.user.ci ?? "No registrado" }}
                                    </p>
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-900">
                                        Correo
                                    </p>
                                    <p>
                                        {{
                                            props.user.email ?? "No registrado"
                                        }}
                                    </p>
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-900">
                                        Estado
                                    </p>
                                    <p
                                        class="mt-1 inline-flex rounded-full px-3 py-1 text-xs font-black uppercase tracking-[0.25em]"
                                        :class="
                                            props.user.estado
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-red-100 text-red-700'
                                        "
                                    >
                                        {{
                                            props.user.estado
                                                ? "Activo"
                                                : "Inactivo"
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="lg:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                        >
                            <div
                                class="flex items-center justify-between gap-4"
                            >
                                <div>
                                    <h2
                                        class="text-xl font-black text-slate-900"
                                    >
                                        Datos personales
                                    </h2>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Información que puedes editar desde este
                                        perfil.
                                    </p>
                                </div>

                                <div
                                    class="hidden rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-[0.28em] text-blue-700 sm:block"
                                >
                                    {{ roleName }}
                                </div>
                            </div>

                            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                <template v-if="editar">
                                    <div>
                                        <label
                                            class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500"
                                            >CI</label
                                        >
                                        <input
                                            v-model="form.ci"
                                            type="text"
                                            class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-200"
                                        />
                                        <p
                                            v-if="form.errors.ci"
                                            class="mt-1 text-xs text-red-600"
                                        >
                                            {{ form.errors.ci }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500"
                                            >Nombre</label
                                        >
                                        <input
                                            v-model="form.nombres"
                                            type="text"
                                            class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-200"
                                        />
                                        <p
                                            v-if="form.errors.nombres"
                                            class="mt-1 text-xs text-red-600"
                                        >
                                            {{ form.errors.nombres }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500"
                                            >Apellido</label
                                        >
                                        <input
                                            v-model="form.apellidos"
                                            type="text"
                                            class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-200"
                                        />
                                        <p
                                            v-if="form.errors.apellidos"
                                            class="mt-1 text-xs text-red-600"
                                        >
                                            {{ form.errors.apellidos }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500"
                                            >Correo</label
                                        >
                                        <input
                                            v-model="form.email"
                                            type="email"
                                            class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-200"
                                        />
                                        <p
                                            v-if="form.errors.email"
                                            class="mt-1 text-xs text-red-600"
                                        >
                                            {{ form.errors.email }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500"
                                            >Teléfono</label
                                        >
                                        <input
                                            v-model="form.telefono"
                                            type="text"
                                            class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-200"
                                        />
                                        <p
                                            v-if="form.errors.telefono"
                                            class="mt-1 text-xs text-red-600"
                                        >
                                            {{ form.errors.telefono }}
                                        </p>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label
                                            class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500"
                                            >Dirección</label
                                        >
                                        <input
                                            v-model="form.direccion"
                                            type="text"
                                            class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-200"
                                        />
                                        <p
                                            v-if="form.errors.direccion"
                                            class="mt-1 text-xs text-red-600"
                                        >
                                            {{ form.errors.direccion }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500"
                                            >Fecha de nacimiento</label
                                        >
                                        <input
                                            v-model="form.fecha_nacimiento"
                                            type="date"
                                            class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-200"
                                        />
                                        <p
                                            v-if="form.errors.fecha_nacimiento"
                                            class="mt-1 text-xs text-red-600"
                                        >
                                            {{ form.errors.fecha_nacimiento }}
                                        </p>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label
                                            class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500"
                                            >Nueva contraseña</label
                                        >
                                        <input
                                            v-model="form.password"
                                            type="password"
                                            placeholder="Dejar vacío para mantener la contraseña actual"
                                            class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-200"
                                        />
                                        <p
                                            v-if="form.errors.password"
                                            class="mt-1 text-xs text-red-600"
                                        >
                                            {{ form.errors.password }}
                                        </p>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label
                                            class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500"
                                            >Confirmar contraseña</label
                                        >
                                        <input
                                            v-model="form.password_confirmation"
                                            type="password"
                                            class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-200"
                                        />
                                        <p
                                            v-if="
                                                form.errors
                                                    .password_confirmation
                                            "
                                            class="mt-1 text-xs text-red-600"
                                        >
                                            {{
                                                form.errors
                                                    .password_confirmation
                                            }}
                                        </p>
                                    </div>
                                </template>

                                <template v-else>
                                    <div>
                                        <p
                                            class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                        >
                                            Nombre completo
                                        </p>
                                        <p
                                            class="mt-2 text-sm font-semibold text-slate-900"
                                        >
                                            {{ fullName }}
                                        </p>
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                        >
                                            Correo
                                        </p>
                                        <p
                                            class="mt-2 text-sm font-semibold text-slate-900"
                                        >
                                            {{
                                                props.user.email ??
                                                "No registrado"
                                            }}
                                        </p>
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                        >
                                            Teléfono
                                        </p>
                                        <p
                                            class="mt-2 text-sm font-semibold text-slate-900"
                                        >
                                            {{
                                                props.user.telefono ??
                                                "No registrado"
                                            }}
                                        </p>
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                        >
                                            Dirección
                                        </p>
                                        <p
                                            class="mt-2 text-sm font-semibold text-slate-900"
                                        >
                                            {{
                                                props.user.direccion ??
                                                "No registrada"
                                            }}
                                        </p>
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                        >
                                            Fecha de nacimiento
                                        </p>
                                        <p
                                            class="mt-2 text-sm font-semibold text-slate-900"
                                        >
                                            {{
                                                props.user.fecha_nacimiento ??
                                                "No registrada"
                                            }}
                                        </p>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-8">
                                <h3 class="text-lg font-black text-slate-900">
                                    Detalles según rol
                                </h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    Campos específicos según tu perfil actual.
                                </p>

                                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                    <template
                                        v-if="
                                            props.user.role?.nombre ===
                                            'propietario'
                                        "
                                    >
                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Código
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user
                                                        .propietario_detalle
                                                        ?.codigo ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Cargo
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user
                                                        .propietario_detalle
                                                        ?.cargo ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>
                                    </template>

                                    <template
                                        v-if="
                                            props.user.role?.nombre ===
                                            'secretaria'
                                        "
                                    >
                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Código
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user
                                                        .secretaria_detalle
                                                        ?.codigo ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Turno de trabajo
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user
                                                        .secretaria_detalle
                                                        ?.turno_trabajo ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Sueldo
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user
                                                        .secretaria_detalle
                                                        ?.sueldo ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>
                                    </template>

                                    <template
                                        v-if="
                                            props.user.role?.nombre ===
                                            'docente'
                                        "
                                    >
                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Código
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user.docente_detalle
                                                        ?.codigo ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Especialidad
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user.docente_detalle
                                                        ?.especialidad ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Título
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user.docente_detalle
                                                        ?.titulo ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Registro profesional
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user.docente_detalle
                                                        ?.registro_profesional ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>
                                    </template>

                                    <template
                                        v-if="
                                            props.user.role?.nombre === 'alumno'
                                        "
                                    >
                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Código
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user.alumno_detalle
                                                        ?.codigo ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Colegio de origen
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user.alumno_detalle
                                                        ?.colegio_origen ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Año de bachillerato
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user.alumno_detalle
                                                        ?.anio_bachillerato ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <p
                                                class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400"
                                            >
                                                Estado académico
                                            </p>
                                            <p
                                                class="mt-2 font-semibold text-slate-900"
                                            >
                                                {{
                                                    props.user.alumno_detalle
                                                        ?.estado_academico ??
                                                    "No registrado"
                                                }}
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="editar"
                        class="mt-8 flex flex-wrap items-center gap-3"
                    >
                        <button
                            type="button"
                            class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                            @click="guardarPerfil"
                        >
                            Guardar cambios
                        </button>

                        <button
                            type="button"
                            class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                            @click="cancelarEdicion"
                        >
                            Cancelar
                        </button>
                    </div>
                </section>
            </div>
        </main>

        <footer
            class="fixed left-4 bottom-4 z-50 bg-transparent p-0 sm:left-6 sm:bottom-6"
        >
            <PageVisitCounter compact />
        </footer>
    </div>
</template>
