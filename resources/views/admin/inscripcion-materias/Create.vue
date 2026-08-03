<template>
    <div class="max-w-2xl">
        <h1 class="text-2xl font-black">Agregar materia</h1>
        <form
            class="mt-6 space-y-5 rounded-3xl border border-slate-200 bg-white p-6"
            @submit.prevent="submit"
        >
            <label class="block text-sm font-bold"
                >Materia<select
                    v-model="form.carrera_materia_id"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3"
                    required
                >
                    <option value="">Selecciona una materia</option>
                    <option
                        v-for="item in carreraMaterias"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{
                            (item.periodo_numero
                                ? "P" + item.periodo_numero + " · "
                                : "") +
                            (item.materia?.codigo ?? "") +
                            " · " +
                            (item.materia?.nombre ?? "")
                        }}
                    </option>
                </select></label
            ><label class="block text-sm font-bold"
                >Estado<select
                    v-model="form.estado"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3"
                >
                    <option>Cursando</option>
                    <option>Aprobada</option>
                    <option>Reprobada</option>
                    <option>Retirada</option>
                </select></label
            >
            <div class="flex gap-3">
                <Link
                    :href="
                        route(
                            'admin.inscripciones.materias.index',
                            inscripcion.id,
                        )
                    "
                    class="rounded-2xl border px-5 py-3 font-bold"
                    >Cancelar</Link
                ><button
                    class="rounded-2xl bg-blue-600 px-5 py-3 font-bold text-white"
                >
                    Guardar
                </button>
            </div>
        </form>
    </div>
</template>
<script setup>
import { Link, useForm } from "@inertiajs/vue3";
const props = defineProps({ inscripcion: Object, carreraMaterias: Array });
const form = useForm({ carrera_materia_id: "", estado: "Cursando" });
const submit = () =>
    form.post(
        route("admin.inscripciones.materias.store", props.inscripcion.id),
    );
</script>
