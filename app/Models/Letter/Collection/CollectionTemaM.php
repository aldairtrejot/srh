<?php

namespace App\Models\Letter\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionTemaM extends Model
{
    //La funcion lista los temas que tengan un status activo, para catalogos
    public function list()
    {
        $result = DB::table('correspondencia.cat_tema')
            ->selectRaw('correspondencia.cat_tema.id_cat_tema AS id, 
                         UPPER(correspondencia.cat_tema.descripcion) AS descripcion')
            ->where('correspondencia.cat_tema.estatus', true)
            ->orderBy('correspondencia.cat_tema.descripcion', 'asc')
            ->get();

        return $result;
    }
}
