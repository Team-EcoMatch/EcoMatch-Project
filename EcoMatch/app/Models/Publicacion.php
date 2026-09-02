<?php

namespace App\Models;

use GuzzleHttp\Psr7\Query;
use Illuminate\Database\Eloquent\Model;
use ReturnTypeWillChange;

class Publicacion extends Model
{
    protected $table = 'publicaciones';
    protected $primaryKey = 'idpublicaciones';

    protected $fillable = [
        'idEmpresa',
        'idCategoria',
        'nombre',
        'descripcion',
        'cantidad',
        'unidadMedida',
        'frecuencia',
        'estado',
        'urlImagen',
        'empresa_idempresa'
        'idEmpresa', 'idcategorias', 'nombre', 'descripcion', 'cantidad',
        'unidadMedida', 'frecuencia', 'estado', 'urlImagen', 'idempresa'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'idempresa');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idcategorias', 'idcategorias');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'idpublicaciones');
    }


    //Filtro por categoria
    public function buscarCategora($query, int $categoriaId)
    {
        return $query->where('idcategoria', $categoriaId);
    }
    //Buscar por texto  nombre o descripcion
    public function buscarTexto($query, string $texto)
    {
        return $query->where(function ($q) use ($texto) {
            $q->where('nombre', 'LIKE', "%{$texto}")
                ->orwhere('description', 'LIKE', "%{$texto}%");
        });
    }
    //filtro por distanca geografica: calcula la distancia
    public function scopeCercaDe($query, float $lat, float $lng, float $radioKm = 50)
    {
        $radioMetros = $radioKm * 1000;

        return $query
            ->join('empresa', 'publicaciones.idempresa', '=', 'empresa.idempresa')
            ->select('publicaciones.*')
            ->selectRaw(
                "ST_Distance_Sphere(POINT(?, ?), POINT(empresa.longitud, empresa.latitud)) / 1000 AS distancia_km",
                [$lng, $lat]
            )
            ->whereRaw(
                "ST_Distance_Sphere(POINT(?, ?), POINT(empresa.longitud, empresa.latitud)) <= ?",
                [$lng, $lat, $radioMetros]
            )
            ->orderBy('distancia_km');
    }
}
