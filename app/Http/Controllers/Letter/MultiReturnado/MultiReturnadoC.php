<?php

namespace App\Http\Controllers\Letter\MultiReturnado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MultiReturnadoC extends Controller
{
    public function areas(Request $request, $idCorr = null)

    {
        $rows = DB::table('correspondencia.cat_area')
            ->select('id_cat_area', 'descripcion', 'clave')
            ->orderBy('descripcion', 'asc')
            ->get();

        return response()->json(['ok' => true, 'value' => $rows]);
    }

    /**
     * LIST: trae turnados creados por MULTI-RETURNADO + detalle
     */
    public function list($idCorr)
    {
        $idCorr = (int)$idCorr;
        if ($idCorr <= 0) {
            return response()->json(['ok' => false, 'message' => 'ID inválido.'], 422);
        }

        $rows = DB::table('correspondencia.tbl_correspondencia_turnado as t')
            ->join('correspondencia.cat_area as a', 'a.id_cat_area', '=', 't.id_cat_area_destino')
            ->leftJoin('correspondencia.tbl_correspondencia_turnado_detalle as d', 'd.id_tbl_correspondencia_turnado', '=', 't.id_tbl_correspondencia_turnado')
            ->leftJoin('correspondencia.cat_tramite as ct', 'ct.id_cat_tramite', '=', 'd.id_cat_tramite')
            ->leftJoin('correspondencia.cat_clave as cc', 'cc.id_cat_clave', '=', 'd.id_cat_clave')
            ->leftJoin('correspondencia.cat_unidad as cu', 'cu.id_cat_unidad', '=', 'd.id_cat_unidad')
            ->leftJoin('correspondencia.cat_coordinacion as cco', 'cco.id_cat_coordinacion', '=', 'd.id_cat_coordinacion')
            ->leftJoin('administration.users as ua', 'ua.id', '=', 'd.id_usuario_area')
            ->leftJoin('administration.users as ue', 'ue.id', '=', 'd.id_usuario_enlace')
            ->where('t.id_tbl_correspondencia', $idCorr)
            ->where('t.observaciones', 'like', 'MULTI-RETURNADO:%')
            ->orderBy('t.consecutivo', 'asc')
            ->select([
                't.id_tbl_correspondencia_turnado',
                't.id_cat_area_destino',
                'a.descripcion as area',
                't.folio_turnado',
                't.estatus',
                't.fecha_turno',

                'd.id_usuario_area',
                'd.id_usuario_enlace',
                'd.id_cat_tramite',
                'd.id_cat_clave',
                'd.id_cat_unidad',
                'd.id_cat_coordinacion',

                'ct.descripcion as tramite',
                'cc.descripcion as clave',
                'cu.descripcion as unidad',
                'cco.descripcion as coordinacion',

                DB::raw("COALESCE(ua.name,'') as usuario_area"),
                DB::raw("COALESCE(ue.name,'') as usuario_enlace"),
            ])
            ->get();

        return response()->json(['ok' => true, 'value' => $rows]);
    }

    /**
     * SAVE: items[] => inserta en PADRE (turnado) y en HIJA (turnado_detalle)
     */
    public function save(Request $request)
    {
        $request->validate([
            'id_tbl_correspondencia'         => 'required|integer',
            'items'                          => 'required|array|min:1',
            'items.*.id_cat_area_destino'    => 'required|integer',
            'items.*.id_usuario_area'        => 'nullable|integer',
            'items.*.id_usuario_enlace'      => 'nullable|integer',
            'items.*.id_cat_tramite'         => 'nullable|integer',
            'items.*.id_cat_clave'           => 'nullable|integer',
            'items.*.id_cat_unidad'          => 'nullable|integer',
            'items.*.id_cat_coordinacion'    => 'nullable|integer',
            'observaciones'                  => 'nullable|string|max:450',
        ]);

        $idCorr = (int)$request->id_tbl_correspondencia;
        $items  = $request->items;
        $obs    = $request->observaciones ?? null;

        $userId = auth()->id();
        if (!$userId) return response()->json(['ok'=>false,'message'=>'Sesión no válida.'], 401);

        $FOLIO_COL = 'folio_gestion';

        try {
            return DB::transaction(function () use ($idCorr, $items, $obs, $userId, $FOLIO_COL) {

                // Folio base
                $folioBase = DB::table('correspondencia.tbl_correspondencia')
                    ->where('id_tbl_correspondencia', $idCorr)
                    ->value($FOLIO_COL);

                $folioBase = is_string($folioBase) ? trim($folioBase) : '';
                if ($folioBase === '') {
                    throw new \RuntimeException("No se encontró folio base (columna: {$FOLIO_COL}).");
                }

                // Normaliza áreas (evita repetidas)
                $areas = [];
                foreach ($items as $it) $areas[] = (int)$it['id_cat_area_destino'];
                $areas = array_values(array_unique($areas));

                // Cat áreas (para clave)
                $catAreas = DB::table('correspondencia.cat_area')
                    ->whereIn('id_cat_area', $areas)
                    ->select('id_cat_area', 'clave', 'descripcion')
                    ->get()->keyBy('id_cat_area');

                // Evitar duplicados (ya existe doc+destino)
                $yaExisten = DB::table('correspondencia.tbl_correspondencia_turnado')
                    ->where('id_tbl_correspondencia', $idCorr)
                    ->whereIn('id_cat_area_destino', $areas)
                    ->pluck('id_cat_area_destino')
                    ->map(fn($v)=>(int)$v)->all();

                $yaSet = array_flip($yaExisten);

                // Consecutivo base
                $maxCons = (int)(DB::table('correspondencia.tbl_correspondencia_turnado')
                    ->where('id_tbl_correspondencia', $idCorr)
                    ->max('consecutivo') ?? 0);

                $now = now();
                $cons = $maxCons;

                $obsFinal = 'MULTI-RETURNADO: ' . trim((string)($obs ?? ''));

                $insertados = 0;

                // Para cada item: si el área ya existía, la ignoramos
                foreach ($items as $it) {
                    $areaId = (int)$it['id_cat_area_destino'];
                    if (isset($yaSet[$areaId])) continue;

                    $cons++;

                    $claveArea = isset($catAreas[$areaId]) ? trim((string)$catAreas[$areaId]->clave) : '';
                    if ($claveArea === '') $claveArea = 'SINCLAVE';

                    $folioTurnado = $folioBase . '-' . $claveArea . '-' . str_pad((string)$cons, 2, '0', STR_PAD_LEFT);

                    // 1) INSERT PADRE
                    $idTurnado = DB::table('correspondencia.tbl_correspondencia_turnado')->insertGetId([
                        'id_tbl_correspondencia' => $idCorr,
                        'id_cat_area_destino'    => $areaId,
                        'id_usuario_turna'       => $userId,
                        'consecutivo'            => $cons,
                        'folio_turnado'          => $folioTurnado,
                        'es_copia'               => false,
                        'estatus'                => 'RETURNADO',
                        'fecha_turno'            => $now,
                        'fecha_limite'           => null,
                        'fecha_atendido'         => null,
                        'observaciones'          => $obsFinal,
                        'fecha_creacion'         => $now,
                        'fecha_actualizacion'    => null,
                    ], 'id_tbl_correspondencia_turnado');

                    // 2) INSERT HIJA (DETALLE) 1 a 1 (por tu unique index)
                    DB::table('correspondencia.tbl_correspondencia_turnado_detalle')->insert([
                        'id_tbl_correspondencia_turnado' => $idTurnado,
                        'id_usuario_area'        => isset($it['id_usuario_area']) ? (int)$it['id_usuario_area'] : null,
                        'id_usuario_enlace'      => isset($it['id_usuario_enlace']) ? (int)$it['id_usuario_enlace'] : null,
                        'id_cat_tramite'         => isset($it['id_cat_tramite']) ? (int)$it['id_cat_tramite'] : null,
                        'id_cat_clave'           => isset($it['id_cat_clave']) ? (int)$it['id_cat_clave'] : null,
                        'id_cat_unidad'          => isset($it['id_cat_unidad']) ? (int)$it['id_cat_unidad'] : null,
                        'id_cat_coordinacion'    => isset($it['id_cat_coordinacion']) ? (int)$it['id_cat_coordinacion'] : null,
                        'created_at'             => $now,
                    ]);

                    $insertados++;
                }

                // Estatus general del documento (opcional) => RE-TURNADO (8)
                DB::table('correspondencia.tbl_correspondencia')
                    ->where('id_tbl_correspondencia', $idCorr)
                    ->update(['id_cat_estatus' => 8]);

                return response()->json([
                    'ok' => true,
                    'insertados' => $insertados,
                    'message' => $insertados
                        ? 'Multi-returnado guardado correctamente.'
                        : 'No se insertó nada (todas esas áreas ya estaban registradas).',
                ]);
            });

        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'message'=>$e->getMessage()], 422);
        }
    }

    /**
     * DELETE: borra PADRE (y por cascade borra HIJA)
     */
    public function delete(Request $request, $idTurnado)
    {
        $idTurnado = (int)$idTurnado;
        if ($idTurnado <= 0) return response()->json(['ok'=>false,'message'=>'ID inválido.'], 422);

        try {
            $row = DB::table('correspondencia.tbl_correspondencia_turnado')
                ->where('id_tbl_correspondencia_turnado', $idTurnado)
                ->first();

            if (!$row) return response()->json(['ok'=>false,'message'=>'No se encontró el registro.'], 404);

            if (!is_string($row->observaciones ?? '') || strpos($row->observaciones, 'MULTI-RETURNADO:') !== 0) {
                return response()->json(['ok'=>false,'message'=>'Este registro no pertenece a Multi-Returnado.'], 422);
            }

            DB::table('correspondencia.tbl_correspondencia_turnado')
                ->where('id_tbl_correspondencia_turnado', $idTurnado)
                ->delete();

            return response()->json(['ok'=>true,'message'=>'Destino eliminado correctamente.']);

        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'message'=>$e->getMessage()], 422);
        }
    }
}
