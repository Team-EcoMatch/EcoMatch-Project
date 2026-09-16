<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PublicacionController extends Controller
{
    public function index()
    {
        $idEmpresa = Auth::user()->idempresa;

        $publicaciones = Publicacion::with(['empresa', 'categoria'])
            ->where(function ($query) use ($idEmpresa) {
                $query->where('idempresa', $idEmpresa)
                    ->orWhere('estado', 'Disponible');
            })
            ->latest('idpublicaciones')
            ->get();

        $categorias = Categoria::where('idempresa', $idEmpresa)->orderBy('nombre')->get();

        return Inertia::render('Publicaciones/Index', [
            'publicaciones' => $publicaciones,
            'categorias' => $categorias,
            'empresaAuthId' => $idEmpresa,
        ]);
    }

    public function create()
    {
        $idEmpresa = Auth::user()->idempresa;
        $categorias = Categoria::where('idempresa', $idEmpresa)->get();

        return Inertia::render('Publicaciones/Create', [
            'categorias' => $categorias
        ]);
    }

    public function store(Request $request)
    {
        $idEmpresa = Auth::user()->idempresa;

        $validated = $request->validate([
            'idcategorias' => 'required|integer|exists:categorias,idcategorias',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'cantidad' => 'required|numeric',
            'unidadMedida' => 'required|string',
            'frecuencia' => 'required|string',
            'urlImagen' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('urlImagen')) {
            $image = $request->file('urlImagen');

            $http = Http::attach(
                'file',
                file_get_contents($image->getRealPath()),
                $image->getClientOriginalName()
            );

            // Si estamos en local (tu PC), apagamos la verificación SSL
            if (app()->environment('local')) {
                $http = $http->withOptions(['verify' => false]);
            }

            $response = $http->post('https://api.cloudinary.com/v1_1/' . env('CLOUDINARY_CLOUD_NAME') . '/image/upload', [
                'upload_preset' => 'ecomatch_preset',
            ]);

            if ($response->successful()) {
                $validated['urlImagen'] = $response->json()['secure_url'];
            } else {
                $errorMessage = $response->json()['error']['message'] ?? 'Error desconocido de Cloudinary.';
                return back()->withErrors(['urlImagen' => 'Cloudinary dice: ' . $errorMessage]);
            }
        }

        // 3. Asignar estado, empresa y el ID del usuario autor
        $esJefe = Auth::user()->rol && strtolower(Auth::user()->rol->tipo) === 'jefe';
        $validated['estado'] = $esJefe ? 'Disponible' : 'Pendiente';
        $validated['idempresa'] = $idEmpresa;
        $validated['user_id'] = Auth::id(); // ← AQUÍ es donde debe ir

        $validated['idempresa'] = $idEmpresa;

        Publicacion::create($validated);

        $mensaje = $esJefe
            ? 'Publicación creada y disponible exitosamente.'
            : 'Publicación enviada a aprobación.';

        return redirect()->route('publicaciones.index')->with('message', $mensaje);
    }

    public function edit(int $id)
    {
        $idEmpresa = Auth::user()->idempresa;
        $publicacion = Publicacion::where('idempresa', $idEmpresa)->findOrFail($id);
        $categorias = Categoria::where('idempresa', $idEmpresa)->get();

        return Inertia::render('Publicaciones/Edit', [
            'publicacion' => $publicacion,
            'categorias' => $categorias
        ]);
    }

    public function update(Request $request, int $id)
    {
        $idEmpresa = Auth::user()->idempresa;
        $publicacion = Publicacion::where('idempresa', $idEmpresa)->findOrFail($id);

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

        if ($request->hasFile('urlImagen')) {
            $image = $request->file('urlImagen');

            $http = Http::attach(
                'file',
                file_get_contents($image->getRealPath()),
                $image->getClientOriginalName()
            );

            // Si estamos en local (tu PC), apagamos la verificación SSL
            if (app()->environment('local')) {
                $http = $http->withOptions(['verify' => false]);
            }

            $response = $http->post('https://api.cloudinary.com/v1_1/' . env('CLOUDINARY_CLOUD_NAME') . '/image/upload', [
                'upload_preset' => 'ecomatch_preset',
            ]);

            if ($response->successful()) {
                $validated['urlImagen'] = $response->json()['secure_url'];
            } else {
                $errorMessage = $response->json()['error']['message'] ?? 'Error desconocido de Cloudinary.';
                return back()->withErrors(['urlImagen' => 'Cloudinary dice: ' . $errorMessage]);
            }
        } else {
            $validated['urlImagen'] = $request->input('urlImagen_actual');
        }

        $publicacion->update($validated);

        return redirect()->route('publicaciones.index')->with('message', 'Publicación actualizada exitosamente.');
    }

    public function destroy(int $id)
    {
        $idEmpresa = Auth::user()->idempresa;
        $publicacion = Publicacion::where('idempresa', $idEmpresa)->findOrFail($id);
        $publicacion->delete();

        return redirect()->route('publicaciones.index')->with('message', 'Publicación eliminada correctamente.');
    }
}
