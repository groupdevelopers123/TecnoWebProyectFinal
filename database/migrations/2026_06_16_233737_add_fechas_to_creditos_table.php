<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creditos', function (Blueprint $table) {
            if (! Schema::hasColumn('creditos', 'fecha_otorgamiento')) {
                $table->date('fecha_otorgamiento')->nullable()->after('cantidad_cuotas');
            }

            if (! Schema::hasColumn('creditos', 'fecha_vencimiento')) {
                $table->date('fecha_vencimiento')->nullable()->after('fecha_otorgamiento');
            }
        });
    }

    public function down(): void
    {
        Schema::table('creditos', function (Blueprint $table) {
            if (Schema::hasColumn('creditos', 'fecha_vencimiento')) {
                $table->dropColumn('fecha_vencimiento');
            }

            if (Schema::hasColumn('creditos', 'fecha_otorgamiento')) {
                $table->dropColumn('fecha_otorgamiento');
            }
        });
    }
};