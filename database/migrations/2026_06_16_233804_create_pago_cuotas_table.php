<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago_cuotas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('credito_id')
                ->constrained('creditos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('monto', 10, 2);
            $table->integer('numero_cuota');
            $table->date('fecha_vencimiento')->nullable();
            $table->date('fecha_pago')->nullable();

            $table->string('estado_cuota', 30)->default('pendiente');
            $table->string('metodo_pago', 50)->nullable();
            $table->text('observacion')->nullable();
            $table->string('codigo_transaccion', 150)->nullable();
            $table->string('correo_solicitante', 150)->nullable();
            $table->string('payment_number', 100)->nullable()->unique();
            $table->text('qr_path')->nullable();
            $table->timestamp('fecha_confirmacion')->nullable();

            $table->timestamps();

            $table->unique(['credito_id', 'numero_cuota']);
            $table->index('estado_cuota');
            $table->index('metodo_pago');
            $table->index('fecha_vencimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_cuotas');
    }
};