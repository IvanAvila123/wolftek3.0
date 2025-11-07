<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RevisionOportunidad extends Model
{
    use SoftDeletes;

    protected $table = 'revision_oportunidads';

     protected $fillable = [
        'oportunidad_id',
        'usuario_id',
        'estado',
        'comentarios',
        'imagenes_rechazo',
        'visto_por_vendedor',
        'fecha_visto',
        'fecha_aprobacion',
        'fecha_rechazo',
    ];

    // ✅ IMPORTANTE: Cast para que las imágenes se guarden como array
    protected $casts = [
        'imagenes_rechazo' => 'array',
        'visto_por_vendedor' => 'boolean',
        'fecha_visto' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_rechazo' => 'datetime',
    ];

    public function oportunidad()
    {
        return $this->belongsTo(Oportunidad::class, 'oportunidad_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
