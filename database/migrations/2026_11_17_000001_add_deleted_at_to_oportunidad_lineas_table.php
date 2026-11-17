<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El modelo OportunidadLinea usa SoftDeletes, pero la tabla se creó sin deleted_at.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('oportunidad_lineas', 'deleted_at')) {
            Schema::table('oportunidad_lineas', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::table('oportunidad_lineas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
