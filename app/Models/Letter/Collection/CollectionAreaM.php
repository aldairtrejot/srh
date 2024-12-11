<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionAreaM extends Model
{
    public function list()
    {
        $query = DB::table('correspondencia.cat_area')
            ->select([
                'correspondencia.cat_area.id_cat_area AS id',
                DB::raw('UPPER(correspondencia.cat_area.descripcion) AS descripcion')
            ])
            ->orderBy('correspondencia.cat_area.descripcion', 'ASC');

        // Ejecutar la consulta y obtener los resultados
        $results = $query->get();

        // Retornar los resultados (puedes pasarlo a tu vista o devolverlo como respuesta)
        return $results;
    }

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_area')
            ->select([
                'correspondencia.cat_area.id_cat_area AS id',
                DB::raw('UPPER(correspondencia.cat_area.descripcion) AS descripcion')
            ])
            ->where('correspondencia.cat_area.id_cat_area', '=', $id)
            ->orderBy('correspondencia.cat_area.descripcion', 'ASC');

        // Usar first() para obtener un único resultado
        $result = $query->first();
        return $result;
    }
}
