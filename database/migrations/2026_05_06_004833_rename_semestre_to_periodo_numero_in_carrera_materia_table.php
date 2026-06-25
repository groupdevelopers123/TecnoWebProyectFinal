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
        Schema::table('carrera_materia', function (Blueprint $table) {
            $table->renameColumn('semestre', 'periodo_numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carrera_materia', function (Blueprint $table) {
            $table->renameColumn('periodo_numero', 'semestre');
        });
    }
};
