import { usePage } from "@inertiajs/vue3";
import { nextTick, onBeforeUnmount, onMounted, ref } from "vue";

export function useRowHighlight(paramName, options = {}) {
    const page = usePage();
    const highlightedRowId = ref(null);
    const highlightClass =
        options.highlightClass ??
        "bg-emerald-50 outline outline-2 outline-emerald-400 outline-offset-[-2px] transition-shadow duration-300";
    const defaultClass = options.defaultClass ?? "transition hover:bg-slate-50";
    const duration = options.duration ?? 3000;
    let timer = null;

    function clearHighlight() {
        highlightedRowId.value = null;

        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
    }

    function resolveHighlightedId() {
        if (typeof window === "undefined") {
            return null;
        }

        try {
            const currentUrl = new URL(page.url, window.location.origin);
            const rawValue = currentUrl.searchParams.get(paramName);

            if (!rawValue) {
                return null;
            }

            const parsed = Number(rawValue);

            return Number.isFinite(parsed) && parsed > 0 ? parsed : null;
        } catch (error) {
            return null;
        }
    }

    function startHighlight() {
        const highlightedId = resolveHighlightedId();

        if (!highlightedId) {
            return;
        }

        highlightedRowId.value = highlightedId;

        nextTick(() => {
            const row = document.querySelector(
                `[data-highlight-id="${highlightedId}"]`,
            );

            if (row) {
                row.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        });

        timer = window.setTimeout(() => {
            highlightedRowId.value = null;
            timer = null;
        }, duration);
    }

    function rowClass(id) {
        return id === highlightedRowId.value ? highlightClass : defaultClass;
    }

    function rowAttrs(id) {
        return {
            "data-highlight-id": id,
            class: rowClass(id),
        };
    }

    onMounted(startHighlight);
    onBeforeUnmount(clearHighlight);

    return {
        highlightedRowId,
        rowClass,
        rowAttrs,
        startHighlight,
        clearHighlight,
    };
}
