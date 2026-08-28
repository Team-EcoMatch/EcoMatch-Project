<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    protected $table = 'publicaciones';
    protected $primaryKey = 'idpublicaciones';

    protected $fillable = [
        'idEmpresa', 'idCategoria', 'nombre', 'descripcion', 'cantidad',
        'unidadMedida', 'frecuencia', 'estado', 'urlImagen', 'empresa_idempresa'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_idempresa');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idCategoria', 'idcategorias');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'publicaciones_idpublicaciones');
    }
}
