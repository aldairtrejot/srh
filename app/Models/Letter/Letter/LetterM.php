<?php

namespace App\Models\Letter\Letter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LetterM extends Model
{
    protected $table = 'correspondencia.tbl_correspondencia';
    public $timestamps = false;
    protected $primaryKey = 'id_tbl_correspondencia';

    protected $fillable = [
        'num_turno_sistema',
        'num_documento',
        'fecha_captura',
        'fecha_inicio',
        'fecha_fin',
        'num_flojas',
        'num_tomos',
        'horas_respuesta',
        'asunto',
        'observaciones',
        'fecha_usuario',
        'id_cat_area',
        'id_usuario_area',
        'id_usuario_enlace',
        'id_cat_estatus',
        'id_cat_remitente',
        'id_cat_anio',
        'id_cat_tramite',
        'id_cat_clave',
        'id_cat_unidad',
        'id_cat_coordinacion',
        'id_usuario_sistema',
        'puesto_remitente',
        'folio_gestion',
        'id_usuario_captura',
        'fecha_usuario_captura',
        'es_doc_fisico',
        'son_mas_remitentes',
        'remitente',
        'fecha_documento',
        'id_cat_entidad',
        'id_cat_area_1',
        'id_cat_area_2',
    ];

    protected $casts = [
        'fecha_captura'          => 'date:Y-m-d',
        'fecha_inicio'           => 'date:Y-m-d',
        'fecha_fin'              => 'date:Y-m-d',
        'fecha_usuario'          => 'datetime',
        'fecha_usuario_captura'  => 'datetime',
        'fecha_documento'        => 'date:Y-m-d',
        'num_flojas'             => 'integer',
        'num_tomos'              => 'integer',
        'horas_respuesta'        => 'integer',
        'es_doc_fisico'          => 'boolean',
        'son_mas_remitentes'     => 'boolean',
        'id_cat_area'            => 'integer',
        'id_cat_area_1'          => 'integer',
        'id_cat_area_2'          => 'integer',
        'id_cat_estatus'         => 'integer',
        'id_cat_unidad'          => 'integer',
        'id_cat_coordinacion'    => 'integer',
        'id_cat_tramite'         => 'integer',
        'id_cat_clave'           => 'integer',
        'id_cat_anio'            => 'integer',
        'id_cat_entidad'         => 'integer',
    ];

    public function getIdFolGestion($folGestion)
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->select('id_tbl_correspondencia as id')
            ->whereRaw('TRIM(UPPER(folio_gestion)) = TRIM(UPPER(?))', [$folGestion])
            ->first();
    }

    public function edit(string $id)
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->where('id_tbl_correspondencia', $id)
            ->first() ?? null;
    }

    public function editFol(string $fol)
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->whereRaw('UPPER(TRIM(folio_gestion)) = ?', [strtoupper(trim($fol))])
            ->first() ?? null;
    }

    public function list($iterator, $searchValue, $idUser, $pageSize = 5)
    {
        $pageSize = max(1, (int)$pageSize);

        $query = DB::table('correspondencia.tbl_correspondencia')
            ->select([
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia AS id',
                DB::raw('UPPER(correspondencia.tbl_correspondencia.num_documento) AS num_documento'),
                DB::raw('UPPER(correspondencia.tbl_correspondencia.folio_gestion) AS folio_gestion'),
                DB::raw('UPPER(correspondencia.cat_estatus.descripcion) AS estatus'),
                DB::raw('UPPER(correspondencia.cat_tramite.descripcion) AS tramite'),
                DB::raw('UPPER(area_main.descripcion) AS area'),
                DB::raw('UPPER(area1.descripcion) AS area_1'),
                DB::raw('UPPER(area2.descripcion) AS area_2'),
                DB::raw('UPPER(correspondencia.tbl_correspondencia.asunto) AS asunto'),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_captura::date, 'DD/MM/YYYY') AS fecha_captura"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin::date, 'DD/MM/YYYY') AS fecha_fin"),
                DB::raw("(
                    SELECT co.uid
                    FROM correspondencia.ctrl_correspondencia_oficio co
                    WHERE co.id_tbl_correspondencia = correspondencia.tbl_correspondencia.id_tbl_correspondencia
                    ORDER BY co.fecha_usuario DESC
                    LIMIT 1
                ) AS uid"),
            ])
            ->join('correspondencia.cat_estatus', 'correspondencia.tbl_correspondencia.id_cat_estatus', '=', 'correspondencia.cat_estatus.id_cat_estatus')
            ->leftJoin('correspondencia.cat_area AS area_main', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'area_main.id_cat_area')
            ->leftJoin('correspondencia.cat_area AS area1', 'correspondencia.tbl_correspondencia.id_cat_area_1', '=', 'area1.id_cat_area')
            ->leftJoin('correspondencia.cat_area AS area2', 'correspondencia.tbl_correspondencia.id_cat_area_2', '=', 'area2.id_cat_area')
            ->join('correspondencia.cat_tramite', 'correspondencia.tbl_correspondencia.id_cat_tramite', '=', 'correspondencia.cat_tramite.id_cat_tramite')
            ->leftJoin('correspondencia.ctrl_transcribir_correspondencia', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.ctrl_transcribir_correspondencia.id_tbl_correspondencia')
            ->groupBy(
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia',
                'correspondencia.tbl_correspondencia.num_documento',
                'correspondencia.tbl_correspondencia.folio_gestion',
                'correspondencia.cat_estatus.descripcion',
                'correspondencia.cat_tramite.descripcion',
                'area_main.descripcion',
                'area1.descripcion',
                'area2.descripcion',
                'correspondencia.tbl_correspondencia.asunto',
                'correspondencia.tbl_correspondencia.fecha_captura',
                'correspondencia.tbl_correspondencia.fecha_fin'
            );

        if (!empty($idUser)) {
            $query->where(function ($q) use ($idUser) {
                $q->whereIn('correspondencia.tbl_correspondencia.id_cat_area', $idUser)
                  ->orWhereIn('correspondencia.tbl_correspondencia.id_cat_area_1', $idUser)
                  ->orWhereIn('correspondencia.tbl_correspondencia.id_cat_area_2', $idUser)
                  ->orWhereIn('correspondencia.ctrl_transcribir_correspondencia.id_cat_area', $idUser);
            });

            $query->where('correspondencia.tbl_correspondencia.id_cat_estatus', '!=', 2);
        }

        if (!empty($searchValue)) {
            $sv = '%'.trim($searchValue).'%';
            $query->where(function ($q) use ($sv) {
                $q->whereRaw("TRIM(correspondencia.tbl_correspondencia.num_documento) ILIKE ?", [$sv])
                  ->orWhereRaw("TRIM(correspondencia.tbl_correspondencia.asunto) ILIKE ?", [$sv])
                  ->orWhereRaw("TRIM(correspondencia.cat_estatus.descripcion) ILIKE ?", [$sv])
                  ->orWhereRaw("TRIM(correspondencia.tbl_correspondencia.folio_gestion) ILIKE ?", [$sv])
                  ->orWhereRaw("TRIM(area_main.descripcion) ILIKE ?", [$sv])
                  ->orWhereRaw("TRIM(area1.descripcion) ILIKE ?", [$sv])
                  ->orWhereRaw("TRIM(area2.descripcion) ILIKE ?", [$sv])
                  ->orWhereRaw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_captura, 'DD/MM/YYYY') ILIKE ?", [$sv]);
            });
        }

        if (!empty($idUser)) {
            $query->orderByRaw('CASE correspondencia.tbl_correspondencia.id_cat_estatus
                                WHEN 1 THEN 1
                                WHEN 2 THEN 2
                                WHEN 3 THEN 3
                                WHEN 4 THEN 4
                                WHEN 5 THEN 5
                                WHEN 6 THEN 6
                                ELSE 7 END ASC');
        } else {
            $query->orderBy('correspondencia.tbl_correspondencia.id_tbl_correspondencia', 'DESC');
        }

        $query->offset($iterator)->limit($pageSize);
        return $query->get();
    }

    public function getArea2OptionsByArea1(int $area1Id)
    {
        return DB::table('correspondencia.rel_cat_area_jerarquia_1 as r')
            ->join('correspondencia.cat_area as a2', 'r.id_cat_area_2', '=', 'a2.id_cat_area')
            ->select('a2.id_cat_area as id', DB::raw('UPPER(a2.descripcion) as descripcion'))
            ->where('r.id_cat_area_1', $area1Id)
            ->distinct()
            ->orderBy('descripcion')
            ->get();
    }

    public function getArea1Options()
    {
        return DB::table('correspondencia.rel_cat_area_jerarquia_1 as r')
            ->join('correspondencia.cat_area as a', 'r.id_cat_area_1', '=', 'a.id_cat_area')
            ->select('a.id_cat_area as id', DB::raw('UPPER(a.descripcion) as descripcion'))
            ->distinct()
            ->orderBy('descripcion')
            ->get();
    }

    public function getArea1OptionsByArea(int $miAreaId)
    {
        return DB::table('correspondencia.rel_cat_area_jerarquia_1 as r')
            ->join('correspondencia.cat_area as a', 'r.id_cat_area_1', '=', 'a.id_cat_area')
            ->select('a.id_cat_area as id', DB::raw('UPPER(a.descripcion) as descripcion'))
            ->where('r.id_cat_area', $miAreaId)
            ->distinct()
            ->orderBy('descripcion')
            ->get();
    }

    public function getArea1EditObj($id = null)
    {
        if (!$id) return null;
        return DB::table('correspondencia.cat_area')
            ->select('id_cat_area as id', DB::raw('UPPER(descripcion) as descripcion'))
            ->where('id_cat_area', $id)
            ->first();
    }

    public function getArea2Options()
    {
        return DB::table('correspondencia.rel_cat_area_jerarquia_2 as r')
            ->join('correspondencia.cat_area as a', 'r.id_cat_area_2', '=', 'a.id_cat_area')
            ->select('a.id_cat_area as id', DB::raw('UPPER(a.descripcion) as descripcion'))
            ->distinct()
            ->orderBy('descripcion')
            ->get();
    }

    public function getArea2EditObj($id = null)
    {
        if (!$id) return null;
        return DB::table('correspondencia.cat_area')
            ->select('id_cat_area as id', DB::raw('UPPER(descripcion) as descripcion'))
            ->where('id_cat_area', $id)
            ->first();
    }

    public function validateNoDocument($id, $value)
    {
        $q = DB::table('correspondencia.tbl_correspondencia')
            ->select('correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->whereRaw('UPPER(TRIM(correspondencia.tbl_correspondencia.num_documento)) = UPPER(TRIM(?))', [trim($value)]);

        if (isset($id)) {
            $q->whereRaw('correspondencia.tbl_correspondencia.id_tbl_correspondencia <> ?', [$id]);
        }

        return $q->first();
    }

    public function uniqueNoDocument($id, $value, $attribute)
    {
        $q = DB::table('correspondencia.tbl_correspondencia')
            ->select('correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->whereRaw('UPPER(TRIM(correspondencia.tbl_correspondencia.' . $attribute . ')) = UPPER(TRIM(?))', [trim($value)]);

        if (isset($id)) {
            $q->whereRaw('correspondencia.tbl_correspondencia.id_tbl_correspondencia <> ?', [$id]);
        }

        return $q->first();
    }

    public function getDataReport($id)
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.num_turno_sistema AS num_turno_sistema',
                'correspondencia.tbl_correspondencia.num_documento AS num_documento',
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin, 'DD/MM/YYYY') AS fecha_fin"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_documento, 'DD/MM/YYYY') AS fecha_documento"),
                'correspondencia.tbl_correspondencia.num_flojas AS num_flojas',
                'correspondencia.tbl_correspondencia.num_tomos AS num_tomos',
                'correspondencia.tbl_correspondencia.horas_respuesta AS horas_respuesta',
                'correspondencia.tbl_correspondencia.asunto AS asunto',
                'correspondencia.tbl_correspondencia.folio_gestion AS folio_gestion',
                'correspondencia.tbl_correspondencia.observaciones AS observaciones',
                DB::raw("COALESCE(correspondencia.cat_remitente.nombre, '') || ' ' || 
                         COALESCE(correspondencia.cat_remitente.primer_apellido, '') || ' ' ||
                         COALESCE(correspondencia.cat_remitente.segundo_apellido, '') || ' ' ||
                         ' - ' || COALESCE(correspondencia.cat_remitente.rfc, '') AS remitente"),
                'correspondencia.cat_anio.descripcion AS anio',
                'correspondencia.cat_tramite.descripcion AS tramite',
                'correspondencia.cat_area.descripcion AS area',
                'correspondencia.cat_clave.descripcion AS codigo',
                'correspondencia.cat_clave.redaccion AS clave',
                'correspondencia.cat_unidad.descripcion AS unidad',
                'correspondencia.cat_coordinacion.descripcion AS coordinacion',
                'correspondencia.tbl_correspondencia.puesto_remitente AS puesto_remitente',
                'correspondencia.cat_entidad.descripcion AS entidad',
                'administration.users.name AS user_area'
            )
            ->leftJoin('correspondencia.cat_area', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->leftJoin('correspondencia.cat_remitente', 'correspondencia.tbl_correspondencia.id_cat_remitente', '=', 'correspondencia.cat_remitente.id_cat_remitente')
            ->leftJoin('correspondencia.cat_anio', 'correspondencia.tbl_correspondencia.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->leftJoin('correspondencia.cat_tramite', 'correspondencia.tbl_correspondencia.id_cat_tramite', '=', 'correspondencia.cat_tramite.id_cat_tramite')
            ->leftJoin('correspondencia.cat_clave', 'correspondencia.tbl_correspondencia.id_cat_clave', '=', 'correspondencia.cat_clave.id_cat_clave')
            ->leftJoin('correspondencia.cat_unidad', 'correspondencia.tbl_correspondencia.id_cat_unidad', '=', 'correspondencia.cat_unidad.id_cat_unidad')
            ->leftJoin('correspondencia.cat_coordinacion', 'correspondencia.tbl_correspondencia.id_cat_coordinacion', '=', 'correspondencia.cat_coordinacion.id_cat_coordinacion')
            ->leftJoin('correspondencia.cat_entidad', 'correspondencia.tbl_correspondencia.id_cat_entidad', '=', 'correspondencia.cat_entidad.id_cat_entidad')
            ->leftJoin('administration.users', 'correspondencia.tbl_correspondencia.id_usuario_captura', '=', 'administration.users.id')
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', $id)
            ->first();
    }

    public function dataCloud($id)
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.num_turno_sistema',
                'correspondencia.tbl_correspondencia.num_documento',
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_inicio, 'DD/MM/YYYY') as fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin, 'DD/MM/YYYY') as fecha_fin"),
                'correspondencia.cat_anio.descripcion as anio'
            )
            ->join('correspondencia.cat_anio', 'correspondencia.tbl_correspondencia.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', $id)
            ->first();
    }

    public function getTurno($id)
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->where('id_tbl_correspondencia', $id)
            ->value('folio_gestion') ?: null;
    }

    public function validateNoTurno($noTurno)
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->whereRaw('UPPER(TRIM(folio_gestion)) = UPPER(TRIM(?))', [$noTurno])
            ->value('id_tbl_correspondencia');
    }

    public function validateNoTurnoArea($noTurno)
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->where('num_turno_sistema', $noTurno)
            ->value('correspondencia.tbl_correspondencia.id_cat_area') ?: null;
    }

    public function getValue($id_letter, $id_area)
    {
        $exists = DB::table('correspondencia.tbl_correspondencia')
            ->select('correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->leftJoin('correspondencia.ctrl_transcribir_correspondencia', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.ctrl_transcribir_correspondencia.id_tbl_correspondencia')
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', $id_letter)
            ->where(function ($q) use ($id_area) {
                $q->where('correspondencia.tbl_correspondencia.id_cat_area', '=', $id_area)
                  ->orWhere('correspondencia.tbl_correspondencia.id_cat_area_1', '=', $id_area)
                  ->orWhere('correspondencia.tbl_correspondencia.id_cat_area_2', '=', $id_area)
                  ->orWhere('correspondencia.ctrl_transcribir_correspondencia.id_cat_area', '=', $id_area);
            })
            ->exists();

        return !$exists;
    }

    public function getUserEnlace($value)
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia',
                'correspondencia.tbl_correspondencia.id_cat_area AS id_cat_area',
                'correspondencia.tbl_correspondencia.id_usuario_area AS id_usuario_area',
                'correspondencia.tbl_correspondencia.id_usuario_enlace AS id_usuario_enlace',
                DB::raw('UPPER(user_area.name) as usuario_area'),
                DB::raw('UPPER(correspondencia.cat_area.descripcion) as area'),
                DB::raw('UPPER(user_enlace.name) as usuario_enlace')
            )
            ->join('administration.users AS user_area', 'correspondencia.tbl_correspondencia.id_usuario_area', '=', 'user_area.id')
            ->join('administration.users AS user_enlace', 'correspondencia.tbl_correspondencia.id_usuario_enlace', '=', 'user_enlace.id')
            ->join('correspondencia.cat_area', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->whereRaw('UPPER(TRIM(correspondencia.tbl_correspondencia.folio_gestion)) = UPPER(TRIM(?))', [$value])
            ->get();
    }

    public function mailLetter($id)
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->join('correspondencia.cat_area', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->join('administration.users AS users_user', 'correspondencia.tbl_correspondencia.id_usuario_area', '=', 'users_user.id')
            ->join('administration.users AS users_enlace', 'correspondencia.tbl_correspondencia.id_usuario_enlace', '=', 'users_enlace.id')
            ->select(
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia',
                DB::raw('UPPER(correspondencia.tbl_correspondencia.asunto) AS asunto'),
                DB::raw('UPPER(correspondencia.tbl_correspondencia.num_turno_sistema) AS num_turno_sistema'),
                DB::raw('UPPER(correspondencia.tbl_correspondencia.folio_gestion) AS folio_gestion'),
                DB::raw('UPPER(correspondencia.tbl_correspondencia.num_documento) AS num_documento'),
                DB::raw('TO_CHAR(correspondencia.tbl_correspondencia.fecha_inicio, \'DD/MM/YYYY\') AS fecha_inicio'),
                DB::raw('TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin, \'DD/MM/YYYY\') AS fecha_fin'),
                DB::raw('UPPER(correspondencia.cat_area.descripcion) AS area_descripcion'),
                DB::raw('UPPER(users_user.name) AS usuario_area'),
                DB::raw('UPPER(users_enlace.name) AS usuario_enlace')
            )
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', $id)
            ->first();
    }

    public function getMaxNuSistem()
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->selectRaw("
                MAX(
                    CAST(
                        substring(num_turno_sistema FROM '/([0-9]{3,})/') 
                    AS INTEGER)
                ) AS max_num_turno
            ")
            ->whereRaw("num_turno_sistema ~ '/[0-9]{3,}/'")
            ->value('max_num_turno');
    }

    public function tableCopy($id)
    {
        return DB::table('correspondencia.ctrl_transcribir_correspondencia')
            ->select(
                'correspondencia.ctrl_transcribir_correspondencia.id_ctrl_transcribir_correspondencia AS id',
                'correspondencia.cat_area.descripcion AS area',
                'usuario_x.name AS usuario',
                'enlace_y.name AS enlace',
                'correspondencia.cat_tramite.descripcion AS tramite',
                'correspondencia.cat_clave.descripcion AS clave'
            )
            ->join('correspondencia.cat_area', 'correspondencia.ctrl_transcribir_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->join('administration.users AS usuario_x', 'correspondencia.ctrl_transcribir_correspondencia.id_usuario_area', '=', 'usuario_x.id')
            ->join('administration.users AS enlace_y', 'correspondencia.ctrl_transcribir_correspondencia.id_usuario_enlace', '=', 'enlace_y.id')
            ->join('correspondencia.cat_tramite', 'correspondencia.ctrl_transcribir_correspondencia.id_cat_tramite', '=', 'correspondencia.cat_tramite.id_cat_tramite')
            ->join('correspondencia.cat_clave', 'correspondencia.ctrl_transcribir_correspondencia.id_cat_clave', '=', 'correspondencia.cat_clave.id_cat_clave')
            ->where('correspondencia.ctrl_transcribir_correspondencia.id_tbl_correspondencia', '=', $id)
            ->limit(20)
            ->get();
    }

    /* =========================================================
     *  NUEVO — helpers para "RETORNADO"
     * ========================================================= */
public function getReturnadoId(): int
{
    // Resuelve por nombre; si no encuentra, usa 8 como fallback
    try {
        $named = DB::table('correspondencia.cat_estatus')
            ->whereRaw('UPPER(TRIM(descripcion)) = ?', ['RETURNADO'])
            ->value('id_cat_estatus');
        if ($named) {
            return (int)$named;
        }
    } catch (\Throwable $e) {
        // noop
    }
    return 8;
}

public function areaHasReturnado(?int $areaId): bool
{
    if (!$areaId) return false;
    $returnadoId = $this->getReturnadoId();

    return DB::table('correspondencia.rel_area_estatus')
        ->where('id_cat_area', $areaId)
        ->where('id_cat_estatus', $returnadoId)
        ->where('estatus', true)
        ->exists();
}

public function areaOnlyReturnado(?int $areaId): bool
{
    if (!$areaId) return false;
    $returnadoId = $this->getReturnadoId();

    // Tiene Returnado activo...
    $hasReturnado = DB::table('correspondencia.rel_area_estatus')
        ->where('id_cat_area', $areaId)
        ->where('estatus', true)
        ->where('id_cat_estatus', $returnadoId)
        ->exists();

    if (!$hasReturnado) return false;

    // ...y NO tiene ningún otro estatus activo
    $hasOther = DB::table('correspondencia.rel_area_estatus')
        ->where('id_cat_area', $areaId)
        ->where('estatus', true)
        ->where('id_cat_estatus', '<>', $returnadoId)
        ->exists();

    return !$hasOther;
}
}
