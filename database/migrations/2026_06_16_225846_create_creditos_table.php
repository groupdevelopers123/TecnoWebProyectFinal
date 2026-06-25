<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inscripcion_id')
                ->constrained('inscripciones')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('concepto_pago_id')
                ->constrained('concepto_pagos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('tipo_pago', 50)->default('CREDITO');
            $table->decimal('monto_total', 10, 2);
            $table->decimal('saldo_pendiente', 10, 2)->nullable();
            $table->integer('cantidad_cuotas')->nullable();
            $table->string('estado', 30)->default('pendiente');

            $table->timestamps();

            $table->index('estado');
            $table->index('tipo_pago');
            $table->index(['inscripcion_id', 'concepto_pago_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creditos');
    }
};