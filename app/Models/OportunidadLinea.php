<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OportunidadLinea extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'oportunidad_id',
        'linea_id',
        'fecha_linea',
        'plan_anterior',
        'plan_nuevo',
        'equipo_nuevo',
        'sku',
    ];

    protected $casts = [
        'fecha_linea' => 'date',
    ];

    // Relación con Oportunidad
    public function oportunidad()
    {
        return $this->belongsTo(Oportunidad::class, 'oportunidad_id');
    }

    // Si tienes una tabla de líneas/productos
    public function linea()
    {
        return $this->belongsTo(Linea::class, 'linea_id');
    }

    // Acceso al usuario a través de la oportunidad
    public function usuario()
    {
        return $this->hasOneThrough(
            User::class,
            Oportunidad::class,
            'id', // Foreign key en oportunidads table
            'id', // Foreign key en users table
            'oportunidad_id', // Local key en oportunidad_lineas table
            'usuario_id' // Local key en oportunidads table
        );
    }

    // O más simple, accessor
    public function getUsuarioAttribute()
    {
        return $this->oportunidad?->usuario;
    }
}
