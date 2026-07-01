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
        if (Schema::hasColumn('carrera_materia', 'semestre') && ! Schema::hasColumn('carrera_materia', 'periodo_numero')) {
            Schema::table('carrera_materia', function (Blueprint $table) {
                $table->renameColumn('semestre', 'periodo_numero');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('carrera_materia', 'periodo_numero') && ! Schema::hasColumn('carrera_materia', 'semestre')) {
            Schema::table('carrera_materia', function (Blueprint $table) {
                $table->renameColumn('periodo_numero', 'semestre');
            });
        }
    }
};
