<template>
    <form class="space-y-6" @submit.prevent="submit">
        <div
            v-if="Object.keys(form.errors).length"
            class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700"
        >
            <p class="font-bold">Revisa los siguientes errores:</p>
            <ul class="mt-2 list-inside list-disc">
                <li v-for="(error, index) in errorList" :key="index">
                    {{ error }}
                </li>
            </ul>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <h3 class="text-lg font-black text-slate-900">
                    Datos de la oferta académica
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Selecciona la carrera, el periodo académico, los cupos y las
                    fechas de vigencia.
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Carrera</label
                >
                <select
                    v-model="form.carrera_id"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Seleccione una carrera</option>
                    <option
                        v-for="carrera in carreras"
                        :key="carrera.id"
                        :value="carrera.id"
                    >
                        {{ carrera.codigo }} - {{ carrera.nombre }}
                    </option>
                </select>
                <p
                    v-if="form.errors.carrera_id"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.carrera_id }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Período académico</label
                >
                <select
                    v-model="form.periodo_academico_id"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Seleccione un periodo</option>
                    <option
                        v-for="periodo in periodos"
                        :key="periodo.id"
                        :value="periodo.id"
                    >
                        {{ periodo.nombre }} - {{ periodo.gestion }}
                    </option>
                </select>
                <p
                    v-if="form.errors.periodo_academico_id"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.periodo_academico_id }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Docente (Opcional)</label
                >
                <select
                    v-model="form.docente_detalle_id"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Seleccione un docente (opcional)</option>
                    <option
                        v-for="docente in docentes"
                        :key="docente.id"
                        :value="docente.id"
                    >
                        {{ docente.codigo }} - {{ docente.user?.nombres }}
                        {{ docente.user?.apellidos }}
                    </option>
                </select>
                <p
                    v-if="form.errors.docente_detalle_id"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.docente_detalle_id }}
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Nombre de la oferta</label
                >
                <input
                    v-model="form.nombre"
                    type="text"
                    required
                    maxlength="150"
                    placeholder="Ejemplo: Sistemas Informáticos - Gestión 2026"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.nombre"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.nombre }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Cantidad de cupos</label
                >
                <input
                    v-model="form.cantidad_cupos"
                    type="number"
                    min="1"
                    step="1"
                    required
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.cantidad_cupos"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.cantidad_cupos }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Cupos disponibles</label
                >
                <input
                    v-model="form.cupos_disponibles"
                    type="number"
                    min="0"
                    step="1"
                    required
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.cupos_disponibles"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.cupos_disponibles }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Fecha de inicio</label
                >
                <input
                    v-model="form.fecha_inicio"
                    type="date"
                    required
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.fecha_inicio"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.fecha_inicio }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Fecha de finalización</label
                >
                <input
                    v-model="form.fecha_fin"
                    type="date"
                    required
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.fecha_fin"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.fecha_fin }}
                </p>
            </div>

            <div class="md:col-span-2">
                <div
                    class="rounded-2xl border border-blue-100 bg-blue-50/50 p-5"
                >
                    <div class="mb-5">
                        <h3 class="text-lg font-black text-slate-900">
                            Precios de la oferta académica
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Ingresa los precios en bolivianos. Se permiten hasta
                            dos decimales.
                        </p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-3">
                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Precio de matrícula</label
                            >
                            <div class="relative">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-500"
                                    >Bs</span
                                >
                                <input
                                    v-model="form.precio_matricula"
                                    type="number"
                                    min="0"
                                    max="99999999.99"
                                    step="0.01"
                                    inputmode="decimal"
                                    required
                                    placeholder="0.00"
                                    class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-12 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                />
                            </div>
                            <p
                                v-if="form.errors.precio_matricula"
                                class="mt-2 text-sm font-medium text-red-600"
                            >
                                {{ form.errors.precio_matricula }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Precio de mensualidad</label
                            >
                            <div class="relative">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-500"
                                    >Bs</span
                                >
                                <input
                                    v-model="form.precio_mensualidad"
                                    type="number"
                                    min="0"
                                    max="99999999.99"
                                    step="0.01"
                                    inputmode="decimal"
                                    required
                                    placeholder="0.00"
                                    class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-12 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                />
                            </div>
                            <p
                                v-if="form.errors.precio_mensualidad"
                                class="mt-2 text-sm font-medium text-red-600"
                            >
                                {{ form.errors.precio_mensualidad }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Precio de carrera completa</label
                            >
                            <div class="relative">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-500"
                                    >Bs</span
                                >
                                <input
                                    v-model="form.precio_carrera_completa"
                                    type="number"
                                    min="0"
                                    max="99999999.99"
                                    step="0.01"
                                    inputmode="decimal"
                                    required
                                    placeholder="0.00"
                                    class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-12 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                />
                            </div>
                            <p
                                v-if="form.errors.precio_carrera_completa"
                                class="mt-2 text-sm font-medium text-red-600"
                            >
                                {{ form.errors.precio_carrera_completa }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Estado</label
                >
                <select
                    v-model="form.estado"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option :value="1">Activa</option>
                    <option :value="0">Inactiva</option>
                </select>
                <p
                    v-if="form.errors.estado"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.estado }}
                </p>
            </div>

            <div
                class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2"
            >
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                    :disabled="form.processing"
                >
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Guardar oferta
                </button>

                <a
                    :href="cancelUrl"
                    class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { computed, watch } from "vue";

const props = defineProps({
    oferta: { type: Object, default: () => ({}) },
    action: { type: String, required: true },
    method: { type: String, default: "post" },
    cancelUrl: { type: String, required: true },
    carreras: { type: Array, default: () => [] },
    periodos: { type: Array, default: () => [] },
    docentes: { type: Array, default: () => [] },
});

const form = useForm({
    carrera_id: props.oferta.carrera_id ?? "",
    periodo_academico_id: props.oferta.periodo_academico_id ?? "",
    docente_detalle_id: props.oferta.docente_detalle_id ?? "",
    nombre: props.oferta.nombre ?? "",
    cantidad_cupos: props.oferta.cantidad_cupos ?? "",
    cupos_disponibles: props.oferta.cupos_disponibles ?? "",
    fecha_inicio: props.oferta.fecha_inicio ?? "",
    fecha_fin: props.oferta.fecha_fin ?? "",
    precio_matricula: props.oferta.precio_matricula ?? "",
    precio_mensualidad: props.oferta.precio_mensualidad ?? "",
    precio_carrera_completa: props.oferta.precio_carrera_completa ?? "",
    estado: props.oferta.estado === false ? 0 : (props.oferta.estado ?? 1),
});

const errorList = computed(() => Object.values(form.errors));

watch(
    () => props.oferta,
    (oferta) => {
        form.carrera_id = oferta.carrera_id ?? "";
        form.periodo_academico_id = oferta.periodo_academico_id ?? "";
        form.docente_detalle_id = oferta.docente_detalle_id ?? "";
        form.nombre = oferta.nombre ?? "";
        form.cantidad_cupos = oferta.cantidad_cupos ?? "";
        form.cupos_disponibles = oferta.cupos_disponibles ?? "";
        form.fecha_inicio = oferta.fecha_inicio ?? "";
        form.fecha_fin = oferta.fecha_fin ?? "";
        form.precio_matricula = oferta.precio_matricula ?? "";
        form.precio_mensualidad = oferta.precio_mensualidad ?? "";
        form.precio_carrera_completa = oferta.precio_carrera_completa ?? "";
        form.estado = oferta.estado === false ? 0 : (oferta.estado ?? 1);
    },
    { deep: true },
);

function submit() {
    const payload = {
        ...form.data(),
        estado: Number(form.estado),
        docente_detalle_id:
            form.docente_detalle_id === "" ? null : form.docente_detalle_id,
    };

    if (props.method.toLowerCase() === "put") {
        form.transform(() => payload).put(props.action, {
            preserveScroll: true,
        });
        return;
    }

    form.transform(() => payload).post(props.action, { preserveScroll: true });
}
</script>
