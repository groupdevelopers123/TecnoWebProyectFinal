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

<div class="mb-6 grid gap-5 md:grid-cols-2">
    <div class="rounded-2xl bg-slate-50 p-4">
        <p class="text-xs font-bold uppercase text-slate-400">Alumno</p>
        <p class="mt-1 font-bold text-slate-800">
            {{ $cuota->credito->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
        </p>
    </div>

    <div class="rounded-2xl bg-slate-50 p-4">
        <p class="text-xs font-bold uppercase text-slate-400">Concepto</p>
        <p class="mt-1 font-bold text-slate-800">
            {{ $cuota->credito->conceptoPago->nombre ?? 'Sin concepto' }}
        </p>
    </div>

    <div class="rounded-2xl bg-blue-50 p-4">
        <p class="text-xs font-bold uppercase text-blue-500">Monto de cuota</p>
        <p class="mt-1 font-bold text-blue-700">
            Bs {{ number_format($cuota->monto, 2) }}
        </p>
    </div>

    <div class="rounded-2xl bg-amber-50 p-4">
        <p class="text-xs font-bold uppercase text-amber-500">Fecha de vencimiento</p>
        <p class="mt-1 font-bold text-amber-700">
            {{ $cuota->fecha_vencimiento?->format('d/m/Y') ?? '-' }}
        </p>
    </div>
</div>

<div class="grid gap-6 md:grid-cols-2">

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Método de pago</label>
        <select name="metodo_pago"
                id="metodo-pago-cuota"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            @foreach (['Efectivo', 'Transferencia', 'QR'] as $metodo)
                <option value="{{ $metodo }}"
                    @selected(old('metodo_pago', $cuota->metodo_pago ?? 'Efectivo') === $metodo)>
                    {{ $metodo }}
                </option>
            @endforeach
        </select>

        <p id="aviso-qr-cuota"
           style="display: none;"
           class="mt-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
            Para pruebas, el QR se generará con el monto de prueba configurado.
        </p>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Estado de cuota</label>
        <select name="estado_cuota"
                id="estado-cuota"
                required
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            @foreach (['pendiente', 'pagado', 'anulado', 'fallido'] as $estado)
                <option value="{{ $estado }}"
                    @selected(old('estado_cuota', $cuota->estado_cuota ?? 'pendiente') === $estado)>
                    {{ ucfirst($estado) }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Fecha de pago</label>
        <input type="date"
               name="fecha_pago"
               value="{{ old('fecha_pago', isset($cuota) && $cuota->fecha_pago ? $cuota->fecha_pago->format('Y-m-d') : now()->format('Y-m-d')) }}"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Correo solicitante</label>
        <input type="email"
               name="correo_solicitante"
               value="{{ old('correo_solicitante', $cuota->correo_solicitante ?? '') }}"
               placeholder="cliente@correo.com"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">Código transacción</label>
        <input type="text"
               name="codigo_transaccion"
               value="{{ old('codigo_transaccion', $cuota->codigo_transaccion ?? '') }}"
               placeholder="Opcional"
               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
    </div>

    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-bold text-slate-700">Observación</label>
        <textarea name="observacion"
                  rows="4"
                  placeholder="Observación del pago"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('observacion', $cuota->observacion ?? '') }}</textarea>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2">

        <button type="submit"
                name="accion"
                value="guardar"
                id="btn-guardar-cuota"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            Guardar pago
        </button>

        <button type="submit"
                name="accion"
                value="generar_qr"
                id="btn-generar-qr-cuota"
                style="display: none;"
                class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:-translate-y-0.5 hover:bg-emerald-700">
            <i class="fa-solid fa-qrcode text-xs"></i>
            Generar QR PagoFácil
        </button>

        <a href="{{ route('admin.pago-cuotas.index') }}"
           class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Cancelar
        </a>
    </div>
</div>

<div id="modal-qr-cuota"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4 backdrop-blur-sm">

    <div class="relative max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-[2rem] bg-white p-6 shadow-2xl">
        <button type="button"
                id="btn-cerrar-modal-cuota"
                class="absolute right-5 top-5 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-red-50 hover:text-red-600">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div id="contenido-cuota-pendiente">
            <div class="mb-5 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-emerald-100 text-2xl text-emerald-700">
                    <i class="fa-solid fa-qrcode"></i>
                </div>

                <h2 class="mt-4 text-2xl font-black text-slate-900">QR de cuota generado</h2>
                <p class="mt-2 text-sm text-slate-500">
                    Escanea el código QR para completar el pago de la cuota.
                </p>
            </div>

            <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-5 text-center">
                <img id="imagen-qr-cuota"
                     src=""
                     alt="QR PagoFácil"
                     class="mx-auto h-72 w-72 rounded-3xl border border-white bg-white p-4 shadow-sm">

                <p class="mt-4 text-xs font-bold uppercase text-emerald-600">Payment Number</p>
                <p id="payment-number-cuota" class="mt-1 break-all text-sm font-black text-slate-800"></p>
            </div>

            <div class="mt-5 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 text-sm text-yellow-800">
                <p class="font-bold">Estado actual: <span id="estado-cuota-texto">Pendiente</span></p>
                <p class="mt-1">
                    El sistema verificará automáticamente si el callback confirmó el pago.
                </p>
            </div>

            <div class="mt-5 flex flex-wrap justify-center gap-3">
                <button type="button"
                        id="btn-verificar-callback-cuota"
                        class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                    <i class="fa-solid fa-bolt text-xs"></i>
                    Verificar callback
                </button>

                <button type="button"
                        id="btn-verificar-manual-cuota"
                        class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    Verificar manualmente
                </button>

                <a href="#"
                   id="btn-ver-detalle-cuota"
                   class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                    <i class="fa-solid fa-eye text-xs"></i>
                    Ver detalle
                </a>
            </div>
        </div>

        <div id="contenido-cuota-exitoso" class="hidden py-8 text-center">
            <div class="mx-auto flex h-24 w-24 animate-bounce items-center justify-center rounded-full bg-green-100 text-5xl text-green-700">
                <i class="fa-solid fa-check"></i>
            </div>

            <h2 class="mt-6 text-3xl font-black text-green-700">
                Pago realizado exitosamente
            </h2>

            <p class="mt-3 text-sm text-slate-500">
                La cuota fue confirmada y guardada automáticamente.
            </p>

            <a href="#"
               id="btn-ver-detalle-cuota-exitoso"
               class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-green-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-green-600/20 transition hover:-translate-y-0.5 hover:bg-green-700">
                <i class="fa-solid fa-eye text-xs"></i>
                Ver detalle de la cuota
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('form-pago-cuota');
        const metodoPago = document.getElementById('metodo-pago-cuota');
        const estadoCuota = document.getElementById('estado-cuota');
        const btnGuardar = document.getElementById('btn-guardar-cuota');
        const btnQr = document.getElementById('btn-generar-qr-cuota');
        const avisoQr = document.getElementById('aviso-qr-cuota');

        const modalQr = document.getElementById('modal-qr-cuota');
        const btnCerrar = document.getElementById('btn-cerrar-modal-cuota');

        const imagenQr = document.getElementById('imagen-qr-cuota');
        const paymentNumber = document.getElementById('payment-number-cuota');
        const estadoTexto = document.getElementById('estado-cuota-texto');

        const btnCallback = document.getElementById('btn-verificar-callback-cuota');
        const btnManual = document.getElementById('btn-verificar-manual-cuota');
        const btnDetalle = document.getElementById('btn-ver-detalle-cuota');
        const btnDetalleExitoso = document.getElementById('btn-ver-detalle-cuota-exitoso');

        const contenidoPendiente = document.getElementById('contenido-cuota-pendiente');
        const contenidoExitoso = document.getElementById('contenido-cuota-exitoso');

        let cuotaActual = null;
        let intervalo = null;
        let accionSeleccionada = 'guardar';

        if (!form || !metodoPago || !estadoCuota || !btnGuardar || !btnQr) {
            return;
        }

        function actualizarBotones() {
            if (metodoPago.value === 'QR') {
                btnGuardar.style.display = 'none';
                btnQr.style.display = 'inline-flex';
                estadoCuota.value = 'pendiente';

                if (avisoQr) {
                    avisoQr.style.display = 'block';
                }
            } else {
                btnGuardar.style.display = 'inline-flex';
                btnQr.style.display = 'none';

                if (avisoQr) {
                    avisoQr.style.display = 'none';
                }
            }
        }

        metodoPago.addEventListener('change', actualizarBotones);

        btnGuardar.addEventListener('click', function () {
            accionSeleccionada = 'guardar';
        });

        btnQr.addEventListener('click', function () {
            accionSeleccionada = 'generar_qr';
        });

        form.addEventListener('submit', function (event) {
            if (metodoPago.value !== 'QR' || accionSeleccionada !== 'generar_qr') {
                return;
            }

            event.preventDefault();

            generarQrCuota();
        });

        function generarQrCuota() {
            const formData = new FormData(form);
            formData.set('accion', 'generar_qr');

            btnQr.disabled = true;
            btnQr.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Generando QR...';

            fetch(form.action, {
                method: 'POST',
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

                    abrirModal(data.cuota);
                })
                .catch(error => {
                    alert(error.message);
                })
                .finally(() => {
                    btnQr.disabled = false;
                    btnQr.innerHTML = '<i class="fa-solid fa-qrcode text-xs"></i> Generar QR PagoFácil';
                });
        }

        function abrirModal(cuota) {
            cuotaActual = cuota;

            imagenQr.src = cuota.qr_url;
            paymentNumber.textContent = cuota.payment_number;
            estadoTexto.textContent = cuota.estado;

            btnDetalle.href = cuota.show_url;
            btnDetalleExitoso.href = cuota.show_url;

            contenidoPendiente.classList.remove('hidden');
            contenidoExitoso.classList.add('hidden');

            modalQr.classList.remove('hidden');
            modalQr.classList.add('flex');
            document.body.classList.add('overflow-hidden');

            iniciarVerificacion();
        }

        function cerrarModal() {
            modalQr.classList.add('hidden');
            modalQr.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');

            detenerVerificacion();
        }

        function iniciarVerificacion() {
            detenerVerificacion();

            intervalo = setInterval(() => {
                verificarCallback(false);
            }, 5000);
        }

        function detenerVerificacion() {
            if (intervalo) {
                clearInterval(intervalo);
                intervalo = null;
            }
        }

        function verificarCallback(mostrarCarga = true) {
            if (!cuotaActual || !cuotaActual.estado_url) {
                return;
            }

            if (mostrarCarga) {
                btnCallback.disabled = true;
                btnCallback.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Verificando...';
            }

            fetch(cuotaActual.estado_url, {
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

                    estadoTexto.textContent = data.cuota.estado;

                    if (data.cuota.estado === 'pagado') {
                        mostrarExito();
                    }
                })
                .finally(() => {
                    if (mostrarCarga) {
                        btnCallback.disabled = false;
                        btnCallback.innerHTML = '<i class="fa-solid fa-bolt text-xs"></i> Verificar callback';
                    }
                });
        }

        function verificarManual() {
            if (!cuotaActual || !cuotaActual.consultar_url) {
                return;
            }

            btnManual.disabled = true;
            btnManual.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Consultando...';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');

            fetch(cuotaActual.consultar_url, {
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
                        alert(data.message || 'No se pudo consultar la cuota.');
                        return;
                    }

                    estadoTexto.textContent = data.cuota.estado;

                    if (data.cuota.estado === 'pagado') {
                        mostrarExito();
                    } else {
                        alert('La cuota todavía no está confirmada.');
                    }
                })
                .finally(() => {
                    btnManual.disabled = false;
                    btnManual.innerHTML = '<i class="fa-solid fa-rotate text-xs"></i> Verificar manualmente';
                });
        }

        function mostrarExito() {
            detenerVerificacion();

            contenidoPendiente.classList.add('hidden');
            contenidoExitoso.classList.remove('hidden');
        }

        btnCerrar.addEventListener('click', cerrarModal);
        btnCallback.addEventListener('click', function () {
            verificarCallback();
        });
        btnManual.addEventListener('click', verificarManual);

        actualizarBotones();
    });
</script>