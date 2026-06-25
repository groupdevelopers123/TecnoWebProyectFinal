<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('page');
            $table->unsignedBigInteger('visits')->default(0);
            $table->timestamp('last_visited_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'page']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
