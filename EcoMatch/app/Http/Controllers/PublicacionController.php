<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PublicacionController extends Controller
{
        public function index()
    {
        $idEmpresa = Auth::user()->idEmpresa;

        $publicaciones = Publicacion::with(['empresa', 'categoria'])
                                    ->where(function ($query) use ($idEmpresa) {
                                        $query->where('idEmpresa', $idEmpresa)
                                              ->orWhere('estado', 'Disponible');
                                    })
                                    ->latest('idpublicaciones')
                                    ->get();

        return Inertia::render('Publicaciones/Index', [
            'publicaciones' => $publicaciones
        ]);
    }

    public function create()
    {
        $idEmpresa = Auth::user()->idEmpresa;
        $categorias = Categoria::where('idempresa', $idEmpresa)->get();

        return Inertia::render('Publicaciones/Create', [
            'categorias' => $categorias
        ]);
    }

    public function store(Request $request)
    {
        $idEmpresa = Auth::user()->idEmpresa;

        $validated = $request->validate([
            'idcategorias' => 'required|integer|exists:categorias,idcategorias',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'cantidad' => 'required|numeric',
            'unidadMedida' => 'required|string',
            'frecuencia' => 'required|string',
            'urlImagen' => 'required|image|mimes:jpg,png,svg|max:2048',
        ]);

                if($request->hasFile('urlImagen')){
            $path = $request->file('urlImagen')->store('publicaciones', 'public');
            $validated['urlImagen'] = '/storage/' . $path; 
        }
        
        $validated['estado'] = 'Pendiente';
        $validated['idEmpresa'] = $idEmpresa;

        Publicacion::create($validated);

        return redirect()->route('publicaciones.index')->with('message', 'Publicación enviada a aprobación.');
    }

    public function edit(int $id)
    {
        $idEmpresa = Auth::user()->idEmpresa;

        $publicacion = Publicacion::where('idEmpresa', $idEmpresa)->findOrFail($id);
        $categorias = Categoria::where('idempresa', $idEmpresa)->get();

        return Inertia::render('Publicaciones/Edit', [
            'publicacion' => $publicacion,
            'categorias' => $categorias
        ]);
    }

        public function update(Request $request, int $id)
    {
        $idEmpresa = Auth::user()->idEmpresa;
        $publicacion = Publicacion::where('idEmpresa', $idEmpresa)->findOrFail($id);

        $validated = $request->validate([
            'idcategorias' => 'required|integer|exists:categorias,idcategorias',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'cantidad' => 'required|numeric',
            'unidadMedida' => 'required|string',
            'frecuencia' => 'required|string',
            'estado' => 'required|string',
            'urlImagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ]);

        if($request->hasFile('urlImagen')){
            $path = $request->file('urlImagen')->store('publicaciones', 'public');
            $validated['urlImagen'] = '/storage/' . $path;
        } else {
            $validated['urlImagen'] = $request->input('urlImagen_actual');
        }

        $publicacion->update($validated);

        return redirect()->route('publicaciones.index')->with('message', 'Publicación actualizada exitosamente.');
    }

    public function destroy(int $id)
    {
        $idEmpresa = Auth::user()->idEmpresa;
        $publicacion = Publicacion::where('idEmpresa', $idEmpresa)->findOrFail($id);
        
        $publicacion->delete();

        return redirect()->route('publicaciones.index')->with('message', 'Publicación eliminada correctamente.');
    }
}