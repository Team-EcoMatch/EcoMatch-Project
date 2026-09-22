<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    protected $table = 'solicitudes';
    protected $primaryKey = 'idsolicitud';

    protected $fillable = [
        'idpublicaciones',
        'idEmpresaOrigen',
        'idEmpresaDestino',
        'user_id',
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
        return $this->hasMany(Mensaje::class, 'idsolicitud', 'idsolicitud');
    }

    public function empresa_origen()
    {
        return $this->belongsTo(Empresa::class, 'idEmpresaOrigen', 'idempresa');
    }

    public function empresa_destino()
    {
        return $this->belongsTo(Empresa::class, 'idEmpresaDestino', 'idempresa');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bloqueos(): HasMany
    {
        return $this->hasMany(Bloqueo::class, 'idsolicitud', 'idsolicitud');
    }
}
