<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * El enum original guardaba etiquetas ("Orden entregada"), pero formularios,
 * filtros y widgets trabajan con las claves de config('oportunidad.estados')
 * ("orden_entregada"). Se convierte la columna a string y los datos a claves.
 */
return new class extends Migration
{
    private const ESTADOS = [
        'pendiente' => 'Pendiente',
        'revision' => 'Revision',
        'haciendo_contratos' => 'Haciendo contratos',
        'entrega_contratos' => 'Entrega de contratos',
        'revision_contratos' => 'Revision de contratos',
        'captura' => 'Captura',
        'verificacion_credito' => 'Verificacion de credito',
        'rechazada_credito' => 'Rechazada por credito',
        'verificacion_rechazada' => 'Verificacion de credito rechazada',
        'verificacion_aprobada' => 'Verificacion de credito aprobada',
        'asignacion_equipo' => 'Asignacion de equipo',
        'cancela_envios' => 'Cancela/envios',
        'envios_confirmar' => 'Envios/por confirmar',
        'envios_ruta' => 'Envios/en ruta',
        'orden_entregada' => 'Orden entregada',
        'aprobada' => 'Aprobada',
        'rechazada' => 'Rechazada',
        'cancelada' => 'Cancelada',
    ];

    public function up(): void
    {
        Schema::table('oportunidads', function (Blueprint $table) {
            $table->string('estado', 50)->default('pendiente')->change();
        });

        foreach (self::ESTADOS as $clave => $etiqueta) {
            DB::table('oportunidads')->where('estado', $etiqueta)->update(['estado' => $clave]);
        }
    }

    public function down(): void
    {
        foreach (self::ESTADOS as $clave => $etiqueta) {
            DB::table('oportunidads')->where('estado', $clave)->update(['estado' => $etiqueta]);
        }

        Schema::table('oportunidads', function (Blueprint $table) {
            $table->enum('estado', array_values(self::ESTADOS))->default('Pendiente')->change();
        });
    }
};
