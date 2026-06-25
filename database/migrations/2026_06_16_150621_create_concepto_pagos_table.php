<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concepto_pagos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120)->unique();
            $table->text('descripcion')->nullable();
            $table->string('estado', 20)->default('Activo');
            $table->timestamps();

            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concepto_pagos');
    }
};