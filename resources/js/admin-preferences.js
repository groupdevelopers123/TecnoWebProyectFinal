import {
    applyVisualPreferences,
    readStoredPreferences,
    resolvePreferences,
} from "./preferences";

const serverPreferences = window.ADMIN_PREFERENCES ?? null;
const currentUserId = window.ADMIN_USER_ID ?? null;

const effectivePreferences = resolvePreferences(
    serverPreferences,
    currentUserId,
);
applyVisualPreferences(effectivePreferences);

window.addEventListener("storage", () => {
    try {
        const storedPreferences = readStoredPreferences(currentUserId);

        if (storedPreferences) {
            applyVisualPreferences(storedPreferences);
        }
    } catch (error) {
        console.error(
            "No se pudieron sincronizar las preferencias visuales:",
            error,
        );
    }
});
