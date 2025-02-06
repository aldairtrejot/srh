<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;

class CollectionAreaInternoM extends Model
{
    // La función lista las areas concatenadas con su clave que esten activas, para catalogos
    public function list()
    {
        $result = DB::table('correspondencia.cat_area_interno')
            ->selectRaw('correspondencia.cat_area_interno.id_cat_area_interno AS id, 
                         UPPER(correspondencia.cat_area_interno.descripcion) || \' (\' || 
                         UPPER(correspondencia.cat_area_interno.clave) || \')\' AS descripcion')
            ->where('correspondencia.cat_area_interno.estatus', true)
            ->orderBy('correspondencia.cat_area_interno.descripcion', 'asc')
            ->get();

        return $result;
    }
}
