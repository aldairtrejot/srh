<?php

namespace App\Models\Letter\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CountReportModel extends Model
{
    // La función muestra el contador total de folios
    public function countReportModel()
    {
        $query = DB::table('correspondencia.tbl_correspondencia');

        // Código para no administradores
        if (
            ! in_array(1, session('SESSION_ROLE_USER', [])) &&
            ! in_array(2, session('SESSION_ROLE_USER', []))
        ) {
            $query->join('correspondencia.ctrl_rol_usuario_area',
                'correspondencia.tbl_correspondencia.id_cat_area',
                '=',
                'correspondencia.ctrl_rol_usuario_area.id_cat_area'
            )->where('correspondencia.ctrl_rol_usuario_area.id_usuario', Auth::id());
        }

        return $query->count();
    }

    // La función actualiza el contador dependiendo de los filtros que el usuario ponga
    public function countReportModelFilter($request)
    {
        $query = DB::table('correspondencia.tbl_correspondencia');

        // Código para no administradores
        if (
            ! in_array(1, session('SESSION_ROLE_USER', [])) &&
            ! in_array(2, session('SESSION_ROLE_USER', []))
        ) {
            $query->join('correspondencia.ctrl_rol_usuario_area',
                'correspondencia.tbl_correspondencia.id_cat_area',
                '=',
                'correspondencia.ctrl_rol_usuario_area.id_cat_area'
            )->where('correspondencia.ctrl_rol_usuario_area.id_usuario', Auth::id());
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

        return $query->count();
    }
}
