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
        Schema::create('oportunidad_lineas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oportunidad_id')->constrained('oportunidads')->onDelete('cascade');
            $table->foreignId('linea_id')->constrained('lineas')->onDelete('cascade')->nulleable();
            $table->date('fecha_linea')->nullable();
            $table->string('plan_anterior')->nullable();
            $table->string('plan_nuevo')->nullable();
            $table->string('equipo_nuevo')->nullable();
            $table->string('sku')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oportunidad_lineas');
    }
};
