<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';
    protected $primaryKey = 'idmensajes';
    public $timestamps = true;

    protected $fillable = [
        'idsolicitud', 'idEmisora', 'contenido', 'leido','tipo','archivo_url','archivo_nombre','archivo_tamano', 
    ];

    protected $casts = [
        'leido' => 'boolean',
        'archivo_tamano' => 'integer',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'idsolicitud', 'idsolicitud');
    }

    public function empresaEmisora()
    {
        return $this->belongsTo(Empresa::class, 'idEmisora', 'idempresa');
    }

    public function emisora()
    {
        return $this->belongsTo(Empresa::class, 'idEmisora', 'idempresa');
    }
}