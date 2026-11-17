<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * usuario_id es la asignación del vendedor: una línea nueva aún no tiene
 * vendedor y al liberar la asignación se pone en null.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lineas', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });

        Schema::table('lineas', function (Blueprint $table) {
            $table->foreignId('usuario_id')->nullable()->change();
            $table->foreign('usuario_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lineas', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });

        Schema::table('lineas', function (Blueprint $table) {
            $table->foreignId('usuario_id')->nullable(false)->change();
            $table->foreign('usuario_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
