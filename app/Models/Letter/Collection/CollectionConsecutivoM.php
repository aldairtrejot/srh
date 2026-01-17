<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionConsecutivoM extends Model
{
    //La funcion retorna el consecutivo de las tablaspublic function noDocumento($idAnio, $idTable)
    public function noDocumento($idAnio, $idTable)
    {
        $query = DB::table('correspondencia.rel_anio_documento')
            ->join('correspondencia.cat_tipo_documento', 'correspondencia.rel_anio_documento.id_cat_tipo_documento', '=', 'correspondencia.cat_tipo_documento.id_cat_tipo_documento')
            ->join('correspondencia.cat_anio', 'correspondencia.rel_anio_documento.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->select(
                DB::raw("
                    UPPER(correspondencia.cat_tipo_documento.clave) || '/' || 
                    TO_CHAR(correspondencia.rel_anio_documento.consecutivo + 1, 'FM00000') || '/' ||
                    correspondencia.cat_anio.descripcion AS documento_id
                ")
            )
            ->where('correspondencia.rel_anio_documento.id_cat_tipo_documento', $idTable)
            ->where('correspondencia.rel_anio_documento.id_cat_anio', $idAnio)
            ->first(); // Obtener el primer resultado

        // Verifica si el resultado tiene la propiedad 'documento_id'
        return $query ? $query->documento_id : null;
    }

    //La funcion actualiza el consecutivo
    public function iteratorConsecutivo($idYear, $idDoc)
    {
        // Usando Query Builder para hacer el UPDATE
        DB::table('correspondencia.rel_anio_documento')
            ->where('id_cat_anio', $idYear)
            ->where('id_cat_tipo_documento', $idDoc)
            ->increment('consecutivo', 1); // Aumenta el campo 'consecutivo' en 1
    }
}
