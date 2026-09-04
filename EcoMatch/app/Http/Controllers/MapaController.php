<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Inertia\Inertia;

class MapaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->withCount(['publicaciones' => function ($query) {
                $query->where('estado', 'Disponible');
            }])
            ->having('publicaciones_count', '>', 0)
            ->get();

        return Inertia::render('Mapa/Index', [
            'empresas' => $empresas
        ]);
    }
}