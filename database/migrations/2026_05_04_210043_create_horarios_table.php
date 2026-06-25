<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('carrera_materia_id')
                ->constrained('carrera_materia')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('periodo_academico_id')
                ->constrained('periodos_academicos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('aula_id')
                ->constrained('aulas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('docente_detalle_id')
                ->constrained('docente_detalles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('dia', 30);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('turno', 50);
            $table->boolean('estado')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
