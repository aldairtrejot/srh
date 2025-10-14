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
     * Edita el registro desde el modal (mantengo el nombre "turnar"
     * por compatibilidad; si envías force_turnado=true, coloca estatus TURNADO).
     */
    public function turnar(Request $r)
    {
        $r->validate([
            'id_tbl_correspondencia' => 'required|integer|min:1',
            'id_cat_area'            => 'required|integer|min:1',
            'id_cat_tramite'         => 'required|integer|min:1',
            'id_cat_clave'           => 'required|integer|min:1',
            'id_usuario_area'        => 'required|integer|min:1',
            // opcionales
            'id_cat_area_1'          => 'nullable|integer|min:1',
            'id_cat_area_2'          => 'nullable|integer|min:1',
            'id_usuario_enlace'      => 'nullable|integer|min:1',
            'id_cat_unidad'          => 'nullable|integer|min:1',
            'id_cat_coordinacion'    => 'nullable|integer|min:1',
            'force_turnado'          => 'nullable|boolean',
        ], [], [
            'id_cat_area'     => 'Área',
            'id_usuario_area' => 'Usuario',
            'id_cat_tramite'  => 'Trámite',
            'id_cat_clave'    => 'Clave',
        ]);

        try {
            DB::beginTransaction();

            /** @var LetterM $letter */
            $letter = LetterM::lockForUpdate()->findOrFail((int)$r->id_tbl_correspondencia);

            // Actualizar campos del "Turnar A"
            $letter->id_cat_area_1       = $r->input('id_cat_area_1');
            $letter->id_cat_area_2       = $r->input('id_cat_area_2');
            $letter->id_cat_area         = $r->input('id_cat_area');
            $letter->id_usuario_area     = $r->input('id_usuario_area');
            $letter->id_usuario_enlace   = $r->input('id_usuario_enlace');
            $letter->id_cat_unidad       = $r->input('id_cat_unidad');
            $letter->id_cat_coordinacion = $r->input('id_cat_coordinacion');
            $letter->id_cat_tramite      = $r->input('id_cat_tramite');
            $letter->id_cat_clave        = $r->input('id_cat_clave');

            // Si se pide, colocar estatus TURNADO (mismo comportamiento que antes)
            $idTurnado = $letter->getTurnadoId();
            if ($r->boolean('force_turnado')) {
                $letter->id_cat_estatus = $idTurnado;
            }

            $letter->fecha_usuario = now();
            if (auth()->check()) {
                $letter->id_usuario_sistema = auth()->id();
            }

            $letter->save();

            DB::commit();

            return response()->json([
                'ok'        => true,
                'idTurnado' => $idTurnado,
                'message'   => 'Registro actualizado correctamente.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('RETURNADO_TURNAR_ERROR: '.$e->getMessage(), ['ex' => $e]);
            return response()->json(['ok' => false, 'message' => 'Error al guardar.'], 500);
        }
    }

    /**
     * Semilla para el modal (al abrir desde la LISTA).
     * Devuelve opciones y la selección actual, igual que esperas en JS.
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
            )->where('id_tbl_correspondencia', $id)->first();

        if (!$item) {
            return response()->json(['ok' => false, 'message' => 'Correspondencia no encontrada.'], 404);
        }

        $m = new LetterM();

        // ================== Áreas ==================
        // A1 (CRH): DEVOLVER TODAS LAS OPCIONES (sin filtrar por el área del usuario)
        // Si quieres mantener un modo restringido, acepta scope=user por querystring.
        $scope = $request->string('scope')->toString(); // '', 'user'
        if ($scope === 'user') {
            $miAreaId = Auth::user()->id_cat_area ?? null;
            $a1Rows = $miAreaId ? $m->getArea1OptionsByArea((int)$miAreaId) : $m->getArea1Options();
        } else {
            $a1Rows = $m->getArea1Options(); // <-- cambio clave
        }
        $a1 = collect($a1Rows)->map(fn($r)=>['id'=>(int)$r->id,'label'=>strtoupper($r->descripcion)])->values();

        // A2 (CRHTOD): hijos del A1 actual (si no hay A1, listo vacío; se llenará al cambiar)
        $a2 = [];
        if (!empty($item->id_cat_area_1)) {
            $a2Rows = $m->getArea2OptionsByArea1((int)$item->id_cat_area_1);
            $a2 = collect($a2Rows)->map(fn($r)=>['id'=>(int)$r->id,'label'=>strtoupper($r->descripcion)])->values();
        }

        // A3 (Área): hijos del A2 actual
        $a3 = [];
        if (!empty($item->id_cat_area_2)) {
            $includeInactive = $request->boolean('include_inactive', false);
            $q = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
                ->join('correspondencia.rel_cat_area_jerarquia_1 as r1','r2.id_cat_area_1','=','r1.id_cat_area_2')
                ->join('correspondencia.cat_area as ca','r2.id_cat_area_2','=','ca.id_cat_area')
                ->where('r1.id_cat_area_2', (int)$item->id_cat_area_2);

            if (!$includeInactive) $q->where('ca.estatus', true);

            $a3 = $q->select('ca.id_cat_area as id', DB::raw('UPPER(ca.descripcion) AS label'))
                    ->distinct()->orderBy('label')->get()
                    ->map(fn($r)=>['id'=>(int)$r->id,'label'=>$r->label])->values();
        }

        // ================== Dependientes (por A3) ==================
        $selectUsuario = $selectEnlace = $selectUnidad = $selectCoor = $selectTramite = $selectClave = [];
        if (!empty($item->id_cat_area)) {
            $relUsuarioM = new CollectionRelUsuarioM();
            $relEnlaceM  = new CollectionRelEnlaceM();
            $unidadM     = new CollectionUnidadM();
            $coorM       = new CollectionCoordinacionM();
            $tramiteM    = new CollectionTramiteM();
            $claveM      = new CollectionClaveM();

            $selectUsuario = $relUsuarioM->idUsuarioByAreaNewX((int)$item->id_cat_area, (int)($item->id_usuario_area ?? 0)) ?? [];
            $selectEnlace  = $relEnlaceM->idUsuarioByAreaNewX((int)$item->id_cat_area, (int)($item->id_usuario_enlace ?? 0)) ?? [];
            $selectUnidad  = $unidadM->listEdit() ?? [];
            if (!empty($item->id_cat_unidad)) {
                $selectCoor = $coorM->listEdit((int)$item->id_cat_unidad) ?? [];
            }
            $selectTramite = $tramiteM->listEdit((int)$item->id_cat_area) ?? [];
            if (!empty($item->id_cat_tramite)) {
                $selectClave = $claveM->listEdit((int)$item->id_cat_tramite) ?? [];
            }
        }

        return response()->json([
            'ok' => true,
            'folio' => $item->folio_gestion,
            'letter' => [
                'id'                 => (int)$item->id_tbl_correspondencia,
                'id_cat_area_1'      => (int)($item->id_cat_area_1 ?? 0),
                'id_cat_area_2'      => (int)($item->id_cat_area_2 ?? 0),
                'id_cat_area'        => (int)($item->id_cat_area ?? 0),
                'id_usuario_area'    => (int)($item->id_usuario_area ?? 0),
                'id_usuario_enlace'  => (int)($item->id_usuario_enlace ?? 0),
                'id_cat_unidad'      => (int)($item->id_cat_unidad ?? 0),
                'id_cat_coordinacion'=> (int)($item->id_cat_coordinacion ?? 0),
                'id_cat_tramite'     => (int)($item->id_cat_tramite ?? 0),
                'id_cat_clave'       => (int)($item->id_cat_clave ?? 0),
            ],
            'selects' => [
                'area1'          => $a1,          // TODAS las CRH
                'area2'          => $a2,          // CRHTOD de la A1 actual
                'area3'          => $a3,          // Áreas de la A2 actual
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
