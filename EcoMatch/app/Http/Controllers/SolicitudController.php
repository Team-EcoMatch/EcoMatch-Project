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
        $idEmpresa = Auth::user()->idempresa;

        $solicitudes = Solicitud::with(['publicacion', 'empresaOrigen'])
            ->where('idEmpresaDestino', $idEmpresa)
            ->latest('idsolicitud')
            ->get();

        return Inertia::render('Solicitudes/Index', [
            'solicitudes' => $solicitudes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idpublicaciones'  => 'required|integer|exists:publicaciones,idpublicaciones',
            'mensaje'          => 'required|string',
        ]);
        
        $publicacion = Publicacion::findOrFail($validated['idpublicaciones']);

        Solicitud::create([
            'idpublicaciones'  => $publicacion->idpublicaciones,
            'idEmpresaOrigen'  => Auth::user()->idempresa,
            'idEmpresaDestino' => $publicacion->idempresa,
            'mensaje'          => $validated['mensaje'],
            'estado'           => 'Pendiente'
        ]);

        return redirect()->back()->with('message', 'Solicitud enviada correctamente');
    }

    public function update(Request $request, int $id)
    {
        $solicitud = Solicitud::findOrFail($id);
        
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