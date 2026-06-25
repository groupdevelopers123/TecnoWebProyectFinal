import "./bootstrap";
import "../css/inertia.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

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
        const aplicacion = createApp({
            render: () => h(App, props),
        });

        aplicacion.use(plugin);
        aplicacion.use(ZiggyVue);

        aplicacion.mount(el);

        const applyPreferences = (preferences) => {
            const root = document.documentElement;

            if (preferences?.theme) {
                root.setAttribute("data-theme", preferences.theme);
            }

            if (preferences?.mode) {
                root.setAttribute("data-mode", preferences.mode);
            }

            if (preferences?.font_size) {
                root.style.setProperty(
                    "--base-font-size",
                    `${preferences.font_size}px`,
                );
                document.body.style.fontSize = `${preferences.font_size}px`;
            }

            if (preferences?.contrast === "high") {
                root.classList.add("high-contrast");
            } else {
                root.classList.remove("high-contrast");
            }
        };

        try {
            const preferences =
                props.initialPage?.props?.auth?.user?.preferences ||
                props.initialPage?.props?.preferences ||
                null;

            applyPreferences(preferences);
        } catch (e) {
            console.error(e);
        }

        window.addEventListener("inertia:navigate", (event) => {
            try {
                const pageProps = event.detail.page.props || {};
                const preferences =
                    pageProps.auth?.user?.preferences ||
                    pageProps.preferences ||
                    null;
                applyPreferences(preferences);
            } catch (error) {
                console.error(error);
            }
        });

        return aplicacion;
    },

    progress: {
        color: "#2563eb",
    },
});
