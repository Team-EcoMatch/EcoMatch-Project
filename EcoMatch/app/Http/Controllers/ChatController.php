<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Bloqueo;
use App\Models\NotificacionBloqueo;
use App\Models\Solicitud;
use App\Models\Empresa;
use App\Events\MensajeEnviado;
use App\Events\EmpresaBloqueada;
use App\Events\EmpresaDesbloqueada;
use App\Events\MensajeLeido;
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
        $empresaActual = Empresa::find($empresaId);

        $otraEmpresaId = $solicitud->idEmpresaOrigen === $empresaId
            ? $solicitud->idEmpresaDestino
            : $solicitud->idEmpresaOrigen;

        $idsLeidos = Mensaje::where('idsolicitud', $solicitudId)
            ->where('idEmisora', $otraEmpresaId)
            ->where('leido', false)
            ->pluck('idmensajes')
            ->toArray();

        if (!empty($idsLeidos)) {
            Mensaje::where('idsolicitud', $solicitudId)
                ->where('idEmisora', $otraEmpresaId)
                ->where('leido', false)
                ->update(['leido' => true]);

            broadcast(new MensajeLeido($solicitudId, $idsLeidos, $empresaId));
        }

        $mensajes = Mensaje::where('idsolicitud', $solicitudId)
            ->with('empresa_emisora')
            ->orderBy('created_at', 'asc')
            ->get();

        $bloqueadoPorMi = Bloqueo::existeBloqueo($empresaId, $otraEmpresaId);
        $bloqueadoHaciaMi = Bloqueo::existeBloqueo($otraEmpresaId, $empresaId);

        $motivoBloqueo = null;
        if ($bloqueadoHaciaMi) {
            $bloqueo = Bloqueo::where('idempresa_bloqueadora', $otraEmpresaId)
                ->where('idempresa_bloqueada', $empresaId)
                ->first();
            $motivoBloqueo = $bloqueo?->motivo;
        }

        return Inertia::render('Chat/Index', [
            'solicitud' => $solicitud,
            'mensajes' => $mensajes,
            'empresaId' => $empresaId,
            'empresaNombre' => $empresaActual?->nombreEmpresa ?? 'Mi empresa',
            'bloqueadoPorMi' => $bloqueadoPorMi,
            'bloqueadoHaciaMi' => $bloqueadoHaciaMi,
            'motivoBloqueo' => $motivoBloqueo,
            'cloudName' => config('services.cloudinary.cloud_name'),
            'uploadPreset' => config('services.cloudinary.upload_preset'),
        ]);
    }

    public function store(Request $request, int $solicitudId)
    {
        $empresaId = Auth::user()->idempresa;

        $solicitud = Solicitud::findOrFail($solicitudId);
        $otraEmpresaId = $solicitud->idEmpresaOrigen === $empresaId
            ? $solicitud->idEmpresaDestino
            : $solicitud->idEmpresaOrigen;

        $bloqueadoHaciaMi = Bloqueo::existeBloqueo($otraEmpresaId, $empresaId);

        if ($bloqueadoHaciaMi) {
            return back()->with('error', 'No puedes enviar mensajes. Fuiste bloqueado.');
        }

        $empresaParticipa = $solicitud->idEmpresaOrigen === $empresaId
            || $solicitud->idEmpresaDestino === $empresaId;

        if (!$empresaParticipa) {
            abort(403, 'No tienes acceso a este chat.');
        }

        if ($request->tipo === 'texto' || !$request->has('archivo_url')) {
            $request->validate([
                'contenido' => 'required|string|max:1000',
                'tipo' => 'in:texto',
            ]);

            $mensaje = Mensaje::create([
                'idsolicitud' => $solicitudId,
                'idEmisora' => $empresaId,
                'contenido' => $request->contenido,
                'tipo' => 'texto',
            ]);
        } else {
            $request->validate([
                'archivo_url' => 'required|string|max:500|url',
                'archivo_nombre' => 'required|string|max:255',
                'archivo_tamano' => 'required|integer|min:1|max:11534336',
                'tipo' => 'required|in:imagen,archivo',
                'contenido' => 'nullable|string|max:1000',
            ]);

            $cloudName = config('services.cloudinary.cloud_name');
            if (!str_contains($request->archivo_url, "res.cloudinary.com/{$cloudName}/")) {
                return back()->with('error', 'URL de archivo inválida.');
            }

            $extension = strtolower(pathinfo($request->archivo_nombre, PATHINFO_EXTENSION));
            $extensionesPermitidas = [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'webp',
                'pdf',
                'doc',
                'docx',
                'xls',
                'xlsx',
                'ppt',
                'pptx',
                'txt',
                'csv',
                'zip',
                'rar',
                '7z',
            ];

            if (!in_array($extension, $extensionesPermitidas)) {
                return back()->with('error', "Extensión .{$extension} no permitida.");
            }

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

        $mensaje->load('empresa_emisora');
        broadcast(new MensajeEnviado($mensaje, $solicitud))->toOthers();

        return response()->json(['mensaje' => $mensaje]);
    }

    public function bloquear(Request $request, int $solicitudId)
    {
        $empresaId = Auth::user()->idempresa;

        if (!$empresaId) {
            return back()->with('error', 'Tu usuario no tiene empresa asignada.');
        }

        $request->validate([
            'motivo' => 'nullable|string|max:500',
        ]);

        $solicitud = Solicitud::findOrFail($solicitudId);
        $otraEmpresaId = $solicitud->idEmpresaOrigen === $empresaId
            ? $solicitud->idEmpresaDestino
            : $solicitud->idEmpresaOrigen;

        $empresaBloqueadora = Empresa::find($empresaId);
        $nombreBloqueadora = $empresaBloqueadora?->nombreEmpresa ?? 'Una empresa';
        $motivo = $request->motivo ?: 'Sin especificar';

        Bloqueo::firstOrCreate(
            [
                'idempresa_bloqueadora' => $empresaId,
                'idempresa_bloqueada' => $otraEmpresaId,
            ],
            [
                'idsolicitud' => $solicitudId,
                'motivo' => $motivo,
            ]
        );

        NotificacionBloqueo::create([
            'idempresa_destinataria' => $otraEmpresaId,
            'idempresa_bloqueadora' => $empresaId,
            'motivo' => $motivo,
            'idsolicitud' => $solicitudId,
            'leida' => false,
        ]);

        Solicitud::where(function ($q) use ($empresaId, $otraEmpresaId) {
            $q->where(function ($q1) use ($empresaId, $otraEmpresaId) {
                $q1->where('idEmpresaOrigen', $empresaId)
                    ->where('idEmpresaDestino', $otraEmpresaId);
            })->orWhere(function ($q2) use ($empresaId, $otraEmpresaId) {
                $q2->where('idEmpresaOrigen', $otraEmpresaId)
                    ->where('idEmpresaDestino', $empresaId);
            });
        })
            ->where('estado', 'Pendiente')
            ->update(['estado' => 'Cancelada']);

        broadcast(new EmpresaBloqueada(
            $empresaId,
            $otraEmpresaId,
            $nombreBloqueadora,
            $motivo
        ));

        return back()->with('message', 'Has bloqueado a la empresa. Todas las solicitudes pendientes fueron canceladas.');
    }

    public function desbloquear(Request $request, int $solicitudId)
    {
        $empresaId = Auth::user()->idempresa;

        if (!$empresaId) {
            return back()->with('error', 'Tu usuario no tiene empresa asignada.');
        }

        $solicitud = Solicitud::findOrFail($solicitudId);
        $otraEmpresaId = $solicitud->idEmpresaOrigen === $empresaId
            ? $solicitud->idEmpresaDestino
            : $solicitud->idEmpresaOrigen;

        Bloqueo::where('idempresa_bloqueadora', $empresaId)
            ->where('idempresa_bloqueada', $otraEmpresaId)
            ->delete();

        $empresaDesbloqueadora = Empresa::find($empresaId);
        $nombreDesbloqueadora = $empresaDesbloqueadora?->nombreEmpresa ?? 'Una empresa';

        broadcast(new EmpresaDesbloqueada(
            $empresaId,
            $otraEmpresaId,
            $nombreDesbloqueadora
        ));

        return back()->with('message', 'Has desbloqueado a la empresa. Ya pueden comunicarse.');
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
    public function marcarLeido(int $solicitudId)
    {
        $empresaId = Auth::user()->idempresa;

        $solicitud = Solicitud::findOrFail($solicitudId);
        $otraEmpresaId = $solicitud->idEmpresaOrigen === $empresaId
            ? $solicitud->idEmpresaDestino
            : $solicitud->idEmpresaOrigen;

        $idsLeidos = Mensaje::where('idsolicitud', $solicitudId)
            ->where('idEmisora', $otraEmpresaId)
            ->where('leido', false)
            ->pluck('idmensajes')
            ->toArray();

        if (!empty($idsLeidos)) {
            Mensaje::where('idsolicitud', $solicitudId)
                ->where('idEmisora', $otraEmpresaId)
                ->where('leido', false)
                ->update(['leido' => true]);

            broadcast(new MensajeLeido($solicitudId, $idsLeidos, $empresaId));
        }

        return back();
    }
}
