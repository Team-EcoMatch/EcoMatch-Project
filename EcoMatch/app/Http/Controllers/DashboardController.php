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
            ]
        ]);
    }
}