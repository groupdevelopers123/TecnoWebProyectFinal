<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ofertas_academicas', function (Blueprint $table) {
            $table->decimal('precio_matricula', 10, 2)
                ->default(0);

            $table->decimal('precio_mensualidad', 10, 2)
                ->default(0);

            $table->decimal('precio_carrera_completa', 10, 2)
                ->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('ofertas_academicas', function (Blueprint $table) {
            $table->dropColumn([
                'precio_matricula',
                'precio_mensualidad',
                'precio_carrera_completa',
            ]);
        });
    }
};