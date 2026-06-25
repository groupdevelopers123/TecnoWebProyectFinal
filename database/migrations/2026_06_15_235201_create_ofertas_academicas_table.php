<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ofertas_academicas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('carrera_id')
                ->constrained('carreras')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('periodo_academico_id')
                ->constrained('periodos_academicos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nombre', 150);
            $table->integer('cantidad_cupos');
            $table->integer('cupos_disponibles');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->index(['carrera_id', 'periodo_academico_id']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ofertas_academicas');
    }
};