<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionStatusM extends Model
{

    //La funcion retorna un catalogo de estatus
    public function list()
    {
        $result = DB::table('correspondencia.cat_estatus')
            ->selectRaw('id_cat_estatus AS id, UPPER(descripcion) AS descripcion')
            ->where('estatus', true)
            ->orderBy('descripcion', 'ASC')
            ->get();

        // Retornar el resultado
        return $result;
    }

    public function listEdit()
    {
        $result = DB::table('correspondencia.cat_estatus')
            ->selectRaw('id_cat_estatus AS id, UPPER(descripcion) AS descripcion')
            ->orderBy('descripcion', 'ASC')
            ->get();

        // Retornar el resultado
        return $result;
    }

    public function listByUser()
    {
        $result = DB::table('correspondencia.cat_estatus')
            ->selectRaw('id_cat_estatus AS id, UPPER(descripcion) AS descripcion')
            ->where('id_cat_estatus', '<>', 2)
            ->where('id_cat_estatus', '<>', 5)
            ->where('estatus', true)
            ->orderBy('descripcion', 'ASC')
            ->get();

        // Retornar el resultado
        return $result;
    }

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_estatus')
            ->select([
                'correspondencia.cat_estatus.id_cat_estatus AS id',
                DB::raw('UPPER(correspondencia.cat_estatus.descripcion) AS descripcion')
            ])
            ->where('correspondencia.cat_estatus.id_cat_estatus', '=', $id);

        // Usar first() para obtener un único resultado
        $result = $query->first();
        return $result;
    }
}
