<script setup>
import { computed, ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import * as yup from "yup";

const props = defineProps({
    mostrar: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["cerrar"]);

const form = useForm({
    current_password: "",
    new_password: "",
    new_password_confirmation: "",
});

const schema = yup.object({
    current_password: yup.string().required("Ingresa tu contraseña actual."),
    new_password: yup
        .string()
        .required("Ingresa una nueva contraseña.")
        .min(8, "La contraseña debe tener al menos 8 caracteres.")
        .matches(/[A-Z]/, "Incluye al menos una letra mayúscula.")
        .matches(/[a-z]/, "Incluye al menos una letra minúscula.")
        .matches(/\d/, "Incluye al menos un número.")
        .matches(/[^\w\s]/, "Incluye al menos un símbolo."),
    new_password_confirmation: yup
        .string()
        .required("Confirma la nueva contraseña.")
        .oneOf([yup.ref("new_password")], "Las contraseñas no coinciden."),
});

const localErrors = ref({
    current_password: "",
    new_password: "",
    new_password_confirmation: "",
});

const showSuccess = ref(false);
const successMessage = ref("");

const cerrarModal = () => {
    form.reset("current_password", "new_password", "new_password_confirmation");
    localErrors.value = {
        current_password: "",
        new_password: "",
        new_password_confirmation: "",
    };
    showSuccess.value = false;
    successMessage.value = "";
    emit("cerrar");
};

const validateField = async (field) => {
    try {
        await schema.validateAt(field, form);
        localErrors.value[field] = "";
    } catch (error) {
        localErrors.value[field] = error.message;
    }
};

const validateAll = async () => {
    try {
        await schema.validate(form, { abortEarly: false });
        localErrors.value = {
            current_password: "",
            new_password: "",
            new_password_confirmation: "",
        };
        return true;
    } catch (error) {
        localErrors.value = {
            current_password: "",
            new_password: "",
            new_password_confirmation: "",
        };

        if (error.inner) {
            error.inner.forEach((err) => {
                localErrors.value[err.path] = err.message;
            });
        }
        return false;
    }
};

const guardarContraseña = async () => {
    form.clearErrors();

    if (!(await validateAll())) {
        return;
    }

    form.put("/configuraciones", {
        preserveState: false,
        onSuccess: () => {
            successMessage.value = "¡Contraseña cambiada con éxito!";
            showSuccess.value = true;
            setTimeout(() => {
                cerrarModal();
            }, 1400);
        },
    });
};

watch(
    () => form.current_password,
    () => validateField("current_password"),
);

watch(
    () => form.new_password,
    () => {
        validateField("new_password");
        if (form.new_password_confirmation) {
            validateField("new_password_confirmation");
        }
    },
);

watch(
    () => form.new_password_confirmation,
    () => validateField("new_password_confirmation"),
);

const handleOverlayClick = (event) => {
    if (event.target === event.currentTarget) {
        cerrarModal();
    }
};
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="props.mostrar"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
                @click="handleOverlayClick"
            >
                <div
                    class="w-full max-w-[55vw] min-w-[min(320px,100%)] rounded-[32px] border border-slate-700 bg-slate-950 text-slate-100 shadow-2xl ring-1 ring-slate-800"
                >
                    <div
                        class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-5 text-white"
                    >
                        <div class="flex items-start justify-between gap-5">
                            <div>
                                <p
                                    class="text-xs font-black uppercase tracking-[0.2em] text-blue-200"
                                >
                                    Cambiar contraseña
                                </p>
                                <h2 class="mt-2 text-2xl font-black">
                                    Actualiza tu contraseña
                                </h2>
                            </div>
                            <button
                                type="button"
                                aria-label="Cerrar modal"
                                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-white transition hover:bg-white/20"
                                @click="cerrarModal"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid gap-6 bg-slate-950 p-6 sm:p-8">
                        <div
                            v-if="showSuccess"
                            class="rounded-3xl border border-emerald-700 bg-emerald-950/80 p-4 text-emerald-100 shadow-sm"
                        >
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700"
                                >
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-white">
                                        ¡Éxito!
                                    </p>
                                    <p class="mt-1 text-sm text-slate-200">
                                        {{ successMessage }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-4">
                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-100"
                                >
                                    Contraseña actual
                                </label>
                                <input
                                    type="password"
                                    v-model="form.current_password"
                                    class="mt-2 w-full rounded-3xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 placeholder:text-slate-500"
                                />
                                <p
                                    v-if="
                                        localErrors.current_password ||
                                        form.errors.current_password
                                    "
                                    class="mt-2 text-sm text-rose-400"
                                >
                                    {{
                                        localErrors.current_password ||
                                        form.errors.current_password
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-100"
                                >
                                    Nueva contraseña
                                </label>
                                <input
                                    type="password"
                                    v-model="form.new_password"
                                    class="mt-2 w-full rounded-3xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 placeholder:text-slate-500"
                                />
                                <p
                                    v-if="
                                        localErrors.new_password ||
                                        form.errors.new_password
                                    "
                                    class="mt-2 text-sm text-rose-400"
                                >
                                    {{
                                        localErrors.new_password ||
                                        form.errors.new_password
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-100"
                                >
                                    Confirmar nueva contraseña
                                </label>
                                <input
                                    type="password"
                                    v-model="form.new_password_confirmation"
                                    class="mt-2 w-full rounded-3xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 placeholder:text-slate-500"
                                />
                                <p
                                    v-if="
                                        localErrors.new_password_confirmation ||
                                        form.errors.new_password_confirmation
                                    "
                                    class="mt-2 text-sm text-rose-400"
                                >
                                    {{
                                        localErrors.new_password_confirmation ||
                                        form.errors.new_password_confirmation
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:justify-end"
                        >
                            <button
                                type="button"
                                class="inline-flex w-full items-center justify-center rounded-3xl border border-slate-700 bg-slate-900 px-5 py-3 text-sm font-bold text-slate-100 transition hover:bg-slate-800 sm:w-auto"
                                @click="cerrarModal"
                            >
                                Cancelar
                            </button>
                            <button
                                type="button"
                                class="inline-flex w-full items-center justify-center rounded-3xl bg-blue-500 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-400 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                                :disabled="form.processing"
                                @click="guardarContraseña"
                            >
                                Guardar contraseña
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
