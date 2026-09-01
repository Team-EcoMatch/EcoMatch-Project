<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';
    protected $primaryKey = 'idmensajes';

    protected $fillable = [
        'idsolicitud', 'idEmisora', 'contenido', 'leido', 
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'idsolicitud');
    }

    public function empresaEmisora()
    {
        return $this->belongsTo(Empresa::class, 'idEmisora');
    }
}
