<?php

namespace App\Models\Letter\Collection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CollectionReportM extends Model
{
    //La funcion retorna la plantilla de reporte
    public function templateReporte($id, $tableName, $idTable)
    {
        $query = DB::table($tableName)
            ->select(
                $tableName . '.num_turno_sistema AS num_turno_sistema',
                DB::raw("TO_CHAR(" . $tableName . ".fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(" . $tableName . ".fecha_fin, 'DD/MM/YYYY') AS fecha_fin"), // Asegúrate de que esta columna tenga un nombre único
                $tableName . '.observaciones AS observaciones',
                $tableName . '.asunto AS asunto',
                'correspondencia.cat_anio.descripcion AS anio',
                DB::raw("CASE WHEN " . $tableName . ".es_por_area THEN 
                                " . $tableName . " .num_documento_area ELSE 
                                correspondencia.tbl_correspondencia.num_turno_sistema END AS num_correspondencia")
            )
            ->leftJoin('correspondencia.tbl_correspondencia', $tableName . '.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->join('correspondencia.cat_anio', $tableName . '.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->where($tableName . '.' . $idTable, $id)
            ->first(); // Obtener solo el primer resultado

        return $query;
    }

    //La funcion retorna el id de area
    public function getIdArea($id, $tableName, $idTable)
    {
        // Usamos el Query Builder de Laravel para construir la consulta
        $result = DB::table($tableName)
            ->leftJoin('correspondencia.tbl_correspondencia', $tableName . '.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->selectRaw('CASE 
                            WHEN ' . $tableName . '.es_por_area THEN ' . $tableName . '.id_cat_area_documento
                            ELSE correspondencia.tbl_correspondencia.id_cat_area 
                        END AS id_area')
            ->where(DB::raw($tableName . '.' . $idTable), $id)
            ->first(); // Usamos first() para obtener solo el primer (y único) resultado

        // Retornamos el resultado con el campo id_area
        return $result ? $result->id_area : null; // Retorna el id_area o null si no se encuentra
    }
}
