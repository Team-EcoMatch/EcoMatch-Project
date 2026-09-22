<?php

use App\Models\Solicitud;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{solicitudId}', function ($user, $solicitudId) {
    $solicitud = Solicitud::find($solicitudId);
    return $solicitud && (
        $user->idempresa === $solicitud->idEmpresaOrigen ||
        $user->idempresa === $solicitud->idEmpresaDestino
    );
}, ['guards' => ['web']]);

Broadcast::channel('empresa.{empresaId}', function ($user, $empresaId) {
    return (int) $user->idempresa === (int) $empresaId;
}, ['guards' => ['web']]);