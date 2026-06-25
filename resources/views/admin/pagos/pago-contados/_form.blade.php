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
        <h3 class="text-lg font-black text-slate-900">Datos del pago al contado</h3>
        <p class="mt-1 text-sm text-slate-500">
            Registra un pago manual o genera un QR con PagoFácil si el método seleccionado es QR.
        </p>
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Inscripción</label>
        <select name="inscripcion_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione una inscripción</option>

            @foreach ($inscripciones as $inscripcion)
                <option value="{{ $inscripcion->id }}"
                    @selected(old('inscripcion_id', $pago->inscripcion_id ?? '') == $inscripcion->id)>
                    {{ $inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
                    /
                    {{ $inscripcion->ofertaAcademica->carrera->nombre ?? 'Sin carrera' }}
                    /
                    {{ $inscripcion->ofertaAcademica->periodoAcademico->nombre ?? 'Sin periodo' }}
                    {{ $inscripcion->ofertaAcademica->periodoAcademico->gestion ?? '' }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Concepto de pago</label>
        <select name="concepto_pago_id"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            <option value="">Seleccione un concepto</option>

            @foreach ($conceptos as $concepto)
                <option value="{{ $concepto->id }}"
                    @selected(old('concepto_pago_id', $pago->concepto_pago_id ?? '') == $concepto->id)>
                    {{ $concepto->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Monto pagado</label>
        <input type="number"
               step="0.01"
               min="0.01"
               name="monto_pagado"
               value="{{ old('monto_pagado', $pago->monto_pagado ?? '') }}"
               required
               placeholder="Ejemplo: 100.00"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">

        <p class="mt-2 text-xs text-slate-500">
            Este monto es el monto real que se guardará en la base de datos.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Fecha de pago</label>
        <input type="date"
               name="fecha_pago"
               value="{{ old('fecha_pago', isset($pago) && $pago->fecha_pago ? $pago->fecha_pago->format('Y-m-d') : now()->format('Y-m-d')) }}"
               required
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
    <label class="mb-2 block text-sm font-bold text-slate-700">Método de pago</label>
    <select name="metodo_pago"
            id="metodo-pago"
            required
            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        @foreach (['Efectivo', 'Transferencia', 'QR'] as $metodo)
            <option value="{{ $metodo }}"
                @selected(old('metodo_pago', $pago->metodo_pago ?? 'Efectivo') === $metodo)>
                {{ $metodo }}
            </option>
        @endforeach
    </select>

    <p id="aviso-qr-prueba"
       style="display: none;"
       class="mt-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
        Para pruebas, el QR de PagoFácil se generará por Bs 0.01, aunque el monto real del pago sea diferente.
    </p>
</div>

<div>
    <label class="mb-2 block text-sm font-bold text-slate-700">Estado</label>
    <select name="estado"
            id="estado-pago"
            required
            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        @foreach (['Pendiente', 'Confirmado', 'Anulado', 'Fallido'] as $estado)
            <option value="{{ $estado }}"
                @selected(old('estado', $pago->estado ?? 'Pendiente') === $estado)>
                {{ $estado }}
            </option>
        @endforeach
    </select>

    <p class="mt-2 text-xs text-slate-500">
        Si el método es QR, el estado se guardará como Pendiente hasta confirmar el pago.
    </p>
</div>

<div>
    <label class="mb-2 block text-sm font-bold text-slate-700">Correo solicitante</label>
    <input type="email"
           name="correo_solicitante"
           value="{{ old('correo_solicitante', $pago->correo_solicitante ?? '') }}"
           placeholder="cliente@correo.com"
           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
</div>

<div>
    <label class="mb-2 block text-sm font-bold text-slate-700">Código transacción</label>
    <input type="text"
           name="codigo_transaccion"
           value="{{ old('codigo_transaccion', $pago->codigo_transaccion ?? '') }}"
           placeholder="Opcional"
           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
</div>

<div class="md:col-span-2">
    <label class="mb-2 block text-sm font-bold text-slate-700">Observación</label>
    <textarea name="observacion"
              rows="4"
              placeholder="Observaciones del pago"
              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('observacion', $pago->observacion ?? '') }}</textarea>
</div>

<div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">

    <button type="submit"
            name="accion"
            value="guardar"
            id="btn-guardar-pago"
            class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
        <i class="fa-solid fa-floppy-disk text-xs"></i>
        Guardar pago
    </button>

    <button type="submit"
            name="accion"
            value="generar_qr"
            id="btn-generar-qr"
            style="display: none;"
            class="items-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:-translate-y-0.5 hover:bg-emerald-700">
        <i class="fa-solid fa-qrcode text-xs"></i>
        Generar QR PagoFácil
    </button>

    <a href="{{ route('admin.pago-contados.index') }}"
       class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
        Cancelar
    </a>
</div>
</div>

<div id="modal-qr-pagofacil"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4 backdrop-blur-sm">

    <div class="relative max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-[2rem] bg-white p-6 shadow-2xl">
        <button type="button"
                id="btn-cerrar-modal-qr"
                class="absolute right-5 top-5 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-red-50 hover:text-red-600">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div id="contenido-qr-pendiente">
            <div class="mb-5 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-emerald-100 text-2xl text-emerald-700">
                    <i class="fa-solid fa-qrcode"></i>
                </div>

                <h2 class="mt-4 text-2xl font-black text-slate-900">QR PagoFácil generado</h2>
                <p class="mt-2 text-sm text-slate-500">
                    Escanea el código QR para completar el pago.
                </p>
            </div>

            <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-5 text-center">
                <img id="imagen-qr-pagofacil"
                     src=""
                     alt="QR PagoFácil"
                     class="mx-auto h-72 w-72 rounded-3xl border border-white bg-white p-4 shadow-sm">

                <p class="mt-4 text-xs font-bold uppercase text-emerald-600">
                    Payment Number
                </p>

                <p id="payment-number-qr"
                   class="mt-1 break-all text-sm font-black text-slate-800"></p>
            </div>

            <div class="mt-5 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 text-sm text-yellow-800">
                <p class="font-bold">Estado actual: <span id="estado-qr-texto">Pendiente</span></p>
                <p class="mt-1">
                    El sistema verificará automáticamente si el callback ya confirmó el pago.
                </p>
            </div>

            <div class="mt-5 flex flex-wrap justify-center gap-3">
                <button type="button"
                        id="btn-verificar-callback"
                        class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                    <i class="fa-solid fa-bolt text-xs"></i>
                    Verificar callback
                </button>

                <button type="button"
                        id="btn-verificar-manual"
                        class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    Verificar manualmente
                </button>

                <a href="#"
                   id="btn-ver-detalle-pago"
                   class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                    <i class="fa-solid fa-eye text-xs"></i>
                    Ver detalle
                </a>
            </div>
        </div>

        <div id="contenido-qr-exitoso" class="hidden py-8 text-center">
            <div class="mx-auto flex h-24 w-24 animate-bounce items-center justify-center rounded-full bg-green-100 text-5xl text-green-700">
                <i class="fa-solid fa-check"></i>
            </div>

            <h2 class="mt-6 text-3xl font-black text-green-700">
                Pago realizado exitosamente
            </h2>

            <p class="mt-3 text-sm text-slate-500">
                El pago fue confirmado y guardado automáticamente en el sistema.
            </p>

            <a href="#"
               id="btn-ver-detalle-pago-exitoso"
               class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-green-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-green-600/20 transition hover:-translate-y-0.5 hover:bg-green-700">
                <i class="fa-solid fa-eye text-xs"></i>
                Ver detalle del pago
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('form-pago-contado');

        const metodoPago = document.getElementById('metodo-pago');
        const estadoPago = document.getElementById('estado-pago');
        const btnGuardarPago = document.getElementById('btn-guardar-pago');
        const btnGenerarQr = document.getElementById('btn-generar-qr');
        const avisoQrPrueba = document.getElementById('aviso-qr-prueba');

        const modalQr = document.getElementById('modal-qr-pagofacil');
        const btnCerrarModalQr = document.getElementById('btn-cerrar-modal-qr');

        const imagenQr = document.getElementById('imagen-qr-pagofacil');
        const paymentNumberQr = document.getElementById('payment-number-qr');
        const estadoQrTexto = document.getElementById('estado-qr-texto');

        const btnVerificarCallback = document.getElementById('btn-verificar-callback');
        const btnVerificarManual = document.getElementById('btn-verificar-manual');
        const btnVerDetallePago = document.getElementById('btn-ver-detalle-pago');
        const btnVerDetallePagoExitoso = document.getElementById('btn-ver-detalle-pago-exitoso');

        const contenidoPendiente = document.getElementById('contenido-qr-pendiente');
        const contenidoExitoso = document.getElementById('contenido-qr-exitoso');

        let pagoActual = null;
        let intervaloVerificacion = null;
        let accionSeleccionada = 'guardar';

        if (!form || !metodoPago || !estadoPago || !btnGuardarPago || !btnGenerarQr) {
            return;
        }

        function actualizarBotonesPago() {
            if (metodoPago.value === 'QR') {
                btnGuardarPago.style.display = 'none';
                btnGenerarQr.style.display = 'inline-flex';

                estadoPago.value = 'Pendiente';

                if (avisoQrPrueba) {
                    avisoQrPrueba.style.display = 'block';
                }
            } else {
                btnGuardarPago.style.display = 'inline-flex';
                btnGenerarQr.style.display = 'none';

                if (avisoQrPrueba) {
                    avisoQrPrueba.style.display = 'none';
                }
            }
        }

        metodoPago.addEventListener('change', actualizarBotonesPago);

        btnGuardarPago.addEventListener('click', function () {
            accionSeleccionada = 'guardar';
        });

        btnGenerarQr.addEventListener('click', function () {
            accionSeleccionada = 'generar_qr';
        });

        form.addEventListener('submit', function (event) {
            if (metodoPago.value !== 'QR' || accionSeleccionada !== 'generar_qr') {
                return;
            }

            event.preventDefault();

            enviarFormularioQr(form);
        });

        function enviarFormularioQr(formulario) {
            const formData = new FormData(formulario);

            formData.set('accion', 'generar_qr');

            btnGenerarQr.disabled = true;
            btnGenerarQr.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Generando QR...';

            fetch(formulario.action, {
                method: formulario.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(async response => {
                    const data = await response.json();

                    if (!response.ok || !data.ok) {
                        throw new Error(data.message || 'No se pudo generar el QR.');
                    }

                    abrirModalQr(data.pago);
                })
                .catch(error => {
                    alert(error.message);
                })
                .finally(() => {
                    btnGenerarQr.disabled = false;
                    btnGenerarQr.innerHTML = '<i class="fa-solid fa-qrcode text-xs"></i> Generar QR PagoFácil';
                });
        }

        function abrirModalQr(pago) {
            pagoActual = pago;

            imagenQr.src = pago.qr_url;
            paymentNumberQr.textContent = pago.payment_number;
            estadoQrTexto.textContent = pago.estado;

            btnVerDetallePago.href = pago.show_url;
            btnVerDetallePagoExitoso.href = pago.show_url;

            contenidoPendiente.classList.remove('hidden');
            contenidoExitoso.classList.add('hidden');

            modalQr.classList.remove('hidden');
            modalQr.classList.add('flex');
            document.body.classList.add('overflow-hidden');

            iniciarVerificacionAutomatica();
        }

        function cerrarModalQr() {
            modalQr.classList.add('hidden');
            modalQr.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');

            detenerVerificacionAutomatica();
        }

        function iniciarVerificacionAutomatica() {
            detenerVerificacionAutomatica();

            intervaloVerificacion = setInterval(() => {
                verificarEstadoPorCallback(false);
            }, 5000);
        }

        function detenerVerificacionAutomatica() {
            if (intervaloVerificacion) {
                clearInterval(intervaloVerificacion);
                intervaloVerificacion = null;
            }
        }

        function verificarEstadoPorCallback(mostrarCarga = true) {
            if (!pagoActual || !pagoActual.estado_url) {
                return;
            }

            if (mostrarCarga && btnVerificarCallback) {
                btnVerificarCallback.disabled = true;
                btnVerificarCallback.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Verificando...';
            }

            fetch(pagoActual.estado_url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.ok) {
                        return;
                    }

                    estadoQrTexto.textContent = data.pago.estado;

                    if (data.pago.estado === 'Confirmado') {
                        mostrarPagoExitoso();
                    }
                })
                .finally(() => {
                    if (mostrarCarga && btnVerificarCallback) {
                        btnVerificarCallback.disabled = false;
                        btnVerificarCallback.innerHTML = '<i class="fa-solid fa-bolt text-xs"></i> Verificar callback';
                    }
                });
        }

        function verificarManualPagoFacil() {
            if (!pagoActual || !pagoActual.consultar_url) {
                return;
            }

            btnVerificarManual.disabled = true;
            btnVerificarManual.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Consultando...';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');

            fetch(pagoActual.consultar_url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.ok) {
                        alert(data.message || 'No se pudo consultar el pago.');
                        return;
                    }

                    estadoQrTexto.textContent = data.pago.estado;

                    if (data.pago.estado === 'Confirmado') {
                        mostrarPagoExitoso();
                    } else {
                        alert('El pago todavía no está confirmado.');
                    }
                })
                .catch(() => {
                    alert('No se pudo consultar manualmente el pago.');
                })
                .finally(() => {
                    btnVerificarManual.disabled = false;
                    btnVerificarManual.innerHTML = '<i class="fa-solid fa-rotate text-xs"></i> Verificar manualmente';
                });
        }

        function mostrarPagoExitoso() {
            detenerVerificacionAutomatica();

            contenidoPendiente.classList.add('hidden');
            contenidoExitoso.classList.remove('hidden');
        }

        if (btnCerrarModalQr) {
            btnCerrarModalQr.addEventListener('click', cerrarModalQr);
        }

        if (btnVerificarCallback) {
            btnVerificarCallback.addEventListener('click', function () {
                verificarEstadoPorCallback();
            });
        }

        if (btnVerificarManual) {
            btnVerificarManual.addEventListener('click', function () {
                verificarManualPagoFacil();
            });
        }

        actualizarBotonesPago();
    });
</script>