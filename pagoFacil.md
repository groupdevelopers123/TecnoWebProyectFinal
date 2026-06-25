# Guía general para implementar pagos con QR de PagoFácil

## 1. Descripción general del flujo

La integración con PagoFácil mediante QR consiste en permitir que un sistema genere una solicitud de pago, reciba un código QR para que el cliente pueda pagar desde su banca móvil y luego procese automáticamente la confirmación del pago mediante un callback.

El flujo general es el siguiente:

```text
1. El usuario solicita realizar un pago.
2. El sistema registra el pago como PENDIENTE.
3. El sistema genera un identificador único para la transacción.
4. El sistema solicita a PagoFácil la generación del QR.
5. PagoFácil devuelve el QR y datos de la transacción.
6. El sistema guarda la transacción en una tabla propia.
7. El usuario escanea el QR y paga desde su banca móvil.
8. PagoFácil envía un callback al sistema.
9. El sistema verifica el callback recibido.
10. El sistema actualiza el estado del pago a PAGADO.
11. El sistema actualiza las tablas relacionadas.
12. El sistema notifica al usuario que el pago fue registrado correctamente.
```

Este flujo puede aplicarse en Java, Laravel, Node.js, Python, C#, PHP u otro lenguaje, siempre que el sistema pueda realizar peticiones HTTP, guardar datos en base de datos y exponer un endpoint público para recibir callbacks.

---

## 2. Requisitos previos

Antes de comenzar, se necesita:

```text
1. Credenciales de PagoFácil.
2. URL base de la API de PagoFácil.
3. Endpoint público para recibir callbacks.
4. Base de datos para registrar pagos y transacciones.
5. Módulo para enviar respuestas al usuario.
6. Módulo para generar solicitudes HTTP.
```

En ambiente local, si el sistema no está desplegado en internet, se puede usar una herramienta como Ngrok para exponer el callback:

```bash
ngrok http 8080
```

Ejemplo de callback público:

```text
https://mi-url-ngrok.ngrok-free.app/pagofacil/callback
```

---

## 3. Configuración general

Se recomienda centralizar los datos de configuración en un archivo o clase.

Ejemplo genérico:

```text
PAGOFACIL_BASE_URL=https://api.pagofacil.com.bo
PAGOFACIL_USERNAME=tu_usuario
PAGOFACIL_PASSWORD=tu_password
PAGOFACIL_ENTITY_ID=tu_entity_id
PAGOFACIL_CALLBACK_URL=https://tu-dominio.com/pagofacil/callback
PAGOFACIL_CURRENCY=BOB
```

Ejemplo en pseudocódigo:

```pseudo
CONFIG = {
    BASE_URL: "https://api.pagofacil.com.bo",
    USERNAME: "usuario",
    PASSWORD: "password",
    CALLBACK_URL: "https://tu-dominio.com/pagofacil/callback",
    CURRENCY: "BOB"
}
```

No es recomendable escribir credenciales directamente en el código final. Lo mejor es usar variables de entorno o un archivo de configuración seguro.

---

## 4. Diseño de base de datos

Para que el flujo funcione correctamente, se recomienda separar la tabla del pago real de la tabla de transacción de PagoFácil.

Por ejemplo, si existe un pago al contado:

```sql
CREATE TABLE pago_contado (
    id SERIAL PRIMARY KEY,
    id_venta INTEGER NOT NULL,
    id_metodo_pago INTEGER NOT NULL,
    fecha DATE,
    monto_pago NUMERIC(12,2),
    interes_mora_cobrado NUMERIC(12,2),
    recargo_extra NUMERIC(12,2),
    observaciones TEXT,
    estado VARCHAR(30) DEFAULT 'PENDIENTE'
);
```

Para pagos por cuota:

```sql
CREATE TABLE pago_cuota (
    id SERIAL PRIMARY KEY,
    id_credito INTEGER NOT NULL,
    id_metodo_pago INTEGER NOT NULL,
    numero_cuota INTEGER,
    interes_cuota NUMERIC(12,2),
    fecha_vencimiento DATE,
    fecha_pago DATE,
    monto_pagado NUMERIC(12,2),
    dias_mora INTEGER,
    monto_pendiente NUMERIC(12,2),
    estado VARCHAR(30) DEFAULT 'PENDIENTE',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

La tabla más importante para la integración es la tabla de transacciones de PagoFácil:

```sql
CREATE TABLE pago_facil_transaccion (
    id SERIAL PRIMARY KEY,
    id_pago_contado INTEGER,
    id_pago_cuota INTEGER,
    payment_number VARCHAR(100) NOT NULL,
    pagofacil_transaction_id VARCHAR(150),
    payment_method_transaction_id VARCHAR(150),
    payment_method_id INTEGER,
    qr_base64 TEXT,
    checkout_url TEXT,
    deep_link TEXT,
    qr_content_url TEXT,
    universal_url TEXT,
    expiration_date TIMESTAMP,
    estado VARCHAR(30) DEFAULT 'PENDIENTE',
    correo_solicitante VARCHAR(150),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

El campo más importante es:

```text
payment_number
```

Este valor debe coincidir exactamente con el `PedidoID` que PagoFácil devolverá en el callback.

Ejemplo:

```text
PC-4-VEN-1
```

Donde:

```text
PC = Pago contado
4 = ID del pago contado
VEN = Venta
1 = ID de la venta
```

---

## 5. Crear el pago como pendiente

Cuando el usuario solicita pagar, el sistema primero debe registrar el pago en la base de datos con estado `PENDIENTE`.

Ejemplo de pseudocódigo:

```pseudo
function crearPagoContado(datosPago):
    pago.id_venta = datosPago.id_venta
    pago.id_metodo_pago = datosPago.id_metodo_pago
    pago.fecha = datosPago.fecha
    pago.monto_pago = datosPago.monto
    pago.observaciones = datosPago.observaciones
    pago.estado = "PENDIENTE"

    idPago = insertarEnTablaPagoContado(pago)

    return idPago
```

Ejemplo SQL:

```sql
INSERT INTO pago_contado (
    id_venta,
    id_metodo_pago,
    fecha,
    monto_pago,
    interes_mora_cobrado,
    recargo_extra,
    observaciones,
    estado
)
VALUES (
    1,
    1,
    '2026-06-10',
    150.00,
    0,
    0,
    'Pago al contado online',
    'PENDIENTE'
);
```

---

## 6. Generar un identificador único para PagoFácil

Después de crear el pago pendiente, se debe generar un número único de pago.

Ejemplo para pago contado:

```pseudo
paymentNumber = "PC-" + idPagoContado + "-VEN-" + idVenta
```

Ejemplo real:

```text
PC-4-VEN-1
```

Ejemplo para pago de cuota:

```pseudo
paymentNumber = "PQ-" + idPagoCuota + "-CRE-" + idCredito
```

Ejemplo real:

```text
PQ-5-CRE-2
```

Este identificador debe guardarse en la tabla `pago_facil_transaccion` y también debe enviarse a PagoFácil. Luego, cuando PagoFácil mande el callback, ese mismo valor llegará como `PedidoID`.

---

## 7. Obtener token de autenticación

Normalmente, antes de consumir los servicios de PagoFácil, se necesita obtener un token de acceso.

Ejemplo genérico de solicitud:

```http
POST /auth/login
Content-Type: application/json

{
    "username": "tu_usuario",
    "password": "tu_password"
}
```

Ejemplo de respuesta esperada:

```json
{
    "access_token": "TOKEN_GENERADO",
    "expires_in": 3600
}
```

Pseudocódigo:

```pseudo
function obtenerAccessToken():
    if tokenExiste and tokenNoExpiro:
        return tokenActual

    response = POST BASE_URL + "/auth/login" with credentials

    tokenActual = response.access_token
    fechaExpiracion = ahora + response.expires_in

    return tokenActual
```

Es importante reutilizar el token mientras siga vigente para no pedir uno nuevo en cada operación.

---

## 8. Consultar métodos de pago habilitados

Antes de generar el QR, se puede consultar qué métodos de pago están habilitados.

Ejemplo genérico:

```http
GET /list-enabled-services
Authorization: Bearer TOKEN
```

Respuesta esperada:

```json
{
    "error": 0,
    "status": 2006,
    "message": "Servicios listados exitosamente.",
    "values": [
        {
            "paymentMethodId": 34,
            "paymentMethodName": "QR ATC",
            "currencyName": "BOB",
            "maxAmountPerDay": 70000,
            "maxAmountPerTransaction": 70000,
            "minAmountPerTransaction": 0.01
        }
    ]
}
```

De esta respuesta se debe obtener el `paymentMethodId`.

Ejemplo:

```text
paymentMethodId = 34
```

Ese ID representa el método de pago QR habilitado por PagoFácil.

---

## 9. Solicitar generación de QR

Una vez que se tiene el token, el método de pago y el número de pago, se solicita el QR.

Ejemplo genérico:

```http
POST /generate-qr
Authorization: Bearer TOKEN
Content-Type: application/json

{
    "paymentNumber": "PC-4-VEN-1",
    "clientName": "Juan Perez",
    "documentId": "7894561",
    "phoneNumber": "70000001",
    "email": "cliente@gmail.com",
    "clientCode": "CLI-1",
    "product": "Venta Tienda ELENA",
    "amount": 150.00,
    "currency": "BOB",
    "paymentMethodId": 34,
    "callbackUrl": "https://tu-dominio.com/pagofacil/callback"
}
```

Pseudocódigo:

```pseudo
function generarQrPagoFacil(paymentNumber, datosCliente, monto):
    token = obtenerAccessToken()
    paymentMethodId = obtenerMetodoQrHabilitado()

    request = {
        paymentNumber: paymentNumber,
        clientName: datosCliente.nombre,
        documentId: datosCliente.documento,
        phoneNumber: datosCliente.telefono,
        email: datosCliente.email,
        clientCode: datosCliente.codigo,
        product: "Pago de venta",
        amount: monto,
        currency: "BOB",
        paymentMethodId: paymentMethodId,
        callbackUrl: CONFIG.CALLBACK_URL
    }

    response = POST CONFIG.BASE_URL + "/generate-qr" with token and request

    return response
```

La respuesta puede contener:

```text
qr_base64
checkout_url
deep_link
qr_content_url
universal_url
expiration_date
pagofacil_transaction_id
payment_method_transaction_id
```

Estos datos deben guardarse.

---

## 10. Guardar la transacción de PagoFácil

Después de generar el QR, se registra la transacción en la tabla `pago_facil_transaccion`.

Ejemplo para pago contado:

```sql
INSERT INTO pago_facil_transaccion (
    id_pago_contado,
    id_pago_cuota,
    payment_number,
    pagofacil_transaction_id,
    payment_method_transaction_id,
    payment_method_id,
    qr_base64,
    checkout_url,
    deep_link,
    qr_content_url,
    universal_url,
    expiration_date,
    estado,
    correo_solicitante,
    fecha_actualizacion
)
VALUES (
    4,
    NULL,
    'PC-4-VEN-1',
    'PGF-123456',
    'ATC-999999',
    34,
    'BASE64_DEL_QR',
    'https://checkout...',
    'bankapp://...',
    'https://qr-content...',
    'https://universal...',
    '2026-06-10 18:00:00',
    'PENDIENTE',
    'cliente@gmail.com',
    CURRENT_TIMESTAMP
);
```

Pseudocódigo:

```pseudo
function guardarTransaccionPagoFacil(data):
    transaccion.id_pago_contado = data.idPagoContado
    transaccion.id_pago_cuota = data.idPagoCuota
    transaccion.payment_number = data.paymentNumber
    transaccion.pagofacil_transaction_id = data.pagofacilTransactionId
    transaccion.payment_method_transaction_id = data.paymentMethodTransactionId
    transaccion.payment_method_id = data.paymentMethodId
    transaccion.qr_base64 = data.qrBase64
    transaccion.checkout_url = data.checkoutUrl
    transaccion.deep_link = data.deepLink
    transaccion.qr_content_url = data.qrContentUrl
    transaccion.universal_url = data.universalUrl
    transaccion.expiration_date = data.expirationDate
    transaccion.estado = "PENDIENTE"
    transaccion.correo_solicitante = data.correoSolicitante

    insert(transaccion)
```

---

## 11. Mostrar o enviar el QR al usuario

El sistema debe mostrar o enviar al usuario el QR generado.

En un sistema web, se puede mostrar en pantalla:

```html
<img src="data:image/png;base64,BASE64_DEL_QR" alt="QR de pago" />
```

En un sistema por correo, se puede enviar una tabla o mensaje HTML:

```html
<h2>Solicitud de pago generada</h2>
<p>Escanee el siguiente código QR para completar el pago.</p>
<img
    src="data:image/png;base64,BASE64_DEL_QR"
    alt="QR de pago"
    style="width:250px;height:250px;"
/>
<p>Monto: 150.00 Bs</p>
<p>Pedido: PC-4-VEN-1</p>
```

Si el cliente de correo bloquea imágenes base64, se recomienda enviar la imagen como adjunto embebido o usar una URL pública segura.

---

## 12. Crear el endpoint de callback

El callback es el endpoint que PagoFácil llamará cuando el cliente pague.

Ejemplo de ruta:

```text
POST /pagofacil/callback
```

Ejemplo de callback recibido:

```json
{
    "PedidoID": "PC-4-VEN-1",
    "Fecha": "2026/06/10",
    "Hora": "17:00:10",
    "Estado": 2,
    "MetodoPago": 34
}
```

Pseudocódigo del endpoint:

```pseudo
POST /pagofacil/callback:
    json = leerBodyRequest()
    respuesta = pagoFacilCallbackService.procesarCallback(json)
    responderHTTP(200, respuesta)
```

Respuesta esperada:

```json
{
    "error": 0,
    "status": 1,
    "message": "Callback procesado correctamente",
    "values": true
}
```

Es importante responder `200 OK` para que PagoFácil sepa que el callback fue recibido.

---

## 13. Interpretar el estado recibido

PagoFácil puede enviar el estado como número. En el caso trabajado, el estado `2` representa pago confirmado.

Pseudocódigo:

```pseudo
function normalizarEstadoPagoFacil(estado):
    if estado == 2:
        return "PAGADO"

    if estado == 1:
        return "PENDIENTE"

    if estado == 3:
        return "ANULADO"

    return "PENDIENTE"
```

Ejemplo:

```text
Estado: 2 -> PAGADO
```

---

## 14. Procesar el callback

Cuando llega el callback, el sistema debe buscar la transacción por `PedidoID`.

El `PedidoID` recibido debe coincidir con el `payment_number` guardado en la base de datos.

Ejemplo:

```sql
SELECT *
FROM pago_facil_transaccion
WHERE payment_number = 'PC-4-VEN-1'
ORDER BY id DESC
LIMIT 1;
```

Pseudocódigo:

```pseudo
function procesarCallback(json):
    pedidoId = json.PedidoID
    estadoPagoFacil = json.Estado
    metodoPago = json.MetodoPago

    if pedidoId is empty:
        return respuestaOk("Callback recibido sin PedidoID")

    transaccion = buscarTransaccionPorPaymentNumber(pedidoId)

    if transaccion is null:
        return respuestaOk("Transaccion no encontrada")

    estadoNormalizado = normalizarEstadoPagoFacil(estadoPagoFacil)

    actualizarEstadoTransaccion(pedidoId, estadoNormalizado)

    if estadoNormalizado == "PAGADO":
        if transaccion.id_pago_contado is not null:
            procesarPagoContado(transaccion)

        if transaccion.id_pago_cuota is not null:
            procesarPagoCuota(transaccion)

    return respuestaOk("Callback procesado correctamente")
```

---

## 15. Procesar pago contado confirmado

Si la transacción corresponde a un pago contado, se actualizan:

```text
1. pago_facil_transaccion.estado = PAGADO
2. pago_contado.estado = PAGADO
3. venta.estado = PAGADA
4. Se envía notificación al usuario
```

Pseudocódigo:

```pseudo
function procesarPagoContado(transaccion):
    idPagoContado = transaccion.id_pago_contado

    pagoContado = buscarPagoContado(idPagoContado)
    idVenta = pagoContado.id_venta

    actualizarPagoContado(idPagoContado, "PAGADO")
    actualizarVenta(idVenta, "PAGADA")

    enviarCorreoPagoContadoExitoso(transaccion.correo_solicitante)
```

SQL:

```sql
UPDATE pago_facil_transaccion
SET estado = 'PAGADO',
    fecha_actualizacion = CURRENT_TIMESTAMP
WHERE payment_number = 'PC-4-VEN-1';
```

```sql
UPDATE pago_contado
SET estado = 'PAGADO'
WHERE id = 4;
```

```sql
UPDATE venta
SET estado = 'PAGADA'
WHERE id = 1;
```

---

## 16. Procesar pago de cuota confirmado

Si la transacción corresponde a un pago de cuota, se actualizan:

```text
1. pago_facil_transaccion.estado = PAGADO
2. pago_cuota.estado = PAGADO
3. credito.monto_pagado aumenta
4. credito.monto_pendiente disminuye
5. Si el crédito queda sin saldo pendiente, credito.estado = PAGADO
6. Se envía notificación al usuario
```

Pseudocódigo:

```pseudo
function procesarPagoCuota(transaccion):
    idPagoCuota = transaccion.id_pago_cuota

    cuota = buscarPagoCuota(idPagoCuota)

    if cuota is null:
        return

    actualizarPagoCuota(idPagoCuota, "PAGADO")
    registrarPagoEnCredito(cuota.id_credito, cuota.monto_pagado)

    enviarCorreoPagoCuotaExitoso(transaccion.correo_solicitante)
```

SQL:

```sql
UPDATE pago_cuota
SET estado = 'PAGADO',
    fecha_pago = CURRENT_DATE,
    fecha_actualizacion = CURRENT_TIMESTAMP
WHERE id = 5;
```

```sql
UPDATE credito
SET monto_pagado = monto_pagado + 120.00,
    monto_pendiente = monto_pendiente - 120.00,
    fecha_actualizacion = CURRENT_TIMESTAMP
WHERE id = 2;
```

Si el crédito queda totalmente pagado:

```sql
UPDATE credito
SET estado = 'PAGADO'
WHERE id = 2
AND monto_pendiente <= 0;
```

---

## 17. Enviar correo o notificación de confirmación

Después de confirmar el pago, se recomienda notificar al usuario.

Ejemplo de HTML:

```html
<html>
    <body style="font-family: Arial; padding:20px;">
        <center>
            <h2 style="color:#4A148C;">Pago realizado exitosamente</h2>
            <p>
                La transacción QR fue confirmada por PagoFácil y registrada
                correctamente.
            </p>
        </center>

        <table style="border-collapse: collapse; width: 95%; margin: auto;">
            <thead>
                <tr style="background-color:#6A1B9A; color:white;">
                    <th>ID</th>
                    <th>MONTO</th>
                    <th>ESTADO</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color:#F3E5F5; color:#4A148C;">
                    <td>4</td>
                    <td>150.00</td>
                    <td>PAGADO</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
```

Pseudocódigo:

```pseudo
function enviarCorreoPagoContadoExitoso(correo):
    pagos = listarPagosContado()
    html = generarTablaHtml("Lista actualizada de pagos contado", pagos)
    enviarCorreo(correo, "Pago contado realizado exitosamente", html)
```

Para pago de cuota:

```pseudo
function enviarCorreoPagoCuotaExitoso(correo):
    pagos = listarPagosCuota()
    html = generarTablaHtml("Lista actualizada de pagos cuota", pagos)
    enviarCorreo(correo, "Pago de cuota realizado exitosamente", html)
```

---

## 18. Estructura recomendada de servicios

La integración puede organizarse de esta manera:

```text
ConfigPagoFacil
PagoFacilAuthService
PagoFacilQrService
PagoFacilCallbackService
PagoFacilTransaccionDAO
PagoContadoDAO
PagoCuotaDAO
VentaDAO
CreditoDAO
NotificacionService o SendEmail
```

Responsabilidad de cada parte:

```text
ConfigPagoFacil:
Guarda URL base, credenciales y callback URL.

PagoFacilAuthService:
Obtiene y reutiliza el token de acceso.

PagoFacilQrService:
Consulta métodos habilitados y genera el QR.

PagoFacilTransaccionDAO:
Guarda la transacción QR y actualiza su estado.

PagoContadoDAO:
Registra pagos al contado y los marca como PAGADO.

PagoCuotaDAO:
Registra pagos de cuotas y los marca como PAGADO.

VentaDAO:
Actualiza la venta como PAGADA.

CreditoDAO:
Actualiza monto pagado, monto pendiente y estado del crédito.

PagoFacilCallbackService:
Recibe el callback, verifica el estado y coordina las actualizaciones.

SendEmail o NotificationService:
Notifica al usuario final.
```

---

## 19. Ejemplo completo en pseudocódigo

```pseudo
function solicitarPagoContado(idVenta, idMetodoPago, monto, correoSolicitante):
    idPagoContado = crearPagoContado({
        id_venta: idVenta,
        id_metodo_pago: idMetodoPago,
        monto_pago: monto,
        estado: "PENDIENTE"
    })

    paymentNumber = "PC-" + idPagoContado + "-VEN-" + idVenta

    qrResponse = generarQrPagoFacil(
        paymentNumber,
        correoSolicitante,
        monto
    )

    guardarTransaccionPagoFacil({
        id_pago_contado: idPagoContado,
        payment_number: paymentNumber,
        payment_method_id: qrResponse.paymentMethodId,
        qr_base64: qrResponse.qrBase64,
        checkout_url: qrResponse.checkoutUrl,
        expiration_date: qrResponse.expirationDate,
        estado: "PENDIENTE",
        correo_solicitante: correoSolicitante
    })

    enviarQrAlUsuario(correoSolicitante, qrResponse.qrBase64, paymentNumber)
```

Callback:

```pseudo
function procesarCallback(json):
    pedidoId = json.PedidoID
    estado = json.Estado

    transaccion = buscarTransaccionPorPaymentNumber(pedidoId)

    if transaccion is null:
        return respuestaOk("Transaccion no encontrada")

    estadoNormalizado = normalizarEstadoPagoFacil(estado)

    actualizarEstadoTransaccion(pedidoId, estadoNormalizado)

    if estadoNormalizado == "PAGADO":
        if transaccion.id_pago_contado is not null:
            procesarPagoContado(transaccion)

        if transaccion.id_pago_cuota is not null:
            procesarPagoCuota(transaccion)

    return respuestaOk("Callback procesado correctamente")
```

---

## 20. Errores comunes y solución

### El QR se genera, pero el sistema no actualiza el pago

Causa probable:

```text
El callback llega, pero el sistema no interpreta correctamente el Estado.
```

Solución:

```pseudo
if Estado == 2:
    estado = "PAGADO"
```

---

### El callback llega, pero dice transacción no encontrada

Causa probable:

```text
El PedidoID recibido no coincide con el payment_number guardado.
```

Solución:

```text
El payment_number enviado a PagoFácil debe ser exactamente igual al PedidoID del callback.
```

Ejemplo correcto:

```text
payment_number = PC-4-VEN-1
PedidoID recibido = PC-4-VEN-1
```

---

### Se actualiza PagoFácil pero no la venta

Causa probable:

```text
El pago contado no está relacionado correctamente con la venta.
```

Solución:

```sql
SELECT id_venta
FROM pago_contado
WHERE id = id_pago_contado;
```

Luego:

```sql
UPDATE venta
SET estado = 'PAGADA'
WHERE id = id_venta;
```

---

### Se confirma el pago, pero no llega correo

Causa probable:

```text
No se guardó correo_solicitante en pago_facil_transaccion.
```

Solución:

```sql
ALTER TABLE pago_facil_transaccion
ADD COLUMN IF NOT EXISTS correo_solicitante VARCHAR(150);
```

Y al crear la transacción:

```pseudo
transaccion.correo_solicitante = correoDelUsuario
```

---

### Pago contado aparece como PAGADO aunque no se pagó

Causa probable:

```text
La consulta usa PAGADO como valor por defecto.
```

Incorrecto:

```sql
COALESCE(estado, 'PAGADO')
```

Correcto:

```sql
COALESCE(estado, 'PENDIENTE')
```

---

## 21. Checklist final

Antes de dar por terminada la integración, verificar:

```text
[ ] El pago se registra como PENDIENTE.
[ ] El payment_number es único.
[ ] El payment_number se guarda en pago_facil_transaccion.
[ ] El payment_number coincide con el PedidoID del callback.
[ ] El QR llega correctamente al usuario.
[ ] El callback es público y responde 200 OK.
[ ] El Estado 2 se interpreta como PAGADO.
[ ] pago_facil_transaccion cambia a PAGADO.
[ ] pago_contado o pago_cuota cambia a PAGADO.
[ ] venta cambia a PAGADA si corresponde.
[ ] credito actualiza monto_pagado y monto_pendiente si corresponde.
[ ] El usuario recibe confirmación del pago.
[ ] El sistema muestra una lista actualizada de pagos.
```

---

## 22. Conclusión

La integración de PagoFácil con QR requiere manejar correctamente tres momentos principales: la creación de la solicitud de pago, el almacenamiento de la transacción pendiente y el procesamiento del callback. La parte más importante es conservar un identificador único, como `payment_number`, que permita relacionar el QR generado con el pago interno del sistema.

Cuando el callback confirma el pago, el sistema debe actualizar sus propias tablas, marcar el pago como realizado y notificar al usuario. Esta lógica permite que el proceso sea automático, seguro y aplicable a cualquier sistema que necesite pagos mediante QR.
