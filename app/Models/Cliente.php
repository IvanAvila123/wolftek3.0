<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nombre',
        'cuenta',
        'id_cliente',
        'representante',
        'telefono',
        'correo',
        'direccion_fiscal',
        'direccion_entrega',
        'direccion_preferencia',
        'rfc',
    ];


    public function lineas()
    {
        return $this->hasMany(Linea::class);
    }



}
