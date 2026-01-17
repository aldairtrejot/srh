<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionTramiteM extends Model
{
    //La funcion lista el tramite dependiendo del area que se seleccionw
    public function list($idArea)
    {
        // Usamos el Query Builder de Laravel para construir la consulta
        $result = DB::table('correspondencia.cat_tramite')
            ->selectRaw('correspondencia.cat_tramite.id_cat_tramite AS id, UPPER(correspondencia.cat_tramite.descripcion) AS descripcion')
            ->join('correspondencia.rel_area_tramite', 'correspondencia.cat_tramite.id_cat_tramite', '=', 'correspondencia.rel_area_tramite.id_cat_tramite')
            ->where('correspondencia.rel_area_tramite.id_cat_area', $idArea)
            ->where('correspondencia.cat_tramite.estatus', true)
            ->orderBy('correspondencia.cat_tramite.descripcion', 'ASC')
            ->get();

        // Retornar el resultado
        return $result;
    }

    public function listEdit($idArea)
    {
        // Usamos el Query Builder de Laravel para construir la consulta
        $result = DB::table('correspondencia.cat_tramite')
            ->selectRaw('correspondencia.cat_tramite.id_cat_tramite AS id, UPPER(correspondencia.cat_tramite.descripcion) AS descripcion')
            ->join('correspondencia.rel_area_tramite', 'correspondencia.cat_tramite.id_cat_tramite', '=', 'correspondencia.rel_area_tramite.id_cat_tramite')
            ->where('correspondencia.rel_area_tramite.id_cat_area', $idArea)
            ->orderBy('correspondencia.cat_tramite.descripcion', 'ASC')
            ->get();

        // Retornar el resultado
        return $result;
    }

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_tramite')
            ->select([
                'correspondencia.cat_tramite.id_cat_tramite AS id', // Eliminar la coma aquí
                DB::raw('UPPER(correspondencia.cat_tramite.descripcion) AS descripcion')
            ])
            ->where('correspondencia.cat_tramite.id_cat_tramite', '=', $id);

        $result = $query->first();
        return $result;
    }
}
