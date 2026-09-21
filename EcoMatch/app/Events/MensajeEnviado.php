<?php

namespace App\Events;

use App\Models\Mensaje;
use App\Models\Solicitud;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MensajeEnviado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Mensaje $mensaje,
        public Solicitud $solicitud
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->solicitud->idsolicitud),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'mensaje' => [
                'idmensajes' => $this->mensaje->idmensajes,
                'idEmisora' => $this->mensaje->idEmisora,
                'contenido' => $this->mensaje->contenido,
                'tipo' => $this->mensaje->tipo,
                'archivo_url' => $this->mensaje->archivo_url,
                'archivo_nombre' => $this->mensaje->archivo_nombre,
                'archivo_tamano' => $this->mensaje->archivo_tamano,
                'created_at' => $this->mensaje->created_at,
                'empresa_emisora' => $this->mensaje->empresaEmisora,
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'mensaje.enviado';
    }
}