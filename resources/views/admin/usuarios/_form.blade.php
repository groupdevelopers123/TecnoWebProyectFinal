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
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">CI</label>
        <input type="text" name="ci" value="{{ old('ci', $usuario->ci ?? '') }}" required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Nombres</label>
        <input type="text" name="nombres" value="{{ old('nombres', $usuario->nombres ?? '') }}" required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Apellidos</label>
        <input type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos ?? '') }}" required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $usuario->email ?? '') }}" required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Teléfono</label>
        <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono ?? '') }}"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Dirección</label>
        <input type="text" name="direccion" value="{{ old('direccion', $usuario->direccion ?? '') }}"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento"
               value="{{ old('fecha_nacimiento', isset($usuario) && $usuario->fecha_nacimiento ? $usuario->fecha_nacimiento->format('Y-m-d') : '') }}"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Estado</label>
        <select name="estado" required
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="1" @selected(old('estado', $usuario->estado ?? 1) == 1)>Activo</option>
            <option value="0" @selected(old('estado', $usuario->estado ?? 1) == 0)>Inactivo</option>
        </select>
    </div>

    <div class="border-t border-slate-200 pt-6 md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">Seguridad</h3>
        <p class="mt-1 text-sm text-slate-500">Credenciales de acceso para iniciar sesión.</p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Contraseña</label>
        <input type="password" name="password" {{ isset($usuario) ? '' : 'required' }}
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" {{ isset($usuario) ? '' : 'required' }}
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
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
    </div>

    <div class="campo-rol hidden md:col-span-2" data-rol="propietario">
        <div class="rounded-3xl border border-blue-100 bg-blue-50 p-5">
            <h4 class="font-black text-slate-900">Datos de propietario</h4>

            <div class="mt-4">
                <label class="mb-2 block text-sm font-bold text-slate-700">Cargo</label>
                <input type="text" name="cargo"
                       value="{{ old('cargo', $usuario->propietarioDetalle->cargo ?? '') }}"
                       class="w-full rounded-2xl border border-blue-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            </div>
        </div>
    </div>

    <div class="campo-rol hidden md:col-span-2" data-rol="secretaria">
        <div class="rounded-3xl border border-violet-100 bg-violet-50 p-5">
            <h4 class="font-black text-slate-900">Datos de secretaria</h4>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Turno de trabajo</label>
                    <input type="text" name="turno_trabajo"
                           value="{{ old('turno_trabajo', $usuario->secretariaDetalle->turno_trabajo ?? '') }}"
                           class="w-full rounded-2xl border border-violet-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Sueldo</label>
                    <input type="number" step="0.01" name="sueldo"
                           value="{{ old('sueldo', $usuario->secretariaDetalle->sueldo ?? '') }}"
                           class="w-full rounded-2xl border border-violet-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
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
                    <input type="text" name="especialidad"
                           value="{{ old('especialidad', $usuario->docenteDetalle->especialidad ?? '') }}"
                           class="w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Título</label>
                    <input type="text" name="titulo"
                           value="{{ old('titulo', $usuario->docenteDetalle->titulo ?? '') }}"
                           class="w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">Registro profesional</label>
                    <input type="text" name="registro_profesional"
                           value="{{ old('registro_profesional', $usuario->docenteDetalle->registro_profesional ?? '') }}"
                           class="w-full rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
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
                    <input type="text" name="colegio_origen"
                           value="{{ old('colegio_origen', $usuario->alumnoDetalle->colegio_origen ?? '') }}"
                           class="w-full rounded-2xl border border-amber-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Año de bachillerato</label>
                    <input type="number" name="anio_bachillerato"
                           value="{{ old('anio_bachillerato', $usuario->alumnoDetalle->anio_bachillerato ?? '') }}"
                           class="w-full rounded-2xl border border-amber-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700">Estado académico</label>
                    <input type="text" name="estado_academico"
                           value="{{ old('estado_academico', $usuario->alumnoDetalle->estado_academico ?? 'activo') }}"
                           class="w-full rounded-2xl border border-amber-100 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button type="submit"
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

    document.getElementById('role_id').addEventListener('change', mostrarCamposPorRol);
    mostrarCamposPorRol();
</script>