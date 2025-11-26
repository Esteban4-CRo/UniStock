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
        Schema::create('configuracions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_sistema')->default('UniStock');
            $table->string('idioma')->default('es');
            $table->string('moneda')->default('COP');
            $table->string('zona_horaria')->default('America/Bogota');
            $table->integer('stock_minimo_global')->default(10);
            $table->boolean('alertas_email')->default(true);
            $table->boolean('modo_mantenimiento')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracions');
    }
};
