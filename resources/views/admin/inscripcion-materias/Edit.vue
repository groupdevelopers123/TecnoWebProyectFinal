<template>
    <div class="max-w-2xl">
        <h1 class="text-2xl font-black">Editar materia inscrita</h1>
        <form
            class="mt-6 space-y-5 rounded-3xl border border-slate-200 bg-white p-6"
            @submit.prevent="submit"
        >
            <p class="font-bold">
                {{ inscripcionMateria.carrera_materia?.materia?.nombre }}
            </p>
            <label class="block text-sm font-bold"
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
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</template>
<script setup>
import { Link, useForm } from "@inertiajs/vue3";
const props = defineProps({ inscripcion: Object, inscripcionMateria: Object });
const form = useForm({
    estado: props.inscripcionMateria?.estado ?? "Cursando",
});
const submit = () =>
    form.put(
        route("admin.inscripciones.materias.update", [
            props.inscripcion.id,
            props.inscripcionMateria.id,
        ]),
    );
</script>
