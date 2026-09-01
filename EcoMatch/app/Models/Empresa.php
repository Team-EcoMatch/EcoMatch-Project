<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';
    protected $primaryKey = 'idempresa';
    public $incrementing = true;
    protected $keyType = 'int';


    protected $fillable = [
        'nombreEmpresa', 'direccion', 'email', 'telefono',
        'tipoEmpresa', 'latitud', 'longitud', 'radioOperacion', 'estado'
    ];


    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'idempresa');
    }

    public function publicaciones()
    {
        return $this->hasMany(Publicacion::class, 'idempresa');
    }

    public function roles()
    {
        return $this->hasMany(Rol::class, 'idempresa');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'idEmpresa', 'idempresa');
    }
}
