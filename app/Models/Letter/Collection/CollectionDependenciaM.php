<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionDependenciaM extends Model
{
    // Retorna la función principal para las tablas de catalogos
    public function list()
    {
        $result = DB::table(table: 'correspondencia.cat_dependencia')
            ->select(
                'correspondencia.cat_dependencia.id_cat_dependencia AS id',
                'correspondencia.cat_dependencia.descripcion AS descripcion'
            )
            ->where('correspondencia.cat_dependencia.estatus', true)
            ->orderBy('correspondencia.cat_dependencia.descripcion', 'ASC')
            ->get();

        return $result;
    }

    // La funcion retorna los valores modificados 
    public function listEdit($id)
    {
        $query = DB::table('correspondencia.cat_dependencia')
            ->select([
                'correspondencia.cat_dependencia.id_cat_dependencia AS id',
                DB::raw('correspondencia.cat_dependencia.descripcion AS descripcion')
            ])
            ->where('correspondencia.cat_dependencia.id_cat_dependencia', '=', $id);
        $result = $query->first();
        return $result;
    }

    // La función retorna las areas, esperando la dependencia esperada
    public function areaList($id)
    {
        $query = DB::table('correspondencia.cat_dependencia_area')
            ->select(
                'correspondencia.cat_dependencia_area.id_cat_dependencia_area AS id',
                'correspondencia.cat_dependencia_area.descripcion AS descripcion'
            )
            ->join('correspondencia.rel_dependencia_area', 'correspondencia.cat_dependencia_area.id_cat_dependencia_area', '=', 'correspondencia.rel_dependencia_area.id_cat_dependencia_area')
            ->where('correspondencia.cat_dependencia_area.estatus', true)
            ->where('correspondencia.rel_dependencia_area.id_cat_dependencia', $id)
            ->orderBy('correspondencia.cat_dependencia_area.descripcion', 'asc')
            ->get();

        return $query;
    }

    // La funcion retorna los valores modificados 
    public function listAreaEdit($id)
    {
        $query = DB::table('correspondencia.cat_dependencia_area')
            ->select([
                'correspondencia.cat_dependencia_area.id_cat_dependencia_area AS id',
                DB::raw('correspondencia.cat_dependencia_area.descripcion AS descripcion')
            ])
            ->where('correspondencia.cat_dependencia_area.id_cat_dependencia_area', '=', $id);
        $result = $query->first();
        return $result;
    }

}
