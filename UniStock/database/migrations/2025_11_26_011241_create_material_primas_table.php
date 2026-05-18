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
        Schema::create('material_primas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->integer('cantidad')->default(0); // stock actual
            $table->string('unidad_medida')->default('unidad'); // kg, litros, unidad, etc
            $table->integer('stock_minimo')->default(0);
            $table->decimal('precio', 10, 2)->default(0.00);
            $table->string('lote')->nullable();
            $table->date('fecha_caducidad')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ubicacion_id')->nullable()->constrained('ubicaciones')->onDelete('set null');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_primas');
    }
};
