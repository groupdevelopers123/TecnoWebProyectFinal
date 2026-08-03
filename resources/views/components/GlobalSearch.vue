<template>
    <div ref="container" class="relative w-full max-w-md">
        <div class="relative">
            <span
                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
            >
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input
                v-model="query"
                @input="onInput"
                @keydown.escape="hide"
                @focus="onFocus"
                type="search"
                placeholder="Buscar (personas, materias, carreras, ofertas)"
                class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 pl-10 text-sm font-normal outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
            />
            <span
                v-if="loading"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400"
            >
                <i class="fa-solid fa-spinner fa-spin"></i>
            </span>
        </div>

        <div
            v-if="open"
            class="absolute z-50 mt-2 w-full rounded-2xl border border-slate-200 bg-white shadow-lg"
        >
            <ul class="max-h-64 overflow-auto">
                <li
                    v-if="!loading && !results.length"
                    class="px-3 py-3 text-sm text-slate-500"
                >
                    No se encontraron resultados.
                </li>
                <li
                    v-for="(item, idx) in results"
                    :key="idx"
                    class="px-3 py-2 hover:bg-slate-50"
                >
                    <a
                        :href="item.url"
                        @click.prevent="goTo(item.url)"
                        class="flex items-start gap-3"
                    >
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-slate-900">
                                {{ item.title }}
                            </div>
                            <div class="text-xs text-slate-500">
                                {{ item.type }}
                                {{ item.subtitle ? "· " + item.subtitle : "" }}
                            </div>
                        </div>
                        <div class="ml-auto text-xs text-slate-400">Ir</div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";

const query = ref("");
const results = ref([]);
const open = ref(false);
const loading = ref(false);
let timer = null;
let container = ref(null);

function hide() {
    open.value = false;
}

function onFocus() {
    if (results.value.length) open.value = true;
}

async function doSearch(q) {
    if (!q || q.length < 2) {
        results.value = [];
        open.value = false;
        return;
    }

    loading.value = true;
    try {
        const base = typeof route === "function" ? route("search") : "/search";
        const res = await axios.get(base, { params: { q } });
        // ignore out-of-order responses
        if (query.value.trim() !== q) return;
        results.value = res.data.results || [];
        open.value = true;
    } catch (e) {
        console.error("GlobalSearch error", e);
        results.value = [];
        open.value = false;
    } finally {
        loading.value = false;
    }
}

function onInput() {
    clearTimeout(timer);
    const q = query.value.trim();
    timer = setTimeout(() => doSearch(q), 300);
}

function onDocumentClick(e) {
    const el = container?.value;
    if (!el) return;
    if (!el.contains(e.target)) hide();
}

onMounted(() => {
    document.addEventListener("click", onDocumentClick);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", onDocumentClick);
});

function goTo(url) {
    try {
        // Force full navigation so hash fragments are preserved and the target page can handle highlight
        window.location.href = url;
    } catch (e) {
        // fallback
        window.location.assign(url);
    }
}
</script>

<style scoped>
/* small visual tweaks */
</style>
