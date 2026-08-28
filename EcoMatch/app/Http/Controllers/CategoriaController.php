<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::with('empresa')->get();
        return Inertia::render('Categorias/Index', ['categorias' => $categorias]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:150',
            'empresa_idempresa' => 'required|integer|exists:empresa,idempresa',
        ]);

        Categoria::create($validated);
        return redirect()->back()->with('message', 'Categoría creada exitosamente.');
    }

    public function update(Request $request, int $id)
    {
        $categoria = Categoria::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:150',
        ]);
        $categoria->update($validated);
        return redirect()->back()->with('message', 'Categoría actualizada');
    }

    public function destroy(int $id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();
        return redirect()->back()->with('message', 'Categoría elimanada correctamente');
    }
}
