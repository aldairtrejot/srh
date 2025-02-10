<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionDestinatarioM extends Model
{
    // La funcion lista los destinantarios que se encuentren activos, para los catalogos
    public function list()
    {
        $result = DB::table('correspondencia.cat_destinatario')
            ->selectRaw('correspondencia.cat_destinatario.id_cat_destinatario AS id, 
                         CASE 
                             WHEN es_mas_remitente THEN UPPER(correspondencia.cat_destinatario.descripcion)
                             ELSE UPPER(correspondencia.cat_destinatario.nombre) || \' \' || 
                                  UPPER(correspondencia.cat_destinatario.primer_apellido) || \' \' || 
                                  UPPER(correspondencia.cat_destinatario.segundo_apellido)
                         END AS descripcion')
            ->where('correspondencia.cat_destinatario.estatus', true)
            ->orderBy('correspondencia.cat_destinatario.id_cat_destinatario', 'asc')
            ->get();

        return $result;
    }

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_destinatario')
            ->select([
                'correspondencia.cat_destinatario.id_cat_destinatario AS id',
                DB::raw('CASE 
                             WHEN es_mas_remitente THEN UPPER(correspondencia.cat_destinatario.descripcion)
                             ELSE UPPER(correspondencia.cat_destinatario.nombre) || \' \' || 
                                  UPPER(correspondencia.cat_destinatario.primer_apellido) || \' \' || 
                                  UPPER(correspondencia.cat_destinatario.segundo_apellido)
                         END AS descripcion')
            ])
            ->where('correspondencia.cat_destinatario.id_cat_destinatario', '=', $id);

        // Usar first() para obtener un único resultado
        $result = $query->first();
        return $result;
    }
}
