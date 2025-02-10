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

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_area_interno')
            ->select([
                'correspondencia.cat_area_interno.id_cat_area_interno AS id',
                DB::raw('correspondencia.cat_area_interno.id_cat_area_interno AS id, 
                         UPPER(correspondencia.cat_area_interno.descripcion) || \' (\' || 
                         UPPER(correspondencia.cat_area_interno.clave) || \')\' AS descripcion')
            ])
            ->where('correspondencia.cat_area_interno.id_cat_area_interno', '=', $id);

        // Usar first() para obtener un único resultado
        $result = $query->first();
        return $result;
    }

    // La función obtiene la clave para mostrarla en pantalla dependiendo del id de area que se le pase
    public function getClave($id)
    {
        $result = DB::table('correspondencia.cat_area_interno')
            ->selectRaw('correspondencia.cat_area_interno.clave AS clave')
            ->where('correspondencia.cat_area_interno.id_cat_area_interno', '=', $id)
            ->first();

        return $result->clave;
    }
}
