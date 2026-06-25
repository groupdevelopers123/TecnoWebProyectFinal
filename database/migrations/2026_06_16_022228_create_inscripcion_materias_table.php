<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripcion_materias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inscripcion_id')
                ->constrained('inscripciones')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('carrera_materia_id')
                ->constrained('carrera_materia')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('estado', 30)->default('Cursando');

            $table->timestamps();

            $table->unique(['inscripcion_id', 'carrera_materia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion_materias');
    }
};