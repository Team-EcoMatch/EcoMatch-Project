<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::with(['publicacion.empresa', 'empresaOrigen', 'empresaDestino', 'mensajes'])->get();
        return Inertia::render('Solicitudes/Index', ['solicitudes' => $solicitudes]);
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
        $validated = $request->validate([
            'estado' => 'required|in:Pendiente,Aceptado,Rechazado,Completado',
        ]);
        
        $solicitud->update($validated);
        return redirect()->back()->with('message', 'Estado de solicitud actualizado correctamente');
    }
}