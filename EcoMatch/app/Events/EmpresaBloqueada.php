<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmpresaBloqueada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $idempresa_bloqueadora,
        public int $idempresa_bloqueada,
        public string $nombreBloqueadora,
        public ?string $motivo = null
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('empresa.' . $this->idempresa_bloqueada),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'idempresa_bloqueadora' => $this->idempresa_bloqueadora,
            'idempresa_bloqueada' => $this->idempresa_bloqueada,
            'nombreBloqueadora' => $this->nombreBloqueadora,
            'motivo' => $this->motivo,
            'accion' => 'bloqueada',
        ];
    }

    public function broadcastAs(): string
    {
        return 'empresa.bloqueada';
    }
}