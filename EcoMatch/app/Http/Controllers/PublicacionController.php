<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Publicacion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PublicacionController extends Controller
{
    public function index()
    {
        $publicaciones = Publicacion::with(['empresa', 'categoria'])
                                    ->where('estado', 'Disponible')
                                    ->latest('idpublicaciones')
                                    ->get();

        return Inertia::render('Publicaciones/Index', [
            'publicaciones' => $publicaciones
        ]);
    }

    public function create()
    {
        // Traer solo las categorias de la empresa del usuario autenticado
        $categorias = Categoria::where('idempresa', Auth::user()->idEmpresa)->get();
        return Inertia::render('Publicaciones/Create', ['categorias' => $categorias]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idcategorias'  => 'required|integer|exists:categorias,idcategorias',
            'nombre'        => 'required|string|max:100',
            'descripcion'   => 'required|string',
            'cantidad'      => 'required|numeric',
            'unidadMedida'  => 'required|in:Kilogramos,Toneladas,Litros,Metros,Unidades,Galones,Gramos',
            'frecuencia'    => 'required|in:Único,Semanal,Mensual,Anual',
            'urlImagen'     => 'nullable|string|max:500',
        ]);

        $validated['idempresa'] = Auth::user()->idEmpresa;
        $validated['estado'] = 'Pendiente';

        Publicacion::create($validated);
        return redirect()->back()->with('message', 'Publicación enviada para aprobación');
    }

    public function edit(int $id)
    {
        $publicacion = Publicacion::findOrFail($id);
        $categorias = Categoria::where('idempresa', Auth::user()->idEmpresa)->get();
        
        return Inertia::render('Publicaciones/Edit',[
            'publicacion' => $publicacion,
            'categorias'  => $categorias,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $publicacion = Publicacion::findOrFail($id);
        $validated = $request->validate([
            'idcategorias'  => 'required|integer',
            'nombre'        => 'required|string|max:100',
            'descripcion'   => 'required|string',
            'cantidad'      => 'required|numeric',
            'unidadMedida'  => 'required|in:Kilogramos,Toneladas,Litros,Metros,Unidades,Galones,Gramos',
            'frecuencia'    => 'required|in:Único,Semanal,Mensual,Anual',
            'estado'        => 'required|in:Disponible,Inactivo,Agotado,Reservado,Intercambiado,Pendiente',
            'urlImagen'     => 'nullable|string|max:500',
        ]);

        $publicacion->update($validated);
        return redirect()->route('publicaciones.index')->with('message', 'Publicación actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        $publicacion = Publicacion::findOrFail($id);
        $publicacion->delete();
        return redirect()->back()->with('message', 'Publicación eliminada correctamente.');
    }

    public function approve(int $id)
    {
        $publicacion = Publicacion::findOrFail($id);
        $publicacion->estado = 'Disponible';
        $publicacion->save();

        return redirect()->back()->with('message', 'Publicación aprobada y ahora es visible');
    }
}