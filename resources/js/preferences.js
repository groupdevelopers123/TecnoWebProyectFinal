const STORAGE_PREFIX = "visual-preferences";
const LEGACY_STORAGE_KEY = "preferences";

const VALID_THEMES = ["ninos", "adultos", "jovenes"];
const VALID_MODES = ["light", "dark"];
const VALID_CONTRASTS = ["normal", "high"];

/*
 * null significa que no existe ningún tema seleccionado.
 * En ese caso se utilizan los estilos originales definidos en :root.
 */
export const DEFAULT_PREFERENCES = Object.freeze({
    theme: null,
    mode: "light",
    font_size: 16,
    contrast: "normal",
});

export function getPreferencesStorageKey(userId = null) {
    return `${STORAGE_PREFIX}:${userId ?? "guest"}`;
}

/**
 * Conserva theme como null cuando no existe un tema seleccionado.
 */
export function normalizePreferences(preferences = {}) {
    const source =
        preferences && typeof preferences === "object" ? preferences : {};

    const parsedFontSize = Number(source.font_size);

    let normalizedTheme = null;

    if (VALID_THEMES.includes(source.theme)) {
        normalizedTheme = source.theme;
    } else if (
        source.theme === null ||
        source.theme === undefined ||
        source.theme === ""
    ) {
        normalizedTheme = null;
    }

    return {
        theme: normalizedTheme,

        mode: VALID_MODES.includes(source.mode)
            ? source.mode
            : DEFAULT_PREFERENCES.mode,

        font_size: Number.isFinite(parsedFontSize)
            ? Math.min(36, Math.max(12, parsedFontSize))
            : DEFAULT_PREFERENCES.font_size,

        contrast: VALID_CONTRASTS.includes(source.contrast)
            ? source.contrast
            : DEFAULT_PREFERENCES.contrast,
    };
}

export function getServerPreferences(pageProps = {}) {
    return pageProps?.auth?.user?.preferences ?? pageProps?.preferences ?? null;
}

export function readStoredPreferences(userId = null) {
    if (typeof window === "undefined") {
        return null;
    }

    const storageKey = getPreferencesStorageKey(userId);

    try {
        const storedValue = localStorage.getItem(storageKey);

        if (storedValue) {
            return normalizePreferences(JSON.parse(storedValue));
        }

        const legacyValue = localStorage.getItem(LEGACY_STORAGE_KEY);

        if (legacyValue) {
            const migratedPreferences = normalizePreferences(
                JSON.parse(legacyValue),
            );

            localStorage.setItem(
                storageKey,
                JSON.stringify(migratedPreferences),
            );

            localStorage.removeItem(LEGACY_STORAGE_KEY);

            return migratedPreferences;
        }
    } catch (error) {
        console.error("No se pudieron leer las preferencias visuales:", error);
    }

    return null;
}

export function writeStoredPreferences(preferences, userId = null) {
    const normalized = normalizePreferences(preferences);

    if (typeof window === "undefined") {
        return normalized;
    }

    try {
        localStorage.setItem(
            getPreferencesStorageKey(userId),
            JSON.stringify(normalized),
        );
    } catch (error) {
        console.error(
            "No se pudieron guardar las preferencias visuales:",
            error,
        );
    }

    return normalized;
}

export function removeStoredPreferences(userId = null) {
    if (typeof window === "undefined") {
        return;
    }

    try {
        localStorage.removeItem(getPreferencesStorageKey(userId));

        localStorage.removeItem(LEGACY_STORAGE_KEY);
    } catch (error) {
        console.error(
            "No se pudieron eliminar las preferencias visuales:",
            error,
        );
    }
}

export function resolvePreferences(serverPreferences = null, userId = null) {
    const storedPreferences = readStoredPreferences(userId);

    if (storedPreferences) {
        return storedPreferences;
    }

    if (serverPreferences && typeof serverPreferences === "object") {
        return normalizePreferences(serverPreferences);
    }

    return getDefaultPreferences();
}

/**
 * Aplica las preferencias al documento.
 *
 * Si theme es null, elimina completamente data-theme.
 */
export function applyVisualPreferences(preferences) {
    const normalized = normalizePreferences(preferences);

    if (typeof document === "undefined") {
        return normalized;
    }

    const root = document.documentElement;

    if (normalized.theme === null) {
        root.removeAttribute("data-theme");
    } else {
        root.setAttribute("data-theme", normalized.theme);
    }

    root.setAttribute("data-mode", normalized.mode);

    root.setAttribute("data-contrast", normalized.contrast);

    root.classList.toggle("high-contrast", normalized.contrast === "high");

    root.style.setProperty("--base-font-size", `${normalized.font_size}px`);

    if (document.body) {
        document.body.style.removeProperty("font-size");
    }

    root.setAttribute("data-preferences-ready", "true");

    window.dispatchEvent(
        new CustomEvent("visual-preferences:changed", {
            detail: normalized,
        }),
    );

    return normalized;
}

export function getDefaultPreferences() {
    return {
        theme: null,
        mode: "light",
        font_size: 16,
        contrast: "normal",
    };
}

export function preferencesAreEqual(first, second) {
    const normalizedFirst = normalizePreferences(first);

    const normalizedSecond = normalizePreferences(second);

    return (
        normalizedFirst.theme === normalizedSecond.theme &&
        normalizedFirst.mode === normalizedSecond.mode &&
        normalizedFirst.font_size === normalizedSecond.font_size &&
        normalizedFirst.contrast === normalizedSecond.contrast
    );
}
