<?php

namespace App\Services;

use App\Models\PagoContado;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\PagoCuota;
use RuntimeException;

class PagoFacilService
{
    private function apiUrl(): string
    {
        return rtrim(
            config('services.pagofacil.api_url')
                ?: config('services.pagofacil.base_url')
                ?: 'https://masterqr.pagofacil.com.bo/api/services/v2',
            '/'
        );
    }

    private function http(): PendingRequest
    {
        return Http::timeout(40)
            ->connectTimeout(25)
            ->retry(2, 1000)
            ->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
            ]);
    }

    public function login(): string
    {
        $tokenService = config('services.pagofacil.tc_token_service');
        $tokenSecret = config('services.pagofacil.tc_token_secret');

        if (! $tokenService || ! $tokenSecret) {
            throw new RuntimeException('Faltan credenciales de PagoFácil en el archivo .env.');
        }

        $response = $this->http()
            ->withHeaders([
                'tctokenservice' => $tokenService,
                'tctokensecret' => $tokenSecret,
                'Response-Language' => config('services.pagofacil.response_language', 'es'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($this->apiUrl() . '/login', []);

        if (! $response->successful()) {
            throw new RuntimeException(
                'No se pudo autenticar con PagoFácil. HTTP ' .
                $response->status() .
                ': ' .
                $response->body()
            );
        }

        $json = $response->json();

        if (($json['error'] ?? 1) != 0 || empty($json['values']['accessToken'])) {
            throw new RuntimeException(
                ($json['message'] ?? 'PagoFácil no devolvió accessToken.') .
                ' Respuesta: ' .
                $response->body()
            );
        }

        return $json['values']['accessToken'];
    }

    private function headersAutenticados(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->login(),
            'Response-Language' => config('services.pagofacil.response_language', 'es'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function listarMetodosHabilitados(): array
    {
        $response = $this->http()
            ->withHeaders($this->headersAutenticados())
            ->post($this->apiUrl() . '/list-enabled-services', []);

        if (! $response->successful()) {
            throw new RuntimeException(
                'No se pudieron listar los métodos habilitados de PagoFácil. HTTP ' .
                $response->status() .
                ': ' .
                $response->body()
            );
        }

        $json = $response->json();

        if (($json['error'] ?? 1) != 0) {
            throw new RuntimeException(
                ($json['message'] ?? 'PagoFácil rechazó la consulta de métodos habilitados.') .
                ' Respuesta: ' .
                $response->body()
            );
        }

        return $json['values'] ?? [];
    }

    private function resolverMetodoQrHabilitado(): int
    {
        $metodos = $this->listarMetodosHabilitados();

        if (empty($metodos)) {
            throw new RuntimeException('PagoFácil no devolvió métodos de pago habilitados.');
        }

        $metodoConfigurado = (int) config('services.pagofacil.payment_method_qr', 0);

        foreach ($metodos as $metodo) {
            $id = (int) ($metodo['paymentMethodId'] ?? 0);
            $nombre = strtoupper((string) ($metodo['paymentMethodName'] ?? ''));

            if ($metodoConfigurado > 0 && $id === $metodoConfigurado && str_contains($nombre, 'QR')) {
                return $id;
            }
        }

        foreach ($metodos as $metodo) {
            $id = (int) ($metodo['paymentMethodId'] ?? 0);
            $nombre = strtoupper((string) ($metodo['paymentMethodName'] ?? ''));
            $moneda = strtoupper((string) ($metodo['currencyName'] ?? ''));

            if ($id > 0 && str_contains($nombre, 'QR') && $moneda === 'BOB') {
                return $id;
            }
        }

        foreach ($metodos as $metodo) {
            $id = (int) ($metodo['paymentMethodId'] ?? 0);
            $nombre = strtoupper((string) ($metodo['paymentMethodName'] ?? ''));

            if ($id > 0 && str_contains($nombre, 'QR')) {
                return $id;
            }
        }

        $detalle = collect($metodos)->map(function ($metodo) {
            return [
                'paymentMethodId' => $metodo['paymentMethodId'] ?? null,
                'paymentMethodName' => $metodo['paymentMethodName'] ?? null,
                'currencyName' => $metodo['currencyName'] ?? null,
            ];
        })->values()->toJson();

        throw new RuntimeException(
            'No se encontró ningún método QR habilitado para esta empresa. Métodos recibidos: ' . $detalle
        );
    }

    public function generarQr(PagoContado $pago): array
    {
        $pago->load([
            'inscripcion.alumnoDetalle.user',
            'inscripcion.ofertaAcademica.carrera',
            'conceptoPago',
        ]);

        $alumno = $pago->inscripcion?->alumnoDetalle?->user;

        $paymentNumber = $pago->payment_number ?: $this->generarPaymentNumber($pago);

        $paymentMethodId = $this->resolverMetodoQrHabilitado();

        $montoQr = config('services.pagofacil.override_amount');

        if ($montoQr === null || $montoQr === '') {
            $montoQr = $pago->monto_pagado;
        }

        $montoQr = round((float) $montoQr, 2);

        if ($montoQr <= 0) {
            throw new RuntimeException('El monto del QR debe ser mayor a 0.');
        }

        $currencyId = (int) config('services.pagofacil.currency_id', 2);

        $callbackUrl = config('services.pagofacil.callback_url');

        if (! $callbackUrl) {
            throw new RuntimeException('No está configurada la URL callback de PagoFácil.');
        }

        $body = [
            'paymentMethod' => $paymentMethodId,
            'clientName' => $alumno?->nombreCompleto() ?: 'Cliente Instituto',
            'documentType' => 1,
            'documentId' => $alumno?->ci ?: '0',
            'phoneNumber' => $alumno?->telefono ?: '70000000',
            'email' => $pago->correo_solicitante ?: $alumno?->email ?: 'cliente@instituto.com',
            'paymentNumber' => $paymentNumber,
            'amount' => $montoQr,
            'currency' => $currencyId,
            'clientCode' => (string) ($alumno?->id ?? $pago->inscripcion_id),
            'callbackUrl' => $callbackUrl,
            'orderDetail' => [
                [
                    'serial' => 1,
                    'product' => $pago->conceptoPago?->nombre ?? 'Pago instituto',
                    'quantity' => 1,
                    'price' => $montoQr,
                    'discount' => 0,
                    'total' => $montoQr,
                ],
            ],
        ];

        $response = $this->http()
            ->withHeaders($this->headersAutenticados())
            ->post($this->apiUrl() . '/generate-qr', $body);

        if (! $response->successful()) {
            throw new RuntimeException(
                'No se pudo generar el QR. HTTP ' .
                $response->status() .
                ': ' .
                $response->body()
            );
        }

        $json = $response->json();

        if (($json['error'] ?? 1) != 0 || empty($json['values'])) {
            throw new RuntimeException(
                ($json['message'] ?? 'PagoFácil no devolvió datos del QR.') .
                ' Respuesta: ' .
                $response->body()
            );
        }

        $values = $json['values'];

        $qrPath = null;

        if (! empty($values['qrBase64'])) {
            $qrPath = $this->guardarQrBase64($values['qrBase64'], $paymentNumber);
        }

        if (! $qrPath) {
            throw new RuntimeException(
                'PagoFácil generó la transacción, pero no devolvió qrBase64. Respuesta: ' .
                $response->body()
            );
        }

        $pago->update([
            'payment_number' => $paymentNumber,
            'codigo_transaccion' => $values['transactionId'] ?? null,
            'qr_path' => $qrPath,
            'estado' => 'Pendiente',
        ]);

        return $values;
    }

    public function consultarTransaccion(PagoContado $pago): array
    {
        if (! $pago->payment_number && ! $pago->codigo_transaccion) {
            throw new RuntimeException('El pago no tiene payment_number ni código de transacción para consultar.');
        }

        $body = [];

        if ($pago->codigo_transaccion) {
            $body['pagofacilTransactionId'] = $pago->codigo_transaccion;
        }

        if ($pago->payment_number) {
            $body['companyTransactionId'] = $pago->payment_number;
        }

        $response = $this->http()
            ->withHeaders($this->headersAutenticados())
            ->post($this->apiUrl() . '/query-transaction', $body);

        if (! $response->successful()) {
            throw new RuntimeException(
                'No se pudo consultar la transacción. HTTP ' .
                $response->status() .
                ': ' .
                $response->body()
            );
        }

        $json = $response->json();

        if (($json['error'] ?? 1) != 0) {
            throw new RuntimeException(
                ($json['message'] ?? 'PagoFácil rechazó la consulta de transacción.') .
                ' Respuesta: ' .
                $response->body()
            );
        }

        return $json;
    }

    public function confirmarSiPagoFueRealizado(PagoContado $pago): bool
    {
        $respuesta = $this->consultarTransaccion($pago);

        $paymentStatus = data_get($respuesta, 'values.paymentStatus');
        $paymentDate = data_get($respuesta, 'values.paymentDate');
        $paymentTime = data_get($respuesta, 'values.paymentTime');

        $estadoNormalizado = $this->normalizarEstadoPagoFacil($paymentStatus);

        if ($estadoNormalizado === 'Confirmado') {
            $pago->update([
                'estado' => 'Confirmado',
                'fecha_confirmacion' => now(),
                'observacion' => trim(
                    ($pago->observacion ?? '') .
                    "\nConsulta PagoFácil: pago confirmado. Fecha: {$paymentDate}. Hora: {$paymentTime}."
                ),
            ]);

            return true;
        }

        return false;
    }

    private function normalizarEstadoPagoFacil(mixed $estado): string
    {
        $estadoTexto = mb_strtolower(trim((string) $estado));

        return match (true) {
            $estadoTexto === '2',
            str_contains($estadoTexto, 'confirm'),
            str_contains($estadoTexto, 'pagado'),
            str_contains($estadoTexto, 'aprob') => 'Confirmado',

            str_contains($estadoTexto, 'fall'),
            str_contains($estadoTexto, 'rechaz'),
            str_contains($estadoTexto, 'anulad') => 'Fallido',

            default => 'Pendiente',
        };
    }

    private function generarPaymentNumber(PagoContado $pago): string
    {
        return 'PAGO-' . now()->format('YmdHis') . '-' . $pago->id . '-' . Str::upper(Str::random(5));
    }

    private function guardarQrBase64(string $base64, string $paymentNumber): string
    {
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);

        $contenido = base64_decode($base64);

        if ($contenido === false) {
            throw new RuntimeException('El QR recibido no tiene formato base64 válido.');
        }

        $path = 'pagos/qr/' . $paymentNumber . '.png';

        Storage::disk('public')->put($path, $contenido);

        return $path;
    }

    public function generarQrPagoCuota(PagoCuota $cuota): array
    {
        $cuota->load([
            'credito.inscripcion.alumnoDetalle.user',
            'credito.inscripcion.ofertaAcademica.carrera',
            'credito.conceptoPago',
        ]);

        $alumno = $cuota->credito?->inscripcion?->alumnoDetalle?->user;

        $paymentNumber = $cuota->payment_number ?: $this->generarPaymentNumberCuota($cuota);

        $paymentMethodId = $this->resolverMetodoQrHabilitado();

        $montoQr = config('services.pagofacil.override_amount');

        if ($montoQr === null || $montoQr === '') {
            $montoQr = $cuota->monto;
        }

        $montoQr = round((float) $montoQr, 2);

        if ($montoQr <= 0) {
            throw new RuntimeException('El monto del QR debe ser mayor a 0.');
        }

        $body = [
            'paymentMethod' => $paymentMethodId,
            'clientName' => $alumno?->nombreCompleto() ?: 'Cliente Instituto',
            'documentType' => 1,
            'documentId' => $alumno?->ci ?: '0',
            'phoneNumber' => $alumno?->telefono ?: '70000000',
            'email' => $cuota->correo_solicitante ?: $alumno?->email ?: 'cliente@instituto.com',
            'paymentNumber' => $paymentNumber,
            'amount' => $montoQr,
            'currency' => (int) config('services.pagofacil.currency_id', 2),
            'clientCode' => (string) ($alumno?->id ?? $cuota->credito_id),
            'callbackUrl' => config('services.pagofacil.callback_url'),
            'orderDetail' => [
                [
                    'serial' => 1,
                    'product' => 'Cuota ' . $cuota->numero_cuota . ' - ' . ($cuota->credito?->conceptoPago?->nombre ?? 'Crédito'),
                    'quantity' => 1,
                    'price' => $montoQr,
                    'discount' => 0,
                    'total' => $montoQr,
                ],
            ],
        ];

        $response = $this->http()
            ->withHeaders($this->headersAutenticados())
            ->post($this->apiUrl() . '/generate-qr', $body);

        if (! $response->successful()) {
            throw new RuntimeException(
                'No se pudo generar el QR de la cuota. HTTP ' .
                $response->status() .
                ': ' .
                $response->body()
            );
        }

        $json = $response->json();

        if (($json['error'] ?? 1) != 0 || empty($json['values'])) {
            throw new RuntimeException(
                ($json['message'] ?? 'PagoFácil no devolvió datos del QR de la cuota.') .
                ' Respuesta: ' .
                $response->body()
            );
        }

        $values = $json['values'];

        $qrPath = null;

        if (! empty($values['qrBase64'])) {
            $qrPath = $this->guardarQrBase64($values['qrBase64'], $paymentNumber);
        }

        if (! $qrPath) {
            throw new RuntimeException('PagoFácil no devolvió qrBase64 para la cuota.');
        }

        $cuota->update([
            'payment_number' => $paymentNumber,
            'codigo_transaccion' => $values['transactionId'] ?? null,
            'qr_path' => $qrPath,
            'estado_cuota' => 'pendiente',
            'metodo_pago' => 'QR',
        ]);

        return $values;
    }

    public function consultarTransaccionPagoCuota(PagoCuota $cuota): array
    {
        if (! $cuota->payment_number && ! $cuota->codigo_transaccion) {
            throw new RuntimeException('La cuota no tiene payment_number ni código de transacción para consultar.');
        }

        $body = [];

        if ($cuota->codigo_transaccion) {
            $body['pagofacilTransactionId'] = $cuota->codigo_transaccion;
        }

        if ($cuota->payment_number) {
            $body['companyTransactionId'] = $cuota->payment_number;
        }

        $response = $this->http()
            ->withHeaders($this->headersAutenticados())
            ->post($this->apiUrl() . '/query-transaction', $body);

        if (! $response->successful()) {
            throw new RuntimeException(
                'No se pudo consultar la cuota. HTTP ' .
                $response->status() .
                ': ' .
                $response->body()
            );
        }

        $json = $response->json();

        if (($json['error'] ?? 1) != 0) {
            throw new RuntimeException(
                ($json['message'] ?? 'PagoFácil rechazó la consulta de la cuota.') .
                ' Respuesta: ' .
                $response->body()
            );
        }

        return $json;
    }

    private function generarPaymentNumberCuota(PagoCuota $cuota): string
    {
        return 'CUOTA-' . now()->format('YmdHis') . '-' . $cuota->id . '-' . Str::upper(Str::random(5));
    }
}