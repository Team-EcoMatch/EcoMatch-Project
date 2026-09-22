<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        $notificacionesBloqueo = [];
        if ($user && $user->idempresa) {
            $notificacionesBloqueo = \App\Models\NotificacionBloqueo::with('bloqueadora')
                ->where('idempresa_destinataria', $user->idempresa)
                ->where('leida', false)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($n) {
                    return [
                        'idnotificacion' => $n->idnotificacion,
                        'nombreBloqueadora' => $n->bloqueadora?->nombreEmpresa ?? 'Una empresa',
                        'motivo' => $n->motivo,
                        'fecha' => $n->created_at?->format('d/m/Y H:i'),
                    ];
                })
                ->toArray();
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user ? array_merge($user->only(['id', 'name', 'email', 'current_team_id', 'idempresa']), [
                    'rol' => $user->rol ? $user->rol->tipo : null,
                    'nombreEmpresa' => $user->empresa ? $user->empresa->nombreEmpresa : null
                ]) : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'currentTeam' => fn() => $user?->currentTeam ? $user->toUserTeam($user->currentTeam) : null,
            'teams' => fn() => $user?->toUserTeams(includeCurrent: true) ?? [],
            'message' => fn() => $request->session()->get('message'),
            'notificacionesBloqueo' => $notificacionesBloqueo,
            'reverbConfig' => [
                'key' => config('broadcasting.connections.reverb.key'),
                'host' => config('broadcasting.connections.reverb.options.host') ?: parse_url(config('app.url'), PHP_URL_HOST),
                'port' => config('broadcasting.connections.reverb.options.port') ?: 8080,
                'scheme' => config('broadcasting.connections.reverb.options.scheme') ?: 'http',
            ],
        ];
    }
}
