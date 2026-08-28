<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';
    protected $primaryKey = 'idsolicitud';

    protected $fillable = [
        'idpublicaciones', 'idEmpresaOrigen', 'idEmpresaDestino',
        'mensaje', 'estado', 'publicaciones_idpublicaciones'
    ];

    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class, 'publicaciones_idpublicaciones');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'solicitudes_idsolicitud');
    }

    
}
