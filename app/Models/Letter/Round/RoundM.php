<?php

namespace App\Models\Letter\Round;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RoundM extends Model
{
    protected $table = 'correspondencia.tbl_circular';
    public $timestamps = false;
    protected $primaryKey = 'id_tbl_circular';

    protected $fillable = [
        'num_turno_sistema',
        'fecha_inicio',
        'fecha_fin',
        'fecha_captura',
        'asunto',
        'observaciones',
        'fecha_usuario',
        'id_usuario_sistema',
        'id_cat_anio',
        'id_tbl_correspondencia',
        'es_por_area',
        'num_documento_area',
        'id_cat_area_documento',
        'id_usuario_captura',
        'id_usuario_area',
        'id_usuario_enlace',
        'id_cat_area',
        'destinatario',
    ];

    public function edit(string $id)
    {
        return DB::table('correspondencia.tbl_circular')
            ->where('id_tbl_circular', $id)
            ->first();
    }

    // ✅ LISTADO PARA TABLA (incluye Área)
    public function list($iterator, $searchValue, $idUser)
    {
        $query = DB::table('correspondencia.tbl_circular')
            ->select([
                'correspondencia.tbl_circular.id_tbl_circular AS id',
                DB::raw('correspondencia.tbl_circular.num_turno_sistema AS num_turno_sistema'),
                DB::raw("
                    CASE 
                        WHEN correspondencia.tbl_circular.es_por_area THEN 
                            correspondencia.tbl_circular.num_documento_area 
                        ELSE 
                            correspondencia.tbl_correspondencia.num_turno_sistema 
                    END AS num_documento
                "),
                DB::raw("UPPER(correspondencia.tbl_circular.asunto) AS asunto"),
                DB::raw("TO_CHAR(correspondencia.tbl_circular.fecha_inicio::date, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_circular.fecha_fin::date, 'DD/MM/YYYY') AS fecha_fin"),
                DB::raw("correspondencia.cat_anio.descripcion AS anio"),
                DB::raw("correspondencia.cat_area.descripcion AS area"), // ✅ NUEVO
            ])
            ->leftJoin(
                'correspondencia.tbl_correspondencia',
                'correspondencia.tbl_circular.id_tbl_correspondencia',
                '=',
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia'
            )
            ->join(
                'correspondencia.cat_anio',
                'correspondencia.tbl_circular.id_cat_anio',
                '=',
                'correspondencia.cat_anio.id_cat_anio'
            )
            ->leftJoin( // ✅ NUEVO
                'correspondencia.cat_area',
                'correspondencia.tbl_circular.id_cat_area',
                '=',
                'correspondencia.cat_area.id_cat_area'
            );

        // (Opcional) Filtro por usuario (si lo ocupas, descomenta)
        /*
        if (!empty($idUser)) {
            $query->where(function ($q) use ($idUser) {
                $q->where('correspondencia.tbl_circular.id_usuario_area', $idUser)
                  ->orWhere('correspondencia.tbl_circular.id_usuario_enlace', $idUser);
            });
        }
        */

        // Búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));

            $query->where(function ($q) use ($searchValue) {
                $q->whereRaw("UPPER(TRIM(correspondencia.tbl_circular.num_turno_sistema)) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_correspondencia.num_turno_sistema)) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_circular.num_documento_area)) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(TRIM(correspondencia.cat_anio.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(TRIM(correspondencia.cat_area.descripcion)) LIKE ?", ['%' . $searchValue . '%']) // ✅ NUEVO (buscar por área)
                  ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_circular.asunto)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Paginación
        $query->orderBy('correspondencia.tbl_circular.id_tbl_circular', 'DESC')
            ->offset($iterator)
            ->limit(5);

        return $query->get();
    }

    public function dataCloud($id)
    {
        return DB::table('correspondencia.tbl_circular')
            ->leftJoin('correspondencia.tbl_correspondencia', 'correspondencia.tbl_circular.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->join('correspondencia.cat_anio', 'correspondencia.tbl_circular.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->select(
                'correspondencia.tbl_circular.num_turno_sistema AS num_turno_sistema',
                DB::raw('CASE WHEN correspondencia.tbl_circular.es_por_area THEN 
                                    correspondencia.tbl_circular.num_documento_area ELSE 
                                    correspondencia.tbl_correspondencia.num_turno_sistema 
                                END AS num_turno_sistema_correspondencia'),
                DB::raw("TO_CHAR(correspondencia.tbl_circular.fecha_inicio::date, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_circular.fecha_fin::date, 'DD/MM/YYYY') AS fecha_fin"),
                'correspondencia.cat_anio.descripcion AS anio'
            )
            ->where('correspondencia.tbl_circular.id_tbl_circular', $id)
            ->first();
    }

    public function getMaxNuSistem()
    {
        return DB::table('correspondencia.tbl_circular')
            ->selectRaw("
                MAX(CAST(REGEXP_REPLACE(num_turno_sistema, '^[^/]+/([0-9]{5})/.*$', '\\1') AS INTEGER)) AS max_num_turno
            ")
            ->whereRaw("num_turno_sistema ~ '/[0-9]{5}/'")
            ->value('max_num_turno');
    }

    public function getOnly($idCatArea, $idAnio)
    {
        return DB::table('correspondencia.tbl_circular')
            ->selectRaw('MAX(CAST(REGEXP_REPLACE(num_documento_area, \'^\\D*(\\d+).*\', \'\\1\') AS INT)) AS max_num')
            ->whereRaw("num_documento_area ~ '/\\d{4}$'")
            ->where('id_cat_area_documento', $idCatArea)
            ->where('id_cat_anio', $idAnio)
            ->first();
    }

    public function getReporteFiltrado($area, $year)
    {
        $query = DB::table('correspondencia.tbl_circular as o')
            ->select([
                'o.num_turno_sistema as No_Turno',
                'o.fecha_captura as Fecha_captura',
                'anio.descripcion as Anio',
                'a.descripcion as Area',
                'o.num_documento_area as No_Doc',
                'ua.name as Usuario',
                'ue.name as Enlace',
                'o.fecha_inicio as Fecha_emision',
                'o.fecha_fin as Fecha_aplicacion',
                'o.asunto as Asunto',
                'o.destinatario as Destinatario',
                'o.observaciones as Observaciones'
            ])
            ->leftJoin('correspondencia.tbl_correspondencia as c', 'o.id_tbl_correspondencia', '=', 'c.id_tbl_correspondencia')
            ->leftJoin('correspondencia.cat_anio as anio', 'o.id_cat_anio', '=', 'anio.id_cat_anio')
            ->leftJoin('correspondencia.cat_area as a', 'o.id_cat_area', '=', 'a.id_cat_area')
            ->leftJoin('administration.users as ua', 'o.id_usuario_area', '=', 'ua.id')
            ->leftJoin('administration.users as ue', 'o.id_usuario_enlace', '=', 'ue.id');

        if (!empty($area)) {
            $query->where('o.id_cat_area', $area);
        }
        if (!empty($year)) {
            $query->where('o.id_cat_anio', $year);
        }

        return $query->orderBy('o.id_tbl_circular', 'ASC')->get();
    }
}
