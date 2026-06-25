<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago_contados', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inscripcion_id')
                ->constrained('inscripciones')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('concepto_pago_id')
                ->constrained('concepto_pagos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('monto_pagado', 10, 2);
            $table->date('fecha_pago');
            $table->string('metodo_pago', 30);
            $table->string('estado', 30)->default('Pendiente');

            $table->string('codigo_transaccion')->nullable();
            $table->string('correo_solicitante')->nullable();
            $table->text('observacion')->nullable();

            $table->string('payment_number')->nullable()->unique();
            $table->string('qr_path')->nullable();
            $table->timestamp('fecha_confirmacion')->nullable();

            $table->timestamps();

            $table->index('estado');
            $table->index('metodo_pago');
            $table->index('fecha_pago');
            $table->index('codigo_transaccion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_contados');
    }
};