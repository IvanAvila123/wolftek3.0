<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $fillable = [
        'nombre',
        'capacidad',
        'pantalla',
        'resolucion',
        'camara',
        'sistema_operativo',
        'foto_principal',
    ];
}
