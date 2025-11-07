<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Oportunidad extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'cliente_id',
        'usuario_id',
        'estado',
        'notas',
        'direccion_entrega',
        'fecha_captura',
        'fecha_entrega',
        'fecha_cancelacion',
        'tipo_venta',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function cliente()
    {

        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function oportunidadLinea()
    {

        return $this->hasMany(OportunidadLinea::class, 'oportunidad_id');
    }

    public function documentoOportunidad()
    {

        return $this->hasMany(DocumentoOportunidad::class, 'oportunidad_id');
    }

    public function revisionOportunidad()
    {

        return $this->hasMany(RevisionOportunidad::class, 'oportunidad_id');
    }
}
