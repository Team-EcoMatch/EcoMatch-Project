<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';
    protected $primaryKey = 'idsolicitud';

    protected $fillable = [
        'idpublicaciones',
        'idEmpresaOrigen',
        'idEmpresaDestino',
        'mensaje',
        'cantidad',
        'estado'
    ];

    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class, 'idpublicaciones', 'idpublicaciones');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'solicitudes_idsolicitud');
    }

    public function empresaOrigen()
    {
        return $this->belongsTo(Empresa::class, 'idEmpresaOrigen', 'idempresa');
    }

    public function empresaDestino()
    {
        return $this->belongsTo(Empresa::class, 'idEmpresaDestino', 'idempresa');
    }
}
