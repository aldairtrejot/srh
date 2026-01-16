<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionConsecutivoM extends Model
{
    //La funcion retorna el consecutivo de las tablaspublic function noDocumento($idAnio, $idTable)
    public function noDocumento($idAnio, $idTable)
{
    $row = DB::table('correspondencia.rel_anio_documento as r')
        ->join('correspondencia.cat_tipo_documento as t', 'r.id_cat_tipo_documento', '=', 't.id_cat_tipo_documento')
        ->join('correspondencia.cat_anio as a', 'r.id_cat_anio', '=', 'a.id_cat_anio')
        ->select(
            't.clave',
            'a.descripcion as anio',
            DB::raw('(r.consecutivo + 1) as next_consecutivo')
        )
        ->where('r.id_cat_tipo_documento', $idTable)
        ->where('r.id_cat_anio', $idAnio)
        ->first();

    if (!$row) return null;

    $clave = strtoupper($row->clave);
    $anio  = $row->anio;
    $n     = (int) $row->next_consecutivo;

    // ✅ FORMATO NUEVO para CIRCULARES
    if ((int)$idTable === (int)config('custom_config.CP_TABLE_CIRCULAR')) {
        // 0003 (4 dígitos)
        $num = str_pad((string)$n, 4, '0', STR_PAD_LEFT);
        return "IB-{$clave}-{$num}-{$anio}";
    }

    // Formato viejo para otros docs
    $num = str_pad((string)$n, 5, '0', STR_PAD_LEFT);
    return "{$clave}/{$num}/{$anio}";
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
