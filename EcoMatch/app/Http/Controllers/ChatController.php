<?php

namespace App\Http\Controllers;

use App\Events\EmpresaBloqueada;
use App\Events\EmpresaDesbloqueada;
use App\Models\Mensaje;
use App\Models\Bloqueo;
use App\Models\Empresa;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(int $solicitudId)
    {
        $solicitud = Solicitud::with(['empresa_origen', 'empresa_destino', 'publicacion'])
            ->findOrFail($solicitudId);

        $empresaId = Auth::user()->idempresa;

        $mensajes = Mensaje::where('idsolicitud', $solicitudId)
            ->with('empresaEmisora')
            ->orderBy('created_at', 'asc')
            ->get();

        $bloqueadoPorMi = Bloqueo::where('idsolicitud', $solicitudId)
            ->where('idempresa_bloqueadora', $empresaId)
            ->exists();

        $bloqueadoHaciaMi = Bloqueo::where('idsolicitud', $solicitudId)
            ->where('idempresa_bloqueada', $empresaId)
            ->exists();

        return Inertia::render('Chat/Index', [
            'solicitud' => $solicitud,
            'mensajes' => $mensajes,
            'empresaId' => $empresaId,
            'bloqueadoPorMi' => $bloqueadoPorMi,
            'bloqueadoHaciaMi' => $bloqueadoHaciaMi,
            'cloudName' => config('services.cloudinary.cloud_name'),
            'uploadPreset' => config('services.cloudinary.upload_preset'),
        ]);
    }

    public function store(Request $request, int $solicitudId)
    {
        $empresaId = Auth::user()->idempresa;

        $bloqueadoHaciaMi = Bloqueo::where('idsolicitud', $solicitudId)
            ->where('idempresa_bloqueada', $empresaId)
            ->exists();

        if ($bloqueadoHaciaMi) {
            return back()->with('error', 'No puedes enviar mensajes. Fuiste bloqueado.');
        }

        if ($request->tipo === 'texto' || !$request->has('archivo_url')) {
            $request->validate([
                'contenido' => 'required|string|max:1000',
            ]);

            $mensaje = Mensaje::create([
                'idsolicitud' => $solicitudId,
                'idEmisora' => $empresaId,
                'contenido' => $request->contenido,
                'tipo' => 'texto',
            ]);
        } else {
            $request->validate([
                'archivo_url' => 'required|string|max:500',
                'archivo_nombre' => 'required|string|max:255',
                'archivo_tamano' => 'required|integer',
                'tipo' => 'required|in:imagen,archivo',
            ]);

            $mensaje = Mensaje::create([
                'idsolicitud' => $solicitudId,
                'idEmisora' => $empresaId,
                'contenido' => $request->contenido ?? '',
                'tipo' => $request->tipo,
                'archivo_url' => $request->archivo_url,
                'archivo_nombre' => $request->archivo_nombre,
                'archivo_tamano' => $request->archivo_tamano,
            ]);
        }

        $mensaje->load('empresaEmisora');
        broadcast(new \App\Events\MensajeEnviado($mensaje, Solicitud::find($solicitudId)))->toOthers();

        return back()->with('message', 'Mensaje enviado.');
    }

    public function listaChats()
    {
        $empresaId = Auth::user()->idempresa;

        $chats = Solicitud::where(function ($q) use ($empresaId) {
            $q->where('idEmpresaOrigen', $empresaId)
                ->orWhere('idEmpresaDestino', $empresaId);
        })
            ->whereIn('estado', ['Aceptado', 'Completado'])
            ->with(['empresa_origen', 'empresa_destino', 'publicacion', 'mensajes' => function ($q) {
                $q->orderBy('created_at', 'desc')->limit(1);
            }])
            ->orderByDesc('updated_at')
            ->get();

        return Inertia::render('Chat/Lista', [
            'chats' => $chats,
            'empresaId' => $empresaId,
        ]);
    }

    public function bloquear(Request $request, int $solicitudId)
    {
        $empresaId = Auth::user()->idempresa;

        // ✅ Validar que el usuario tenga empresa asignada
        if (!$empresaId) {
            return back()->with('error', 'Tu usuario no tiene empresa asignada. Contacta al administrador.');
        }

        $solicitud = Solicitud::findOrFail($solicitudId);

        // ✅ Validar que la empresa participe en la solicitud
        $empresaParticipa = $solicitud->idEmpresaOrigen === $empresaId
            || $solicitud->idEmpresaDestino === $empresaId;

        if (!$empresaParticipa) {
            abort(403, 'No tienes permiso para bloquear en este chat.');
        }

        $otraEmpresaId = $solicitud->idEmpresaOrigen === $empresaId
            ? $solicitud->idEmpresaDestino
            : $solicitud->idEmpresaOrigen;

        Bloqueo::firstOrCreate([
            'idsolicitud' => $solicitudId,
            'idempresa_bloqueadora' => $empresaId,
            'idempresa_bloqueada' => $otraEmpresaId,
        ]);

        $empresaBloqueadora = \App\Models\Empresa::find($empresaId);
        $nombreBloqueadora = $empresaBloqueadora?->nombreEmpresa ?? 'Una empresa';

        broadcast(new \App\Events\EmpresaBloqueada(
            $solicitudId,
            $empresaId,
            $otraEmpresaId,
            $nombreBloqueadora
        ));

        return back()->with('message', 'Has bloqueado a la empresa.');
    }

    public function desbloquear(Request $request, int $solicitudId)
    {
        $empresaId = Auth::user()->idempresa;

        if (!$empresaId) {
            return back()->with('error', 'Tu usuario no tiene empresa asignada.');
        }

        Bloqueo::where('idsolicitud', $solicitudId)
            ->where('idempresa_bloqueadora', $empresaId)
            ->delete();

        $solicitud = Solicitud::findOrFail($solicitudId);
        $otraEmpresaId = $solicitud->idEmpresaOrigen === $empresaId
            ? $solicitud->idEmpresaDestino
            : $solicitud->idEmpresaOrigen;

        $empresaDesbloqueadora = \App\Models\Empresa::find($empresaId);
        $nombreDesbloqueadora = $empresaDesbloqueadora?->nombreEmpresa ?? 'Una empresa';

        broadcast(new \App\Events\EmpresaDesbloqueada(
            $solicitudId,
            $empresaId,
            $otraEmpresaId,
            $nombreDesbloqueadora
        ));

        return back()->with('message', 'Has desbloqueado a la empresa.');
    }
}
