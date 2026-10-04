<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CategoriaController extends Controller
{
    public function index()
    {
        $idEmpresa = Auth::user()->idempresa;

        $categorias = Categoria::where('categorias.idempresa', $idEmpresa)
            ->leftJoin('publicaciones', function ($join) {
                $join->on('categorias.idcategorias', '=', 'publicaciones.idcategorias')
                    ->where('publicaciones.estado', 'Disponible');
            })
            ->selectRaw('categorias.idcategorias, categorias.nombre, categorias.descripcion, COALESCE(COUNT(publicaciones.idpublicaciones), 0) as publicaciones_count')
            ->groupBy('categorias.idcategorias', 'categorias.nombre', 'categorias.descripcion')
            ->get();

        return Inertia::render('Categorias/Index', [
            'categorias' => $categorias,
            'message' => session('message')
        ]);
    }

    public function create()
    {
        return Inertia::render('Categorias/Create');
    }

    public function edit(int $id)
    {
        $idEmpresa = Auth::user()->idempresa;
        $categoria = Categoria::where('idempresa', $idEmpresa)->findOrFail($id);
        return Inertia::render('Categorias/Edit', ['categoria' => $categoria]);
    }

    public function store(Request $request)
    {
        $idEmpresa = Auth::user()->idempresa;

        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('categorias')->where(function ($query) use ($idEmpresa) {
                    return $query->where('idempresa', $idEmpresa);
                })
            ],
            'descripcion' => 'nullable|string|max:150',
        ], [
            'nombre.unique' => 'Ya existe una categoría con este nombre en tu empresa.',
        ]);

        $validated['idempresa'] = $idEmpresa;

        Categoria::create($validated);
        return redirect()->route('categorias.index')->with('message', 'Categoría creada exitosamente.');
    }

    public function update(Request $request, int $id)
    {
        $idEmpresa = Auth::user()->idempresa;
        $categoria = Categoria::where('idempresa', $idEmpresa)->findOrFail($id);

        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('categorias')->ignore($categoria->idcategorias, 'idcategorias')->where(function ($query) use ($idEmpresa) {
                    return $query->where('idempresa', $idEmpresa);
                })
            ],
            'descripcion' => 'nullable|string|max:150',
        ], [
            'nombre.unique' => 'Ya existe una categoría con este nombre en tu empresa.',
        ]);

        $categoria->update($validated);
        return redirect()->route('categorias.index')->with('message', 'Categoría actualizada exitosamente.');
    }

    public function destroy(int $id)
    {
        $idEmpresa = Auth::user()->idempresa;
        $categoria = Categoria::where('idempresa', $idEmpresa)->findOrFail($id);
        $categoria->delete();
        return redirect()->route('categorias.index')->with('message', 'Categoría eliminada correctamente.');
    }
}
