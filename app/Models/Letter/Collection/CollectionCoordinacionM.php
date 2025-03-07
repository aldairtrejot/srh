<?php

namespace App\Models\Letter\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CollectionCoordinacionM extends Model
{

    protected $table = 'correspondencia.cat_coordinacion';
    public $timestamps = false;

    protected $fillable = [
        'id_cat_coordinacion',
        'descripcion',
        'estatus',
    ];
    //La funcion obtinene las coordinaciones dependiendo de la unidad que se seleccione
    public function list($idUnidad)
    {
        return DB::table('correspondencia.cat_coordinacion')
            ->select(DB::raw('correspondencia.cat_coordinacion.id_cat_coordinacion AS id, UPPER(correspondencia.cat_coordinacion.descripcion) AS descripcion'))
            ->join('correspondencia.rel_unidad_coordinacion', 'correspondencia.cat_coordinacion.id_cat_coordinacion', '=', 'correspondencia.rel_unidad_coordinacion.id_cat_coordinacion')
            ->where('correspondencia.rel_unidad_coordinacion.id_cat_unidad', $idUnidad)
            ->where('correspondencia.cat_coordinacion.estatus', true)
            ->orderBy('correspondencia.cat_coordinacion.descripcion', 'ASC')
            ->get();
    }


    public function listEdit($idUnidad)
    {
        return DB::table('correspondencia.cat_coordinacion')
            ->select(DB::raw('correspondencia.cat_coordinacion.id_cat_coordinacion AS id, UPPER(correspondencia.cat_coordinacion.descripcion) AS descripcion'))
            ->join('correspondencia.rel_unidad_coordinacion', 'correspondencia.cat_coordinacion.id_cat_coordinacion', '=', 'correspondencia.rel_unidad_coordinacion.id_cat_coordinacion')
            ->where('correspondencia.rel_unidad_coordinacion.id_cat_unidad', $idUnidad)
            ->orderBy('correspondencia.cat_coordinacion.descripcion', 'ASC')
            ->get();
    }

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_coordinacion')
            ->select([
                'correspondencia.cat_coordinacion.id_cat_coordinacion AS id',
                DB::raw('UPPER(correspondencia.cat_coordinacion.descripcion) AS descripcion')
            ])
            ->where('correspondencia.cat_coordinacion.id_cat_coordinacion', '=', $id);
        $result = $query->first();
        return $result;
    }

    // La función lista la coordinacinaón dependiendo del area que se ha cargado
    public function listOfArea($idArea)
    {
        $query = DB::table('correspondencia.cat_coordinacion')
            ->select(
                'correspondencia.cat_coordinacion.id_cat_coordinacion AS id',
                DB::raw('UPPER(correspondencia.cat_coordinacion.descripcion) AS descripcion')
            )
            ->join('correspondencia.rel_area_coordinacion', 'correspondencia.cat_coordinacion.id_cat_coordinacion', '=', 'correspondencia.rel_area_coordinacion.id_cat_coordinacion')
            ->where('correspondencia.rel_area_coordinacion.id_cat_area', $idArea)
            ->get();

        return $query;
    }
}

/*
 $query = DB::table('correspondencia.cat_coordinacion')
     ->select([
         'correspondencia.cat_coordinacion.id_cat_coordinacion AS id',
         DB::raw('UPPER(correspondencia.cat_coordinacion.descripcion) AS descripcion')
     ])
     ->where('correspondencia.cat_coordinacion.id_cat_coordinacion', '=', $id);
 $result = $query->first();
 return $result;
 */