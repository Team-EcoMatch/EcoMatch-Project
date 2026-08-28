<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MapaController extends Controller
{
    public function index()
    {
        $publicaciones = Publicacion::with('empresa')
            ->where('estado', 'Disponible')
            ->whereHas('empresa', function($query){
                $query->whereNotNull('latitud')->whereNotNull('longitud');
            })
            ->get();

            return Inertia::render('Mapa/Index', ['publicaciones' => $publicaciones]);
    }

}
