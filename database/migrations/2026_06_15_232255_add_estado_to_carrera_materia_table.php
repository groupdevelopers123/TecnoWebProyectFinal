<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carrera_materia', function (Blueprint $table) {
            $table->boolean('estado')->default(true)->after('periodo_numero');
        });
    }

    public function down(): void
    {
        Schema::table('carrera_materia', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};