<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bloqueo extends Model
{
    protected $table = 'bloqueos';

    protected $primaryKey = 'idbloqueo';

    public $timestamps = true;

    protected $fillable = [
        'idsolicitud',
        'idempresa_bloqueadora',
        'idempresa_bloqueada',
        'fecha_bloqueo',
    ];

    public function bloqueadora()
    {
        return $this->belongsTo(Empresa::class, 'idempresa_bloqueadora', 'idempresa');
    }

    public function bloqueada()
    {
        return $this->belongsTo(Empresa::class, 'idempresa_bloqueada', 'idempresa');
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'idsolicitud', 'idsolicitud');
    }
}