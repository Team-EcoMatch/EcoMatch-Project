<?php

namespace App\Http\Controllers;

use App\Exports\ReporteExport;
use App\Models\Empresa;
use App\Models\Publicacion;
use App\Models\Solicitud;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $datos = $this->obtenerDatos($request);

        return Inertia::render('Reportes/Index', $datos);
    }

    public function exportPdf(Request $request)
    {
        $datos = $this->obtenerDatos($request);

        $pdf = Pdf::loadView('reportes.pdf', $datos)
            ->setPaper('a4', 'portrait');

        $nombreArchivo = 'reporte-ecomatch-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($nombreArchivo);
    }

    public function exportExcel(Request $request)
    {
        $datos = $this->obtenerDatos($request);

        $nombreArchivo = 'reporte-ecomatch-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new ReporteExport($datos), $nombreArchivo);
    }

    private function obtenerDatos(Request $request): array
    {
        $user = Auth::user();
        $rol = $user->rol?->tipo;
        $empresaId = $user->idempresa;
        $userId = $user->id;

        $esAdminGlobal = $rol === 'admin';
        $esJefe = $rol === 'Jefe';
        $esEmpleado = $rol === 'Empresa';

        $desde = $request->input('desde', now()->subDays(30)->format('Y-m-d'));
        $hasta = $request->input('hasta', now()->format('Y-m-d'));
        $desdeDate = $desde . ' 00:00:00';
        $hastaDate = $hasta . ' 23:59:59';

        // ===== HELPERS DE FILTRADO POR ROL =====
        $filtrarPublicaciones = function ($query) use ($esJefe, $esEmpleado, $empresaId, $userId) {
            if ($esEmpleado) {
                $query->where('publicaciones.user_id', $userId);
            } elseif ($esJefe) {
                $query->where('publicaciones.idempresa', $empresaId);
            }
            return $query;
        };

        $filtrarSolicitudes = function ($query) use ($esJefe, $esEmpleado, $empresaId, $userId) {
            if ($esEmpleado) {
                $query->where('solicitudes.user_id', $userId);
            } elseif ($esJefe) {
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
            $esEmpleado => 1,
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
            ->select(DB::raw('DATE_FORMAT(publicaciones.created_at, "%Y-%m") as mes'), DB::raw('count(*) as total'))
            ->where('publicaciones.created_at', '>=', now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->pluck('total', 'mes')
            ->toArray();

        $solicitudesPorMes = $filtrarSolicitudes(Solicitud::query())
            ->select(DB::raw('DATE_FORMAT(solicitudes.created_at, "%Y-%m") as mes'), DB::raw('count(*) as total'))
            ->where('solicitudes.created_at', '>=', now()->subMonths(12))
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
            ? Empresa::withCount('publicaciones')
            ->orderBy('publicaciones_count', 'desc')
            ->limit(5)
            ->get(['idempresa', 'nombreEmpresa', 'publicaciones_count'])
            : [];

        // ===== TOP MATERIALES =====
        $topMateriales = $filtrarPublicaciones(Publicacion::query())
            ->select('publicaciones.nombre', DB::raw('count(*) as total'))
            ->groupBy('publicaciones.nombre')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // ===== POR CATEGORÍA =====
        $porCategoria = $filtrarPublicaciones(Publicacion::query())
            ->join('categorias', 'publicaciones.idcategorias', '=', 'categorias.idcategorias')
            ->select('categorias.nombre', DB::raw('count(*) as total'))
            ->groupBy('categorias.nombre')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

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
                })
                ->toArray();
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

        return [
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
            'titulo' => $esAdminGlobal
                ? 'Reporte Global de la Plataforma'
                : ($esJefe ? 'Reporte de mi Empresa' : 'Reporte Personal'),
            'desde' => $desde,
            'hasta' => $hasta,
        ];
    }
}