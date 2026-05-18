<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reportes', function (Blueprint $table) {
            $table->unsignedBigInteger('material_prima_id')->nullable()->after('user_id');
            $table->foreign('material_prima_id')->references('id')->on('material_primas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('reportes', function (Blueprint $table) {
            $table->dropForeign(['material_prima_id']);
            $table->dropColumn('material_prima_id');
        });
    }
};
