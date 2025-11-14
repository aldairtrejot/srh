<?php

namespace App\Http\Controllers\Letter\Letter;

use App\Http\Controllers\Controller;
use App\Models\Letter\Letter\LetterM;
use App\Models\Letter\Collection\CollectionRelUsuarioM;
use App\Models\Letter\Collection\CollectionRelEnlaceM;
use App\Models\Letter\Collection\CollectionUnidadM;
use App\Models\Letter\Collection\CollectionTramiteM;
use App\Models\Letter\Collection\CollectionClaveM;
use App\Models\Letter\Collection\CollectionCoordinacionM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnadoC extends Controller
{
    /**
     * Turnar/Returnar desde el modal.
     *
     * - toggle_status=true alterna 1↔8 (TURNADO↔RE-TURNADO).
     * - force_turnado=true fuerza TURNADO.
     * - auto_level=true (o si faltan A1/A2/A3) autodetecta el nivel (A3 || A2 || A1)
     *   tomando los valores ya guardados en el registro.
     */
    public function turnar(Request $r)
    {
        // ¿Activamos autodetección?
        $auto = filter_var($r->input('auto_level'), FILTER_VALIDATE_BOOLEAN);

        // Validación flexible: si auto_level=true, Usuario/Trámite/Clave pueden venir vacíos
        $rules = [
            'id_tbl_correspondencia' => 'required|string',
            'id_cat_area_1'          => 'nullable|string', // CRH
            'id_cat_area_2'          => 'nullable|string', // CRHTOD
            'id_cat_area'            => 'nullable|string', // Área (destino más específico)
            'id_usuario_area'        => $auto ? 'nullable|string' : 'required|string',
            'id_usuario_enlace'      => 'nullable|string',
            'id_cat_unidad'          => 'nullable|string',
            'id_cat_coordinacion'    => 'nullable|string',
            'id_cat_tramite'         => $auto ? 'nullable|string' : 'required|string',
            'id_cat_clave'           => $auto ? 'nullable|string' : 'required|string',
            'force_turnado'          => 'nullable',
            'toggle_status'          => 'nullable',
        ];

        $r->validate($rules, [], [
            'id_usuario_area' => 'Usuario',
            'id_cat_tramite'  => 'Trámite',
            'id_cat_clave'    => 'Clave',
        ]);
  // 🔒 CANDADO: si el usuario SOLO tiene este folio como COPIA, no puede turnar/returnar
        $idCorr = (int) $r->input('id_tbl_correspondencia');
        $userId = (int) (Auth::id() ?? 0);

        if ($idCorr > 0 && $userId > 0 && $this->userHasCopyOnlyAccess($idCorr, $userId)) {
            return response()->json([
                'ok'      => false,
                'message' => 'Este registro está disponible solo como copia (solo lectura). No puede cambiar el estatus TURNADO/RE-TURNADO.',
            ], 403);
        }


        try {
            DB::beginTransaction();

            /** @var LetterM $letter */
            $letter = LetterM::lockForUpdate()->findOrFail((int) $r->id_tbl_correspondencia);

            // Normalizador ('' → null)
            $in  = fn(string $k) => trim((string)$r->input($k, '')) !== '' ? (int)$r->input($k) : null;

            // Entradas del request (si vienen)
            $reqA1 = $in('id_cat_area_1');  // CRH
            $reqA2 = $in('id_cat_area_2');  // CRHTOD
            $reqA3 = $in('id_cat_area');    // Área

            $reqUsr = $in('id_usuario_area');
            $reqEnl = $in('id_usuario_enlace');
            $reqUni = $in('id_cat_unidad');
            $reqCoo = $in('id_cat_coordinacion');
            $reqTra = $in('id_cat_tramite');
            $reqCla = $in('id_cat_clave');

            // Autocompletar desde el registro si no vinieron (o si activaste auto_level)
            $effA1 = $reqA1 ?? ($auto ? ($letter->id_cat_area_1 ? (int)$letter->id_cat_area_1 : null) : null);
            $effA2 = $reqA2 ?? ($auto ? ($letter->id_cat_area_2 ? (int)$letter->id_cat_area_2 : null) : null);
            $effA3 = $reqA3 ?? ($auto ? ($letter->id_cat_area   ? (int)$letter->id_cat_area   : null) : null);

            $effUsr = $reqUsr ?? ($auto ? ($letter->id_usuario_area     ? (int)$letter->id_usuario_area     : null) : null);
            $effEnl = $reqEnl ?? ($auto ? ($letter->id_usuario_enlace   ? (int)$letter->id_usuario_enlace   : null) : null);
            $effUni = $reqUni ?? ($auto ? ($letter->id_cat_unidad       ? (int)$letter->id_cat_unidad       : null) : null);
            $effCoo = $reqCoo ?? ($auto ? ($letter->id_cat_coordinacion ? (int)$letter->id_cat_coordinacion : null) : null);
            $effTra = $reqTra ?? ($auto ? ($letter->id_cat_tramite      ? (int)$letter->id_cat_tramite      : null) : null);
            $effCla = $reqCla ?? ($auto ? ($letter->id_cat_clave        ? (int)$letter->id_cat_clave        : null) : null);

            // Resolver destino por especificidad: A3 || A2 || A1
            $destinoId = $effA3 ?: ($effA2 ?: $effA1);
            $nivel     = $effA3 ? 3 : ($effA2 ? 2 : ($effA1 ? 1 : 0));

            if (!$destinoId) {
                DB::rollBack();
                return response()->json([
                    'ok' => false,
                    'message' => 'No se pudo determinar automáticamente el nivel/destino (A1/A2/A3).'
                ], 422);
            }

            // Aplicar cadena "Turnar A"
            $letter->id_cat_area_1       = $effA1;
            $letter->id_cat_area_2       = $effA2;
            $letter->id_cat_area         = $destinoId;
            $letter->id_usuario_area     = $effUsr;
            $letter->id_usuario_enlace   = $effEnl;
            $letter->id_cat_unidad       = $effUni;
            $letter->id_cat_coordinacion = $effCoo;
            $letter->id_cat_tramite      = $effTra;
            $letter->id_cat_clave        = $effCla;

            // Estatus (toggle 1↔8 o forzar turnado)
            $idTurnado   = method_exists($letter, 'getTurnadoId')
                ? (int) $letter->getTurnadoId()
                : (int) config('letter.status.turnado_id', 1);
            $idReturnado = (int) config('letter.status.returnado_id', 8);

            if (filter_var($r->input('toggle_status'), FILTER_VALIDATE_BOOLEAN)) {
                if ((int)$letter->id_cat_estatus === $idReturnado) {
                    $letter->id_cat_estatus = $idTurnado;   // 8 -> 1
                } elseif ((int)$letter->id_cat_estatus === $idTurnado) {
                    $letter->id_cat_estatus = $idReturnado; // 1 -> 8
                }
            } elseif (filter_var($r->input('force_turnado'), FILTER_VALIDATE_BOOLEAN)) {
                $letter->id_cat_estatus = $idTurnado;
            }

            $letter->fecha_usuario = now();
            if (Auth::check()) {
                $letter->id_usuario_sistema = Auth::id();
            }

            $letter->save();
            DB::commit();

            return response()->json([
                'ok'             => true,
                'idTurnado'      => $idTurnado,
                'newStatusId'    => (int) $letter->id_cat_estatus,
                'nivelDetectado' => $nivel,           // 1, 2 o 3
                'destinoId'      => (int) $destinoId, // id de área aplicado
                'message'        => 'Registro actualizado correctamente.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('RETURNADO_TURNAR_ERROR: ' . $e->getMessage(), ['ex' => $e]);
            return response()->json(['ok' => false, 'message' => 'Error al guardar.'], 500);
        }
    }

    /**
     * Seed para abrir el modal desde la lista (precarga valores y catálogos).
     * Devuelve "nivelDetectado": 1=A1, 2=A2, 3=A3.
     */
    public function seed(Request $request, int $id)
    {
        $item = DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'id_tbl_correspondencia',
                'folio_gestion',
                'id_cat_area_1','id_cat_area_2','id_cat_area',
                'id_usuario_area','id_usuario_enlace',
                'id_cat_unidad','id_cat_coordinacion',
                'id_cat_tramite','id_cat_clave'
            )
            ->where('id_tbl_correspondencia', $id)
            ->first();

        if (!$item) {
            return response()->json(['ok' => false, 'message' => 'Correspondencia no encontrada.'], 404);
        }

        $m = new LetterM();

        // A1 (CRH): todas o acotadas al área del usuario si scope=user
        $scope = (string) $request->input('scope', ''); // '', 'user'
        if ($scope === 'user') {
            $miAreaId = Auth::user()->id_cat_area ?? null;
            $a1Rows = $miAreaId ? $m->getArea1OptionsByArea((int) $miAreaId) : $m->getArea1Options();
        } else {
            $a1Rows = $m->getArea1Options();
        }
        $a1 = collect($a1Rows)->map(
            fn($r) => ['id' => (int) $r->id, 'label' => strtoupper($r->descripcion)]
        )->values();

        // A2 (CRHTOD): hijos del A1 actual
        $a2 = [];
        if (!empty($item->id_cat_area_1)) {
            $a2Rows = $m->getArea2OptionsByArea1((int) $item->id_cat_area_1);
            $a2 = collect($a2Rows)->map(
                fn($r) => ['id' => (int) $r->id, 'label' => strtoupper($r->descripcion)]
            )->values();
        }

        // A3 (Área): hijos del A2 (con include_inactive opcional)
        $a3 = [];
        if (!empty($item->id_cat_area_2)) {
            $includeInactive = $request->boolean('include_inactive', false);
            $q = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
                ->join('correspondencia.rel_cat_area_jerarquia_1 as r1', 'r2.id_cat_area_1', '=', 'r1.id_cat_area_2')
                ->join('correspondencia.cat_area as ca', 'r2.id_cat_area_2', '=', 'ca.id_cat_area')
                ->where('r1.id_cat_area_2', (int) $item->id_cat_area_2);

            if (!$includeInactive) {
                $q->where('ca.estatus', true);
            }

            $a3 = $q->select('ca.id_cat_area as id', DB::raw('UPPER(ca.descripcion) AS label'))
                ->distinct()
                ->orderBy('label')
                ->get()
                ->map(fn($r) => ['id' => (int) $r->id, 'label' => $r->label])
                ->values();
        }

        // Dependientes por A3 (si hay)
        $selectUsuario = $selectEnlace = $selectUnidad = $selectCoor = $selectTramite = $selectClave = [];
        if (!empty($item->id_cat_area)) {
            $relUsuarioM = new CollectionRelUsuarioM();
            $relEnlaceM  = new CollectionRelEnlaceM();
            $unidadM     = new CollectionUnidadM();
            $coorM       = new CollectionCoordinacionM();
            $tramiteM    = new CollectionTramiteM();
            $claveM      = new CollectionClaveM();

            $selectUsuario = $relUsuarioM->idUsuarioByAreaNewX(
                (int) $item->id_cat_area,
                (int) ($item->id_usuario_area ?? 0)
            ) ?? [];

            $selectEnlace = $relEnlaceM->idUsuarioByAreaNewX(
                (int) $item->id_cat_area,
                (int) ($item->id_usuario_enlace ?? 0)
            ) ?? [];

            $selectUnidad = $unidadM->listEdit() ?? [];

            if (!empty($item->id_cat_unidad)) {
                $selectCoor = $coorM->listEdit((int) $item->id_cat_unidad) ?? [];
            }

            $selectTramite = $tramiteM->listEdit((int) $item->id_cat_area) ?? [];

            if (!empty($item->id_cat_tramite)) {
                $selectClave = $claveM->listEdit((int) $item->id_cat_tramite) ?? [];
            }
        }

        // Nivel detectado actual: 3 si tiene A3, 2 si tiene A2, 1 si tiene A1
        $nivelDetectado = !empty($item->id_cat_area)
            ? 3
            : (!empty($item->id_cat_area_2)
                ? 2
                : (!empty($item->id_cat_area_1) ? 1 : 0));

        return response()->json([
            'ok'      => true,
            'folio'   => $item->folio_gestion,
            'nivelDetectado' => $nivelDetectado,
            'letter'  => [
                'id'                  => (int) $item->id_tbl_correspondencia,
                'id_cat_area_1'       => (int) ($item->id_cat_area_1 ?? 0),
                'id_cat_area_2'       => (int) ($item->id_cat_area_2 ?? 0),
                'id_cat_area'         => (int) ($item->id_cat_area ?? 0),
                'id_usuario_area'     => (int) ($item->id_usuario_area ?? 0),
                'id_usuario_enlace'   => (int) ($item->id_usuario_enlace ?? 0),
                'id_cat_unidad'       => (int) ($item->id_cat_unidad ?? 0),
                'id_cat_coordinacion' => (int) ($item->id_cat_coordinacion ?? 0),
                'id_cat_tramite'      => (int) ($item->id_cat_tramite ?? 0),
                'id_cat_clave'        => (int) ($item->id_cat_clave ?? 0),
            ],
            'selects' => [
                'area1'          => $a1,
                'area2'          => $a2,
                'area3'          => $a3,
                'usuarios'       => $selectUsuario,
                'enlaces'        => $selectEnlace,
                'unidades'       => $selectUnidad,
                'coordinaciones' => $selectCoor,
                'tramites'       => $selectTramite,
                'claves'         => $selectClave,
            ],
        ]);
    }
}
