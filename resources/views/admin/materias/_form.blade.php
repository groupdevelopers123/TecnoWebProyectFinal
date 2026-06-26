<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Datos de la materia</h3>
        <p class="mt-1 text-sm text-slate-500">
            Registra la información principal de la materia académica.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Código</label>
        <input type="text"
               id="codigo"
               name="codigo"
               value="{{ old('codigo', $materia->codigo ?? '') }}"
               placeholder="Ej: WEB-101"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-codigo" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Nombre</label>
        <input type="text"
               id="nombre"
               name="nombre"
               value="{{ old('nombre', $materia->nombre ?? '') }}"
               placeholder="Ej: Tecnología Web"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-nombre" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Carga horaria</label>
        <input type="number"
               id="carga_horaria"
               name="carga_horaria"
               value="{{ old('carga_horaria', $materia->carga_horaria ?? '') }}"
               placeholder="Ej: 80"
               min="1"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-carga_horaria" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Docente (Opcional)</label>
        <select id="docente_detalle_id" name="docente_detalle_id"
            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un docente (opcional)</option>
            @foreach ($docentes as $docente)
                <option value="{{ $docente->id }}"
                    @selected(old('docente_detalle_id', $materia->docente_detalle_id ?? '') == $docente->id)>
                    {{ $docente->codigo }} - {{ $docente->user->nombres }} {{ $docente->user->apellidos }}
                </option>
            @endforeach
        </select>
        <span class="mt-1 text-sm text-red-600" id="error-docente_detalle_id" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Estado</label>
        <select name="estado"
                required
                id="estado"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="1" @selected(old('estado', $materia->estado ?? 1) == 1)>Activa</option>
            <option value="0" @selected(old('estado', $materia->estado ?? 1) == 0)>Inactiva</option>
        </select>
        <span class="mt-1 text-sm text-red-600" id="error-estado" aria-live="polite"></span>
    </div>



    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button id="materia-submit" type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar materia
        </button>

        <a href="{{ route('admin.materias.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rules = {
        codigo: {regex: /^[A-Za-z0-9\-_ ]+$/, msg: 'Sólo letras, números, espacios, guiones y guiones bajos.'},
        nombre: {regex: /^[A-Za-zÀ-ÿ0-9\s\.,'\-]+$/, msg: 'Nombre inválido — sin caracteres especiales.'},
        carga_horaria: {fn: v => v === '' || (/^[0-9]+$/.test(v) && parseInt(v,10) > 0), msg: 'Ingrese un número entero mayor a 0.'}
    };

    const fields = ['codigo','nombre','carga_horaria','docente_detalle_id','estado'];
    const touched = {};
    const submit = document.getElementById('materia-submit');

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

        // required: codigo, nombre
        if ((id === 'codigo' || id === 'nombre') && val === '') return 'Este campo es obligatorio.';

        if (rules[id]) {
            const r = rules[id];
            const ok = r.fn ? r.fn(val) : r.regex.test(val);
            if (!ok) return r.msg;
        }

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

    fields.forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        touched[id] = false;
        el.addEventListener('focus', function () { touched[id] = true; });
        el.addEventListener('input', function () {
            const msg = validateField(id);
            if (touched[id]) {
                if ((id === 'codigo' || id === 'nombre') && (el.value || '').toString().trim() === '') { showError(id, ''); return; }
                showError(id, msg);
            }
        });
        el.addEventListener('blur', function () { showError(id, validateField(id)); });
    });

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