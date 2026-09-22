<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mensaje extends Model
{
    protected $table = 'mensajes';

    protected $primaryKey = 'idmensajes';

    public $timestamps = true;

    protected $fillable = [
        'idEmisora',
        'contenido',
        'tipo',
        'archivo_url',
        'archivo_nombre',
        'archivo_tamano',
        'leido',
        'idsolicitud',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'archivo_tamano' => 'integer',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'idsolicitud', 'idsolicitud');
    }

    public function empresa_emisora(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'idEmisora', 'idempresa');
    }
}