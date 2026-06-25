<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alumno_detalle_id')
                ->constrained('alumno_detalles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('oferta_academica_id')
                ->constrained('ofertas_academicas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('user_id_registro')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->integer('periodo_numero');
            $table->date('fecha_inscripcion');
            $table->text('observacion')->nullable();

            $table->timestamps();

            $table->unique(['alumno_detalle_id', 'oferta_academica_id']);

            $table->index('fecha_inscripcion');
            $table->index('periodo_numero');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};