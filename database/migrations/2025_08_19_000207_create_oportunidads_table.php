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
        Schema::create('oportunidads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->enum('estado', ['Pendiente', 'Revision', 'Haciendo contratos',
            'Entrega de contratos', 'Revision de contratos', 'Captura', 'Verificacion de credito',
            'Rechazada por credito','Verificacion de credito rechazada','Verificacion de credito aprobada','Asignacion de equipo',
            'Cancela/envios', 'Envios/por confirmar', 'Envios/en ruta', 'Orden entregada','Aprobada', 'Rechazada', 'Cancelada'])->default('Pendiente');
            $table->string('notas')->nullable();
            $table->string('direccion_entrega');
            $table->date('fecha_captura')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->date('fecha_cancelacion')->nullable();
            $table->string('tipo_venta');
            $table->softDeletes(); // For soft delete functionality
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oportunidads');
    }
};
