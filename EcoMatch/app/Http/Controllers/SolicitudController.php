<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Publicacion;
use App\Models\Bloqueo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SolicitudController extends Controller
{
    public function index()
    {
        $empresaId = Auth::user()->idempresa;

        $recibidas = Solicitud::with([
            'publicacion',
            'publicacion.empresa',
            'empresa_origen'
        ])
            ->where('idEmpresaDestino', $empresaId)
            ->orderBy('created_at', 'desc')
            ->get();

        $enviadas = Solicitud::with([
            'publicacion',
            'publicacion.empresa',
            'empresa_destino'
        ])
            ->where('idEmpresaOrigen', $empresaId)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Solicitudes/Index', [
            'recibidas' => $recibidas,
            'enviadas'  => $enviadas,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (is_null($user->idempresa)) {
            return redirect()->back()->withErrors([
                'error' => 'No tienes una empresa asociada. No puedes enviar solicitudes.'
            ]);
        }

        $validated = $request->validate([
            'idpublicaciones' => 'required|integer|exists:publicaciones,idpublicaciones',
            'mensaje'         => 'required|string|max:500',
            'cantidad'        => 'required|numeric|min:0.01',
        ]);

        $publicacion = Publicacion::findOrFail($validated['idpublicaciones']);
        $empresaOrigen = $user->idempresa;
        $empresaDestino = $publicacion->idempresa;

        if ($empresaOrigen === $empresaDestino) {
            return redirect()->back()->withErrors([
                'error' => 'No puedes solicitar tu propio material.'
            ]);
        }

        if ($publicacion->cantidad < $validated['cantidad']) {
            return redirect()->back()->withErrors([
                'error' => "No hay suficiente stock disponible. Disponible: {$publicacion->cantidad} {$publicacion->unidadMedida}, Solicitado: {$validated['cantidad']} {$publicacion->unidadMedida}."
            ]);
        }

        $bloqueada = Bloqueo::where(function ($q) use ($empresaOrigen, $empresaDestino) {
            $q->where('idempresa_bloqueadora', $empresaDestino)
                ->where('idempresa_bloqueada', $empresaOrigen);
        })->exists();

        if ($bloqueada) {
            return redirect()->back()->withErrors([
                'error' => 'No puedes enviar solicitudes a esta empresa. Has sido bloqueado.'
            ]);
        }

        $existe = Solicitud::where('idpublicaciones', $publicacion->idpublicaciones)
            ->where('idEmpresaOrigen', $empresaOrigen)
            ->where('estado', 'Pendiente')
            ->exists();

        if ($existe) {
            return redirect()->back()->withErrors([
                'error' => 'Ya tienes una solicitud pendiente para este material.'
            ]);
        }

        Solicitud::create([
            'idpublicaciones'  => $publicacion->idpublicaciones,
            'idEmpresaOrigen'  => $empresaOrigen,
            'idEmpresaDestino' => $empresaDestino,
            'user_id'          => Auth::id(),
            'mensaje'          => $validated['mensaje'],
            'cantidad'         => $validated['cantidad'],
            'estado'           => 'Pendiente'
        ]);

        return redirect()->back()->with('message', 'Solicitud enviada correctamente.');
    }

    public function update(Request $request, int $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        $empresaId = Auth::user()->idempresa;
        if ($empresaId !== $solicitud->idEmpresaDestino) {
            abort(403, 'No tiene permiso para modificar esta solicitud.');
        }

        $bloqueada = Bloqueo::where(function ($q) use ($empresaId, $solicitud) {
            $q->where('idempresa_bloqueadora', $solicitud->idEmpresaOrigen)
                ->where('idempresa_bloqueada', $empresaId);
        })->exists();

        if ($bloqueada) {
            return redirect()->back()->withErrors([
                'error' => 'No puedes aceptar solicitudes de esta empresa. Te ha bloqueado.'
            ]);
        }

        $validated = $request->validate([
            'estado' => 'required|in:Aceptado,Rechazado,Completado',
        ]);

        if ($validated['estado'] === 'Aceptado') {
            $publicacion = Publicacion::findOrFail($solicitud->idpublicaciones);

            if ($publicacion->cantidad < $solicitud->cantidad) {
                return redirect()->back()->withErrors([
                    'error' => 'El stock disponible ya no es suficiente para esta solicitud.'
                ]);
            }

            $publicacion->cantidad -= $solicitud->cantidad;
            $publicacion->save();

            if ($publicacion->cantidad <= 0) {
                $publicacion->estado = 'Agotado';
                $publicacion->save();
            }
        }

        $solicitud->update($validated);

        return redirect()->back()->with('message', "Solicitud {$validated['estado']} correctamente.");
    }

    public function historial()
    {
        $idEmpresa = Auth::user()->idempresa;

        $historial = Solicitud::with(['publicacion.empresa', 'empresa_origen', 'empresa_destino'])
            ->where(function ($query) use ($idEmpresa) {
                $query->where('idEmpresaOrigen', $idEmpresa)
                    ->orWhere('idEmpresaDestino', $idEmpresa);
            })
            ->where('estado', '!=', 'Pendiente')
            ->latest('idsolicitud')
            ->get();

        return Inertia::render('Historial/Index', [
            'historial' => $historial
        ]);
    }
}