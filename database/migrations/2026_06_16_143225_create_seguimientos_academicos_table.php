<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguimientos_academicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inscripcion_materia_id')
                ->constrained('inscripcion_materias')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('docente_detalle_id')
                ->constrained('docente_detalles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('nota_final', 5, 2)->nullable();
            $table->decimal('porcentaje_asistencia', 5, 2)->nullable();
            $table->text('observacion')->nullable();
            $table->string('estado_academico', 40);
            $table->date('fecha_registro');

            $table->timestamps();

            $table->unique('inscripcion_materia_id');
            $table->index('docente_detalle_id');
            $table->index('estado_academico');
            $table->index('fecha_registro');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimientos_academicos');
    }
};