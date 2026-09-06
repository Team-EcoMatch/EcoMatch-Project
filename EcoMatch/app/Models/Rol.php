<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'idroles';

    protected $fillable = [
        'tipo', 'idempresa'
    ];


    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'idempresa');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'idRol', 'idroles');
    }
}