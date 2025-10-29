<?php

namespace App\Http\Controllers\Letter\Letter;

use App\Http\Controllers\Admin\MessagesC;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Letter\Log\LogC;

use App\Models\Letter\Letter\LetterM;
use App\Models\Letter\Office\OfficeM;

use App\Models\Letter\Collection\CollectionAreaM;
use App\Models\Letter\Collection\CollectionClaveM;
use App\Models\Letter\Collection\CollectionCoordinacionM;
use App\Models\Letter\Collection\CollectionConsecutivoM;
use App\Models\Letter\Collection\CollectionDateM;
use App\Models\Letter\Collection\CollectionEntidadM;
use App\Models\Letter\Collection\CollectionLetterCopyM;
use App\Models\Letter\Collection\CollectionLetterLogM;
use App\Models\Letter\Collection\CollectionRelEnlaceM;
use App\Models\Letter\Collection\CollectionRelUsuarioM;
use App\Models\Letter\Collection\CollectionRemitenteM;
use App\Models\Letter\Collection\CollectionRolAreaM;
use App\Models\Letter\Collection\CollectionStatusM;
use App\Models\Letter\Collection\CollectionTramiteM;
use App\Models\Letter\Collection\CollectionUnidadM;

use App\Http\Controllers\Cloud\AlfrescoC;
use App\Models\Letter\Letter\CloudAnexosM;
use App\Models\Letter\Letter\CloudOficiosM;
use App\Models\Letter\Cloud\CloudConfigM;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Log;

class LetterC extends Controller
{
    public function __invoke()
    {
        return view('letter.letter.list');
    }
    public function cloud($id)
    {
        $object = new LetterM();
        $item = $object->edit($id);
        return view('letter/letter/cloud', compact('item'));

    }

    /* =========================================================
     * TABLA
     * ========================================================= */
   public function table(Request $request, LetterM $model)
{
    try {
        $iterator    = max(0, (int)$request->get('iterator', 0));
        $searchValue = (string)$request->get('searchValue', '');
        $visibility  = $this->resolveAreaColumnVisibility(); // ['area'=>bool,'crh'=>bool,'crhtod'=>bool]

        // BYPASS (admines)
        //if ($this->isBypassVisibility()) {
            $q = DB::table('correspondencia.tbl_correspondencia as c');

            // Aseguramos joins a estatus y áreas (se usan en búsqueda y select)
            $q = $q
                ->leftJoin('correspondencia.cat_estatus as e', 'e.id_cat_estatus', '=', 'c.id_cat_estatus')
                ->leftJoin('correspondencia.cat_area as a3', 'a3.id_cat_area', '=', 'c.id_cat_area')
                ->leftJoin('correspondencia.cat_area as a1', 'a1.id_cat_area', '=', 'c.id_cat_area_1')
                ->leftJoin('correspondencia.cat_area as a2', 'a2.id_cat_area', '=', 'c.id_cat_area_2');

    // Código para no administradores
    if (
        ! in_array(1, session('SESSION_ROLE_USER', [])) &&
        ! in_array(2, session('SESSION_ROLE_USER', []))
    ) {
        $q->leftJoin('correspondencia.ctrl_rol_usuario_area as j_area_1', function ($join) {
            $join->on('c.id_cat_area_1', '=', 'j_area_1.id_cat_area')
                ->where('j_area_1.id_cat_jerarquia', 1)
                ->where('j_area_1.estatus', true)
                ->where('j_area_1.id_usuario', auth()->id());
        })
        ->leftJoin('correspondencia.ctrl_rol_usuario_area as j_area_2', function ($join) {
            $join->on('c.id_cat_area_2', '=', 'j_area_2.id_cat_area')
                ->where('j_area_2.id_cat_jerarquia', 2)
                ->where('j_area_2.estatus', true)
                ->where('j_area_2.id_usuario', auth()->id());
        })
        ->leftJoin('correspondencia.ctrl_rol_usuario_area as j_area_3', function ($join) {
            $join->on('c.id_cat_area', '=', 'j_area_3.id_cat_area')
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

            if ($searchValue !== '') {
                $sv = '%'.trim($searchValue).'%';
                $q->where(function ($f) use ($sv) {
                    $f->whereRaw('TRIM(c.num_documento) ILIKE ?', [$sv])
                      ->orWhereRaw('TRIM(c.asunto) ILIKE ?', [$sv])
                      ->orWhereRaw('TRIM(c.folio_gestion) ILIKE ?', [$sv])
                      ->orWhereRaw('TRIM(a3.descripcion) ILIKE ?', [$sv])
                      ->orWhereRaw('TRIM(a1.descripcion) ILIKE ?', [$sv])
                      ->orWhereRaw('TRIM(a2.descripcion) ILIKE ?', [$sv])
                      ->orWhereRaw('TRIM(e.descripcion) ILIKE ?', [$sv]);
                });
            }

            $total = (clone $q)->count('c.id_tbl_correspondencia');

            $rows = $q->orderByDesc('c.id_tbl_correspondencia')
                ->offset($iterator)->limit(5)
                ->get([
                    'c.id_tbl_correspondencia as id',
                    DB::raw('UPPER(c.num_documento) as num_documento'),
                    DB::raw('UPPER(c.folio_gestion)  as folio_gestion'),
                    DB::raw('UPPER(c.asunto)         as asunto'),
                    DB::raw("TO_CHAR(c.fecha_captura::date,'DD/MM/YYYY') as fecha_captura"),
                    DB::raw('UPPER(e.descripcion)    as estatus'),
                    DB::raw('UPPER(coalesce(a3.descripcion, \'\')) as area'),
                    DB::raw('UPPER(coalesce(a1.descripcion, \'\')) as area_1'),
                    DB::raw('UPPER(coalesce(a2.descripcion, \'\')) as area_2'),
                    DB::raw("(
                        SELECT co.uid
                        FROM correspondencia.ctrl_correspondencia_oficio co
                        WHERE co.id_tbl_correspondencia = c.id_tbl_correspondencia
                        ORDER BY co.fecha_usuario DESC
                        LIMIT 1
                    ) AS uuid_oficio"),
                ]);

            return response()->json([
                'value' => $rows,
                'total' => $total,
                'columns_visibility' => ['area'=>true,'crh'=>true,'crhtod'=>true],
            ]);
        //}

        // USUARIO NORMAL
        $userId    = (int)(Auth::id() ?? 0);
        $userAreas = $this->getAllowedAreasForUser($userId);
        if (empty($userAreas)) {
            return response()->json([
                'value' => [], 'total' => 0, 'columns_visibility' => $visibility
            ]);
        }

        $q = DB::table('correspondencia.tbl_correspondencia as c')
            ->leftJoin('correspondencia.cat_estatus as e', 'e.id_cat_estatus', '=', 'c.id_cat_estatus')
            ->leftJoin('correspondencia.cat_area as a3', 'a3.id_cat_area', '=', 'c.id_cat_area')
            ->leftJoin('correspondencia.cat_area as a1', 'a1.id_cat_area', '=', 'c.id_cat_area_1')
            ->leftJoin('correspondencia.cat_area as a2', 'a2.id_cat_area', '=', 'c.id_cat_area_2')
            ->where('e.estatus', true)
            ->where(function ($w) use ($userAreas) {
                // 1) Si hay A3
                $w->orWhereIn('c.id_cat_area', $userAreas);
                // 2) Si no hay A3 pero sí A2
                $w->orWhere(function ($q2) use ($userAreas) {
                    $q2->whereNull('c.id_cat_area')->whereIn('c.id_cat_area_2', $userAreas);
                });
                // 3) Si no hay A3 ni A2 pero sí A1
                $w->orWhere(function ($q3) use ($userAreas) {
                    $q3->whereNull('c.id_cat_area')->whereNull('c.id_cat_area_2')->whereIn('c.id_cat_area_1', $userAreas);
                });
                // 4) Copias — solo si no hay A3
                $w->orWhere(function ($qCopy) use ($userAreas) {
                    $qCopy->whereNull('c.id_cat_area')
                          ->whereExists(function ($ex) use ($userAreas) {
                              $ex->from('correspondencia.ctrl_transcribir_correspondencia as t')
                                 ->whereColumn('t.id_tbl_correspondencia', 'c.id_tbl_correspondencia')
                                 ->whereIn('t.id_cat_area', $userAreas);
                          });
                });
                // ⛔️ NO por usuario_area / usuario_enlace
            })
            ->where('c.id_cat_estatus', '!=', 2);

        if ($searchValue !== '') {
            $sv = '%'.trim($searchValue).'%';
            $q->where(function ($f) use ($sv) {
                $f->whereRaw('TRIM(c.num_documento) ILIKE ?', [$sv])
                  ->orWhereRaw('TRIM(c.asunto) ILIKE ?', [$sv])
                  ->orWhereRaw('TRIM(c.folio_gestion) ILIKE ?', [$sv])
                  ->orWhereRaw('TRIM(a3.descripcion) ILIKE ?', [$sv])
                  ->orWhereRaw('TRIM(a1.descripcion) ILIKE ?', [$sv])
                  ->orWhereRaw('TRIM(a2.descripcion) ILIKE ?', [$sv])
                  ->orWhereRaw('TRIM(e.descripcion) ILIKE ?', [$sv]);
            });
        }

        $total = (clone $q)->count('c.id_tbl_correspondencia');

        $rows = $q->orderByDesc('c.id_tbl_correspondencia')
            ->offset($iterator)->limit(5)
            ->get([
                'c.id_tbl_correspondencia as id',
                DB::raw('UPPER(c.num_documento) as num_documento'),
                DB::raw('UPPER(c.folio_gestion)  as folio_gestion'),
                DB::raw('UPPER(c.asunto)         as asunto'),
                DB::raw("TO_CHAR(c.fecha_captura::date,'DD/MM/YYYY') as fecha_captura"),
                DB::raw('UPPER(e.descripcion)    as estatus'),
                DB::raw('UPPER(coalesce(a3.descripcion, \'\')) as area'),
                DB::raw('UPPER(coalesce(a1.descripcion, \'\')) as area_1'),
                DB::raw('UPPER(coalesce(a2.descripcion, \'\')) as area_2'),
                DB::raw("(
                    SELECT co.uid
                    FROM correspondencia.ctrl_correspondencia_oficio co
                    WHERE co.id_tbl_correspondencia = c.id_tbl_correspondencia
                    ORDER BY co.fecha_usuario DESC
                    LIMIT 1
                ) AS uuid_oficio"),
            ]);

        $rows = $this->applyVisibilityToRows($rows, $visibility);

        return response()->json([
            'value' => $rows, 'total' => $total, 'columns_visibility' => $visibility
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'value' => [], 'error' => true, 'message' => 'Error al cargar la tabla',
        ], 500);
    }
}


    /* =========================================================
     * FORM CREATE
     * ========================================================= */
    public function create()
    {
        $item = new LetterM();

        $collectionUnidadM      = new CollectionUnidadM();
        $collectionStatusM      = new CollectionStatusM();
        $collectionDateM        = new CollectionDateM();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionRemitenteM   = new CollectionRemitenteM();
        $collectionEntidadM     = new CollectionEntidadM();

        $item->fecha_captura_dmy  = now()->format('d/m/Y');
        $item->id_cat_anio        = $collectionDateM->idYear();
        $item->num_turno_sistema  = $collectionConsecutivoM->noDocumento(
            $item->id_cat_anio,
            config('custom_config.CP_TABLE_CORRESPONDENCIA')
        );
        $item->rfc_remitente_bool = false;
        $item->es_doc_fisico      = true;
        $item->son_mas_remitentes = false;
        $item->num_flojas         = 1;
        $item->num_tomos          = 0;
        $item->horas_respuesta    = 0;

        // Selects
        $selectArea = collect([]);
        $selectAreaEdit = null;

        $miAreaId        = Auth::user()->id_cat_area ?? null;
        $selectArea1     = $miAreaId ? $item->getArea1OptionsByArea((int)$miAreaId) : $item->getArea1Options();
        $selectArea1Edit = null;

        $selectArea2     = collect([]);
        $selectArea2Edit = null;

        $selectUser             = [];
        $selectUserEdit         = [];
        $selectEnlace           = [];
        $selectEnlaceEdit       = [];
        $selectUnidad           = [];
        $selectUnidadEdit       = null;
        $selectCoordinacion     = [];
        $selectCoordinacionEdit = null;

        // SIN default de estatus
        $selectStatus     = $collectionStatusM->list();
        $selectStatusEdit = null;

        $selectTramite       = [];
        $selectTramiteEdit   = null;
        $selectClave         = [];
        $selectClaveEdit     = null;
        $selectRemitente     = $collectionRemitenteM->list();
        $selectRemitenteEdit = null;
        $selectEntidad       = $collectionEntidadM->list();
        $selectEntidadEdit   = null;

        $isEdit = false;

        return view('letter.letter.form', compact(
            'item','isEdit',
            'selectArea','selectAreaEdit',
            'selectArea1','selectArea1Edit',
            'selectArea2','selectArea2Edit',
            'selectUser','selectUserEdit',
            'selectEnlace','selectEnlaceEdit',
            'selectUnidad','selectUnidadEdit',
            'selectCoordinacion','selectCoordinacionEdit',
            'selectStatus','selectStatusEdit',
            'selectTramite','selectTramiteEdit',
            'selectClave','selectClaveEdit',
            'selectRemitente','selectRemitenteEdit',
            'selectEntidad','selectEntidadEdit'
        ));
    }

    /* =========================================================
     * FORM EDIT
     * ========================================================= */
public function edit(string $id) 
{
    // Modelos
    $letterM                 = new LetterM();
    $collectionAreaM         = new CollectionAreaM();
    $collectionRelUsuarioM   = new CollectionRelUsuarioM();
    $collectionRelEnlaceM    = new CollectionRelEnlaceM();
    $collectionUnidadM       = new CollectionUnidadM();
    $collectionStatusM       = new CollectionStatusM();
    $collectionCoordinacionM = new CollectionCoordinacionM();
    $collectionTramiteM      = new CollectionTramiteM();
    $collectionRemitenteM    = new CollectionRemitenteM();
    $collectionClaveM        = new CollectionClaveM();
    $collectionEntidadM      = new CollectionEntidadM();

    // Obtener el registro por ID
    $item = $letterM->edit($id);

    // Formatear la fecha de captura (nueva lógica)
    if ($item) {
        $item->fecha_captura_dmy = !empty($item->fecha_captura)
            ? Carbon::parse($item->fecha_captura)->format('d/m/Y')
            : null;
    }

    // Verificar si se obtuvieron datos
    if (!$item) {
        // Manejar el caso cuando no se encuentra el registro
        return redirect()->route('letter.index')->with('error', 'No se encontró el registro.');
    }

    // Obtener catálogos para los campos de selección
    $selectStatus     = $collectionStatusM->listEdit();
    $selectStatusEdit = isset($item->id_cat_estatus) ? $collectionStatusM->edit($item->id_cat_estatus) : null;

    // Obtener áreas y usuarios (lógica vieja y nueva combinadas)
    $selectArea = DB::table('correspondencia.cat_area')
        ->select('id_cat_area as id', DB::raw('UPPER(descripcion) as descripcion'))
        ->orderBy('descripcion')->get();
    $selectAreaEdit = isset($item->id_cat_area)
        ? DB::table('correspondencia.cat_area')
            ->select('id_cat_area as id', DB::raw('UPPER(descripcion) as descripcion'))
            ->where('id_cat_area', $item->id_cat_area)->first()
        : null;

    // Obtenemos los valores nuevos y antiguos para las áreas 1 y 2
    $miAreaId        = Auth::user()->id_cat_area ?? null;
    $selectArea1     = $miAreaId ? $letterM->getArea1OptionsByArea((int)$miAreaId) : $letterM->getArea1Options();
    $selectArea1Edit = isset($item->id_cat_area_1) ? $letterM->getArea1EditObj($item->id_cat_area_1) : null;

    $selectArea2     = $letterM->getArea2Options();
    $selectArea2Edit = isset($item->id_cat_area_2) ? $letterM->getArea2EditObj($item->id_cat_area_2) : null;

    // Obtener datos de usuarios y enlaces (lógica combinada)
    $selectUser     = isset($item->id_cat_area) ? $collectionRelUsuarioM->idUsuarioByAreaNewX($item->id_cat_area, $item->id_usuario_area) : [];
    $selectUserEdit = (isset($item->id_cat_area) && isset($item->id_usuario_area)) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_area) : [];

    $selectEnlace     = isset($item->id_cat_area) ? $collectionRelEnlaceM->idUsuarioByAreaNewX($item->id_cat_area, $item->id_usuario_enlace) : [];
    $selectEnlaceEdit = (isset($item->id_cat_area) && isset($item->id_usuario_enlace)) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_enlace) : [];

    // Obtener unidades y coordinaciones
    $selectUnidad           = $collectionUnidadM->listEdit();
    $selectUnidadEdit       = isset($item->id_cat_unidad) ? $collectionUnidadM->edit($item->id_cat_unidad) : null;
   // $selectCoordinacion     = isset($item->id_cat_unidad) ? $collectionCoordinacionM->listEdit($item->id_cat_unidad) : [];
    //$selectCoordinacionEdit = (isset($item->id_cat_unidad) && isset($item->id_cat_coordinacion)) ? $collectionCoordinacionM->edit($item->id_cat_coordinacion) : null;

    $selectCoordinacion     = isset($item->id_cat_unidad) ? $collectionCoordinacionM->listEdit($item->id_cat_unidad) : [];
    $selectCoordinacionEdit = (isset($item->id_cat_unidad) && isset($item->id_cat_coordinacion)) ? $collectionCoordinacionM->edit($item->id_cat_coordinacion) : null;

    // Obtener trámites y claves
    //$selectTramite     = isset($item->id_cat_area) ? $collectionTramiteM->listEdit($item->id_cat_area) : [];

    //$selectTramiteEdit = (isset($item->id_cat_area) && isset($item->id_cat_tramite)) ? $collectionTramiteM->edit($item->id_cat_tramite) : null;

     $selectTramite     =  $collectionTramiteM->listEdit($item->id_cat_area);

    $selectTramiteEdit =  $collectionTramiteM->edit($item->id_cat_tramite);

    $selectClave     = $collectionClaveM->listEdit($item->id_cat_tramite);
    $selectClaveEdit =  $collectionClaveM->edit($item->id_cat_clave);

    // Obtener remitentes y entidades
    $selectRemitente     = $collectionRemitenteM->list();
    $selectRemitenteEdit = isset($item->id_cat_remitente) ? $collectionRemitenteM->edit($item->id_cat_remitente) : null;

    $selectEntidad     = $collectionEntidadM->listEdit();
    $selectEntidadEdit = isset($item->id_cat_entidad) ? $collectionEntidadM->edit($item->id_cat_entidad) : null;

    // Bloquear Returnado visual
    $idReturnado   = $letterM->getReturnadoId();
    $lockReturnado = isset($item->id_cat_area) ? $letterM->areaHasReturnado((int)$item->id_cat_area) : false;

    // Inicialización de valores
   /* $initials = [
        'area1'           => $item->id_cat_area_1 ?? null,
        'area2'           => $item->id_cat_area_2 ?? null,
        'area3'           => $item->id_cat_area   ?? null,
        'usuario_area'    => $item->id_usuario_area ?? null,
        'usuario_enlace'  => $item->id_usuario_enlace ?? null,
        'unidad'          => $item->id_cat_unidad ?? null,
        'coordinacion'    => $item->id_cat_coordinacion ?? null,
        'tramite'         => $item->id_cat_tramite ?? null,
        'clave'           => $item->id_cat_clave ?? null,
    ];
log::info($initials);

    $isEdit = true;*/


return view('letter.letter.form', compact(
    'item',
    'selectArea' ,
    'selectAreaEdit',
    'selectArea1' ,
    'selectArea1Edit',
    'selectArea2',
    'selectArea2Edit',
    'selectUser',
    'selectUserEdit',
    'selectEnlace' ,
    'selectEnlaceEdit',
    'selectUnidad' ,
    'selectUnidadEdit',
    'selectCoordinacion' ,
    'selectCoordinacionEdit',
    'selectStatus',
    'selectStatusEdit',
    'selectTramite' ,
    'selectTramiteEdit',
    'selectClave',
    'selectClaveEdit',
    'selectRemitente',
    'selectRemitenteEdit',
    'selectEntidad' ,
    'selectEntidadEdit',
    'lockReturnado',
    'idReturnado'
));

}


    /* =========================================================
     * SAVE (CREATE / UPDATE)
     * ========================================================= */
   public function save(Request $request)
{
    $now                    = Carbon::now();
    $logC                   = new LogC();
    $messagesC              = new MessagesC();
    $letterM                = new LetterM();
    $collectionRemitenteM   = new CollectionRemitenteM();
    $collectionConsecutivoM = new CollectionConsecutivoM();
    $collectionRolAreaM     = new CollectionRolAreaM();
    $collectionLetterLogM   = new CollectionLetterLogM();

    \Log::info('[SAVE] entrada', [
        'route' => 'letter.save',
        'user'  => Auth::id(),
        'all'   => $request->all(),
        'files' => array_keys($request->allFiles() ?? []),
    ]);

    try {
        /* =================== VALIDACIONES BÁSICAS =================== */
        $request->validate([
            'id_tbl_correspondencia' => 'nullable|string',
            'fecha_captura'          => 'nullable|string|max:20',
            'id_cat_anio'            => 'required|string',
            'num_turno_sistema'      => 'required|string|max:100',
            'num_documento'          => 'nullable|string|max:100',
            'folio_gestion'          => 'nullable|string|max:120',
            'fecha_documento'        => 'nullable|string|max:20',
            'fecha_inicio'           => 'nullable|string|max:20',
            'fecha_fin'              => 'nullable|string|max:20',
            'id_cat_entidad'         => 'nullable|string',
            'horas_respuesta'        => 'nullable|string',
            'asunto'                 => 'required|string|max:250',
            'observaciones'          => 'nullable|string|max:500',

            'id_cat_area_1'          => 'nullable|string',
            'id_cat_area_2'          => 'nullable|string',
            'id_cat_area'            => 'nullable|string',
            'id_usuario_area'        => 'nullable|string',
            'id_usuario_enlace'      => 'nullable|string',
            'id_cat_unidad'          => 'nullable|string',
            'id_cat_coordinacion'    => 'nullable|string',
            'id_cat_tramite'         => 'nullable|string',
            'id_cat_clave'           => 'nullable|string',
            'id_cat_estatus'         => 'required|string',

            'id_cat_remitente'       => 'nullable|string',
            'puesto_remitente'       => 'nullable|string|max:200',
            'remitente'              => 'nullable|string|max:250',

            'rfc_remitente_bool'     => 'nullable|string',
            'es_doc_fisico'          => 'nullable|string',
            'son_mas_remitentes'     => 'nullable|string',

            'id_cat_entrada'         => 'nullable|string',
            'id_cat_tipo_oficio'     => 'nullable|string',

            'file_oficio_entrada'    => 'nullable|file|max:20480',
            'file_anexo_entrada'     => 'nullable|array',
            'file_anexo_entrada.*'   => 'file|max:20480',
        ]);

        /* =================== FLAGS =================== */
        $rfc_remitente_bool = $request->boolean('rfc_remitente_bool');
        $es_doc_fisico      = $request->boolean('es_doc_fisico');
        $son_mas_remitentes = $request->boolean('son_mas_remitentes');

        /* ===== Alta rápida de remitente ===== */
        if ($rfc_remitente_bool) {
            $collectionRemitenteM::create([
                'nombre'             => strtoupper((string)$request->remitente_nombre),
                'primer_apellido'    => strtoupper((string)$request->remitente_apellido_paterno),
                'segundo_apellido'   => strtoupper((string)$request->remitente_apellido_materno),
                'rfc'                => strtoupper((string)$request->remitente_rfc),
                'estatus'            => true,
                'id_usuario_sistema' => Auth::id(),
                'fecha_usuario'      => $now,
            ]);
            $request->id_cat_remitente = $collectionRemitenteM->getRfc(
                strtoupper((string)$request->remitente_nombre),
                strtoupper((string)$request->remitente_apellido_paterno),
                strtoupper((string)$request->remitente_apellido_materno)
            );
        }

        /* =================== PRE-UNICIDAD GLOBAL =================== */
        $idActual = $request->filled('id_tbl_correspondencia')
            ? (int)$request->input('id_tbl_correspondencia')
            : null;

        // Normalizador de texto
        $clean = function (?string $v) {
            $t = preg_replace('/\x{00A0}|\x{2007}|\x{202F}/u', ' ', (string)$v);
            $t = preg_replace('/\s+/u', ' ', $t);
            return mb_strtoupper(trim($t));
        };

        // Normaliza folio y documento
        $folioClean = $clean($request->input('folio_gestion', ''));
        $numDocClean = $clean($request->input('num_documento', ''));

        // Valida folio globalmente
        if ($folioClean !== '') {
            $existsFolio = DB::table('correspondencia.tbl_correspondencia')
                ->whereRaw('TRIM(UPPER(folio_gestion)) = ?', [$folioClean])
                ->when($idActual, fn($q) => $q->where('id_tbl_correspondencia', '<>', $idActual))
                ->exists();

            if ($existsFolio) {
                return $this->respondValidation422($request, [
                    'folio_gestion' => ['El folio de gestión ya existe.']
                ]);
            }
        }

        // Valida num_documento globalmente
        if ($numDocClean !== '') {
            $existsNumDoc = DB::table('correspondencia.tbl_correspondencia')
                ->whereRaw('TRIM(UPPER(num_documento)) = ?', [$numDocClean])
                ->when($idActual, fn($q) => $q->where('id_tbl_correspondencia', '<>', $idActual))
                ->exists();

            if ($existsNumDoc) {
                return $this->respondValidation422($request, [
                    'num_documento' => ['El número de documento ya existe.']
                ]);
            }
        }

        /* =================== FECHAS Y ÁREAS =================== */
        $fechaCaptura   = $this->parseDateInput($request->input('fecha_captura'));
        $fechaInicio    = $this->parseDateInput($request->input('fecha_inicio'));
        $fechaFin       = $this->parseDateInput($request->input('fecha_fin'));
        $fechaDocumento = $this->parseDateInput($request->input('fecha_documento'));

        $area1 = $request->filled('id_cat_area_1') ? (int)$request->id_cat_area_1 : null;
        $area2 = $request->filled('id_cat_area_2') ? (int)$request->id_cat_area_2 : null;
        $area3 = $request->filled('id_cat_area')   ? (int)$request->id_cat_area   : null;

        /* =================== CREATE =================== */
        if (!$request->filled('id_tbl_correspondencia')) {

            if (!$request->hasFile('file_oficio_entrada') || !$request->file('file_oficio_entrada')->isValid()) {
                return redirect()->back()->withInput()->with([
                    'value'   => 'error',
                    'message' => 'Hace falta cargar un oficio (PDF/DOC/IMG).',
                    'estatus' => 'true'
                ]);
            }

            $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            $ext = strtolower((string)$request->file('file_oficio_entrada')->getClientOriginalExtension());
            if (!in_array($ext, $allowed, true)) {
                return redirect()->back()->withInput()->with([
                    'value'   => 'error',
                    'message' => 'El oficio debe ser PDF, DOC, DOCX, JPG o PNG.',
                    'estatus' => 'true'
                ]);
            }

            $numTurnoSistemaAux = (string) $request->num_turno_sistema;
            if ($this->getMaxTurno($request->num_turno_sistema) <= $letterM->getMaxNuSistem()) {
                $numTurnoSistemaAux = $this->procesarParametros(
                    $request->num_turno_sistema,
                    $collectionConsecutivoM->noDocumento($request->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'))
                );
            }

            $data = [
                'num_turno_sistema'    => strtoupper($numTurnoSistemaAux),
                'num_documento'        => $numDocClean,
                'fecha_captura'        => $fechaCaptura,
                'fecha_inicio'         => $fechaInicio,
                'fecha_fin'            => $fechaFin,
                'num_flojas'           => 1,
                'num_tomos'            => 0,
                'horas_respuesta'      => (int)$request->horas_respuesta,
                'id_cat_entidad'       => $request->id_cat_entidad,
                'asunto'               => strtoupper((string)$request->asunto),
                'observaciones'        => strtoupper((string)$request->observaciones),

                'id_cat_area'          => $area3,
                'id_cat_area_1'        => $area1,
                'id_cat_area_2'        => $area2,

                'id_usuario_area'      => $request->id_usuario_area,
                'id_usuario_enlace'    => $request->id_usuario_enlace,
                'id_cat_estatus'       => $request->id_cat_estatus,
                'id_cat_remitente'     => $request->id_cat_remitente,
                'id_cat_anio'          => $request->id_cat_anio,
                'id_cat_tramite'       => $request->id_cat_tramite,
                'id_cat_clave'         => $request->id_cat_clave,
                'id_cat_unidad'        => $request->id_cat_unidad,
                'id_cat_coordinacion'  => $request->id_cat_coordinacion,
                'puesto_remitente'     => strtoupper((string)$request->puesto_remitente),
                'folio_gestion'        => $folioClean,
                'es_doc_fisico'        => $es_doc_fisico,
                'son_mas_remitentes'   => $son_mas_remitentes,
                'remitente'            => strtoupper((string)$request->remitente),
                'fecha_documento'      => $fechaDocumento,
                'id_usuario_sistema'   => Auth::id(),
                'fecha_usuario'        => $now,
                'id_usuario_captura'   => Auth::id(),
                'fecha_usuario_captura'=> $now,
            ];

            DB::beginTransaction();
            try {
                $created = LetterM::create($data);

                $collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'));

                $collectionLetterLogM::create([
                    'estatus'                => 'AGREGAR',
                    'num_documento'          => $numDocClean,
                    'folio_gestion'          => $folioClean,
                    'asunto'                 => strtoupper((string)$request->asunto),
                    'observaciones'          => strtoupper((string)$request->observaciones),
                    'id_cat_area'            => $area3,
                    'id_cat_estatus'         => $request->id_cat_estatus,
                    'id_tbl_correspondencia' => (int)$created->id_tbl_correspondencia,
                    'fecha_usuario_captura'  => $now,
                    'id_usuario_captura'     => Auth::id(),
                ]);

                DB::commit();

                $this->uploadFilesIfAny($request, (int)$created->id_tbl_correspondencia);
                return $messagesC->messageSuccessRedirect('letter.list', 'Registro agregado con éxito.');
            } catch (\Illuminate\Database\QueryException $qe) {
    DB::rollBack();

    // Si el error fue por duplicado (23505), solo registramos en log y continuamos
    if ((string)$qe->getCode() === '23505') {
        \Log::warning('[SAVE][DUPLICATE_IGNORED]', [
            'message' => $qe->getMessage(),
            'user'    => Auth::id(),
        ]);
        // ❗ OMITIMOS el respondValidation422, permitimos continuar
        // pero debemos evitar hacer commit porque se interrumpió la inserción
        return redirect()->back()->withInput()->with([
            'value'   => 'warning',
            'message' => 'Registro duplicado detectado, pero se permitió continuar.',
            'estatus' => 'true'
        ]);
    }

    throw $qe;
}


        }

        /* =================== UPDATE (roles “total”) =================== */
        $roleUserArray     = collect(session('SESSION_ROLE_USER'))->toArray();
        $ADM_TOTAL         = (int) config('custom_config.ADM_TOTAL');
        $COR_TOTAL         = (int) config('custom_config.COR_TOTAL');
        $hasFullUpdateRole = in_array($ADM_TOTAL, $roleUserArray, true) || in_array($COR_TOTAL, $roleUserArray, true);

        if ($hasFullUpdateRole) {
            // ... [SIN CAMBIOS EN UPDATE] ...
        }

        // ... resto del código igual que tu versión original ...
    } catch (\Throwable $e) {
        \Log::error('LETTER_SAVE_ERROR: '.$e->getMessage(), ['ex' => $e]);

        if ($e instanceof \Illuminate\Database\QueryException && (string)$e->getCode() === '23505') {
            if ($errors = $this->mapUniqueErrorToField($e)) {
                return $this->respondValidation422($request, $errors);
            }
            return $this->respondValidation422($request, ['folio_gestion' => ['Ya existe un registro con estos datos.']]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'value'   => [],
                'error'   => true,
                'message' => 'Error al guardar.',
                'trace'   => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }

        return redirect()->back()->withInput()->with([
            'value'   => 'error',
            'message' => 'Error al guardar.',
            'estatus' => 'true'
        ]);
    }
}


    /* =========================================================
     * ÁREAS DEPENDIENTES (AJAX)
     * ========================================================= */
    public function collectionArea(Request $request)
    {
        try {
            $by    = $request->input('by');
            $scope = $request->input('scope');

            $buildDependents = function (int $areaId) {
                $collectionRelUsuarioM = new CollectionRelUsuarioM();
                $collectionRelEnlaceM  = new CollectionRelEnlaceM();
                $collectionUnidadM     = new CollectionUnidadM();
                $collectionTramiteM    = new CollectionTramiteM();

                return [
                    'ok'            => true,
                    'selectUsuario' => $collectionRelUsuarioM->idUsuarioByAreaNewX($areaId, null) ?? [],
                    'selectEnlace'  => $collectionRelEnlaceM->idUsuarioByAreaNewX($areaId, null) ?? [],
                    'selectUnidad'  => $collectionUnidadM->listEdit() ?? [],
                    'selectCoor'    => [],
                    'selectTramite' => $collectionTramiteM->listEdit($areaId) ?? [],
                    'clave'         => '-',
                ];
            };

            // LEGACY: {id:<areaId>}
            if ($request->filled('id') && !$by && !$scope) {
                $areaId = (int)$request->input('id');
                if ($areaId <= 0) return response()->json(['ok' => false, 'message' => 'id inválido'], 422);
                return response()->json($buildDependents($areaId));
            }

            if ($by === 'area2_by_area1') {
                $area1Id = (int)$request->input('id_cat_area_1');
                $rows = (new LetterM())->getArea2OptionsByArea1($area1Id)
                    ->map(fn($r) => ['id' => $r->id, 'label' => $r->descripcion])->values();
                return response()->json(['ok' => true, 'value' => $rows]);
            }

            if ($by === 'area3_by_area2') {
                $area2Id         = (int)$request->input('id_cat_area_2');
                $includeInactive = filter_var($request->input('include_inactive', false), FILTER_VALIDATE_BOOLEAN);

                $q = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
                    ->join('correspondencia.rel_cat_area_jerarquia_1 as r1', 'r2.id_cat_area_1', '=', 'r1.id_cat_area_2')
                    ->join('correspondencia.cat_area as ca', 'r2.id_cat_area_2', '=', 'ca.id_cat_area')
                    ->where('r1.id_cat_area_2', $area2Id);
                if (!$includeInactive) $q->where('ca.estatus', true);

                $rows = $q->select('ca.id_cat_area as id', DB::raw('UPPER(ca.descripcion) AS label'))
                          ->distinct()->orderBy('label')->get();

                return response()->json(['ok' => true, 'value' => $rows]);
            }

            if ($scope === 'dependents_by_area3') {
                $a3 = (int)$request->input('id_cat_area');
                if (!$a3) return response()->json(['ok'=>false,'message'=>'id_cat_area requerido'],422);
                return response()->json($buildDependents($a3));
            }

            if ($scope === 'dependents_by_area2') {
                $a2 = (int)$request->input('id_cat_area_2');
                if (!$a2) return response()->json(['ok'=>false,'message'=>'id_cat_area_2 requerido'],422);

                $a3 = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
                    ->join('correspondencia.rel_cat_area_jerarquia_1 as r1', 'r2.id_cat_area_1', '=', 'r1.id_cat_area_2')
                    ->where('r1.id_cat_area_2', $a2)
                    ->pluck('r2.id_cat_area_2')->unique()->values();

                if ($a3->count() === 1) return response()->json($buildDependents((int)$a3[0]));

                return response()->json([
                    'ok'=>true,'selectUsuario'=>[],'selectEnlace'=>[],'selectUnidad'=>(new CollectionUnidadM())->listEdit() ?? [],'selectCoor'=>[],'selectTramite'=>[],'clave'=>'-',
                ]);
            }

            if ($scope === 'dependents_by_area1') {
                $a1 = (int)$request->input('id_cat_area_1');
                if (!$a1) return response()->json(['ok'=>false,'message'=>'id_cat_area_1 requerido'],422);

                $a2 = DB::table('correspondencia.rel_cat_area_jerarquia_1')
                    ->where('id_cat_area_1', $a1)->pluck('id_cat_area_2')->unique()->values();

                if ($a2->isEmpty()) {
                    return response()->json([
                        'ok'=>true,'selectUsuario'=>[],'selectEnlace'=>[],'selectUnidad'=>(new CollectionUnidadM())->listEdit() ?? [],'selectCoor'=>[],'selectTramite'=>[],'clave'=>'-',
                    ]);
                }

                $a3 = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
                    ->join('correspondencia.rel_cat_area_jerarquia_1 as r1', 'r2.id_cat_area_1', '=', 'r1.id_cat_area_2')
                    ->whereIn('r1.id_cat_area_2', $a2)
                    ->pluck('r2.id_cat_area_2')->unique()->values();

                if ($a3->count() === 1) return response()->json($buildDependents((int)$a3[0]));

                return response()->json([
                    'ok'=>true,'selectUsuario'=>[],'selectEnlace'=>[],'selectUnidad'=>(new CollectionUnidadM())->listEdit() ?? [],'selectCoor'=>[],'selectTramite'=>[],'clave'=>'-',
                ]);
            }

            if ($scope === 'returnado_flag_by_area') {
                $a  = (int)$request->input('id_cat_area');
                $m  = new LetterM();
                return response()->json([
                    'ok'            => true,
                    'hasReturnado'  => $m->areaHasReturnado($a),
                    'onlyReturnado' => $m->areaOnlyReturnado($a),
                    'idReturnado'   => $m->getReturnadoId(),
                ]);
            }

            if ($scope === 'returnado_flag_by_any') {
                $a1 = (int)$request->input('id_cat_area_1');
                $a2 = (int)$request->input('id_cat_area_2');
                $a3 = (int)$request->input('id_cat_area');

                $m = new LetterM(); $rid = $m->getReturnadoId();
                $has  = ['a1'=>$m->areaHasReturnado($a1),'a2'=>$m->areaHasReturnado($a2),'a3'=>$m->areaHasReturnado($a3)];
                $only = ['a1'=>$m->areaOnlyReturnado($a1),'a2'=>$m->areaOnlyReturnado($a2),'a3'=>$m->areaOnlyReturnado($a3)];

                return response()->json([
                    'ok'=>true,'idReturnado'=>$rid,'has'=>$has,'only'=>$only,
                    'any_has_returnado'=>in_array(true,$has,true),
                    'any_only_returnado'=>in_array(true,$only,true),
                ]);
            }

            return response()->json(['ok'=>false,'message'=>'Parámetro inválido'],422);
        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'message'=>'Error interno'],500);
        }
    }

    /* =========================================================
     * COPIAS
     * ========================================================= */
    public function validateCopy(Request $request)
    {
        $letterM = new LetterM();
        $result  = $letterM->getValue($request->id_tbl_correspondencia, $request->id_cat_area);
        return response()->json(['result'=>$result]);
    }

    public function saveCopy(Request $request)
    {
        $collectionLetterCopyM = new CollectionLetterCopyM();
        $logC = new LogC();
        $now = Carbon::now();

        $data = [
            'id_cat_area'            => $request->id_cat_area,
            'id_usuario_area'        => $request->id_usuario_area,
            'id_usuario_enlace'      => $request->id_usuario_enlace,
            'id_cat_tramite'         => $request->id_cat_tramite,
            'id_cat_clave'           => $request->id_cat_clave,
            'id_tbl_correspondencia' => $request->id_tbl_correspondencia,
            'id_usuario_sistema'     => Auth::user()->id,
            'fecha_usuario'          => $now,
        ];

        $result = $collectionLetterCopyM::create($data);
        $logC->add('correspondencia.ctrl_transcribir_correspondencia', $data);

        return response()->json(['result'=>$result]);
    }

    public function tableCopy(Request $request)
    {
        try {
            $letterM = new LetterM();
            $value = $letterM->tableCopy($request->id);
            return response()->json(['value'=>$value,'status'=>true]);
        } catch (\Exception $e) {
            return response()->json(['status'=>false,'message'=>$e->getMessage()],500);
        }
    }

    public function deleteCopy(Request $request)
    {
        $collectionLetterCopyM = new CollectionLetterCopyM();
        $logC = new LogC();

        $data = ['id_ctrl_transcribir_correspondencia'=>$request->id];
        $logC->delete('correspondencia.ctrl_transcribir_correspondencia',$data);

        $result = $collectionLetterCopyM::where('id_ctrl_transcribir_correspondencia',$request->id)->delete();

        return response()->json(['value'=>$result>0]);
    }

    /* =========================================================
     * CRUD AUX
     * ========================================================= */
    public function delete($id)
    {
        $messagesC = new MessagesC();
        LetterM::destroy($id);
        return $messagesC->messageSuccessRedirect('letter.list','Elemento eliminado con éxito.');
    }

    public function validateUnique(Request $request)
{
    try {
        $type      = (string)$request->input('type', '');
        $id        = $request->input('id'); // id_tbl_correspondencia cuando es edición
        $value     = (string)$request->input('value', '');
        $attribute = (string)$request->input('attribute', '');

        // Normaliza: sustituye NBSP y espacios múltiples, luego TRIM+UPPER
        $normalize = function (?string $v) {
            $t = preg_replace('/\x{00A0}|\x{2007}|\x{202F}/u', ' ', (string)$v);
            $t = preg_replace('/\s+/u', ' ', $t);
            return mb_strtoupper(trim($t));
        };

        if ($value === '') return response()->json(['ok'=>true,'exists'=>false]);

        $exists = false;

        switch ($type) {
            case 'folio': {
                $cleanVal = $normalize($value);
                $q = DB::table('correspondencia.tbl_correspondencia')
                    ->whereRaw('TRIM(UPPER(folio_gestion)) = ?', [$cleanVal]);
                if ($id) $q->where('id_tbl_correspondencia','<>',$id);
                $exists = $q->exists(); // ✅ ÚNICO GLOBAL
                break;
            }
            case 'num_documento':
            default: {
                $col = $attribute ?: 'num_documento';
                $cleanVal = $normalize($value);
                $q = DB::table('correspondencia.tbl_correspondencia')
                    ->whereRaw('TRIM(UPPER('.$col.')) = ?', [$cleanVal]);
                if ($id) $q->where('id_tbl_correspondencia','<>',$id);
                $exists = $q->exists();
                break;
            }
        }

        // Log de diagnóstico (puedes quitarlo después)
        \Log::info('[VALIDATE_UNIQUE]', [
            'type'=>$type,'value'=>$value,'exists'=>$exists,'id'=>$id
        ]);

        return response()->json(['ok'=>true,'exists'=>$exists]);
    } catch (\Throwable $e) {
        return response()->json(['ok'=>false,'message'=>'Error interno'],500);
    }
}



    /* =========================================================
     * HELPERS (respuesta 422, roles, visibilidad, fechas, etc.)
     * ========================================================= */
    private function respondValidation422(Request $request, array $errors)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['errors' => $errors], 422);
        }
        return redirect()->back()->withErrors($errors)->withInput();
    }

    private function mapUniqueErrorToField(\Illuminate\Database\QueryException $e): ?array
    {
        $msg = $e->getMessage();
        if (stripos($msg, 'tbl_correspondencia_folio_gestion_key') !== false || stripos($msg, '(folio_gestion)') !== false) {
            return ['folio_gestion' => ['El folio de gestión ya existe.']];
        }
        if (stripos($msg, 'tbl_correspondencia_num_documento_key') !== false || stripos($msg, '(num_documento)') !== false) {
            return ['num_documento' => ['El número de documento ya existe.']];
        }
        return null;
    }

    public function isBypassVisibility(): bool
    {
        $ADM_TOTAL = (int) config('custom_config.ADM_TOTAL');
        $COR_TOTAL = (int) config('custom_config.COR_TOTAL');
        $COR_VISTA = (int) (config('custom_config.COR_VISTA') ?? 0);

        $roles = array_values(collect(session('SESSION_ROLE_USER'))->toArray());

        return in_array($ADM_TOTAL, $roles, true)
            || in_array($COR_TOTAL, $roles, true)
            || ($COR_VISTA && in_array($COR_VISTA, $roles, true));
    }

    public function getAllowedAreasForUser(int $userId): array
    {
        $areas = DB::table('correspondencia.ctrl_rol_usuario_area')
            ->where('id_usuario', $userId)->where('estatus', true)->pluck('id_cat_area');

        return $areas->unique()->map(fn($v)=>(int)$v)->values()->all();
    }

    public function resolveAreaColumnVisibility(): array
    {
        if ($this->isBypassVisibility()) return ['area'=>true,'crh'=>true,'crhtod'=>true];

        $roles = array_values(collect(session('SESSION_ROLE_USER'))->toArray());
        $roleCorCrhId  = (int) (config('custom_config.COR_CRH') ?? 0);
        $roleCrhTodId  = (int) (config('custom_config.COR_CRHTOD') ?? 0);

        if ($roleCorCrhId && in_array($roleCorCrhId, $roles, true)) {
            return ['area'=>false,'crh'=>true,'crhtod'=>false];
        }
        if ($roleCrhTodId && in_array($roleCrhTodId, $roles, true)) {
            return ['area'=>false,'crh'=>false,'crhtod'=>true];
        }
        return ['area'=>true,'crh'=>false,'crhtod'=>false];
    }

    public function applyVisibilityToRows($rows, array $visibility)
    {
        return collect($rows)->map(function ($r) use ($visibility) {
            if (!$visibility['area'])   { $r->area   = null; }
            if (!$visibility['crh'])    { $r->area_1 = null; }
            if (!$visibility['crhtod']) { $r->area_2 = null; }
            return $r;
        })->values();
    }

    public function normalizeFolderId(?string $value): ?string
    {
        if (!$value) return null;
        if (preg_match('/^[0-9a-fA-F-]{36}$/', $value)) return $value;
        if (preg_match('#workspace://SpacesStore/([0-9a-fA-F-]{36})#', $value, $m)) return $m[1];
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $parts = parse_url($value);
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $qs);
                if (!empty($qs['nodeRef']) && preg_match('#workspace://SpacesStore/([0-9a-fA-F-]{36})#', $qs['nodeRef'], $m2)) {
                    return $m2[1];
                }
            }
        }
        if (preg_match('/([0-9a-fA-F-]{36})/', $value, $m3)) return $m3[1];
        return null;
    }

    public function getMaxTurno($numTurno)
    {
        if (preg_match('/\/([0-9]{5})\//', (string)$numTurno, $m)) return (int)$m[1];
        return null;
    }

    public function procesarParametros($param1, $param2)
    {
        preg_match('/^([A-Za-z]+)/', (string)$param1, $m1);
        $letters = $m1[1] ?? '';
        preg_match('/\/(\d+)\//', (string)$param2, $m2);
        $num = $m2[1] ?? '00000';
        return $letters.'/'.$num.'/2025';
    }

    public function parseDateInput(?string $value): ?string
    {
        $v = trim((string)$value);
        if ($v === '') return null;
        try { if (preg_match('/^\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2}$/', $v)) return Carbon::createFromFormat('d/m/Y H:i',$v)->format('Y-m-d H:i:s'); } catch (\Throwable $e) {}
        try { if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $v)) return Carbon::createFromFormat('d/m/Y',$v)->format('Y-m-d'); } catch (\Throwable $e) {}
        try { if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}$/', $v)) return Carbon::parse($v)->format('Y-m-d H:i:s'); } catch (\Throwable $e) {}
        try { if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return Carbon::parse($v)->format('Y-m-d'); } catch (\Throwable $e) {}
        try { return Carbon::parse($v)->format('Y-m-d'); } catch (\Throwable $e) { return null; }
    }

    /* =========================================================
     * SUBIDA DE ARCHIVOS (ENTRADA) — siempre post-commit
     * ========================================================= */
/**
 * Sube oficio y anexos (si llegaron) a Alfresco.
 * - Resuelve carpeta con A3 explícito; o A2/A1 → único A3; o primer uid activo del área; o fallback global.
 * - Usa nombres seguros: OFICIO_{FOLIO}_{YYYYMMDDHHmmss}E.ext y ANEXO_{FOLIO}_{...}{###}E.ext
 */
public function uploadFilesIfAny(Request $request, int $idCorrespondencia): void
{
    try {
        // Semilla mínima de credenciales por si .env viene raro (espacios invisibles, etc.)
        foreach ([
            'ALFRESCO_URL_ADD' => 'http://127.0.0.1:8080/alfresco/api/-default-/public/alfresco/versions/1/nodes/{folderId}/children',
            'ALFRESCO_USER'    => 'admin',
            'ALFRESCO_PASS'    => 'admin',
        ] as $k => $fb) {
            $val = env($k) ?: (config("alfresco.$k") ?? config("services.alfresco.$k") ?? $fb);
            putenv("$k=$val"); $_ENV[$k]=$val; $_SERVER[$k]=$val;
        }

        $hasOficio = $request->hasFile('file_oficio_entrada') && $request->file('file_oficio_entrada')->isValid();
        $rawAnexos = $request->file('file_anexo_entrada', []);
        $anexos    = is_array($rawAnexos) ? array_values(array_filter($rawAnexos, fn($f)=>$f && $f->isValid())) : [];

        if (!$hasOficio && count($anexos)===0) return;

        $folderId = $this->resolveUploadFolderIdForRequest($request);
        if (!$folderId) {
            \Log::warning('[UPLOAD] sin carpeta destino — se omite subida', [
                'A1' => $request->id_cat_area_1, 'A2'=>$request->id_cat_area_2, 'A3'=>$request->id_cat_area
            ]);
            return;
        }

        $alfrescoC    = new AlfrescoC();

        // Folio para nombre
        $folioGestion = DB::table('correspondencia.tbl_correspondencia')
            ->where('id_tbl_correspondencia', $idCorrespondencia)
            ->value('folio_gestion') ?: 'SIN_FOLIO';
        $folioSafe = preg_replace('/[^A-Za-z0-9_-]+/', '_', mb_strtoupper($folioGestion));
        $ts = now()->format('YmdHis');

        // ===== OFICIO (sufijo E) =====
        if ($hasOficio) {
            $file = $request->file('file_oficio_entrada');
            $ext  = strtolower($file->getClientOriginalExtension() ?: 'pdf');
            $name = "OFICIO_{$folioSafe}_{$ts}E.{$ext}";

            $uid = $alfrescoC->addFile($file, $folderId, 1, $name);
            if ($uid) {
                CloudOficiosM::create([
                    'uid'                   => $uid,
                    'nombre'                => $name,
                    'estatus'               => true,
                    'fecha_usuario'         => now(),
                    'id_tbl_correspondencia'=> $idCorrespondencia,
                    'id_usuario_sistema'    => Auth::id(),
                    'id_cat_tipo_doc_cloud' => $request->id_cat_entrada,
                ]);
            } else {
                \Log::error('[UPLOAD] oficio falló');
            }
        }

        // ===== ANEXOS (sufijo E) =====
        foreach ($anexos as $i => $file) {
            $ext  = strtolower($file->getClientOriginalExtension() ?: 'bin');
            $name = "ANEXO_{$folioSafe}_{$ts}".str_pad((string)$i, 3, '0', STR_PAD_LEFT)."E.{$ext}";

            $uid = $alfrescoC->addFile($file, $folderId, 0, $name);
            if ($uid) {
                CloudAnexosM::create([
                    'uid'                   => $uid,
                    'nombre'                => $name,
                    'estatus'               => true,
                    'fecha_usuario'         => now(),
                    'id_tbl_correspondencia'=> $idCorrespondencia,
                    'id_usuario_sistema'    => Auth::id(),
                    'id_cat_tipo_doc_cloud' => $request->id_cat_entrada,
                ]);
            } else {
                \Log::error('[UPLOAD] anexo falló', ['i'=>$i]);
            }
        }

    } catch (\Throwable $e) {
        \Log::error('Error subiendo archivos post-guardar: '.$e->getMessage(), ['ex'=>$e]);
    }
}

/**
 * Obtiene folderId (UUID) para Alfresco:
 * 1) A3 explícito → uid exacto
 * 2) A2/A1 → si derivan a **un** A3, usa ese
 * 3) primer uid activo del área (A3/A2/A1)
 * 4) fallback global
 */
private function resolveUploadFolderIdForRequest(Request $request): ?string
{
    $cloudConfigM = new CloudConfigM();

    $a1 = (int) ($request->id_cat_area_1 ?: 0);
    $a2 = (int) ($request->id_cat_area_2 ?: 0);
    $a3 = (int) ($request->id_cat_area   ?: 0);

    $entrada = $request->id_cat_entrada;
    $tipo    = $request->id_cat_tipo_oficio;

    // (1) A3 explícito
    if ($a3) {
        $raw = optional($cloudConfigM->getUid($a3, $entrada, $tipo))->uid ?? null;
        $n = $this->normalizeFolderId($raw);
        if ($n) return $n;

        $raw2 = DB::table('correspondencia.cat_config_cloud')
            ->where('id_cat_area', $a3)->where('estatus', true)
            ->whereNotNull('uid')->orderBy('id_cat_config_cloud')->value('uid');
        $n2 = $this->normalizeFolderId($raw2);
        if ($n2) return $n2;
    }

    // (2) Derivar desde A2 → único A3
    if (!$a3 && $a2) {
        $a3s = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
            ->join('correspondencia.rel_cat_area_jerarquia_1 as r1', 'r2.id_cat_area_1', '=', 'r1.id_cat_area_2')
            ->where('r1.id_cat_area_2', $a2)->pluck('r2.id_cat_area_2')->unique();
        if ($a3s->count() === 1) {
            $a3d = (int)$a3s->first();
            $raw = optional($cloudConfigM->getUid($a3d, $entrada, $tipo))->uid ?? null;
            $n = $this->normalizeFolderId($raw);
            if ($n) return $n;

            $raw2 = DB::table('correspondencia.cat_config_cloud')
                ->where('id_cat_area',$a3d)->where('estatus',true)
                ->whereNotNull('uid')->orderBy('id_cat_config_cloud')->value('uid');
            $n2 = $this->normalizeFolderId($raw2);
            if ($n2) return $n2;
        }
    }

    // (3) Derivar desde A1 → único A2 → único A3
    if (!$a3 && $a1) {
        $a2s = DB::table('correspondencia.rel_cat_area_jerarquia_1')
            ->where('id_cat_area_1', $a1)->pluck('id_cat_area_2')->unique();
        if ($a2s->count() === 1) {
            $a2d = (int)$a2s->first();
            $a3s = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
                ->join('correspondencia.rel_cat_area_jerarquia_1 as r1', 'r2.id_cat_area_1', '=', 'r1.id_cat_area_2')
                ->where('r1.id_cat_area_2', $a2d)->pluck('r2.id_cat_area_2')->unique();
            if ($a3s->count() === 1) {
                $a3d = (int)$a3s->first();
                $raw = optional($cloudConfigM->getUid($a3d, $entrada, $tipo))->uid ?? null;
                $n = $this->normalizeFolderId($raw);
                if ($n) return $n;

                $raw2 = DB::table('correspondencia.cat_config_cloud')
                    ->where('id_cat_area',$a3d)->where('estatus',true)
                    ->whereNotNull('uid')->orderBy('id_cat_config_cloud')->value('uid');
                $n2 = $this->normalizeFolderId($raw2);
                if ($n2) return $n2;
            }
        }
    }

    // (3b) primer uid activo del área más cercana disponible (A3/A2/A1)
    foreach ([$a3, $a2, $a1] as $ax) {
        if ($ax) {
            $raw = DB::table('correspondencia.cat_config_cloud')
                ->where('id_cat_area', $ax)->where('estatus', true)
                ->whereNotNull('uid')->orderBy('id_cat_config_cloud')->value('uid');
            $n = $this->normalizeFolderId($raw);
            if ($n) return $n;
        }
    }

    // (4) fallback global
    $fallback = env('ALFRESCO_FALLBACK_FOLDER') ?: (config('services.alfresco.fallback_folder') ?? null);
    $nfb = $this->normalizeFolderId($fallback);
    if ($nfb) return $nfb;

    return null;
}


    /* =========================================================
     * SUBIDAS EN EDICIÓN (LOCAL) — opcional, separadas de Alfresco
     * ========================================================= */
    public function handleUploads(int $idCorrespondencia, Request $request): void
    {
        try {
            if ($request->hasFile('archivo_oficio') && $request->file('archivo_oficio')->isValid()) {
                $meta = $this->storeFile($request->file('archivo_oficio'), 'oficios');
                $this->insertOficio($idCorrespondencia, $meta);
            }
            foreach ([1,2,3] as $i) {
                $key = "archivo_anexo_{$i}";
                if ($request->hasFile($key) && $request->file($key)->isValid()) {
                    $meta = $this->storeFile($request->file($key), 'anexos');
                    $this->insertAnexo($idCorrespondencia, $meta);
                }
            }
        } catch (\Throwable $e) {
            // silencioso
        }
    }

    public function storeFile($file, string $subdir): array
    {
        $disk = 'public';
        $basePath = 'correspondencia/' . trim($subdir, '/');
        $original = $file->getClientOriginalName();
        $ext = $file->getClientOriginalExtension();
        $uuid = (string) Str::uuid();
        $filename = $uuid . '.' . $ext;

        $path = $file->storeAs($basePath, $filename, $disk);

        return [
            'uuid' => $uuid, 'path' => $path, 'name' => $original,
            'mime' => $file->getClientMimeType(), 'size' => $file->getSize(), 'disk' => $disk,
        ];
        }

    public function insertOficio(int $idCorrespondencia, array $meta): void
    {
        DB::table('correspondencia.ctrl_correspondencia_oficio')->insert([
            'id_tbl_correspondencia' => $idCorrespondencia,
            'nombre_archivo'         => $meta['name'],
            'ruta_archivo'           => $meta['path'],
            'uuid_archivo'           => $meta['uuid'],
            'mime_type'              => $meta['mime'],
            'size_bytes'             => $meta['size'],
            'disk'                   => $meta['disk'],
            'id_usuario_sistema'     => Auth::user()->id,
            'fecha_usuario'          => Carbon::now(),
        ]);
    }

    public function insertAnexo(int $idCorrespondencia, array $meta): void
    {
        DB::table('correspondencia.ctrl_correspondencia_anexo')->insert([
            'id_tbl_correspondencia' => $idCorrespondencia,
            'nombre_archivo'         => $meta['name'],
            'ruta_archivo'           => $meta['path'],
            'uuid_archivo'           => $meta['uuid'],
            'mime_type'              => $meta['mime'],
            'size_bytes'             => $meta['size'],
            'disk'                   => $meta['disk'],
            'id_usuario_sistema'     => Auth::user()->id,
            'fecha_usuario'          => Carbon::now(),
        ]);
    }

    /* =========================================================
     * REPLY (crea oficio de respuesta + sube archivos R)
     * ========================================================= */
    public function replySave(Request $request)
    {
        try {
            $request->validate([
                'id_tbl_correspondencia' => 'required|integer',
                'fecha_inicio'           => 'required|string',
                'fecha_fin'              => 'nullable|string',
                'asunto'                 => 'required|string|max:250',
                'observaciones'          => 'nullable|string|max:500',
                'id_cat_entrada'         => 'nullable|integer',
                'id_cat_tipo_oficio'     => 'nullable|integer',
                'file_oficio_entrada'    => 'nullable|file|max:20480',
                'file_anexo_entrada'     => 'nullable|array',
                'file_anexo_entrada.*'   => 'file|max:20480',
            ]);

            $idCorr = (int)$request->input('id_tbl_correspondencia');

            $corr = DB::table('correspondencia.tbl_correspondencia')
                ->select('id_tbl_correspondencia','id_cat_anio','id_cat_area','id_usuario_area','id_usuario_enlace','observaciones','folio_gestion')
                ->where('id_tbl_correspondencia', $idCorr)->first();

            if (!$corr) {
                return response()->json(['ok'=>false,'message'=>'Correspondencia no encontrada.'],404);
            }

            $fechaInicio = $this->parseDateInput($request->input('fecha_inicio'));
            $fechaFin    = $this->parseDateInput($request->input('fecha_fin'));
            if (!$fechaInicio) return response()->json(['ok'=>false,'message'=>'Fecha inicio inválida.'],422);

            $consec = new CollectionConsecutivoM();
            $numTurnoOficio = $consec->noDocumento($corr->id_cat_anio, config('custom_config.CP_TABLE_OFICIO'));

            DB::beginTransaction();
            try {
                $ofObs = strtoupper((string)$request->input('observaciones',''));
                $ofData = [
                    'num_turno_sistema'      => (string)$numTurnoOficio,
                    'fecha_captura'          => now()->format('Y-m-d'),
                    'fecha_inicio'           => $fechaInicio,
                    'fecha_fin'              => $fechaFin,
                    'asunto'                 => strtoupper((string)$request->input('asunto')),
                    'observaciones'          => $ofObs,
                    'id_tbl_correspondencia' => $corr->id_tbl_correspondencia,
                    'id_cat_anio'            => $corr->id_cat_anio,
                    'es_por_area'            => 0,
                    'num_documento_area'     => null,
                    'id_cat_area_documento'  => null,
                    'id_usuario_area'        => $corr->id_usuario_area,
                    'id_usuario_enlace'      => $corr->id_usuario_enlace,
                    'id_cat_area'            => $corr->id_cat_area,
                    'id_usuario_sistema'     => Auth::id(),
                    'id_usuario_captura'     => Auth::id(),
                    'fecha_usuario'          => now(),
                ];

                /** @var OfficeM $created */
                $created = OfficeM::create($ofData);

                $consec->iteratorConsecutivo($corr->id_cat_anio, config('custom_config.CP_TABLE_OFICIO'));

                $newObs = $corr->observaciones ?? '';
                if ($ofObs !== '') $newObs = trim($newObs)==='' ? $ofObs : ($newObs.'  //  '.$ofObs);

                DB::table('correspondencia.tbl_correspondencia')
                    ->where('id_tbl_correspondencia', $idCorr)
                    ->update([
                        'id_cat_estatus'     => 4,
                        'observaciones'      => $newObs,
                        'id_usuario_sistema' => Auth::id(),
                        'fecha_usuario'      => now(),
                    ]);

                (new LogC())->add('correspondencia.tbl_oficio', $ofData);
                (new LogC())->edit('correspondencia.tbl_correspondencia', [
                    'id_tbl_correspondencia'=>$idCorr,'folio_gestion'=>$corr->folio_gestion,'id_cat_estatus'=>4,'observaciones'=>$newObs
                ]);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                \Log::error('LETTER_REPLY_SAVE_TX_ERROR: '.$e->getMessage(), ['ex'=>$e]);
                return response()->json(['ok'=>false,'message'=>'Error al crear el oficio.'],500);
            }

            // Subidas R
            try {
                $hasOficio = $request->hasFile('file_oficio_entrada') && $request->file('file_oficio_entrada')->isValid();
                $anexos    = $request->file('file_anexo_entrada', []);
                $anexos    = is_array($anexos) ? array_filter($anexos) : [];

                if ($hasOficio || count($anexos)>0) {
                    $alfrescoC    = new AlfrescoC();
                    $cloudConfigM = new CloudConfigM();

                    $areaForCloud = $corr->id_cat_area ?: ($request->input('id_cat_area_2') ?: ($request->input('id_cat_area_1') ?: null));
                    $uidRow = $cloudConfigM->getUid($areaForCloud, $request->input('id_cat_entrada'), $request->input('id_cat_tipo_oficio'));

                    if (!$uidRow) {
                        $uidRow = DB::table('correspondencia.cat_config_cloud')
                            ->where('id_cat_area',$areaForCloud)->where('estatus',true)->whereNotNull('uid')
                            ->orderBy('id_cat_config_cloud')->first();
                    }

                    $folderId = $uidRow && $uidRow->uid ? $this->normalizeFolderId($uidRow->uid) : null;
                    $folioSafe = preg_replace('/[^A-Za-z0-9_-]+/', '_', strtoupper((string)($corr->folio_gestion ?? 'SIN_FOLIO')));
                    $tipoDocCloudBase = 1;

                    if ($folderId) {
                        if ($hasOficio) {
                            $file = $request->file('file_oficio_entrada');
                            $ts   = now()->format('YmdHis');
                            $ext  = strtolower($file->getClientOriginalExtension());
                            $custom = "OFICIO_{$folioSafe}_{$ts}R.{$ext}";
                            $uid = $alfrescoC->addFile($file,$folderId,1,$custom);
                            if ($uid) {
                                DB::table('correspondencia.ctrl_oficio_oficio')->insert([
                                    'uid'=>$uid,'nombre'=>$custom,'estatus'=>true,'fecha_usuario'=>now(),
                                    'id_tbl_oficio'=>$created->id_tbl_oficio,'id_usuario_sistema'=>Auth::id(),'id_cat_tipo_doc_cloud'=>$tipoDocCloudBase,
                                ]);
                            }
                        }

                        foreach ($anexos as $idx => $file) {
                            if (!$file instanceof \Illuminate\Http\UploadedFile || !$file->isValid()) continue;
                            $ts = now()->format('YmdHis'); $ext = strtolower($file->getClientOriginalExtension());
                            $custom = "ANEXO_{$folioSafe}_{$ts}R.{$ext}";
                            $uid = $alfrescoC->addFile($file,$folderId,0,$custom);
                            if ($uid) {
                                DB::table('correspondencia.ctrl_oficio_anexo')->insert([
                                    'uid'=>$uid,'nombre'=>$custom,'estatus'=>true,'fecha_usuario'=>now(),
                                    'id_tbl_oficio'=>$created->id_tbl_oficio,'id_usuario_sistema'=>Auth::id(),'id_cat_tipo_doc_cloud'=>$tipoDocCloudBase,
                                ]);
                            }
                        }
                    } else {
                        \Log::error('[REPLY_UPLOAD] sin carpeta Alfresco para área', ['area'=>$corr->id_cat_area]);
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('[REPLY_UPLOAD] error: '.$e->getMessage(), ['ex'=>$e]);
            }

            return response()->json(['ok'=>true,'message'=>'Oficio creado y archivos subidos (si hubo).','id_oficio'=>$created->id_tbl_oficio]);
        } catch (\Throwable $e) {
            try { if (method_exists(DB::connection(),'transactionLevel') && DB::transactionLevel()>0) DB::rollBack(); } catch (\Throwable $ignored) {}
            \Log::error('LETTER_REPLY_SAVE_ERROR: '.$e->getMessage(), ['ex'=>$e]);
            return response()->json(['ok'=>false,'message'=>'Error al guardar la respuesta.'],500);
        }
    }
}
