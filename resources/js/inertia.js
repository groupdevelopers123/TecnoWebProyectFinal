import "./bootstrap";
import "../css/app.css";
import "../css/inertia.css";

import { computed, createApp, h } from "vue";
import { createInertiaApp, router, usePage } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import AdminLayout from "../views/layouts/AdminLayout.vue";

import {
    applyVisualPreferences,
    getServerPreferences,
    readStoredPreferences,
    resolvePreferences,
} from "./preferences";

function getCurrentUserId(pageProps = {}) {
    return pageProps?.auth?.user?.id ?? null;
}

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

        applyPreferencesFromPage(currentPageProps);

        const application = createApp({
            setup() {
                const page = usePage();
                const isAdminPage = computed(() => {
                    const componentName = String(page.component ?? "");
                    const isFromAdmin = page.props?.isFromAdmin === true;
                    return componentName.startsWith("admin/") || isFromAdmin;
                });

                return () => {
                    const content = h(App, props);

                    return isAdminPage.value
                        ? h(AdminLayout, null, { default: () => content })
                        : content;
                };
            },
        });

        application.use(plugin);
        application.use(ZiggyVue);
        application.mount(el);

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
