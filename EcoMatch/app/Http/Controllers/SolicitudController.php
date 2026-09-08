<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Publicacion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SolicitudController extends Controller
{
    public function index()
    {
        $empresaId = Auth::user()->idEmpresa;
        //solictudes de la empresa de destino
        $recibidas = Solicitud::with([
            'publicacion',
            'publicacion.empresa',
            'empresaOrigen'
        ])
            ->where('idEmpresaDestino', $empresaId)
            ->orderBy('created_at', 'desc')
            ->get();

        //solicitudes de la empresa de origen
        $enviadas = Solicitud::with([
            'publicacion',
            'publicacion.empresa',
            'empresaDestino'
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
        $validated = $request->validate([
            'idpublicaciones'  => 'required|integer|exists:publicaciones,idpublicaciones',
            'mensaje'          => 'required|string',
        ]);

        $publicacion = Publicacion::findOrFail($validated['idpublicaciones']);
        $empresaOrigen = Auth::user()->idEmpresa;

        
        //no permite solicitar un material propio
        if ($empresaOrigen === $publicacion->idempresa) {
            return redirect()->back()->withErrors([
                'error' => 'No puede solicitar un materia propio'
            ]);
        }

        //verificar que no exista una solicitud pendiente duplicada
        $existe = Solicitud::where('idpublicaciones', $publicacion->idpublicaciones)
        ->where('idEmpresaOrigen', $empresaOrigen)
        ->where('estado', 'Pendiente')
        ->exists();

        Solicitud::create([
            'idpublicaciones'  => $publicacion->idpublicaciones,
            'idEmpresaOrigen'  => Auth::user()->idEmpresa,
            'idEmpresaDestino' => $publicacion->idempresa,
            'mensaje'          => $validated['mensaje'],
            'estado'           => 'Pendiente'
        ]);

        return redirect()->back()->with('message', 'Solicitud enviada correctamente');
    }

    public function update(Request $request, int $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        $empresaId = Auth::user()->idEmpresa;
        if ($empresaId !== $solicitud->idEmpresaDestino) {
            abort(403, 'No tiene permiso para modificar esta solicitud.');
        }

        $validated = $request->validate([
            'estado' => 'required|in:Aceptado,Rechazado,Completado',
        ]);

        $solicitud->update($validated);

        $mensaje = $validated['estado'] == 'Aceptado' 
            ? 'Solicitud aceptada. Ya puedes contactar a la empresa.' 
            : 'Solicitud rechazada.';

        return redirect()->back()->with('message', $mensaje);
    }
}
