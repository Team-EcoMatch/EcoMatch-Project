<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Publicacion;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $rol = $user->rol?->tipo; // 'admin', 'Jefe', 'Empresa'
        $empresaId = $user->idempresa;
        $userId = $user->id;

        // Determinar alcance
        $esAdminGlobal = $rol === 'admin';
        $esJefe = $rol === 'Jefe';
        $esEmpleado = $rol === 'Empresa';

        // Filtros de fecha
        $desde = $request->input('desde', now()->subDays(30)->format('Y-m-d'));
        $hasta = $request->input('hasta', now()->format('Y-m-d'));
        $desdeDate = $desde . ' 00:00:00';
        $hastaDate = $hasta . ' 23:59:59';

        $filtrarPublicaciones = function ($query) use ($esAdminGlobal, $esJefe, $esEmpleado, $empresaId, $userId) {
            if ($esEmpleado) {
                // Solo sus propias publicaciones
                $query->where('publicaciones.user_id', $userId);
            } elseif ($esJefe) {
                // Todas las de su empresa
                $query->where('publicaciones.idempresa', $empresaId);
            }
            // Admin global: sin filtro
            return $query;
        };

        $filtrarSolicitudes = function ($query) use ($esAdminGlobal, $esJefe, $esEmpleado, $empresaId, $userId) {
            if ($esEmpleado) {
                // Solo las que él creó
                $query->where('solicitudes.user_id', $userId);
            } elseif ($esJefe) {
                // Todas donde la empresa participa
                $query->where(function ($q) use ($empresaId) {
                    $q->where('solicitudes.idEmpresaOrigen', $empresaId)
                        ->orWhere('solicitudes.idEmpresaDestino', $empresaId);
                });
            }
            return $query;
        };

        // ===== INDICADORES GENERALES =====
        $totalEmpresas = $esAdminGlobal ? Empresa::count() : 0;
        $totalUsuarios = match (true) {
            $esAdminGlobal => User::count(),
            $esJefe => User::where('idempresa', $empresaId)->count(),
            $esEmpleado => 1, // solo él mismo
        };

        $totalPublicaciones = $filtrarPublicaciones(Publicacion::query())->count();
        $totalSolicitudes = $filtrarSolicitudes(Solicitud::query())->count();

        // ===== POR ESTADO =====
        $publicacionesPorEstado = $filtrarPublicaciones(Publicacion::query())
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        $solicitudesPorEstado = $filtrarSolicitudes(Solicitud::query())
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // ===== TENDENCIA MENSUAL =====
        $publicacionesPorMes = $filtrarPublicaciones(Publicacion::query())
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->pluck('total', 'mes')
            ->toArray();

        $solicitudesPorMes = $filtrarSolicitudes(Solicitud::query())
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->pluck('total', 'mes')
            ->toArray();

        // ===== MATERIALES INTERCAMBIADOS =====
        $materialesIntercambiados = $filtrarSolicitudes(
            Solicitud::whereIn('solicitudes.estado', ['Aceptado', 'Completado'])
        )
            ->join('publicaciones', 'solicitudes.idpublicaciones', '=', 'publicaciones.idpublicaciones')
            ->select(
                'publicaciones.unidadMedida',
                DB::raw('SUM(solicitudes.cantidad) as total_cantidad'),
                DB::raw('count(*) as total_intercambios')
            )
            ->groupBy('publicaciones.unidadMedida')
            ->get();

        // ===== TASA DE ACEPTACIÓN =====
        $totales = $filtrarSolicitudes(Solicitud::query())->count();
        $aceptadas = $filtrarSolicitudes(Solicitud::whereIn('solicitudes.estado', ['Aceptado', 'Completado']))->count();
        $tasaAceptacion = $totales > 0 ? round(($aceptadas / $totales) * 100, 2) : 0;

        // ===== TOP EMPRESAS (solo admin global) =====
        $topEmpresas = $esAdminGlobal
            ? Empresa::withCount('publicaciones')->orderBy('publicaciones_count', 'desc')->limit(5)
            ->get(['idempresa', 'nombreEmpresa', 'publicaciones_count'])
            : [];

        // ===== TOP MATERIALES =====
        $topMateriales = $filtrarPublicaciones(Publicacion::query())
            ->select('nombre', DB::raw('count(*) as total'))
            ->groupBy('nombre')->orderBy('total', 'desc')->limit(5)->get();

        // ===== POR CATEGORÍA =====
        $porCategoria = $filtrarPublicaciones(Publicacion::query())
            ->join('categorias', 'publicaciones.idcategorias', '=', 'categorias.idcategorias')
            ->select('categorias.nombre', DB::raw('count(*) as total'))
            ->groupBy('categorias.nombre')->orderBy('total', 'desc')->limit(10)->get();

        // ===== DESEMPEÑO POR EMPLEADO (solo Jefe) =====
        $desempenoEmpleados = [];
        if ($esJefe) {
            $desempenoEmpleados = User::where('idempresa', $empresaId)
                ->withCount([
                    'publicaciones as total_publicaciones',
                    'solicitudesEnviadas as total_solicitudes',
                ])
                ->get(['id', 'name', 'email'])
                ->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'nombre' => $u->name,
                        'email' => $u->email,
                        'publicaciones' => $u->total_publicaciones,
                        'solicitudes' => $u->total_solicitudes,
                    ];
                });
        }

        // ===== PERIODO FILTRADO =====
        $periodo = [
            'publicaciones' => $filtrarPublicaciones(Publicacion::query())->whereBetween('publicaciones.created_at', [$desdeDate, $hastaDate])->count(),
            'solicitudes' => $filtrarSolicitudes(Solicitud::query())->whereBetween('solicitudes.created_at', [$desdeDate, $hastaDate])->count(),
            'empresas_nuevas' => $esAdminGlobal ? Empresa::whereBetween('created_at', [$desdeDate, $hastaDate])->count() : 0,
            'usuarios_nuevos' => $esAdminGlobal
                ? User::whereBetween('created_at', [$desdeDate, $hastaDate])->count()
                : User::where('idempresa', $empresaId)->whereBetween('created_at', [$desdeDate, $hastaDate])->count(),
        ];

        return Inertia::render('Reportes/Index', [
            'rol' => $rol,
            'esAdmin' => $esAdminGlobal,
            'esJefe' => $esJefe,
            'esEmpleado' => $esEmpleado,
            'indicadores' => [
                'totalEmpresas' => $totalEmpresas,
                'totalUsuarios' => $totalUsuarios,
                'totalPublicaciones' => $totalPublicaciones,
                'totalSolicitudes' => $totalSolicitudes,
                'tasaAceptacion' => $tasaAceptacion,
            ],
            'publicacionesPorEstado' => $publicacionesPorEstado,
            'solicitudesPorEstado' => $solicitudesPorEstado,
            'publicacionesPorMes' => $publicacionesPorMes,
            'solicitudesPorMes' => $solicitudesPorMes,
            'materialesIntercambiados' => $materialesIntercambiados,
            'topEmpresas' => $topEmpresas,
            'topMateriales' => $topMateriales,
            'porCategoria' => $porCategoria,
            'desempenoEmpleados' => $desempenoEmpleados,
            'periodo' => $periodo,
            'filtros' => ['desde' => $desde, 'hasta' => $hasta],
        ]);
    }
}
