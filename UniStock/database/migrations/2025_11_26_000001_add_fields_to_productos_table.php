<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('unidad_medida')->nullable();
            $table->integer('stock_minimo')->default(0);
            $table->date('fecha_caducidad')->nullable();
            $table->string('lote')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['unidad_medida', 'stock_minimo', 'fecha_caducidad', 'lote']);
        });
    }
};
