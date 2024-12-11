<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionRemitenteM extends Model
{
    //LA funcion obtienen el catalogo de remitente
    public function list()
    {
        // Usamos el Query Builder para realizar la consulta.
        $query = DB::table('correspondencia.cat_remitente')
            ->select(
                'correspondencia.cat_remitente.id_cat_remitente AS id',
                DB::raw("UPPER(correspondencia.cat_remitente.nombre) || ' ' || 
                         UPPER(correspondencia.cat_remitente.primer_apellido) || ' ' || 
                         UPPER(correspondencia.cat_remitente.segundo_apellido) || ' - ' || 
                         UPPER(correspondencia.cat_remitente.rfc) AS descripcion")
            )
            ->where('correspondencia.cat_remitente.estatus', true)
            ->orderBy('correspondencia.cat_remitente.nombre', 'ASC')
            ->get(); // Esto ejecuta la consulta y obtiene todos los resultados.

        return $query;
    }

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_remitente')
            ->select([
                'correspondencia.cat_remitente.id_cat_remitente AS id',
                DB::raw("UPPER(correspondencia.cat_remitente.nombre) || ' ' || 
                UPPER(correspondencia.cat_remitente.primer_apellido) || ' ' || 
                UPPER(correspondencia.cat_remitente.segundo_apellido) || ' - ' || 
                UPPER(correspondencia.cat_remitente.rfc) AS descripcion")
            ])
            ->where('correspondencia.cat_remitente.id_cat_remitente', '=', $id);

        $result = $query->first();
        return $result;
    }
}
