<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoOportunidad extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'oportunidad_id',
        'usuario_id',
        'tipo_documento',
        'ruta',
        'nombre_archivo',
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
