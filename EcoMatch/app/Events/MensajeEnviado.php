<?php

namespace App\Events;

use App\Models\Mensaje;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MensajeEnviado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mensaje;

    public function __construct(Mensaje $mensaje)
    {
        $this->mensaje = $mensaje->load('empresaEmisora');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->mensaje->idsolicitud),
        ];
    }

    public function broadcastAs(): string
    {
        return 'mensaje.enviado';
    }
}