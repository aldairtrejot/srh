<?php

namespace App\Http\Controllers\Letter\Multiturno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MultiTurnoC extends Controller
{
    /**
     * GET: catálogo real de áreas destino
     * Devuelve: [{id_cat_area, descripcion, clave}]
     */
    public function areas(Request $request)
    {
        $rows = DB::table('correspondencia.cat_area')
            ->select('id_cat_area', 'descripcion', 'clave')
            ->orderBy('descripcion', 'asc')
            ->get();

        return response()->json([
            'ok' => true,
            'value' => $rows,
        ]);
    }

    /**
     * GET: list/{idCorr}
     * Devuelve los turnados YA guardados de un documento
     * value: [{id_cat_area_destino, area, folio_turnado, estatus, es_copia, fecha_turno}]
     */
    public function list($idCorr)
    {
        $idCorr = (int) $idCorr;
        if ($idCorr <= 0) {
            return response()->json([
                'ok' => false,
                'message' => 'ID de correspondencia inválido.',
            ], 422);
        }

        $rows = DB::table('correspondencia.tbl_correspondencia_turnado as t')
            ->join('correspondencia.cat_area as a', 'a.id_cat_area', '=', 't.id_cat_area_destino')
            ->where('t.id_tbl_correspondencia', $idCorr)
            ->orderBy('t.consecutivo', 'asc')
            ->select([
                't.id_tbl_correspondencia_turnado',
                't.id_tbl_correspondencia',
                't.id_cat_area_destino',
                'a.descripcion as area',
                't.folio_turnado',
                't.estatus',
                't.es_copia',
                't.fecha_turno',
            ])
            ->get();

        return response()->json([
            'ok' => true,
            'value' => $rows,
        ]);
    }

    /**
     * POST: guardar multiturno
     * Inserta N filas en correspondencia.tbl_correspondencia_turnado
     */
    public function save(Request $request)
{
    $request->validate([
        'id_tbl_correspondencia' => 'required|integer',
        'areas_destino'          => 'required|array|min:1',
        'areas_destino.*'        => 'integer',
        'observaciones'          => 'nullable|string|max:450',
    ]);

    $idCorr = (int) $request->id_tbl_correspondencia;
    $areas  = array_values(array_unique(array_map('intval', $request->areas_destino)));
    $obs    = $request->observaciones ?? null;

    $userId = auth()->id();
    if (!$userId) {
        return response()->json(['ok' => false, 'message' => 'Sesión no válida.'], 401);
    }

    // ✅ AJUSTA AQUÍ EL NOMBRE REAL DE TU COLUMNA DE FOLIO BASE:
    $FOLIO_COL = 'folio_gestion'; // <-- cámbialo si es otro

    try {
        return DB::transaction(function () use ($idCorr, $areas, $obs, $userId, $FOLIO_COL) {

            // 1) Folio base
            $folioBase = DB::table('correspondencia.tbl_correspondencia')
                ->where('id_tbl_correspondencia', $idCorr)
                ->value($FOLIO_COL);

            $folioBase = is_string($folioBase) ? trim($folioBase) : '';
            if ($folioBase === '') {
                throw new \RuntimeException("No se encontró el folio base del documento (columna: {$FOLIO_COL}).");
            }

            // 2) Catálogo áreas
            $catAreas = DB::table('correspondencia.cat_area')
                ->whereIn('id_cat_area', $areas)
                ->select('id_cat_area', 'clave', 'descripcion')
                ->get()
                ->keyBy('id_cat_area');

            $faltantes = [];
            foreach ($areas as $a) {
                if (!isset($catAreas[$a])) $faltantes[] = $a;
            }
            if (count($faltantes)) {
                throw new \RuntimeException('Áreas inexistentes en catálogo: ' . implode(', ', $faltantes));
            }

            // 3) Evitar duplicados (por unique doc+destino)
            $yaExisten = DB::table('correspondencia.tbl_correspondencia_turnado')
                ->where('id_tbl_correspondencia', $idCorr)
                ->whereIn('id_cat_area_destino', $areas)
                ->pluck('id_cat_area_destino')
                ->map(fn($v) => (int)$v)
                ->all();

            $yaSet = array_flip($yaExisten);
            $areasNuevas = array_values(array_filter($areas, fn($a) => !isset($yaSet[$a])));

            if (!count($areasNuevas)) {
                return response()->json([
                    'ok' => true,
                    'insertados' => 0,
                    'message' => 'No se insertó nada: todas esas áreas ya estaban turnadas.',
                ]);
            }

            // 4) Consecutivo siguiente
            $maxCons = (int) (DB::table('correspondencia.tbl_correspondencia_turnado')
                ->where('id_tbl_correspondencia', $idCorr)
                ->max('consecutivo') ?? 0);

            $now = now();
            $rows = [];
            $cons = $maxCons;

            foreach ($areasNuevas as $areaId) {
                $cons++;

                $clave = (string)($catAreas[$areaId]->clave ?? '');
                $clave = trim($clave);
                if ($clave === '') $clave = 'SINCLAVE';

                // folio único por turno
                $folioTurnado = $folioBase . '-' . $clave . '-' . str_pad((string)$cons, 2, '0', STR_PAD_LEFT);

                $rows[] = [
                    'id_tbl_correspondencia' => $idCorr,
                    'id_cat_area_destino'    => $areaId,
                    'id_usuario_turna'       => $userId,
                    'consecutivo'            => $cons,
                    'folio_turnado'          => $folioTurnado,
                    'es_copia'               => false,
                    'estatus'                => 'PENDIENTE',
                    'fecha_turno'            => $now,
                    'fecha_limite'           => null,
                    'fecha_atendido'         => null,
                    'observaciones'          => $obs,
                    'fecha_creacion'         => $now,
                    'fecha_actualizacion'    => null,
                ];
            }

            DB::table('correspondencia.tbl_correspondencia_turnado')->insert($rows);

            // ✅ 5) ACTUALIZAR ESTATUS EN LA CORRESPONDENCIA A "MULTI-TURNO" (id 9)
            DB::table('correspondencia.tbl_correspondencia')
                ->where('id_tbl_correspondencia', $idCorr)
                ->update([
                    'id_cat_estatus' => 9,       // MULTI-TURNO
                    // 'id_usuario_sistema' => $userId, // (opcional) si existe la columna
                    // 'fecha_usuario'      => $now,    // (opcional) si existe la columna
                ]);

            return response()->json([
                'ok' => true,
                'insertados' => count($rows),
                'message' => 'Multi-turno guardado correctamente.',
            ]);
        });

    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'message' => $e->getMessage(),
        ], 422);
    }
}

    public function delete(Request $request, $idTurnado)
{
    $idTurnado = (int)$idTurnado;

    if ($idTurnado <= 0) {
        return response()->json(['ok' => false, 'message' => 'ID inválido.'], 422);
    }

    try {
        $deleted = DB::table('correspondencia.tbl_correspondencia_turnado')
            ->where('id_tbl_correspondencia_turnado', $idTurnado)
            ->delete();

        if (!$deleted) {
            return response()->json(['ok' => false, 'message' => 'No se encontró el registro a eliminar.'], 404);
        }

        return response()->json(['ok' => true, 'message' => 'Destino eliminado correctamente.']);

    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
    }
}

}


