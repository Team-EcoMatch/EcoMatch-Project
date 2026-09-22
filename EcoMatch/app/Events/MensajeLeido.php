<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MensajeLeido implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $idsolicitud,
        public array $idsMensajes,
        public int $idempresa_lectora
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->idsolicitud),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'idsolicitud' => $this->idsolicitud,
            'idsMensajes' => $this->idsMensajes,
            'idempresa_lectora' => $this->idempresa_lectora,
        ];
    }

    public function broadcastAs(): string
    {
        return 'mensaje.leido';
    }
}