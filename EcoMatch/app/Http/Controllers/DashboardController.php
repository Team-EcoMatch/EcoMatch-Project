<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Rol;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $idEmpresa = Auth::user()->idempresa;
        $idUsuario = Auth::id();

        $publicacionesActivas = Publicacion::where('idempresa', $idEmpresa)->where('estado', 'Disponible')->count();
        $publicacionesPendientes = Publicacion::where('idempresa', $idEmpresa)->where('estado', 'Pendiente')->count();
        $solicitudesPendientes = Solicitud::where('idEmpresaDestino', $idEmpresa)->where('estado', 'Pendiente')->count();
        $mercadoTotal = Publicacion::where('estado', 'Disponible')->count();
        $totalCategorias = Categoria::where('idempresa', $idEmpresa)->count();
        
        $rolEmpleado = Rol::where('idempresa', $idEmpresa)->where('tipo', 'Empresa')->first();
        $totalEmpleados = 0;
        if ($rolEmpleado) {
            $totalEmpleados = User::where('idempresa', $idEmpresa)->where('idRol', $rolEmpleado->idroles)->count();
        }

        $misSolicitudesEnviadas = Solicitud::where('idEmpresaOrigen', $idEmpresa)->where('estado', 'Pendiente')->count();
        $misPublicacionesTotales = Publicacion::where('idempresa', $idEmpresa)->count();

        $materialesPorCategoria = Publicacion::where('publicaciones.idempresa', $idEmpresa)
            ->join('categorias', 'publicaciones.idcategorias', '=', 'categorias.idcategorias')
            ->select('categorias.nombre', DB::raw('count(publicaciones.idpublicaciones) as total'))
            ->groupBy('categorias.nombre')
            ->pluck('total', 'categorias.nombre')
            ->toArray();

        $publicacionesPorDia = Publicacion::where('idempresa', $idEmpresa)
            ->where('created_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $diasLabels = [];
        $diasData = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            $fechaString = $fecha->format('Y-m-d');
            $diasLabels[] = $fecha->format('d/m');
            $diasData[] = $publicacionesPorDia[$fechaString] ?? 0;
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'activas' => $publicacionesActivas,
                'pendientes' => $publicacionesPendientes,
                'solicitudes' => $solicitudesPendientes,
                'mercado' => $mercadoTotal,
                'empleados' => $totalEmpleados,
                'categorias' => $totalCategorias,
                'misSolicitudes' => $misSolicitudesEnviadas,
                'misPublicaciones' => $misPublicacionesTotales,
            ],
            'charts' => [
                'categorias' => [
                    'labels' => array_keys($materialesPorCategoria),
                    'data' => array_values($materialesPorCategoria),
                ],
                'dias' => [
                    'labels' => $diasLabels,
                    'data' => $diasData,
                ]
            ]
        ]);
    }
}