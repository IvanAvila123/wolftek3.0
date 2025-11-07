<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Linea extends Model
{
    use SoftDeletes;
   protected $fillable = [
        'dn',
        'fecha_fin',
        'plan',
        'equipo',
        'cliente_id',
        'usuario_id',
        'asignado_en',
    ];

    protected $casts = [
    'fecha_fin' => 'date',
    'asignado_en' => 'datetime',
    // otros casts...
];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function oportunidadLinea()
    {
        return $this->hasMany(OportunidadLinea::class, 'linea_id');
    }

}
