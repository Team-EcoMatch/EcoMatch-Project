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
        'motivo',
        'fecha_bloqueo',
    ];

    public function bloqueadora(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'idempresa_bloqueadora', 'idempresa');
    }

    public function bloqueada(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'idempresa_bloqueada', 'idempresa');
    }

    public static function existeBloqueo(int $bloqueadora, int $bloqueada): bool
    {
        return self::where('idempresa_bloqueadora', $bloqueadora)
            ->where('idempresa_bloqueada', $bloqueada)
            ->exists();
    }
}