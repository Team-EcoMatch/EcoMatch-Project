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

        //solo mustra publicaciones de la propia empresa
        $publicaciones = Publicacion::with(['empresa', 'categoria'])
            ->where('idempresa', $idEmpresa)
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

    public function search(Request $request)
    {
        $request->validate([
            'lat'       => 'nullable|numeric|between:-90,90',
            'lng'       => 'nullable|numeric|between:-180,180',
            'radio'     => 'nullable|numeric|min:1|max:1000',
            'categoria' => 'nullable|integer|exists:categorias,idcategorias',
            'busqueda'  => 'nullable|string|max:100',
        ]);

        $empresaId = Auth::user()->idempresa;
        $empresa = Auth::user()->empresa;

        // Si no vienen coordenadas en la URL, intentar usar las de su empresa por defecto
        $lat = $request->filled('lat') ? (float) $request->input('lat') : ($empresa?->latitud ? (float) $empresa->latitud : null);
        $lng = $request->filled('lng') ? (float) $request->input('lng') : ($empresa?->longitud ? (float) $empresa->longitud : null);
        $radio = (float) $request->input('radio', 50);
        $categoria = $request->input('categoria');
        $busqueda = $request->input('busqueda');

        // Si aún no hay coordenadas, mostrar la vista con la lista vacía lista para buscar
        if (is_null($lat) || is_null($lng)) {
            return Inertia::render('Publicaciones/Search', [
                'publicaciones' => [
                    'data' => [],
                    'total' => 0,
                    'from' => 0,
                    'to' => 0,
                    'prev_page_url' => null,
                    'next_page_url' => null,
                ],
                'filtros' => [
                    'lat' => '',
                    'lng' => '',
                    'radio' => $radio,
                    'categoria' => $categoria ?? '',
                    'busqueda' => $busqueda ?? '',
                ],
                'categorias' => Categoria::where('idempresa', $empresaId)->get(),
                'advertencia' => 'Por favor ingresa coordenadas o presiona "Usar mi ubicación" para encontrar materiales.',
            ]);
        }

        // Si sí hay coordenadas, ejecutar la búsqueda geoespacial
        $query = Publicacion::disponibles()
            ->with(['empresa', 'categoria'])
            ->cercaDe($lat, $lng, $radio)
            ->where('publicaciones.idempresa', '!=', $empresaId);

        if ($categoria) {
            $query->deCategoria($categoria);
        }

        if ($busqueda) {
            $query->buscarTexto($busqueda);
        }

        $publicaciones = $query->paginate(12)->withQueryString();

        // Redondear distancia
        $publicaciones->getCollection()->transform(function ($item) {
            $item->distancia_km = round($item->distancia_km, 2);
            return $item;
        });

        return Inertia::render('Publicaciones/Search', [
            'publicaciones' => $publicaciones,
            'filtros' => compact('lat', 'lng', 'radio', 'categoria', 'busqueda'),
            'categorias' => Categoria::where('idempresa', $empresaId)->get(),
        ]);
    }
}
