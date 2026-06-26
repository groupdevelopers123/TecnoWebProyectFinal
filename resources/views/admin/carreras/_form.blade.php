<div class="grid gap-6 md:grid-cols-2">
    <div>
        <label class="mb-2 block font-bold">Código</label>
        <input id="codigo" name="codigo" value="{{ old('codigo', $carrera->codigo ?? '') }}" required
               class="w-full rounded-2xl border px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-codigo" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block font-bold">Nombre</label>
        <input id="nombre" name="nombre" value="{{ old('nombre', $carrera->nombre ?? '') }}" required
               class="w-full rounded-2xl border px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-nombre" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block font-bold">Duración</label>
        <input id="duracion" type="number" name="duracion" value="{{ old('duracion', $carrera->duracion ?? '') }}"
               class="w-full rounded-2xl border px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-duracion" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block font-bold">Régimen académico</label>
        <select id="regimen_academico" name="regimen_academico" class="w-full rounded-2xl border px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione</option>
            @foreach (['Semestral', 'Anual', 'Modular'] as $regimen)
                <option value="{{ $regimen }}" @selected(old('regimen_academico', $carrera->regimen_academico ?? '') === $regimen)>{{ $regimen }}</option>
            @endforeach
        </select>
        <span class="mt-1 text-sm text-red-600" id="error-regimen_academico" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block font-bold">Estado</label>
        <select id="estado" name="estado" class="w-full rounded-2xl border px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="1" @selected(old('estado', $carrera->estado ?? 1) == 1)>Activo</option>
            <option value="0" @selected(old('estado', $carrera->estado ?? 1) == 0)>Inactivo</option>
        </select>
        <span class="mt-1 text-sm text-red-600" id="error-estado" aria-live="polite"></span>
    </div>

    <div class="md:col-span-2">
        <button id="carrera-submit" type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">Guardar</button>
        <a href="{{ route('admin.carreras.index') }}" class="ml-2 rounded-2xl bg-slate-100 px-6 py-3 font-bold text-slate-700 transition hover:bg-slate-200">Cancelar</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rules = {
        codigo: {regex: /^[A-Za-z0-9\-_ ]+$/, msg: 'Sólo letras, números, espacios, guiones y guiones bajos.'},
        nombre: {regex: /^[A-Za-zÀ-ÿ0-9\s\.,'\-]+$/, msg: 'Nombre inválido — sin caracteres especiales.'},
        duracion: {fn: v => v === '' || (/^[0-9]+$/.test(v) && parseInt(v,10) > 0), msg: 'Duración debe ser un número entero mayor a 0.'}
    };

    const fields = ['codigo','nombre','duracion','regimen_academico','estado'];

    const touched = {};
    const submit = document.getElementById('carrera-submit');

    function showError(id, message) {
        const err = document.getElementById('error-' + id);
        const el = document.getElementById(id);
        if (err) err.textContent = message || '';
        if (el) {
            if (message) el.classList.add('border-red-500'); else el.classList.remove('border-red-500');
            if (message) el.setAttribute('aria-invalid','true'); else el.removeAttribute('aria-invalid');
        }
        toggleSubmit();
    }

    function validateField(id) {
        const el = document.getElementById(id);
        if (!el) return '';
        const val = (el.value || '').toString().trim();

        // required fields: codigo, nombre
        if ((id === 'codigo' || id === 'nombre') && val === '') return 'Este campo es obligatorio.';

        if (rules[id]) {
            const r = rules[id];
            const ok = r.fn ? r.fn(val) : r.regex.test(val);
            if (!ok) return r.msg;
        }

        // regimen_academico allowed empty
        // estado should be present but select always has value

        return '';
    }

    function toggleSubmit() {
        const hasError = fields.some(id => {
            const e = document.getElementById('error-' + id);
            return e && e.textContent.trim() !== '';
        });
        if (submit) {
            submit.disabled = hasError;
            submit.classList.toggle('opacity-60', hasError);
            submit.classList.toggle('pointer-events-none', hasError);
        }
    }

    function attach(id) {
        const el = document.getElementById(id);
        if (!el) return;
        touched[id] = false;
        el.addEventListener('focus', () => { touched[id] = true; });
        el.addEventListener('input', () => {
            const msg = validateField(id);
            if (touched[id]) {
                // if required and empty, don't show required until blur
                if ((id === 'codigo' || id === 'nombre') && (el.value || '').toString().trim() === '') {
                    showError(id, '');
                    return;
                }
                showError(id, msg);
            }
        });
        el.addEventListener('blur', () => { showError(id, validateField(id)); });
    }

    fields.forEach(attach);
    // initial clear
    fields.forEach(id => { const e = document.getElementById('error-'+id); if (e) e.textContent = ''; });
    toggleSubmit();

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            let hasError = false;
            fields.forEach(id => {
                const msg = validateField(id);
                showError(id, msg);
                if (msg) hasError = true;
            });
            if (hasError) {
                e.preventDefault();
                const first = fields.find(id => document.getElementById('error-'+id) && document.getElementById('error-'+id).textContent.trim() !== '');
                if (first) { const el = document.getElementById(first); if (el) el.focus(); }
            }
        });
    }
});
</script>
 