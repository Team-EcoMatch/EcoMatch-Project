<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    public function edit(int $id)
    {
        $idEmpresa = Auth::user()->idempresa;

        if ($id != $idEmpresa) {
            abort(403, 'No tienes permiso para editar esta empresa.');
        }

        $empresa = Empresa::findOrFail($id);
        return Inertia::render('Empresas/Edit', ['empresa' => $empresa]);
    }

    public function update(Request $request, int $id)
    {
        $idEmpresa = Auth::user()->idempresa;

        if ($id != $idEmpresa) {
            abort(403, 'No tienes permiso para actualizar esta empresa.');
        }

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

        return redirect()->route('dashboard')->with('message', 'Perfil de empresa actualizado correctamente.');
    }
}