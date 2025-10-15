<?php

namespace App\Http\Controllers\Letter\Collection;

use App\Http\Controllers\Controller;
use App\Models\Administration\UserM;
use App\Models\Letter\Collection\CollectionAreaM;
use App\Models\Letter\Collection\CollectionCoordinacionM;
use App\Models\Letter\Collection\CollectionTramiteM;
use App\Models\Letter\Collection\CollectionRelEnlaceM;
use App\Models\Letter\Collection\CollectionRelUsuarioM;
use App\Models\Letter\Collection\CollectionUnidadM;
use App\Models\Letter\Collection\CollectionClaveM;
use App\Models\Letter\Letter\LetterM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CollectionAreaC extends Controller
{
    public function getletter(Request $request)
    {
        $letterM = new LetterM();
        $result  = $letterM->validateNoTurno($request->value);
        $status  = $result ? false : true;

        return response()->json([
            'ok'     => $status,
            'status' => $status,
        ]);
    }

    public function areaAutoincrement(Request $request)
    {
        $collectionAreaM = new CollectionAreaM();
        $id_cat_anio = $request->id_cat_anio;
        $id          = $request->id;

        $consecutivo = $collectionAreaM->noDocumentoAux($id_cat_anio, $id, $request->name);

        return response()->json([
            'consecutivo' => $consecutivo,
            'ok'          => true,
            'status'      => true,
        ]);
    }

    /**
     * Endpoint polivalente de colecciones.
     * - by=area2_by_area1
     * - by=area3_by_area2   (corregido)
     * - by=clave_by_tramite
     * - legacy: id=<area>
     */
    public function collection(Request $r)
    {
        try {
            $by = (string) $r->input('by', '');

            // 1) CRHTOD por CRH
            if ($by === 'area2_by_area1') {
                $a1 = (int) $r->input('id_cat_area_1', 0);
                if (!$a1) return response()->json(['ok' => true, 'status' => true, 'value' => []]);

                $includeInactive = $r->boolean('include_inactive', false);

                $q = DB::table('correspondencia.rel_cat_area_jerarquia_1 as r1')
                    ->join('correspondencia.cat_area as ca', 'r1.id_cat_area_2', '=', 'ca.id_cat_area')
                    ->where('r1.id_cat_area_1', $a1);

                if (!$includeInactive) $q->where('ca.estatus', true);

                $rows = $q->select('ca.id_cat_area as id', DB::raw('UPPER(ca.descripcion) as label'))
                          ->distinct()->orderBy('label')->get();

                $out = $rows->map(fn($x) => ['id' => (int)$x->id, 'label' => (string)$x->label])->values();

                return response()->json(['ok' => true, 'status' => true, 'value' => $out]);
            }

            // 2) Áreas por CRHTOD (CORREGIDO: mismo join que seed)
            if ($by === 'area3_by_area2') {
                $a2 = (int) $r->input('id_cat_area_2', 0);
                if (!$a2) return response()->json(['ok' => true, 'status' => true, 'value' => []]);

                $includeInactive = $r->boolean('include_inactive', false);

                $q = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
                    ->join('correspondencia.rel_cat_area_jerarquia_1 as r1','r2.id_cat_area_1','=','r1.id_cat_area_2')
                    ->join('correspondencia.cat_area as ca','r2.id_cat_area_2','=','ca.id_cat_area')
                    ->where('r1.id_cat_area_2', $a2);

                if (!$includeInactive) $q->where('ca.estatus', true);

                $rows = $q->select('ca.id_cat_area as id', DB::raw('UPPER(ca.descripcion) as label'))
                          ->distinct()->orderBy('label')->get();

                $out = $rows->map(fn($x) => ['id' => (int)$x->id, 'label' => (string)$x->label])->values();

                return response()->json(['ok' => true, 'status' => true, 'value' => $out]);
            }

            // 3) Claves por Trámite
            if ($by === 'clave_by_tramite') {
                $tramiteId = (int) $r->input('id_cat_tramite', 0);
                if (!$tramiteId) return response()->json(['ok' => true, 'status' => true, 'value' => []]);

                $claveM = new CollectionClaveM();
                $rows   = $claveM->listEdit($tramiteId) ?? [];

                $out = collect($rows)->map(function ($x) {
                    $id = (int)($x->id ?? $x->id_cat_clave ?? 0);
                    $label = (string)($x->label ?? $x->descripcion ?? $x->text ?? '');
                    return ['id' => $id, 'label' => strtoupper($label)];
                })->values();

                return response()->json(['ok' => true, 'status' => true, 'value' => $out]);
            }

            // 4) LEGACY: dependientes por Área
            $collectionRelEnlaceM  = new CollectionRelEnlaceM();
            $collectionRelUsuarioM = new CollectionRelUsuarioM();
            $collectionTramiteM    = new CollectionTramiteM();
            $collectionAreaM       = new CollectionAreaM();
            $collectionUnidadM     = new CollectionUnidadM();
            $collectionCoorM       = new CollectionCoordinacionM();

            $idArea = (int) $r->input('id', 0);

            if (!$idArea) {
                return response()->json([
                    'ok'             => true,
                    'status'         => true,
                    'clave'          => null,
                    'selectEnlace'   => [],
                    'selectUsuario'  => [],
                    'selectTramite'  => [],
                    'selectUnidad'   => [],
                    'selectCoor'     => [],
                    // aliases
                    'enlaces'        => [],
                    'usuarios'       => [],
                    'tramites'       => [],
                    'unidades'       => [],
                    'coordinaciones' => [],
                ]);
            }

            $selectEnlace   = $collectionRelEnlaceM->idUsuarioByArea($idArea)  ?? [];
            $selectUsuario  = $collectionRelUsuarioM->idUsuarioByArea($idArea) ?? [];
            $selectTramite  = $collectionTramiteM->list($idArea)               ?? [];
            $selectUnidad   = $collectionUnidadM->listOfUnidad($idArea)        ?? [];
            $selectCoor     = $collectionCoorM->listOfArea($idArea)            ?? [];
            $clave          = $collectionAreaM->getClave($idArea);

            return response()->json([
                'ok'             => true,
                'status'         => true,
                'clave'          => $clave,
                'selectEnlace'   => $selectEnlace,
                'selectUsuario'  => $selectUsuario,
                'selectTramite'  => $selectTramite,
                'selectUnidad'   => $selectUnidad,
                'selectCoor'     => $selectCoor,
                // aliases
                'enlaces'        => $selectEnlace,
                'usuarios'       => $selectUsuario,
                'tramites'       => $selectTramite,
                'unidades'       => $selectUnidad,
                'coordinaciones' => $selectCoor,
            ]);
        } catch (\Throwable $e) {
            Log::error('COLLECTION_AREA_ERROR: '.$e->getMessage(), ['ex' => $e]);
            return response()->json(['ok' => false, 'status' => false, 'message' => 'Error de servidor'], 500);
        }
    }

    // Aux: nombres para confirmación en el form
    public function getUserArea(Request $request)
    {
        $collectionAreaM = new CollectionAreaM();
        $userM = new UserM();

        $nameArea   = $collectionAreaM->getName($request->id_area);
        $nameUser   = $userM->getName($request->id_usuario);
        $nameEnlace = $userM->getName($request->id_enlace);

        return response()->json([
            'nameArea'   => $nameArea,
            'nameUser'   => $nameUser,
            'nameEnlace' => $nameEnlace,
            'ok'         => true,
            'status'     => true,
        ]);
    }
}
