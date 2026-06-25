import "./bootstrap";
import "../css/app.css";
import "../css/inertia.css";

import { createApp, h } from "vue";
import { createInertiaApp, router } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

import {
    applyVisualPreferences,
    getServerPreferences,
    readStoredPreferences,
    resolvePreferences,
} from "./preferences";

/**
 * Retorna el identificador del usuario actual.
 */
function getCurrentUserId(pageProps = {}) {
    return pageProps?.auth?.user?.id ?? null;
}

/**
 * Aplica las preferencias correspondientes a una página de Inertia.
 */
function applyPreferencesFromPage(pageProps = {}) {
    const userId = getCurrentUserId(pageProps);
    const serverPreferences = getServerPreferences(pageProps);

    const effectivePreferences = resolvePreferences(serverPreferences, userId);

    applyVisualPreferences(effectivePreferences);

    return effectivePreferences;
}

createInertiaApp({
    title: (title) => {
        return title
            ? `${title} - Instituto Andrés Ibáñez`
            : "Instituto Andrés Ibáñez";
    },

    resolve: (name) =>
        resolvePageComponent(
            `../views/${name}.vue`,
            import.meta.glob("../views/**/*.vue"),
        ),

    setup({ el, App, props, plugin }) {
        let currentPageProps = props.initialPage?.props ?? {};

        /*
         * Se aplican las preferencias antes de montar Vue.
         * Esto reduce el parpadeo entre modo claro y oscuro.
         */
        applyPreferencesFromPage(currentPageProps);

        const application = createApp({
            render: () => h(App, props),
        });

        application.use(plugin);
        application.use(ZiggyVue);

        application.mount(el);

        /*
         * Cada vez que Inertia cambia de página se vuelven a aplicar
         * las preferencias del usuario actual.
         */
        router.on("navigate", (event) => {
            try {
                currentPageProps = event.detail?.page?.props ?? {};

                applyPreferencesFromPage(currentPageProps);
            } catch (error) {
                console.error(
                    "No se pudieron aplicar las preferencias después de navegar:",
                    error,
                );
            }
        });

        /*
         * Sincronización entre pestañas del navegador.
         */
        window.addEventListener("storage", () => {
            try {
                const userId = getCurrentUserId(currentPageProps);

                const storedPreferences = readStoredPreferences(userId);

                if (storedPreferences) {
                    applyVisualPreferences(storedPreferences);
                }
            } catch (error) {
                console.error(
                    "No se pudieron sincronizar las preferencias:",
                    error,
                );
            }
        });

        return application;
    },

    progress: {
        color: "#2563eb",
        showSpinner: true,
    },
});
