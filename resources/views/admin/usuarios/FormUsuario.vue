<template>
    <form @submit.prevent="submit" class="grid gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <h3 class="text-lg font-black text-slate-900">Datos generales</h3>
            <p class="mt-1 text-sm text-slate-500">
                Información principal del usuario dentro del sistema.
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Rol</label
            >
            <select
                v-model="form.role_id"
                required
                :class="inputClass('role_id')"
            >
                <option value="">Seleccione un rol</option>
                <option
                    v-for="r in roles"
                    :key="r.id"
                    :value="r.id"
                    :data-role="r.nombre"
                >
                    {{ r.nombre.charAt(0).toUpperCase() + r.nombre.slice(1) }}
                </option>
            </select>
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("role_id")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >CI</label
            >
            <input
                type="text"
                v-model="form.ci"
                required
                :class="inputClass('ci')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("ci")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Nombres</label
            >
            <input
                type="text"
                v-model="form.nombres"
                required
                :class="inputClass('nombres')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("nombres")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Apellidos</label
            >
            <input
                type="text"
                v-model="form.apellidos"
                required
                :class="inputClass('apellidos')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("apellidos")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Email</label
            >
            <input
                type="email"
                v-model="form.email"
                required
                :class="inputClass('email')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("email")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Teléfono</label
            >
            <input
                type="text"
                v-model="form.telefono"
                :class="inputClass('telefono')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("telefono")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Dirección</label
            >
            <input
                type="text"
                v-model="form.direccion"
                :class="inputClass('direccion')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("direccion")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Fecha de nacimiento</label
            >
            <input
                type="date"
                v-model="form.fecha_nacimiento"
                :class="inputClass('fecha_nacimiento')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("fecha_nacimiento")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Estado</label
            >
            <select
                v-model="form.estado"
                required
                :class="inputClass('estado')"
            >
                <option :value="1">Activo</option>
                <option :value="0">Inactivo</option>
            </select>
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("estado")
            }}</span>
        </div>

        <div class="border-t border-slate-200 pt-6 md:col-span-2">
            <h3 class="text-lg font-black text-slate-900">Datos del rol</h3>
            <p class="mt-1 text-sm text-slate-500">
                Completa el código y los campos específicos según el tipo de
                usuario.
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Código</label
            >
            <input
                type="text"
                v-model="form.codigo"
                required
                :class="inputClass('codigo')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("codigo")
            }}</span>
        </div>

        <div
            v-if="!roleName"
            class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-slate-500 md:col-span-2"
        >
            Selecciona un rol para mostrar los campos específicos.
        </div>

        <div
            v-if="roleName === 'propietario'"
            class="grid gap-6 md:col-span-2 md:grid-cols-2"
        >
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Cargo</label
                >
                <input
                    type="text"
                    v-model="form.cargo"
                    :class="inputClass('cargo')"
                />
                <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                    errorMessage("cargo")
                }}</span>
            </div>
        </div>

        <div
            v-if="roleName === 'secretaria'"
            class="grid gap-6 md:col-span-2 md:grid-cols-2"
        >
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Turno de trabajo</label
                >
                <input
                    type="text"
                    v-model="form.turno_trabajo"
                    :class="inputClass('turno_trabajo')"
                />
                <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                    errorMessage("turno_trabajo")
                }}</span>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Sueldo</label
                >
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    v-model="form.sueldo"
                    :class="inputClass('sueldo')"
                />
                <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                    errorMessage("sueldo")
                }}</span>
            </div>
        </div>

        <div
            v-if="roleName === 'docente'"
            class="grid gap-6 md:col-span-2 md:grid-cols-2"
        >
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Especialidad</label
                >
                <input
                    type="text"
                    v-model="form.especialidad"
                    :class="inputClass('especialidad')"
                />
                <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                    errorMessage("especialidad")
                }}</span>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Título</label
                >
                <input
                    type="text"
                    v-model="form.titulo"
                    :class="inputClass('titulo')"
                />
                <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                    errorMessage("titulo")
                }}</span>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Registro profesional</label
                >
                <input
                    type="text"
                    v-model="form.registro_profesional"
                    :class="inputClass('registro_profesional')"
                />
                <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                    errorMessage("registro_profesional")
                }}</span>
            </div>
        </div>

        <div
            v-if="roleName === 'alumno'"
            class="grid gap-6 md:col-span-2 md:grid-cols-2"
        >
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Colegio de origen</label
                >
                <input
                    type="text"
                    v-model="form.colegio_origen"
                    :class="inputClass('colegio_origen')"
                />
                <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                    errorMessage("colegio_origen")
                }}</span>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Año de bachillerato</label
                >
                <input
                    type="number"
                    min="1950"
                    :max="currentYear"
                    step="1"
                    v-model="form.anio_bachillerato"
                    :class="inputClass('anio_bachillerato')"
                />
                <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                    errorMessage("anio_bachillerato")
                }}</span>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Estado académico</label
                >
                <input
                    type="text"
                    v-model="form.estado_academico"
                    :class="inputClass('estado_academico')"
                />
                <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                    errorMessage("estado_academico")
                }}</span>
            </div>
        </div>

        <div class="border-t border-slate-200 pt-6 md:col-span-2">
            <h3 class="text-lg font-black text-slate-900">Seguridad</h3>
            <p class="mt-1 text-sm text-slate-500">
                Credenciales de acceso para iniciar sesión.
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Contraseña</label
            >
            <input
                type="password"
                v-model="form.password"
                :required="!isEdit"
                :class="inputClass('password')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("password")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Confirmar contraseña</label
            >
            <input
                type="password"
                v-model="form.password_confirmation"
                :required="!isEdit"
                :class="inputClass('password_confirmation')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("password_confirmation")
            }}</span>
        </div>

        <div
            class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2"
        >
            <button
                :disabled="hasError || form.processing"
                type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:opacity-60 disabled:pointer-events-none"
            >
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                Guardar usuario
            </button>

            <a
                :href="cancelUrlComputed"
                class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >Cancelar</a
            >
        </div>
    </form>
</template>

<script setup>
import { reactive, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    usuario: { type: Object, default: () => ({}) },
    roles: { type: Array, default: () => [] },
    action: { type: String, required: true },
    method: { type: String, default: "post" },
    cancelUrl: { type: String, default: null },
});

const isEdit = computed(() => !!props.usuario && !!props.usuario.id);

function normalizeDateValue(value) {
    if (!value) {
        return "";
    }

    return String(value).slice(0, 10);
}

function normalizeEstadoValue(value) {
    if (value === true || value === 1 || value === "1") {
        return 1;
    }

    return 0;
}

function getDetalle(usuario, key) {
    if (!usuario || typeof usuario !== "object") {
        return null;
    }

    return (
        usuario[key] ??
        usuario[
            key.replace(/_([a-z])/g, (_, letter) => letter.toUpperCase())
        ] ??
        null
    );
}

const initialDetalle =
    getDetalle(props.usuario, "propietario_detalle") ??
    getDetalle(props.usuario, "secretaria_detalle") ??
    getDetalle(props.usuario, "docente_detalle") ??
    getDetalle(props.usuario, "alumno_detalle") ??
    null;

const form = useForm({
    role_id: props.usuario.role_id ?? "",
    ci: props.usuario.ci ?? "",
    nombres: props.usuario.nombres ?? "",
    apellidos: props.usuario.apellidos ?? "",
    email: props.usuario.email ?? "",
    telefono: props.usuario.telefono ?? "",
    direccion: props.usuario.direccion ?? "",
    fecha_nacimiento: normalizeDateValue(props.usuario.fecha_nacimiento),
    estado: normalizeEstadoValue(props.usuario.estado),
    codigo: initialDetalle?.codigo ?? "",
    cargo: initialDetalle?.cargo ?? "",
    turno_trabajo: initialDetalle?.turno_trabajo ?? "",
    sueldo: initialDetalle?.sueldo ?? "",
    especialidad: initialDetalle?.especialidad ?? "",
    titulo: initialDetalle?.titulo ?? "",
    registro_profesional: initialDetalle?.registro_profesional ?? "",
    colegio_origen: initialDetalle?.colegio_origen ?? "",
    anio_bachillerato: initialDetalle?.anio_bachillerato ?? "",
    estado_academico: initialDetalle?.estado_academico ?? "",
    password: "",
    password_confirmation: "",
});

const localErrors = reactive({
    role_id: "",
    ci: "",
    nombres: "",
    apellidos: "",
    email: "",
    telefono: "",
    direccion: "",
    fecha_nacimiento: "",
    estado: "",
    codigo: "",
    cargo: "",
    turno_trabajo: "",
    sueldo: "",
    especialidad: "",
    titulo: "",
    registro_profesional: "",
    colegio_origen: "",
    anio_bachillerato: "",
    estado_academico: "",
    password: "",
    password_confirmation: "",
});

const roleName = computed(() => {
    const r = props.roles.find((rr) => rr.id == form.role_id);
    return r ? r.nombre : null;
});

const currentYear = new Date().getFullYear();

function validateField(id) {
    const val = (form[id] ?? "").toString().trim();
    let ok = true;
    let msg = "";
    if (
        [
            "role_id",
            "ci",
            "nombres",
            "apellidos",
            "email",
            "estado",
            "codigo",
        ].includes(id)
    ) {
        if (val === "") {
            ok = false;
            msg = "Este campo es obligatorio.";
        }
    }
    if (id === "ci" && val !== "" && !/^[0-9\-]+$/.test(val)) {
        ok = false;
        msg = "CI sólo puede tener números y guiones.";
    }
    if (id === "email" && val !== "" && !/\S+@\S+\.\S+/.test(val)) {
        ok = false;
        msg = "Email inválido.";
    }
    if (id === "sueldo" && val !== "" && Number.isNaN(Number(val))) {
        ok = false;
        msg = "El sueldo debe ser numérico.";
    }
    if (
        id === "anio_bachillerato" &&
        val !== "" &&
        (!/^\d+$/.test(val) || Number(val) < 1950 || Number(val) > currentYear)
    ) {
        ok = false;
        msg = "El año de bachillerato no es válido.";
    }
    if (
        id === "password_confirmation" &&
        form.password &&
        form.password !== form.password_confirmation
    ) {
        ok = false;
        msg = "Las contraseñas no coinciden.";
    }
    if (!isEdit.value && id === "password" && val === "") {
        ok = false;
        msg = "Este campo es obligatorio.";
    }
    if (
        (id === "password_confirmation" && !isEdit.value && val === "") ||
        (id === "password_confirmation" && form.password && val === "")
    ) {
        ok = false;
        msg = "Este campo es obligatorio.";
    }
    localErrors[id] = ok ? "" : msg;
}

Object.keys(localErrors).forEach((k) => {
    watch(
        () => form[k],
        () => validateField(k),
        { immediate: true },
    );
});

function inputClass(id) {
    const base =
        "w-full rounded-2xl border px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100";
    return localErrors[id]
        ? base + " border-red-500"
        : base + " border-slate-200";
}

const hasError = computed(
    () =>
        Object.values(localErrors).some(
            (v) => v && v.toString().trim() !== "",
        ) || Object.keys(form.errors).length > 0,
);
function errorMessage(id) {
    return localErrors[id] || form.errors[id] || "";
}
const cancelUrlComputed = computed(
    () =>
        props.cancelUrl ||
        (typeof route === "function" ? route("admin.usuarios.index") : "#"),
);

function submit() {
    if (hasError.value) return;
    const method = (props.method || "post").toLowerCase();
    if (method === "put" || method === "patch") form.put(props.action);
    else form.post(props.action);
}
</script>
