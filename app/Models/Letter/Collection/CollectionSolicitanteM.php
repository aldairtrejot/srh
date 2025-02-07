<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionSolicitanteM extends Model
{
    // LA función lista todos los solicitantes activos, para los catalogos
    public function list()
    {
        $result = DB::table('correspondencia.cat_solicitante')
            ->selectRaw('correspondencia.cat_solicitante.id_cat_solicitante AS id, 
                         UPPER(correspondencia.cat_solicitante.nombre) || \' \' || 
                         UPPER(correspondencia.cat_solicitante.primer_apellido) || \' \' || 
                         UPPER(correspondencia.cat_solicitante.segundo_apellido) AS descripcion')
            ->where('correspondencia.cat_solicitante.estatus', true)
            ->orderBy('correspondencia.cat_solicitante.nombre', 'asc')
            ->get();

        return $result;
    }
}
