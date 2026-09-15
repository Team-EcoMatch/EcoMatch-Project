<?php

use App\Models\Solicitud;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{solicitudId}', function ($user, $solicitudId) {
    $solicitud = Solicitud::find($solicitudId);

    return $solicitud && (
        $user->idempresa === $solicitud->idEmpresaOrigen ||
        $user->idempresa === $solicitud->idEmpresaDestino
    );
});