<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->boolean('anulado')->default(false);
        });

        Schema::table('salidas', function (Blueprint $table) {
            $table->boolean('anulado')->default(false);
        });
    }

    public function down()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn('anulado');
        });

        Schema::table('salidas', function (Blueprint $table) {
            $table->dropColumn('anulado');
        });
    }
};
