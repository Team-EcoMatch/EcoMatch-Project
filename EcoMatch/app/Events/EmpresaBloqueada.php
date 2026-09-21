<?php

namespace App\Events;

use App\Models\Solicitud;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmpresaBloqueada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $idsolicitud,
        public int $idempresa_bloqueadora,
        public int $idempresa_bloqueada,
        public string $nombreBloqueadora
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
            'idempresa_bloqueadora' => $this->idempresa_bloqueadora,
            'idempresa_bloqueada' => $this->idempresa_bloqueada,
            'nombreBloqueadora' => $this->nombreBloqueadora,
            'accion' => 'bloqueada',
        ];
    }

    public function broadcastAs(): string
    {
        return 'empresa.bloqueada';
    }
}