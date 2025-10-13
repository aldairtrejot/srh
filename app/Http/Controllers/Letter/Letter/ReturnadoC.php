<?php

namespace App\Http\Controllers\Letter\Letter;

use App\Http\Controllers\Controller;
use App\Models\Letter\Letter\LetterM;
use App\Models\Letter\Collection\CollectionRelUsuarioM;
use App\Models\Letter\Collection\CollectionRelEnlaceM;
use App\Models\Letter\Collection\CollectionUnidadM;
use App\Models\Letter\Collection\CollectionCoordinacionM;
use App\Models\Letter\Collection\CollectionTramiteM;
use App\Models\Letter\Collection\CollectionClaveM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnadoC extends Controller
{
    /**
     * Guarda el turnado desde el modal y pone el estatus en TURNADO.
     */
    public function turnar(Request $r)
    {
        $r->validate([
            'id_tbl_correspondencia' => 'required|integer|min:1',
            'id_cat_area'            => 'required|integer|min:1',
            'id_cat_tramite'         => 'required|integer|min:1',
            'id_cat_clave'           => 'required|integer|min:1',
            'id_usuario_area'        => 'required|integer|min:1', // evita NOT NULL
            // opcionales
            'id_cat_area_1'          => 'nullable|integer|min:1',
            'id_cat_area_2'          => 'nullable|integer|min:1',
            'id_usuario_enlace'      => 'nullable|integer|min:1',
            'id_cat_unidad'          => 'nullable|integer|min:1',
            'id_cat_coordinacion'    => 'nullable|integer|min:1',
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

            // Id de estatus TURNADO
            $idTurnado = $letter->getTurnadoId();

            // Actualizar campos de “Turnar A”
            $letter->id_cat_area_1       = $r->input('id_cat_area_1');
            $letter->id_cat_area_2       = $r->input('id_cat_area_2');
            $letter->id_cat_area         = $r->input('id_cat_area');
            $letter->id_usuario_area     = $r->input('id_usuario_area');
            $letter->id_usuario_enlace   = $r->input('id_usuario_enlace');
            $letter->id_cat_unidad       = $r->input('id_cat_unidad');
            $letter->id_cat_coordinacion = $r->input('id_cat_coordinacion');
            $letter->id_cat_tramite      = $r->input('id_cat_tramite');
            $letter->id_cat_clave        = $r->input('id_cat_clave');

            // Estatus -> TURNADO
            $letter->id_cat_estatus = $idTurnado;
            $letter->fecha_usuario  = now();
            if (auth()->check()) {
                $letter->id_usuario_sistema = auth()->id();
            }

            $letter->save();

            DB::commit();

            return response()->json([
                'ok'        => true,
                'idTurnado' => $idTurnado,
                'message'   => 'Turnado actualizado correctamente.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('RETURNADO_TURNAR_ERROR: '.$e->getMessage(), ['ex' => $e]);
            return response()->json(['ok' => false, 'message' => 'Error al guardar turnado.'], 500);
        }
    }

    /**
     * Semilla para el modal cuando se abre desde la LISTA (no desde el form).
     * Devuelve opciones y selección actual.
     */
    public function seed(Request $request, int $id)
    {
        $row = DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'id_tbl_correspondencia',
                'id_cat_area_1','id_cat_area_2','id_cat_area',
                'id_usuario_area','id_usuario_enlace',
                'id_cat_unidad','id_cat_coordinacion',
                'id_cat_tramite','id_cat_clave',
                'folio_gestion'
            )
            ->where('id_tbl_correspondencia', $id)
            ->first();

        if (!$row) {
            return response()->json(['ok'=>false,'message'=>'Correspondencia no encontrada.'],404);
        }

        $m = new LetterM();

        // === A1 (CRH)
        $miAreaId = Auth::user()->id_cat_area ?? null;
        $a1Rows = $miAreaId ? $m->getArea1OptionsByArea((int)$miAreaId) : $m->getArea1Options();
        $a1 = collect($a1Rows)->map(fn($r)=>['id'=>(int)$r->id,'label'=>strtoupper($r->descripcion)])->values();

        // === A2 (CRHTOD) según A1 actual
        $a2 = [];
        if (!empty($row->id_cat_area_1)) {
            $a2Rows = $m->getArea2OptionsByArea1((int)$row->id_cat_area_1);
            $a2 = collect($a2Rows)->map(fn($r)=>['id'=>(int)$r->id,'label'=>strtoupper($r->descripcion)])->values();
        }

        // === A3 (Área) según A2 actual
        $a3 = [];
        if (!empty($row->id_cat_area_2)) {
            $includeInactive = (bool) ($request->boolean('include_inactive') ?? (config('app.env') !== 'production'));

            $q = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
                ->join('correspondencia.rel_cat_area_jerarquia_1 as r1', 'r2.id_cat_area_1', '=', 'r1.id_cat_area_2')
                ->join('correspondencia.cat_area as ca', 'r2.id_cat_area_2', '=', 'ca.id_cat_area')
                ->where('r1.id_cat_area_2', (int)$row->id_cat_area_2);

            if (!$includeInactive) {
                $q->where('ca.estatus', true);
            }

            $a3Rows = $q->select('ca.id_cat_area as id', DB::raw('UPPER(ca.descripcion) AS label'))
                        ->distinct()->orderBy('label')->get();
            $a3 = collect($a3Rows)->map(fn($r)=>['id'=>(int)$r->id,'label'=>$r->label])->values();
        }

        // === Dependientes por Área 3
        $selectUsuario     = [];
        $selectEnlace      = [];
        $selectUnidad      = [];
        $selectCoordinacion= [];
        $selectTramite     = [];
        $selectClave       = [];

        if (!empty($row->id_cat_area)) {
            $relUsuarioM = new CollectionRelUsuarioM();
            $relEnlaceM  = new CollectionRelEnlaceM();
            $unidadM     = new CollectionUnidadM();
            $coorM       = new CollectionCoordinacionM();
            $tramiteM    = new CollectionTramiteM();
            $claveM      = new CollectionClaveM();

            $selectUsuario = $relUsuarioM->idUsuarioByAreaNewX((int)$row->id_cat_area, (int)($row->id_usuario_area ?? 0)) ?? [];
            $selectEnlace  = $relEnlaceM->idUsuarioByAreaNewX((int)$row->id_cat_area, (int)($row->id_usuario_enlace ?? 0)) ?? [];
            $selectUnidad  = $unidadM->listEdit() ?? [];

            if (!empty($row->id_cat_unidad)) {
                $selectCoordinacion = $coorM->listEdit((int)$row->id_cat_unidad) ?? [];
            }

            $selectTramite = $tramiteM->listEdit((int)$row->id_cat_area) ?? [];
            if (!empty($row->id_cat_tramite)) {
                $selectClave = $claveM->listEdit((int)$row->id_cat_tramite) ?? [];
            }
        }

        return response()->json([
            'ok'   => true,
            'folio'=> $row->folio_gestion,
            'letter' => [
                'id'                  => (int)$row->id_tbl_correspondencia,
                'id_cat_area_1'       => (int)($row->id_cat_area_1 ?? 0),
                'id_cat_area_2'       => (int)($row->id_cat_area_2 ?? 0),
                'id_cat_area'         => (int)($row->id_cat_area ?? 0),
                'id_usuario_area'     => (int)($row->id_usuario_area ?? 0),
                'id_usuario_enlace'   => (int)($row->id_usuario_enlace ?? 0),
                'id_cat_unidad'       => (int)($row->id_cat_unidad ?? 0),
                'id_cat_coordinacion' => (int)($row->id_cat_coordinacion ?? 0),
                'id_cat_tramite'      => (int)($row->id_cat_tramite ?? 0),
                'id_cat_clave'        => (int)($row->id_cat_clave ?? 0),
            ],
            'selects' => [
                'area1'          => $a1,    // [{id,label}]
                'area2'          => $a2,
                'area3'          => $a3,
                'usuarios'       => $selectUsuario,
                'enlaces'        => $selectEnlace,
                'unidades'       => $selectUnidad,
                'coordinaciones' => $selectCoordinacion,
                'tramites'       => $selectTramite,
                'claves'         => $selectClave,
            ],
        ]);
    }
}
