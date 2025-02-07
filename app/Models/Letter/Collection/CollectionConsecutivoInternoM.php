<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionConsecutivoInternoM extends Model
{
    // Obtiene el maximo consecutivo de correspondencia interna, tomando como referencia
    // el año y el tipo de documento
    public function getMaxConsecutivo($idDoc, $idAnio)
    {
        $query = DB::table('correspondencia.cat_consecutivo_interno')
            ->select(DB::raw("LPAD((correspondencia.cat_consecutivo_interno.valor + 1)::text, 5, '0') AS iterator"))
            ->where('correspondencia.cat_consecutivo_interno.id_cat_anio', $idAnio)
            ->where('correspondencia.cat_consecutivo_interno.id_cat_tipo_documento', $idDoc)
            ->first();

        return $query;
    }
}
