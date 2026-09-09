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
        $empresaId = Auth::user()->idempresa;

        $recibidas = Solicitud::with([
            'publicacion',
            'publicacion.empresa',
            'empresaOrigen'
        ])
            ->where('idEmpresaDestino', $empresaId)
            ->orderBy('created_at', 'desc')
            ->get();

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
        $user = Auth::user();

        // Validar que el usuario tenga empresa
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

        // No permitir solicitar material propio
        if ($empresaOrigen === $publicacion->idempresa) {
            return redirect()->back()->withErrors([
                'error' => 'No puedes solicitar tu propio material.'
            ]);
        }
        //validacion  para verificar stock disponble
        if ($publicacion->cantidad < $validated['cantidad']) {
            return redirect()->back()->withErrors([
                'error' => "No hay suficiente stock disponible. Disponible: {$publicacion->cantidad} {$publicacion->unidadMedida}, Solicitado: {$validated['cantidad']} {$publicacion->unidadMedida}."
            ]);
        }
        // Verificar duplicado
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
            'idEmpresaDestino' => $publicacion->idempresa,
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

        $validated = $request->validate([
            'estado' => 'required|in:Aceptado,Rechazado,Completado',
        ]);

        if ($validated['estado'] === 'Aceptado') {
            $publicacion = Publicacion::findOrFail($solicitud->idpublicaciones);

            if ($publicacion->cantidad < $solicitud->cantidad) {
                return redirect()->back()->withErrors([
                    'error' => 'El stock disponible ya no es suficiente para esta solicitud. Por favor contactar con el solicitante.'
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
}
