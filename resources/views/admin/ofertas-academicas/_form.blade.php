@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-bold">Revisa los siguientes errores:</p>

        <ul class="mt-2 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">
        <h3 class="text-lg font-black text-slate-900">
            Datos de la oferta académica
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Selecciona la carrera, el periodo académico, los cupos y las fechas
            de vigencia.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">
            Carrera
        </label>

        <select name="carrera_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

            <option value="">
                Seleccione una carrera
            </option>

            @foreach ($carreras as $carrera)
                <option value="{{ $carrera->id }}"
                    @selected(
                        old(
                            'carrera_id',
                            $oferta->carrera_id ?? ''
                        ) == $carrera->id
                    )>
                    {{ $carrera->codigo }} - {{ $carrera->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">
            Período académico
        </label>

        <select name="periodo_academico_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

            <option value="">
                Seleccione un periodo
            </option>

            @foreach ($periodos as $periodo)
                <option value="{{ $periodo->id }}"
                    @selected(
                        old(
                            'periodo_academico_id',
                            $oferta->periodo_academico_id ?? ''
                        ) == $periodo->id
                    )>
                    {{ $periodo->nombre }} - {{ $periodo->gestion }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">
            Docente (Opcional)
        </label>

        <select name="docente_detalle_id"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

            <option value="">
                Seleccione un docente (opcional)
            </option>

            @foreach ($docentes as $docente)
                <option value="{{ $docente->id }}"
                    @selected(
                        old(
                            'docente_detalle_id',
                            $oferta->docente_detalle_id ?? ''
                        ) == $docente->id
                    )>
                    {{ $docente->codigo }} - {{ $docente->user->nombres }} {{ $docente->user->apellidos }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">
            Nombre de la oferta
        </label>

        <input type="text"
               name="nombre"
               value="{{ old('nombre', $oferta->nombre ?? '') }}"
               required
               maxlength="150"
               placeholder="Ejemplo: Sistemas Informáticos - Gestión 2026"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">
            Cantidad de cupos
        </label>

        <input type="number"
               name="cantidad_cupos"
               value="{{ old('cantidad_cupos', $oferta->cantidad_cupos ?? '') }}"
               min="1"
               step="1"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">
            Cupos disponibles
        </label>

        <input type="number"
               name="cupos_disponibles"
               value="{{ old('cupos_disponibles', $oferta->cupos_disponibles ?? '') }}"
               min="0"
               step="1"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">
            Fecha de inicio
        </label>

        <input type="date"
               name="fecha_inicio"
               value="{{ old(
                    'fecha_inicio',
                    isset($oferta) && $oferta->fecha_inicio
                        ? $oferta->fecha_inicio->format('Y-m-d')
                        : ''
               ) }}"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">
            Fecha de finalización
        </label>

        <input type="date"
               name="fecha_fin"
               value="{{ old(
                    'fecha_fin',
                    isset($oferta) && $oferta->fecha_fin
                        ? $oferta->fecha_fin->format('Y-m-d')
                        : ''
               ) }}"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div class="md:col-span-2">
        <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-5">
            <div class="mb-5">
                <h3 class="text-lg font-black text-slate-900">
                    Precios de la oferta académica
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Ingresa los precios en bolivianos. Se permiten hasta dos
                    decimales.
                </p>
            </div>

            <div class="grid gap-5 md:grid-cols-3">

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Precio de matrícula
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-500">
                            Bs
                        </span>

                        <input type="number"
                               name="precio_matricula"
                               value="{{ old(
                                    'precio_matricula',
                                    $oferta->precio_matricula ?? ''
                               ) }}"
                               min="0"
                               max="99999999.99"
                               step="0.01"
                               inputmode="decimal"
                               required
                               placeholder="0.00"
                               class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-12 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Precio de mensualidad
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-500">
                            Bs
                        </span>

                        <input type="number"
                               name="precio_mensualidad"
                               value="{{ old(
                                    'precio_mensualidad',
                                    $oferta->precio_mensualidad ?? ''
                               ) }}"
                               min="0"
                               max="99999999.99"
                               step="0.01"
                               inputmode="decimal"
                               required
                               placeholder="0.00"
                               class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-12 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Precio de carrera completa
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-500">
                            Bs
                        </span>

                        <input type="number"
                               name="precio_carrera_completa"
                               value="{{ old(
                                    'precio_carrera_completa',
                                    $oferta->precio_carrera_completa ?? ''
                               ) }}"
                               min="0"
                               max="99999999.99"
                               step="0.01"
                               inputmode="decimal"
                               required
                               placeholder="0.00"
                               class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-12 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">
            Estado
        </label>

        <select name="estado"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

            <option value="1"
                @selected(old('estado', $oferta->estado ?? 1) == 1)>
                Activa
            </option>

            <option value="0"
                @selected(old('estado', $oferta->estado ?? 1) == 0)>
                Inactiva
            </option>
        </select>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">

            <i class="fa-solid fa-floppy-disk text-xs"></i>

            Guardar oferta
        </button>

        <a href="{{ route('admin.ofertas-academicas.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>

</div>