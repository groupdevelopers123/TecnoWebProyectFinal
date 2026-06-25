<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->string('ubicacion', 150)->nullable();
            $table->string('piso', 30)->nullable();
            $table->integer('capacidad')->default(0);
            $table->decimal('largo', 10, 2)->nullable();
            $table->decimal('ancho', 10, 2)->nullable();
            $table->boolean('disponible')->default(true);

            $table->foreignId('user_id_registro')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aulas');
    }
};