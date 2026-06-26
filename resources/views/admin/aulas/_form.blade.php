<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Datos del aula</h3>
        <p class="mt-1 text-sm text-slate-500">
            Registra la información necesaria para identificar y administrar el aula.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Código</label>
        <input type="text"
               id="codigo"
               name="codigo"
               value="{{ old('codigo', $aula->codigo ?? '') }}"
               placeholder="Ej: AULA-101"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-codigo" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Nombre</label>
        <input type="text"
               id="nombre"
               name="nombre"
               value="{{ old('nombre', $aula->nombre ?? '') }}"
               placeholder="Ej: Aula de Sistemas"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-nombre" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Ubicación</label>
        <input type="text"
               id="ubicacion"
               name="ubicacion"
               value="{{ old('ubicacion', $aula->ubicacion ?? '') }}"
               placeholder="Ej: Bloque A"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-ubicacion" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Piso</label>

        <select id="piso" name="piso"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un piso</option>

            <option value="Planta baja" @selected(old('piso', $aula->piso ?? '') === 'Planta baja')>
                Planta baja
            </option>

            <option value="Primer piso" @selected(old('piso', $aula->piso ?? '') === 'Primer piso')>
                Primer piso
            </option>

            <option value="Segundo piso" @selected(old('piso', $aula->piso ?? '') === 'Segundo piso')>
                Segundo piso
            </option>

            <option value="Tercer piso" @selected(old('piso', $aula->piso ?? '') === 'Tercer piso')>
                Tercer piso
            </option>

            <option value="Cuarto piso" @selected(old('piso', $aula->piso ?? '') === 'Cuarto piso')>
                Cuarto piso
            </option>

            <option value="Quinto piso" @selected(old('piso', $aula->piso ?? '') === 'Quinto piso')>
                Quinto piso
            </option>
        </select>
        <span class="mt-1 text-sm text-red-600" id="error-piso" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Capacidad</label>
        <input type="number"
               id="capacidad"
               name="capacidad"
               value="{{ old('capacidad', $aula->capacidad ?? '') }}"
               min="1"
               placeholder="Ej: 30"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-capacidad" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Disponibilidad</label>
        <select id="disponible" name="disponible"
                required
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="1" @selected(old('disponible', $aula->disponible ?? 1) == 1)>Disponible</option>
            <option value="0" @selected(old('disponible', $aula->disponible ?? 1) == 0)>No disponible</option>
        </select>
        <span class="mt-1 text-sm text-red-600" id="error-disponible" aria-live="polite"></span>
    </div>

    <div class="border-t border-slate-200 pt-6 md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Dimensiones</h3>
        <p class="mt-1 text-sm text-slate-500">
            Estos datos ayudarán a describir mejor el espacio físico del aula.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Largo en metros</label>
        <input type="number"
               step="0.01"
               id="largo"
               name="largo"
               value="{{ old('largo', $aula->largo ?? '') }}"
               placeholder="Ej: 8.50"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-largo" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Ancho en metros</label>
        <input type="number"
               step="0.01"
               id="ancho"
               name="ancho"
               value="{{ old('ancho', $aula->ancho ?? '') }}"
               placeholder="Ej: 6.20"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-ancho" aria-live="polite"></span>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button id="aula-submit" type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar aula
        </button>

        <a href="{{ route('admin.aulas.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const get = id => document.getElementById(id);

    const rules = {
        codigo: {regex: /^[A-Za-z0-9\-_ ]+$/, msg: 'Sólo letras, números, espacios, guiones y guiones bajos.'},
        nombre: {regex: /^[A-Za-zÀ-ÿ0-9\s\.,'\-]+$/, msg: 'Nombre inválido — sin caracteres especiales.'},
        ubicacion: {regex: /^[A-Za-zÀ-ÿ0-9\s\.,'\-]+$/, msg: 'Ubicación inválida — sin caracteres especiales.'}
    };

    const fields = ['codigo','nombre','ubicacion','piso','capacidad','disponible','largo','ancho'];

    function toggleSubmit() {
        const submit = get('aula-submit');
        const hasError = fields.some(id => {
            const err = get('error-' + id);
            return err && err.textContent.trim() !== '';
        });
        submit.disabled = hasError;
        if (hasError) {
            submit.classList.add('opacity-60', 'pointer-events-none');
        } else {
            submit.classList.remove('opacity-60', 'pointer-events-none');
        }
    }

    fields.forEach(function (id) {
        const el = get(id);
        const errorEl = get('error-' + id);
        if (!el || !errorEl) return;

        function validate() {
            const val = (el.value || '').toString().trim();
            let ok = true;
            let msg = '';

            if (id === 'piso' || id === 'disponible') {
                if (val === '') { ok = false; msg = 'Seleccione una opción.'; }
            } else if (id === 'capacidad') {
                if (val === '' || !/^[0-9]+$/.test(val) || parseInt(val, 10) < 1) { ok = false; msg = 'Ingrese un número entero mayor a 0.'; }
            } else if (id === 'largo' || id === 'ancho') {
                if (val !== '' && !/^[0-9]+(\.[0-9]{1,2})?$/.test(val)) { ok = false; msg = 'Ingrese un número válido (ej: 8.50).'; }
            } else {
                if (val === '') { ok = false; msg = 'Este campo es obligatorio.'; }
                else if (rules[id] && !rules[id].regex.test(val)) { ok = false; msg = rules[id].msg; }
            }

            if (!ok) {
                errorEl.textContent = msg;
                el.classList.add('border-red-500');
                el.setAttribute('aria-invalid', 'true');
            } else {
                errorEl.textContent = '';
                el.classList.remove('border-red-500');
                el.removeAttribute('aria-invalid');
            }
            toggleSubmit();
        }

        // On input, try to strip disallowed characters for text fields
        if (rules[id]) {
            el.addEventListener('input', function (e) {
                const r = rules[id].regex;
                const original = el.value;
                // remove characters not matching allowed set
                const filtered = original.split('').filter(ch => r.test(ch) || ch === ' ').join('');
                if (filtered !== original) el.value = filtered;
                validate();
            });
        } else {
            el.addEventListener('input', validate);
        }
        el.addEventListener('change', validate);
        // initial validation
        validate();
    });
});
</script>