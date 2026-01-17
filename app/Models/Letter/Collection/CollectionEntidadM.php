<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionEntidadM extends Model
{

    // La funcón lista el catalogo de entidad
    public function list()
    {
        $result = DB::table(table: 'correspondencia.cat_entidad')
            ->select('id_cat_entidad as id', 'descripcion as descripcion')
            ->where('estatus', true)
            ->orderBy('descripcion', 'ASC')
            ->get();

        return $result;
    }


    public function listEdit()
    {
        $result = DB::table(table: 'correspondencia.cat_entidad')
            ->select('id_cat_entidad as id', 'descripcion as descripcion')
            ->orderBy('descripcion', 'ASC')
            ->get();

        return $result;
    }

    
    // La funcón obtiene el estado actual del id que se selecciono
    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_entidad')
        ->select('id_cat_entidad as id', 'descripcion as descripcion')
            ->where('correspondencia.cat_entidad.id_cat_entidad', '=', $id);

        // Usar first() para obtener un único resultado
        $result = $query->first();
        return $result;
    }
}
