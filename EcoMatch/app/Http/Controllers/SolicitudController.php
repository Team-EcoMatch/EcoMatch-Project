<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::with(['publicacion', 'mensajes'])->get();
        return Inertia::render('Solicitudes/Index', ['solicitudes' => $solicitudes]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idpublicaciones' => 'required|integer|exists:publicaciones,idpublicaciones',
            'idEmpresaOrigen' => 'required|integer',
            'idEmpresaDestino' => 'required|integer',
            'mensaje' => 'required|string',
        ]);
        $validated['estado'] = 'Pendiente';
        $validated['publicaciones_idpublicaciones'] = $validated['idpublicaciones'];

        Solicitud::create($validated);
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
