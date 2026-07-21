<template>
    <Head :title="`Actualizar cuota #${form.id}`" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"
        >
            <div>
                <p
                    class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500"
                >
                    Registro de pago
                </p>
                <h2 class="mt-2 text-2xl font-black text-slate-900">
                    Cuota #{{ form.id }}
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    {{ nombreCompleto(cuota?.alumno) }} •
                    {{ cuota?.concepto?.nombre ?? "Sin concepto" }}
                </p>
            </div>

            <Link
                :href="route('admin.pago-cuotas.index')"
                class="rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
            >
                Volver al listado
            </Link>
        </div>

        <form @submit.prevent="submit" class="mt-8 space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 p-5">
                    <h3 class="text-lg font-black text-slate-900">
                        Datos del pago
                    </h3>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Método de pago</label
                            >
                            <select
                                v-model="form.metodo_pago"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="">Seleccione</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">
                                    Transferencia
                                </option>
                                <option value="QR">QR</option>
                            </select>
                            <p
                                v-if="form.errors.metodo_pago"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.metodo_pago }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Estado de cuota</label
                            >
                            <select
                                v-model="form.estado_cuota"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="pendiente">Pendiente</option>
                                <option value="pagado">Pagado</option>
                                <option value="anulado">Anulado</option>
                                <option value="fallido">Fallido</option>
                            </select>
                            <p
                                v-if="form.errors.estado_cuota"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.estado_cuota }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Fecha de pago</label
                            >
                            <input
                                v-model="form.fecha_pago"
                                type="date"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            />
                            <p
                                v-if="form.errors.fecha_pago"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.fecha_pago }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Correo solicitante</label
                            >
                            <input
                                v-model="form.correo_solicitante"
                                type="email"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            />
                            <p
                                v-if="form.errors.correo_solicitante"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.correo_solicitante }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 p-5">
                    <h3 class="text-lg font-black text-slate-900">
                        Detalle y observación
                    </h3>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Código de transacción</label
                            >
                            <input
                                v-model="form.codigo_transaccion"
                                type="text"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            />
                            <p
                                v-if="form.errors.codigo_transaccion"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.codigo_transaccion }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Observación</label
                            >
                            <textarea
                                v-model="form.observacion"
                                rows="6"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            ></textarea>
                            <p
                                v-if="form.errors.observacion"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.observacion }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <Link
                    :href="route('admin.pago-cuotas.index')"
                    class="rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    Cancelar
                </Link>

                <button
                    type="submit"
                    class="rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700"
                >
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { watch } from "vue";

const props = defineProps({
    cuota: { type: Object, default: () => ({}) },
});

const form = useForm({
    id: props.cuota?.id ?? null,
    metodo_pago: props.cuota?.metodo_pago ?? "",
    fecha_pago: props.cuota?.fecha_pago ?? "",
    estado_cuota: props.cuota?.estado_cuota ?? "pendiente",
    correo_solicitante: props.cuota?.correo_solicitante ?? "",
    codigo_transaccion: props.cuota?.codigo_transaccion ?? "",
    observacion: props.cuota?.observacion ?? "",
});

watch(
    () => props.cuota,
    (cuota) => {
        form.id = cuota?.id ?? null;
        form.metodo_pago = cuota?.metodo_pago ?? "";
        form.fecha_pago = cuota?.fecha_pago ?? "";
        form.estado_cuota = cuota?.estado_cuota ?? "pendiente";
        form.correo_solicitante = cuota?.correo_solicitante ?? "";
        form.codigo_transaccion = cuota?.codigo_transaccion ?? "";
        form.observacion = cuota?.observacion ?? "";
    },
    { deep: true },
);

function submit() {
    form.put(route("admin.pago-cuotas.update", props.cuota?.id));
}

function nombreCompleto(alumno) {
    const completo =
        `${alumno?.nombres ?? ""} ${alumno?.apellidos ?? ""}`.trim();
    return completo || "Sin nombre";
}
</script>
