<?php

namespace App\Models\Letter\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionUnidadM extends Model
{
    public function list()
    {
        // Realizar la consulta usando el Query Builder de Laravel
        $result = DB::table('correspondencia.cat_unidad')
            ->select('id_cat_unidad as id', 'descripcion')
            ->where('estatus', true)
            ->orderBy('descripcion', 'ASC')
            ->get();

        return $result;
    }

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_unidad')
            ->select([
                'correspondencia.cat_unidad.id_cat_unidad AS id',
                DB::raw('UPPER(correspondencia.cat_unidad.descripcion) AS descripcion')
            ])
            ->where('correspondencia.cat_unidad.id_cat_unidad', '=', $id);

        // Usar first() para obtener un único resultado
        $result = $query->first();
        return $result;
    }
}
