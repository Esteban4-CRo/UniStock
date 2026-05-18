<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_prima_id')->constrained('material_primas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('cantidad');
            $table->string('lote')->nullable();
            $table->string('destino')->nullable(); // Destino del material (ej. Producción, Ajuste, Merma)
            $table->text('motivo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salidas');
    }
};