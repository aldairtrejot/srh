<?php

namespace App\Models\Letter\Dashboard;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ReportM extends Model
{
    //La funcion retorna el reporte del dashboard
    public function generateReport($idArea, $idStatus, $isFechas, $fechaInicio, $fechaFin, $idAnio, $allHoras, $inicio, $fin)
    {
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.folio_gestion AS folio_gestion',
                'correspondencia.tbl_correspondencia.num_documento AS num_documento',
                'correspondencia.tbl_correspondencia.num_turno_sistema AS num_turno_sistema',
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_captura, 'DD/MM/YYYY') AS fecha_captura"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin, 'DD/MM/YYYY') AS fecha_fin"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_documento, 'DD/MM/YYYY') AS fecha_documento"),
                'correspondencia.tbl_correspondencia.asunto AS asunto',
                'correspondencia.tbl_correspondencia.observaciones AS observaciones',
                'correspondencia.cat_area.descripcion AS area',
                'user_titular.name AS titular',
                'user_enlace.name AS enlace',
                'correspondencia.cat_estatus.descripcion AS estatus',
                'correspondencia.cat_anio.descripcion AS anio',
                'correspondencia.cat_tramite.descripcion AS tramite',
                'correspondencia.cat_clave.descripcion AS clave',
                'correspondencia.cat_unidad.descripcion AS unidad',
                'area_cc.descripcion AS area_cc',
                'correspondencia.cat_coordinacion.descripcion AS coordinacion',
                'correspondencia.tbl_correspondencia.horas_respuesta AS horas_respuesta',
                DB::raw("CASE WHEN correspondencia.tbl_correspondencia.es_doc_fisico THEN 'FÍSICO' ELSE 'DIGITAL' END AS tipo_documento"),
                'correspondencia.cat_entidad.descripcion AS entidad',
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_usuario_captura::timestamp, 'DD/MM/YYYY') AS fecha_captura"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_usuario_captura::timestamp, 'HH24:MI') AS hora_captura"),
                'user_add.name AS usuario_add',
                DB::raw("CASE WHEN correspondencia.tbl_correspondencia.son_mas_remitentes THEN correspondencia.tbl_correspondencia.remitente ELSE correspondencia.cat_remitente.nombre || ' ' || correspondencia.cat_remitente.primer_apellido || ' ' || correspondencia.cat_remitente.segundo_apellido END AS remitente"),
                'correspondencia.tbl_correspondencia.puesto_remitente AS puesto_remitente'
            )
            ->join('correspondencia.cat_area', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->join('administration.users AS user_titular', 'correspondencia.tbl_correspondencia.id_usuario_area', '=', 'user_titular.id')
            ->join('administration.users AS user_enlace', 'correspondencia.tbl_correspondencia.id_usuario_enlace', '=', 'user_enlace.id')
            ->join('correspondencia.cat_estatus', 'correspondencia.tbl_correspondencia.id_cat_estatus', '=', 'correspondencia.cat_estatus.id_cat_estatus')
            ->join('correspondencia.cat_anio', 'correspondencia.tbl_correspondencia.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->join('correspondencia.cat_tramite', 'correspondencia.tbl_correspondencia.id_cat_tramite', '=', 'correspondencia.cat_tramite.id_cat_tramite')
            ->join('correspondencia.cat_clave', 'correspondencia.tbl_correspondencia.id_cat_clave', '=', 'correspondencia.cat_clave.id_cat_clave')
            ->join('correspondencia.cat_unidad', 'correspondencia.tbl_correspondencia.id_cat_unidad', '=', 'correspondencia.cat_unidad.id_cat_unidad')
            ->join('correspondencia.cat_coordinacion', 'correspondencia.tbl_correspondencia.id_cat_coordinacion', '=', 'correspondencia.cat_coordinacion.id_cat_coordinacion')
            ->join('correspondencia.cat_entidad', 'correspondencia.tbl_correspondencia.id_cat_entidad', '=', 'correspondencia.cat_entidad.id_cat_entidad')
            ->leftJoin('administration.users AS user_add', 'correspondencia.tbl_correspondencia.id_usuario_captura', '=', 'user_add.id')
            ->leftJoin('correspondencia.cat_remitente', 'correspondencia.tbl_correspondencia.id_cat_remitente', '=', 'correspondencia.cat_remitente.id_cat_remitente')
            ->leftJoin('correspondencia.ctrl_transcribir_correspondencia', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.ctrl_transcribir_correspondencia.id_tbl_correspondencia')
            ->leftJoin('correspondencia.cat_area AS area_cc', 'correspondencia.ctrl_transcribir_correspondencia.id_cat_area', '=', 'area_cc.id_cat_area');

        // Aplicar los filtros opcionales usando el método `when()`
        $query->when(!empty($idArea), function ($query) use ($idArea) {
            return $query->where('correspondencia.tbl_correspondencia.id_cat_area', '=', $idArea);
        });

        $query->when(!empty($idStatus), function ($query) use ($idStatus) {
            return $query->where('correspondencia.tbl_correspondencia.id_cat_estatus', '=', $idStatus);
        });

        // Filtro por fechas
        if ($isFechas) {
            // Asegurarse de que las fechas estén en formato adecuado antes de hacer la comparación
            $query->whereRaw('DATE(correspondencia.tbl_correspondencia.fecha_usuario_captura) BETWEEN ? AND ?', [$fechaInicio, $fechaFin]);
        } else {
            // Filtro por anio
            $query->when(!empty($idAnio), function ($query) use ($idAnio) {
                return $query->where('correspondencia.tbl_correspondencia.id_cat_anio', '=', $idAnio);
            });
        }

        //Filtro por horas
        if (!$allHoras) {
            $query->whereRaw('EXTRACT(HOUR FROM correspondencia.tbl_correspondencia.fecha_usuario_captura) BETWEEN ? AND ?', [$inicio, $fin]);
        }
        // El orden
        $query->orderBy('correspondencia.tbl_correspondencia.id_tbl_correspondencia', 'DESC');

        return $query->get();
    }


}
