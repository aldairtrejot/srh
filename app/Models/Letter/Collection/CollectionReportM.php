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
                'correspondencia.cat_area.descripcion AS area',
                'correspondencia.cat_anio.descripcion AS anio',
                DB::raw("COALESCE(correspondencia.cat_remitente.nombre, '') || ' ' || 
                            COALESCE(correspondencia.cat_remitente.primer_apellido, '') || ' ' ||
                            COALESCE(correspondencia.cat_remitente.segundo_apellido, '') || ' - ' || 
                            COALESCE(correspondencia.cat_remitente.rfc, '') AS remitente"),
                DB::raw("CASE WHEN " . $tableName . ".es_por_area THEN 
                                " . $tableName . " .num_documento_area ELSE 
                                correspondencia.tbl_correspondencia.num_turno_sistema END AS num_correspondencia")
            )
            ->leftJoin('correspondencia.tbl_correspondencia', $tableName . '.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->join('correspondencia.cat_area', $tableName . '.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->join('correspondencia.cat_anio', $tableName . '.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->join('correspondencia.cat_remitente', $tableName . '.id_cat_remitente', '=', 'correspondencia.cat_remitente.id_cat_remitente')
            ->where($tableName . '.' . $idTable, $id)
            ->first(); // Obtener solo el primer resultado

        return $query;
    }
}
