<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Datos generales</h3>
        <p class="mt-1 text-sm text-slate-500">Información principal del usuario dentro del sistema.</p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Rol</label>
        <select name="role_id" id="role_id" required
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un rol</option>
            @foreach ($roles as $role)
                <option
                    value="{{ $role->id }}"
                    data-role="{{ $role->nombre }}"
                    @selected(old('role_id', $usuario->role_id ?? '') == $role->id)
                >
                    {{ ucfirst($role->nombre) }}
                </option>
            @endforeach
        </select>
        <span class="mt-1 text-sm text-red-600" id="error-role_id" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">CI</label>
        <input id="ci" type="text" name="ci" value="{{ old('ci', $usuario->ci ?? '') }}" required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-ci" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Nombres</label>
        <input id="nombres" type="text" name="nombres" value="{{ old('nombres', $usuario->nombres ?? '') }}" required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-nombres" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Apellidos</label>
        <input id="apellidos" type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos ?? '') }}" required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-apellidos" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $usuario->email ?? '') }}" required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-email" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Teléfono</label>
        <input id="telefono" type="text" name="telefono" value="{{ old('telefono', $usuario->telefono ?? '') }}"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-telefono" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Dirección</label>
        <input id="direccion" type="text" name="direccion" value="{{ old('direccion', $usuario->direccion ?? '') }}"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-direccion" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Fecha de nacimiento</label>
        <input id="fecha_nacimiento" type="date" name="fecha_nacimiento"
               value="{{ old('fecha_nacimiento', isset($usuario) && $usuario->fecha_nacimiento ? $usuario->fecha_nacimiento->format('Y-m-d') : '') }}"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-fecha_nacimiento" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Estado</label>
        <select id="estado" name="estado" required
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="1" @selected(old('estado', $usuario->estado ?? 1) == 1)>Activo</option>
            <option value="0" @selected(old('estado', $usuario->estado ?? 1) == 0)>Inactivo</option>
        </select>
        <span class="mt-1 text-sm text-red-600" id="error-estado" aria-live="polite"></span>
    </div>

    <div class="border-t border-slate-200 pt-6 md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Seguridad</h3>
        <p class="mt-1 text-sm text-slate-500">Credenciales de acceso para iniciar sesión.</p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Contraseña</label>
        <input id="password" type="password" name="password" {{ isset($usuario) ? '' : 'required' }}
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-password" aria-live="polite"></span>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Confirmar contraseña</label>
        <input id="password_confirmation" type="password" name="password_confirmation" {{ isset($usuario) ? '' : 'required' }}
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <span class="mt-1 text-sm text-red-600" id="error-password_confirmation" aria-live="polite"></span>
    </div>

    @if (isset($usuario))
        <div class="md:col-span-2">
            <p class="rounded-2xl bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">
                Deja la contraseña vacía si no deseas cambiarla.
            </p>
        </div>
    @endif

    <div class="border-t border-slate-200 pt-6 md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Datos específicos del rol</h3>
        <p class="mt-1 text-sm text-slate-500">Estos campos cambian según el tipo de usuario seleccionado.</p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Código</label>
        <input
            id="codigo"
            type="text"
            name="codigo"
            value="{{ old('codigo',
                $usuario->propietarioDetalle->codigo
                ?? $usuario->secretariaDetalle->codigo
                ?? $usuario->docenteDetalle->codigo
                ?? $usuario->alumnoDetalle->codigo
                ?? ''
            ) }}"
            required
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
        >
        <span class="mt-1 text-sm text-red-600" id="error-codigo" aria-live="polite"></span>
    </div>

    <div class="campo-rol hidden md:col-span-2" data-rol="propietario">
        <div class="rounded-3xl border border-blue-100 bg-blue-50 p-5">
            <h4 class="font-black text-slate-900">Datos de propietario</h4>

              <div class="mt-4">
              <label class="mb-2 block text-sm font-bold text-slate-700">Cargo</label>
              <input id="cargo" type="text" name="cargo"
                  value="{{ old('cargo', $usuario->propietarioDetalle->cargo ?? '') }}"
                  class="w-full rounded-2xl border border-blue-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
              <span class="mt-1 text-sm text-red-600" id="error-cargo" aria-live="polite"></span>
              </div>
        </div>
    </div>

    <div class="campo-rol hidden md:col-span-2" data-rol="secretaria">
        <div class="rounded-3xl border border-violet-100 bg-violet-50 p-5">
            <h4 class="font-black text-slate-900">Datos de secretaria</h4>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Turno de trabajo</label>
                    <input id="turno_trabajo" type="text" name="turno_trabajo"
                           value="{{ old('turno_trabajo', $usuario->secretariaDetalle->turno_trabajo ?? '') }}"
                           class="w-full rounded-2xl border border-violet-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <span class="mt-1 text-sm text-red-600" id="error-turno_trabajo" aria-live="polite"></span>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Sueldo</label>
                    <input id="sueldo" type="number" step="0.01" name="sueldo"
                           value="{{ old('sueldo', $usuario->secretariaDetalle->sueldo ?? '') }}"
                           class="w-full rounded-2xl border border-violet-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <span class="mt-1 text-sm text-red-600" id="error-sueldo" aria-live="polite"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="campo-rol hidden md:col-span-2" data-rol="docente">
        <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-5">
            <h4 class="font-black text-slate-900">Datos de docente</h4>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Especialidad</label>
                    <input id="especialidad" type="text" name="especialidad"
                           value="{{ old('especialidad', $usuario->docenteDetalle->especialidad ?? '') }}"
                           class="w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <span class="mt-1 text-sm text-red-600" id="error-especialidad" aria-live="polite"></span>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Título</label>
                    <input id="titulo" type="text" name="titulo"
                           value="{{ old('titulo', $usuario->docenteDetalle->titulo ?? '') }}"
                           class="w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <span class="mt-1 text-sm text-red-600" id="error-titulo" aria-live="polite"></span>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">Registro profesional</label>
                    <input id="registro_profesional" type="text" name="registro_profesional"
                           value="{{ old('registro_profesional', $usuario->docenteDetalle->registro_profesional ?? '') }}"
                           class="w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <span class="mt-1 text-sm text-red-600" id="error-registro_profesional" aria-live="polite"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="campo-rol hidden md:col-span-2" data-rol="alumno">
        <div class="rounded-3xl border border-amber-100 bg-amber-50 p-5">
            <h4 class="font-black text-slate-900">Datos de alumno</h4>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Colegio de origen</label>
                    <input id="colegio_origen" type="text" name="colegio_origen"
                           value="{{ old('colegio_origen', $usuario->alumnoDetalle->colegio_origen ?? '') }}"
                           class="w-full rounded-2xl border border-amber-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <span class="mt-1 text-sm text-red-600" id="error-colegio_origen" aria-live="polite"></span>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Año de bachillerato</label>
                    <input id="anio_bachillerato" type="number" name="anio_bachillerato"
                           value="{{ old('anio_bachillerato', $usuario->alumnoDetalle->anio_bachillerato ?? '') }}"
                           class="w-full rounded-2xl border border-amber-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <span class="mt-1 text-sm text-red-600" id="error-anio_bachillerato" aria-live="polite"></span>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">Estado académico</label>
                    <input id="estado_academico" type="text" name="estado_academico"
                           value="{{ old('estado_academico', $usuario->alumnoDetalle->estado_academico ?? 'activo') }}"
                           class="w-full rounded-2xl border border-amber-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <span class="mt-1 text-sm text-red-600" id="error-estado_academico" aria-live="polite"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button id="usuario-submit" type="submit"
                class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            Guardar usuario
        </button>

        <a href="{{ route('admin.usuarios.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Mostrar campos por rol (mantener comportamiento anterior)
    function mostrarCamposPorRol() {
        const select = document.getElementById('role_id');
        const selectedOption = select.options[select.selectedIndex];
        const rol = selectedOption ? selectedOption.getAttribute('data-role') : null;

        document.querySelectorAll('.campo-rol').forEach(function (campo) {
            if (campo.getAttribute('data-rol') === rol) {
                campo.classList.remove('hidden');
                campo.classList.add('animate-[fadeUp_.25s_ease-out]');
            } else {
                campo.classList.add('hidden');
            }
        });
    }

    document.getElementById('role_id').addEventListener('change', function () {
        mostrarCamposPorRol();
        // re-init validation for newly visible fields
        initValidation();
    });
    mostrarCamposPorRol();

    // VALIDATION
    let formSubmitted = false;
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('usuario-submit');

    const rules = {
        ci: {regex: /^[0-9\-]+$/, msg: 'CI sólo puede tener números y guiones.'},
        nombres: {regex: /^[A-Za-zÀ-ÿ\s'\-]+$/, msg: 'Nombres sin caracteres especiales.'},
        apellidos: {regex: /^[A-Za-zÀ-ÿ\s'\-]+$/, msg: 'Apellidos sin caracteres especiales.'},
        telefono: {regex: /^[0-9+\s\-()]{0,20}$/, msg: 'Teléfono inválido.'},
        codigo: {regex: /^[A-Za-z0-9\-_ ]+$/, msg: 'Código: letras, números, guiones y espacios.'},
        email: {fn: (v) => /\S+@\S+\.\S+/.test(v), msg: 'Email inválido.'}
    };

    const defaultFields = [
        'role_id','ci','nombres','apellidos','email','telefono','direccion','fecha_nacimiento','estado',
        'password','password_confirmation','codigo','cargo','turno_trabajo','sueldo','especialidad','titulo','registro_profesional',
        'colegio_origen','anio_bachillerato','estado_academico'
    ];

    function showError(id, message) {
        const el = document.getElementById(id);
        const err = document.getElementById('error-' + id);
        if (err) err.textContent = message || '';
        if (el) {
            if (message) el.classList.add('border-red-500'); else el.classList.remove('border-red-500');
            if (message) el.setAttribute('aria-invalid', 'true'); else el.removeAttribute('aria-invalid');
        }
        toggleSubmit();
    }

    function validateField(id) {
        const el = document.getElementById(id);
        if (!el) return '';
        const val = (el.value || '').toString().trim();
        // required rules for some fields
        const requiredAlways = ['role_id','ci','nombres','apellidos','email','estado','codigo'];
        // password required only on create - handled by presence of required attribute in blade

        // if not required and empty -> ok
        if (val === '' && requiredAlways.indexOf(id) === -1) return '';

        // required validation
        if (requiredAlways.indexOf(id) !== -1 && val === '') return 'Este campo es obligatorio.';

        // specific rules
        if (rules[id]) {
            const r = rules[id];
            const ok = r.fn ? r.fn(val) : r.regex.test(val);
            if (!ok) return r.msg;
        }

        // passwords match
        if (id === 'password_confirmation') {
            const pass = document.getElementById('password') ? document.getElementById('password').value : '';
            if (val !== pass) return 'Las contraseñas no coinciden.';
        }

        // numeric validations
        if (id === 'anio_bachillerato') {
            if (val !== '' && !/^[0-9]{4}$/.test(val)) return 'Ingrese un año válido (4 dígitos).';
        }

        return '';
    }

    const touched = {};

    function toggleSubmit() {
        const hasError = defaultFields.some(id => {
            const err = document.getElementById('error-' + id);
            return err && err.textContent.trim() !== '';
        });
        if (submitBtn) {
            submitBtn.disabled = hasError;
            submitBtn.classList.toggle('opacity-60', hasError);
            submitBtn.classList.toggle('pointer-events-none', hasError);
        }
    }

    function attachHandlers(id) {
        const el = document.getElementById(id);
        if (!el) return;

        if (!(id in touched)) touched[id] = false;

        el.addEventListener('focus', function () { touched[id] = true; });

        el.addEventListener('input', function () {
            // show immediate pattern errors only when user already touched field
            const msg = validateField(id);
            if (touched[id]) {
                // for required fields, only show required message on blur or after submit
                const requiredAlways = ['role_id','ci','nombres','apellidos','email','estado','codigo'];
                if (requiredAlways.indexOf(id) !== -1) {
                    // if value empty, don't show required yet
                    const val = (el.value || '').toString().trim();
                    if (val === '') {
                        showError(id, '');
                        return;
                    }
                }
                showError(id, msg);
            }
        });

        el.addEventListener('blur', function () {
            // on blur show required messages if missing
            const msg = validateField(id);
            showError(id, msg);
        });
    }

    function initValidation() {
        defaultFields.forEach(function (id) {
            attachHandlers(id);
            // initial clear
            const err = document.getElementById('error-' + id);
            if (err) err.textContent = '';
            const el = document.getElementById(id);
            if (el) el.classList.remove('border-red-500');
        });
        toggleSubmit();
    }

    // initialize
    initValidation();

    // on form submit, mark submitted and validate all
    if (form) {
        form.addEventListener('submit', function (e) {
            // run full validation
            formSubmitted = true;
            let hasError = false;
            defaultFields.forEach(function (id) {
                const msg = validateField(id);
                showError(id, msg);
                if (msg) hasError = true;
            });
            if (hasError) {
                e.preventDefault();
                // focus first error
                const first = defaultFields.find(id => document.getElementById('error-' + id) && document.getElementById('error-' + id).textContent.trim() !== '');
                if (first) {
                    const el = document.getElementById(first);
                    if (el) el.focus();
                }
            }
        });
    }

});
</script>