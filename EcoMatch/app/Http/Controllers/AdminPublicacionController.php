<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Publicacion;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class AdminPublicacionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Publicacion::query()
            ->with(['empresa:idempresa,nombreEmpresa,email', 'categoria:idcategorias,nombre'])
            ->latest('idpublicaciones');

        // Filtro por término de búsqueda (título o empresa)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhereHas('empresa', fn($e) => $e->where('nombreEmpresa', 'like', "%{$search}%"));
            });
        }

        // Filtro por estado
        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        return Inertia::render('Publicaciones', [
            'publicaciones' => $query->paginate(10)->withQueryString(),
            'filters'       => $request->only(['search', 'estado']),
            'estadosValidos' => ['Pendiente', 'Disponible', 'Inactivo', 'Agotado', 'Reservado', 'Intercambiado', 'Rechazado'],
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $publicacion = Publicacion::findOrFail($id);
        if ($publicacion->estado !== 'Pendiente') {
            return back()->with('error', 'Esta publicacion no esta pendiente para su aprovacion');
        }

        $publicacion->update(['estado' => 'Disponible']);

        return back()->with('success', 'Publicaion aprovada');
    }

    public function reject(int $id): RedirectResponse
    {
        $publicacion = Publicacion::findOrFail($id); // Mayúscula P

        if ($publicacion->estado !== 'Pendiente') {
            return back()->with('error', 'Esta publicación no está pendiente para su rechazo');
        }

        $publicacion->update(['estado' => 'Rechazado']); // Array asociativo correcto

        return back()->with('success', 'Publicación rechazada.');
    }

    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show(string $id)
    {
        //
    }


    public function edit(string $id)
    {
        //
    }


    public function updateEstado(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'estado' => 'required|in:Disponible,Inactivo,Agotado,Reservado,Intercambiado,Pendiente',
        ]);

        $publicacion = Publicacion::findOrFail($id);
        $publicacion->update([
            'estado' => $request->input('estado'),
        ]);

        return back()->with('success', "El estado de la publicación se actualizó a '{$publicacion->estado}'.");
    }


    public function destroy(int $id): RedirectResponse
    {
        $publicacion = Publicacion::findOrFail($id);
        $publicacion->delete();

        return back()->with('success', 'La publicación fue eliminada correctamente.');
    }
}
