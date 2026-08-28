<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'idroles';

    protected $fillable = [
        'tipo', 'empresa_idempresa'
    ];


    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_idempresa');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'roles_idroles');
    }
}
