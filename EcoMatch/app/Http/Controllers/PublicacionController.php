<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Publicacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        $categorias = Categoria::all();
        $empresas = Empresa::all();
        return Inertia::render('Publicaciones/Create', ['categorias' => $categorias, 'empresas' => $empresas]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idEmpresa' => 'required|integer',
            'idCategoria' => 'required|integer',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'cantidad' => 'required|numeric',
            'unidadMedida' => 'required|string',
            'frecuencia' => 'required|string',
            'urlImagen' => 'required|string',
        ]);
        $validated['estado'] = 'Pendiente';
        $validated['empresa_idempresa'] = $validated['idEmpresa'];

        Publicacion::create($validated);
        return redirect()->back()->with('message', 'Publicación enviada para aprobación');

    }

    public function edit(int $id)
    {
        $publicacion = Publicacion::findOrFail($id);
        $categorias = Categoria::all();
        $empresas = Empresa::all();
        return Inertia::render('Publicaciones/Edit',[
            'publicacion' => $publicacion,
            'categorias' => $categorias,
            'empresas' => $empresas,
        ]);

        
    }

    public function update(Request $request, int $id)
    {
        $publicacion = Publicacion::findOrFail($id);
        $validated = $request->validate([
            'idEmpresa' => 'required|integer',
            'idCategoria' => 'required|integer',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'cantidad' => 'required|numeric',
            'unidadMedida' => 'required|string',
            'frecuencia' => 'required|string',
            'estado' => 'required|string',
            'urlImagen' => 'required|string',
        ]);
        $validated['empresa_idempresa'] = $validated['idEmpresa'];

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
