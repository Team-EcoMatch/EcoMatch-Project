<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index(int $solicitudId)
    {
        $solicitud = Solicitud::with(['empresaOrigen', 'empresaDestino', 'publicacion'])
            ->findOrFail($solicitudId);

        $user = Auth::user();
        $empresaId = $user->idempresa;

        // Verificar que el usuario pertenezca a una de las dos empresas
        if ($empresaId !== $solicitud->idEmpresaOrigen && $empresaId !== $solicitud->idEmpresaDestino) {
            abort(403, 'No tienes acceso a este chat.');
        }

        // Verificar que la solicitud esté aceptada o completada
        if (!in_array($solicitud->estado, ['Aceptado', 'Completado'])) {
            return redirect()->route('solicitudes.index')->with('error', 'Este chat aún no está disponible.');
        }

        // Obtener mensajes
        $mensajes = Mensaje::where('idsolicitud', $solicitudId)
            ->with('empresaEmisora')
            ->orderBy('created_at', 'asc')
            ->get();

        // Marcar mensajes como leídos (opcional)
        // Mensaje::where('idsolicitud', $solicitudId)->where('idEmisora', '!=', $empresaId)->update(['leido' => 1]);

        return Inertia::render('Chat/Index', [
            'solicitud' => $solicitud,
            'mensajes' => $mensajes,
            'empresaId' => $empresaId,
        ]);
    }

    public function store(Request $request, int $solicitudId)
    {
        $solicitud = Solicitud::findOrFail($solicitudId);
        $user = Auth::user();
        $empresaId = $user->idempresa;

        // Validar pertenencia
        if ($empresaId !== $solicitud->idEmpresaOrigen && $empresaId !== $solicitud->idEmpresaDestino) {
            abort(403, 'No tienes acceso a este chat.');
        }

        // Verificar que la solicitud esté aceptada o completada
        if (!in_array($solicitud->estado, ['Aceptado', 'Completado'])) {
            return back()->withErrors(['error' => 'Este chat aún no está disponible.']);
        }

        $request->validate([
            'contenido' => 'required|string|max:500',
        ]);

        Mensaje::create([
            'idsolicitud' => $solicitudId,
            'idEmisora' => $empresaId,
            'contenido' => $request->contenido,
            'leido' => 0,
        ]);

        return back()->with('message', 'Mensaje enviado.');
    }
}