<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';
    protected $primaryKey = 'idmensajes';

    protected $fillable = [
        'idsolicitud', 'idEmisora', 'contenido', 'leido', 'solicitudes_idsolicitud'
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitudes_idsolicitud');
    }
}
