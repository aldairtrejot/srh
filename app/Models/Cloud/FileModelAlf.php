<?php

namespace App\Models\Cloud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class FileModelAlf extends Model
{
    // Método principal para construir queries
    public function fileModelAlf($request)
    {
        // Definir las configuraciones de las queries
        $queryConfigs = [
            [
                'type' => 'oficio',
                'joinTable' => 'ctrl_correspondencia_oficio',
                'joinCondition' => 'correspondencia.ctrl_correspondencia_oficio.id_tbl_correspondencia',
                'namePattern' => 'of_',
            ],
            [
                'type' => 'anexo',
                'joinTable' => 'ctrl_correspondencia_anexo',
                'joinCondition' => 'correspondencia.ctrl_correspondencia_anexo.id_tbl_correspondencia',
                'namePattern' => 'ax_of_',
            ],
            [
                'type' => 'rep_anexo',
                'joinTable' => 'ctrl_oficio_anexo',
                'joinCondition' => 'correspondencia.ctrl_oficio_anexo.id_tbl_oficio',
                'namePattern' => 'rep_ax_of_',
            ],
            [
                'type' => 'rep_oficio',
                'joinTable' => 'ctrl_oficio_oficio',
                'joinCondition' => 'correspondencia.ctrl_oficio_oficio.id_tbl_oficio',
                'namePattern' => 'rep_of_',
            ],
        ];

        // Construir todas las queries
        $queries = [];
        foreach ($queryConfigs as $config) {
            $queries[] = $this->buildQuery($config, $request);
        }

        // Unir todas las consultas
        $finalQuery = $queries[0];
        for ($i = 1; $i < count($queries); $i++) {
            $finalQuery = $finalQuery->unionAll($queries[$i]);
        }

        return $finalQuery->get();
    }

    /**
     * Construye una query individual basada en la configuración
     */
    private function buildQuery($config, $request)
    {
        $type = $config['type'];
        $joinTable = $config['joinTable'];
        $joinCondition = $config['joinCondition'];
        $namePattern = $config['namePattern'];

        $query = DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia as id',
                DB::raw("'{$namePattern}' || correspondencia.tbl_correspondencia.folio_gestion as name"),
                "correspondencia.{$joinTable}.uid as uuid"
            );

        // Configurar JOIN según el tipo
        if ($type === 'oficio' || $type === 'anexo') {
            $query->leftJoin("correspondencia.{$joinTable}", function ($join) use ($joinTable, $joinCondition) {
                $join->on('correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', $joinCondition)
                    ->where("correspondencia.{$joinTable}.estatus", true);
            });
        } else {
            $query->join('correspondencia.tbl_oficio', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.tbl_oficio.id_tbl_correspondencia')
                ->join("correspondencia.{$joinTable}", function ($join) use ($joinTable, $joinCondition) {
                    $join->on('correspondencia.tbl_oficio.id_tbl_oficio', '=', $joinCondition)
                        ->where("correspondencia.{$joinTable}.estatus", true);
                });
        }

        // Aplicar condiciones comunes
        $this->applyCommonConditions($query, $request);

        return $query;
    }

    /**
     * Aplica condiciones comunes a todas las queries
     */
    private function applyCommonConditions($query, $request)
    {
        // Condiciones de seguridad por roles
        if (
            ! in_array(1, session('SESSION_ROLE_USER', [])) &&
            ! in_array(2, session('SESSION_ROLE_USER', []))
        ) {
            $this->applySecurityConditions($query);
        }

        // Filtros por áreas jerárquicas
        $this->applyAreaFilters($query, $request);

        // Filtros por estatus y año
        $this->applyStatusAndYearFilters($query, $request);

        // Filtros por fechas
        $this->applyDateFilters($query, $request);

        // Filtros por horas
        $this->applyHourFilters($query, $request);
    }

    /**
     * Aplica condiciones de seguridad basadas en roles
     */
    private function applySecurityConditions($query)
    {
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

    /**
     * Aplica filtros por áreas jerárquicas
     */
    private function applyAreaFilters($query, $request)
    {
        $areaFilters = [
            'cat_area_j_1' => 'id_cat_area_1',
            'cat_area_j_2' => 'id_cat_area_2',
            'cat_area_j_3' => 'id_cat_area',
        ];

        foreach ($areaFilters as $requestKey => $column) {
            if (! empty($request->$requestKey)) {
                $query->where("correspondencia.tbl_correspondencia.{$column}", $request->$requestKey);
            }
        }
    }

    /**
     * Aplica filtros por estatus y año
     */
    private function applyStatusAndYearFilters($query, $request)
    {
        if (! empty($request->id_cat_status)) {
            $query->where('correspondencia.tbl_correspondencia.id_cat_estatus', $request->id_cat_status);
        }

        if (! empty($request->id_cat_date_informe)) {
            $query->where('correspondencia.tbl_correspondencia.id_cat_anio', $request->id_cat_date_informe);
        }
    }

    /**
     * Aplica filtros por fechas
     */
    private function applyDateFilters($query, $request)
    {
        $fechaInicio = $request->fecha_inicio_informe;
        $fechaFin = $request->fecha_fin_informe;

        if (! empty($fechaInicio) && empty($fechaFin)) {
            $query->whereDate('correspondencia.tbl_correspondencia.fecha_usuario_captura', $fechaInicio);
        } elseif (empty($fechaInicio) && ! empty($fechaFin)) {
            $query->whereDate('correspondencia.tbl_correspondencia.fecha_usuario_captura', $fechaFin);
        } elseif (! empty($fechaInicio) && ! empty($fechaFin)) {
            $query->whereDate('correspondencia.tbl_correspondencia.fecha_usuario_captura', '>=', $fechaInicio)
                ->whereDate('correspondencia.tbl_correspondencia.fecha_usuario_captura', '<=', $fechaFin);
        }
    }

    /**
     * Aplica filtros por horas
     */
    private function applyHourFilters($query, $request)
    {
        if (isset($request->incluir_horas) && $request->incluir_horas == 0 && ! empty($request->inicio) && ! empty($request->fin)) {
            $query->whereRaw('EXTRACT(HOUR FROM correspondencia.tbl_correspondencia.fecha_usuario_captura) >= ?', [$request->inicio])
                ->whereRaw('EXTRACT(HOUR FROM correspondencia.tbl_correspondencia.fecha_usuario_captura) <= ?', [$request->fin]);
        }
    }
}
