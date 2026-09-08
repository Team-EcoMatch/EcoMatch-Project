<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    protected $table = 'publicaciones';
    protected $primaryKey = 'idpublicaciones';

    protected $fillable = [
        'idempresa', 'idcategorias', 'nombre', 'descripcion', 'cantidad',
        'unidadMedida', 'frecuencia', 'estado', 'urlImagen', 
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'idempresa', 'idempresa');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idcategorias', 'idcategorias');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'idpublicaciones');
    }
}