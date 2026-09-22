<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificacionBloqueo extends Model
{
    protected $table = 'notificaciones_bloqueo';

    protected $primaryKey = 'idnotificacion';

    public $timestamps = true;

    protected $fillable = [
        'idempresa_destinataria',
        'idempresa_bloqueadora',
        'motivo',
        'idsolicitud',
        'leida',
    ];

    protected $casts = [
        'leida' => 'boolean',
    ];

    public function destinataria(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'idempresa_destinataria', 'idempresa');
    }

    public function bloqueadora(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'idempresa_bloqueadora', 'idempresa');
    }
}