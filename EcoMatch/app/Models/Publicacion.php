<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    protected $table = 'publicaciones';
    protected $primaryKey = 'idpublicaciones';

    protected $fillable = [
        'idempresa',
        'idcategorias',
        'nombre',
        'descripcion',
        'cantidad',
        'unidadMedida',
        'frecuencia',
        'estado',
        'urlImagen',
        'user_id',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'idempresa', 'idempresa');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idcategorias', 'idcategorias');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'idpublicaciones');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //Metodos para el matchmaking
    public function scopeDisponibles($query)
    {
        return $query->where('publicaciones.estado', 'Disponible');
    }

 
    //Calcula la distancia entre las coordenadas de la empresa y el punto dado.
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
