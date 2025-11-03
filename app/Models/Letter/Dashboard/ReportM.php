<?php

namespace App\Models\Letter\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReportM extends Model
{
    // La funcion retorna el reporte del dashboard
    public function generateReport($request)
    {
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->selectRaw('correspondencia.tbl_correspondencia.id_tbl_correspondencia AS id')
            ->selectRaw('correspondencia.tbl_correspondencia.folio_gestion AS folio_gestion')
            ->selectRaw('correspondencia.cat_estatus.descripcion AS estatus')
            ->selectRaw('correspondencia.tbl_correspondencia.num_documento AS oficio_recibido')
            ->selectRaw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_captura, 'DD/MM/YYYY') AS fecha_alta")
            ->selectRaw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin, 'DD/MM/YYYY') AS fecha_fin")
            ->selectRaw('correspondencia.tbl_correspondencia.puesto_remitente AS puesto_remitente')
            ->selectRaw('correspondencia.tbl_correspondencia.asunto AS asunto')
            ->selectRaw('c_r_h.descripcion AS c_r_h')
            ->selectRaw('c_r_h_t.descripcion AS c_r_h_t')
            ->selectRaw('area_zona.descripcion AS area_zona')
            ->selectRaw('correspondencia.cat_tramite.descripcion AS tramite_general')
            ->selectRaw('correspondencia.cat_clave.descripcion AS tramite_especifico')
            ->selectRaw("CASE 
                                        WHEN correspondencia.tbl_correspondencia.es_doc_fisico THEN 'FÍSICO'
                                        ELSE 'DÍGITAL' 
                                    END AS tipo_documento")
            ->selectRaw('correspondencia.tbl_correspondencia.observaciones AS observaciones');

        if ($request->check_copia_a) { // copia a
            $query->selectRaw('copia_a.descripcion AS copia_a');
        }

        $query->selectRaw('UPPER(administration.users.name) AS usuario_captura')
            ->selectRaw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_usuario_captura, 'DD/MM/YYYY') AS fecha_captura")
            ->selectRaw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_usuario_captura, 'HH24:MI') AS hora_captura")
            ->selectRaw("CASE 
                                        WHEN correspondencia.tbl_oficio.id_tbl_correspondencia IS NOT NULL THEN 'SI'
                                        ELSE 'NO'
                                    END AS estatus_respuesta")
            ->selectRaw('correspondencia.tbl_oficio.asunto AS descripcion_cierre')

            // joins
            ->join('correspondencia.cat_estatus', 'correspondencia.tbl_correspondencia.id_cat_estatus', '=', 'correspondencia.cat_estatus.id_cat_estatus')
            ->join('correspondencia.cat_tramite', 'correspondencia.tbl_correspondencia.id_cat_tramite', '=', 'correspondencia.cat_tramite.id_cat_tramite')
            ->join('correspondencia.cat_clave', 'correspondencia.tbl_correspondencia.id_cat_clave', '=', 'correspondencia.cat_clave.id_cat_clave')
            ->leftJoin('correspondencia.cat_area AS c_r_h', 'correspondencia.tbl_correspondencia.id_cat_area_1', '=', 'c_r_h.id_cat_area')
            ->leftJoin('correspondencia.cat_area AS c_r_h_t', 'correspondencia.tbl_correspondencia.id_cat_area_2', '=', 'c_r_h_t.id_cat_area')
            ->leftJoin('correspondencia.cat_area AS area_zona', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'area_zona.id_cat_area');

        if ($request->check_copia_a) { // copia a
            $query->leftJoin('correspondencia.ctrl_transcribir_correspondencia', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.ctrl_transcribir_correspondencia.id_tbl_correspondencia')
                ->leftJoin('correspondencia.cat_area AS copia_a', 'correspondencia.ctrl_transcribir_correspondencia.id_cat_area', '=', 'copia_a.id_cat_area');
        }

        $query->leftJoin('administration.users', 'correspondencia.tbl_correspondencia.id_usuario_captura', '=', 'administration.users.id')
            ->leftJoin('correspondencia.tbl_oficio', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.tbl_oficio.id_tbl_correspondencia');

        // Código para no administradores
        if (
            ! in_array(1, session('SESSION_ROLE_USER', [])) &&
            ! in_array(2, session('SESSION_ROLE_USER', []))
        ) {
            $query->leftJoin('correspondencia.ctrl_rol_usuario_area as j_area_1', function ($join) {
                $join->on('tbl_correspondencia.id_cat_area_1', '=', 'j_area_1.id_cat_area')
                    ->where('j_area_1.id_cat_jerarquia', 1)
                    ->where('j_area_1.estatus', true)
                    ->where('j_area_1.id_usuario', auth()->id());
            })
                ->leftJoin('correspondencia.ctrl_rol_usuario_area as j_area_2', function ($join) {
                    $join->on('tbl_correspondencia.id_cat_area_2', '=', 'j_area_2.id_cat_area')
                        ->where('j_area_2.id_cat_jerarquia', 2)
                        ->where('j_area_2.estatus', true)
                        ->where('j_area_2.id_usuario', auth()->id());
                })
                ->leftJoin('correspondencia.ctrl_rol_usuario_area as j_area_3', function ($join) {
                    $join->on('tbl_correspondencia.id_cat_area', '=', 'j_area_3.id_cat_area')
                        ->where('j_area_3.id_cat_jerarquia', 3)
                        ->where('j_area_3.estatus', true)
                        ->where('j_area_3.id_usuario', auth()->id());
                })
                ->where(function ($q) {
                    $q->whereNotNull('j_area_1.id_cat_area')
                        ->orWhereNotNull('j_area_2.id_cat_area')
                        ->orWhereNotNull('j_area_3.id_cat_area');
                });
        }

        // Integración de filtrado de información de jerarquia de área 1
        if (! empty($request->cat_area_j_1)) {
            $query->where('correspondencia.tbl_correspondencia.id_cat_area_1', $request->cat_area_j_1);
        }

        // Integración de filtrado de información de jerarquia de área 2
        if (! empty($request->cat_area_j_2)) {
            $query->where('correspondencia.tbl_correspondencia.id_cat_area_2', $request->cat_area_j_2);
        }

        // Integración de filtrado de información de jerarquia de área 2
        if (! empty($request->cat_area_j_3)) {
            $query->where('correspondencia.tbl_correspondencia.id_cat_area', $request->cat_area_j_3);
        }

        // Integración de filtrado de información por estatus
        if (! empty($request->id_cat_status)) {
            $query->where('correspondencia.tbl_correspondencia.id_cat_estatus', $request->id_cat_status);
        }

        // Integración de filtrado de información por año
        if (! empty($request->id_cat_date_informe)) {
            $query->where('correspondencia.tbl_correspondencia.id_cat_anio', $request->id_cat_date_informe);
        }

        // Filtrado por fechas de captura
        // Primer filtro si fecha de inicio no es null y fecha fin si es null, entonces se filtra solo por fecha de inicio
        if (! empty($request->fecha_inicio_informe) && empty($request->fecha_fin_informe)) {
            $query->whereDate('correspondencia.tbl_correspondencia.fecha_usuario_captura', $request->fecha_inicio_informe);
        }

        // Primer filtro si fecha de fin no es null y fecha de inicio si es null, entonces se filtra solo por fecha de fin
        if (! empty($request->fecha_fin_informe) && empty($request->fecha_inicio_informe)) {
            $query->whereDate('correspondencia.tbl_correspondencia.fecha_usuario_captura', $request->fecha_fin_informe);
        }

        // Filtrado por fecha cuando ambas fechas llevan datos
        if (! empty($request->fecha_inicio_informe) && ! empty($request->fecha_fin_informe)) {
            $query->whereDate('correspondencia.tbl_correspondencia.fecha_usuario_captura', '>=', $request->fecha_inicio_informe)
                ->whereDate('correspondencia.tbl_correspondencia.fecha_usuario_captura', '<=', $request->fecha_fin_informe);
        }

        // Filtrado de información por hr de captura
        if ($request->incluir_horas == 0) {
            $query->whereRaw('EXTRACT(HOUR FROM correspondencia.tbl_correspondencia.fecha_usuario_captura) >= ?', [$request->inicio])
                ->whereRaw('EXTRACT(HOUR FROM correspondencia.tbl_correspondencia.fecha_usuario_captura) <= ?', [$request->fin]);
        }

        // Order by
        $query->orderBy('correspondencia.tbl_correspondencia.id_tbl_correspondencia', 'ASC');

        return $query->get();
    }

    public function generateReportGuest()
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

        // El orden
        $query->where('correspondencia.tbl_correspondencia.id_cat_estatus', '!=', 2)
            ->where('correspondencia.tbl_correspondencia.num_documento', 'ILIKE', 'IB-UAF-TURNOS-%');

        $query->orderBy('correspondencia.tbl_correspondencia.id_tbl_correspondencia', 'ASC');

        return $query->get();
    }
}
