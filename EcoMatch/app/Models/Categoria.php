<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'idcategorias';

    protected $fillable = ['nombre', 'descripcion', 'empresa_idempresa'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_idempresa');
    }

    public function publicaciones()
    {
        return $this->hasMany(Publicacion::class, 'idCategoria');
    }
}
