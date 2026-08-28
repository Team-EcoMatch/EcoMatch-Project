<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmpresaController extends Controller
{
    public function show(int $id)
    {
        $empresa = Empresa::with('categorias')->findOrFail($id);

        return Inertia::render('Empresas/Show', [
            'empresa' => $empresa
        ]);
    }

    public function edit(int $id)
    {
        $empresa = Empresa::findOrFail($id);

        return Inertia::render('Empresas/Edit', [
            'empresa' => $empresa
        ]);
    }

    public function update(Request $request, int $id)
    {
        $empresa = Empresa::findOrFail($id);

        $validated = $request->validate([
            'nombreEmpresa' => 'required|string|max:150',
            'direccion' => 'required|string|max:250',
            'email' => 'required|email|max:150',
            'telefono' => 'required|string|max:20',
            'tipoEmpresa' => 'required|string|max:100',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'radioOperacion' => 'required|numeric',
            'estado' => 'required|boolean',
        ]);

        $empresa->update($validated);

        return redirect()->back()->with('message', 'Perfil de empresa actualizado correctamente.');
    }
}
