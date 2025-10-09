<?php

namespace App\Http\Controllers\Letter\Letter;

use App\Http\Controllers\Admin\MessagesC;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Letter\Log\LogC;

use App\Models\Letter\Letter\LetterM;
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

// 👇 NUEVO: para crear el registro en tbl_oficio
use App\Models\Letter\Office\OfficeM;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LetterC extends Controller
{
    public function __invoke()
    {
        return view('letter.letter.list');
    }

    /* ======================== TABLA ======================== */
    public function table(Request $request, LetterM $model)
    {
        try {
            $iterator    = (int) $request->get('iterator', 0);
            $searchValue = (string) $request->get('searchValue', '');
            $idUser      = []; // si necesitas filtrar por usuario, ajústalo

            $rows = $model->list($iterator, $searchValue, $idUser);
            return response()->json(['value' => $rows]);
        } catch (\Throwable $e) {
            Log::error('LETTER_TABLE_ERROR: '.$e->getMessage(), ['ex' => $e]);
            return response()->json([
                'value' => [],
                'error' => true,
                'message' => 'Error al cargar la tabla',
            ], 500);
        }
    }

    public function dashboard()
    {
        $item = [];
        return view('letter.dashboard.dashboard', compact('item'));
    }

    public function cloud($id)
    {
        $object = new LetterM();
        $item = $object->edit($id);
        return view('letter.letter.cloud', compact('item'));
    }

    /* ======================== CREATE ======================== */
    public function create()
    {
        $item = new LetterM();

        $collectionUnidadM      = new CollectionUnidadM();
        $collectionStatusM      = new CollectionStatusM();
        $collectionDateM        = new CollectionDateM();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionRemitenteM   = new CollectionRemitenteM();
        $collectionEntidadM     = new CollectionEntidadM();

        // Defaults (no tocar la columna real fecha_captura: usar virtual para la vista)
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

        // Área 3 (vacío hasta elegir Área 2)
        $selectArea     = collect([]);
        $selectAreaEdit = null;

        // Área 1
        $miAreaId        = Auth::user()->id_cat_area ?? null;
        $selectArea1     = $miAreaId
            ? $item->getArea1OptionsByArea((int)$miAreaId)
            : $item->getArea1Options();
        $selectArea1Edit = null;

        // Área 2 (vacío hasta elegir Área 1)
        $selectArea2     = collect([]);
        $selectArea2Edit = null;

        // Otros selects
        $selectUser             = [];
        $selectUserEdit         = [];
        $selectEnlace           = [];
        $selectEnlaceEdit       = [];
        $selectUnidad           = [];
        $selectUnidadEdit       = null;
        $selectCoordinacion     = [];
        $selectCoordinacionEdit = null;
        $selectStatus           = $collectionStatusM->list();
        $selectStatusEdit       = $collectionStatusM->edit(1);
        $selectTramite          = [];
        $selectTramiteEdit      = null;
        $selectClave            = [];
        $selectClaveEdit        = null;
        $selectRemitente        = $collectionRemitenteM->list();
        $selectRemitenteEdit    = null;
        $selectEntidad          = $collectionEntidadM->list();
        $selectEntidadEdit      = null;

        $isEdit = false;

        return view('letter.letter.form', compact(
            'item',
            'isEdit',
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

    /* ======================== EDIT ======================== */
    public function edit(string $id)
    {
        $letterM                 = new LetterM();
        $collectionRelUsuarioM   = new CollectionRelUsuarioM();
        $collectionRelEnlaceM    = new CollectionRelEnlaceM();
        $collectionUnidadM       = new CollectionUnidadM();
        $collectionStatusM       = new CollectionStatusM();
        $collectionCoordinacionM = new CollectionCoordinacionM();
        $collectionTramiteM      = new CollectionTramiteM();
        $collectionRemitenteM    = new CollectionRemitenteM();
        $collectionClaveM        = new CollectionClaveM();
        $collectionEntidadM      = new CollectionEntidadM();

        $item = $letterM->edit($id);

        // Valor formateado para el input (d/m/Y) sin tocar la columna real
        if ($item) {
            $item->fecha_captura_dmy = !empty($item->fecha_captura)
                ? Carbon::parse($item->fecha_captura)->format('d/m/Y')
                : null;
        }

        // Estatus
        $selectStatus     = $collectionStatusM->listEdit();
        $selectStatusEdit = isset($item->id_cat_estatus) ? $collectionStatusM->edit($item->id_cat_estatus) : null;

        // Área 3 (todas, incl. inactivas)
        $selectArea = DB::table('correspondencia.cat_area')
            ->select('id_cat_area as id', DB::raw('UPPER(descripcion) as descripcion'))
            ->orderBy('descripcion')->get();

        $selectAreaEdit = isset($item->id_cat_area)
            ? DB::table('correspondencia.cat_area')
                ->select('id_cat_area as id', DB::raw('UPPER(descripcion) as descripcion'))
                ->where('id_cat_area', $item->id_cat_area)->first()
            : null;

        // Área 1
        $miAreaId        = Auth::user()->id_cat_area ?? null;
        $selectArea1     = $miAreaId ? $letterM->getArea1OptionsByArea((int)$miAreaId) : $letterM->getArea1Options();
        $selectArea1Edit = isset($item->id_cat_area_1) ? $letterM->getArea1EditObj($item->id_cat_area_1) : null;

        // Área 2
        $selectArea2     = $letterM->getArea2Options();
        $selectArea2Edit = isset($item->id_cat_area_2) ? $letterM->getArea2EditObj($item->id_cat_area_2) : null;

        // Usuarios / Enlace
        $selectUser     = isset($item->id_cat_area) ? $collectionRelUsuarioM->idUsuarioByAreaNewX($item->id_cat_area, $item->id_usuario_area) : [];
        $selectUserEdit = (isset($item->id_cat_area) && isset($item->id_usuario_area)) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_area) : [];

        $selectEnlace     = isset($item->id_cat_area) ? $collectionRelEnlaceM->idUsuarioByAreaNewX($item->id_cat_area, $item->id_usuario_enlace) : [];
        $selectEnlaceEdit = (isset($item->id_cat_area) && isset($item->id_usuario_enlace)) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_enlace) : [];

        // Unidad / Coordinación
        $selectUnidad           = $collectionUnidadM->listEdit();
        $selectUnidadEdit       = isset($item->id_cat_unidad) ? $collectionUnidadM->edit($item->id_cat_unidad) : null;
        $selectCoordinacion     = isset($item->id_cat_unidad) ? $collectionCoordinacionM->listEdit($item->id_cat_unidad) : [];
        $selectCoordinacionEdit = (isset($item->id_cat_unidad) && isset($item->id_cat_coordinacion)) ? $collectionCoordinacionM->edit($item->id_cat_coordinacion) : null;

        // Trámite / Clave
        $selectTramite     = isset($item->id_cat_area) ? $collectionTramiteM->listEdit($item->id_cat_area) : [];
        $selectTramiteEdit = (isset($item->id_cat_area) && isset($item->id_cat_tramite)) ? $collectionTramiteM->edit($item->id_cat_tramite) : null;

        $selectClave     = (isset($item->id_cat_area) && isset($item->id_cat_tramite)) ? $collectionClaveM->listEdit($item->id_cat_tramite) : [];
        $selectClaveEdit = (isset($item->id_cat_area) && isset($item->id_cat_tramite) && isset($item->id_cat_clave)) ? $collectionClaveM->edit($item->id_cat_clave) : null;

        // Remitente / Entidad
        $selectRemitente     = $collectionRemitenteM->list();
        $selectRemitenteEdit = isset($item->id_cat_remitente) ? $collectionRemitenteM->edit($item->id_cat_remitente) : null;

        $selectEntidad     = $collectionEntidadM->listEdit();
        $selectEntidadEdit = isset($item->id_cat_entidad) ? $collectionEntidadM->edit($item->id_cat_entidad) : null;

        $isEdit = true;

        return view('letter.letter.form', compact(
            'item',
            'isEdit',
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

    /* ======================== SAVE (CREATE/UPDATE) ======================== */
    public function save(Request $request)
    {
        $logC = new LogC();
        $collectionRemitenteM   = new CollectionRemitenteM();
        $letterM                = new LetterM();
        $messagesC              = new MessagesC();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionRolAreaM     = new CollectionRolAreaM();
        $collectionLetterLogM   = new CollectionLetterLogM();
        $now = Carbon::now();

        $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray();
        $ADM_TOTAL = config('custom_config.ADM_TOTAL');
        $COR_TOTAL = config('custom_config.COR_TOTAL');

        $rfc_remitente_bool = isset($request->rfc_remitente_bool) ? 1 : 0;
        $es_doc_fisico      = isset($request->es_doc_fisico) ? 1 : 0;
        $son_mas_remitentes = isset($request->son_mas_remitentes) ? 1 : 0;

        // Alta rápida de remitente por RFC
        if ($rfc_remitente_bool) {
            $collectionRemitenteM::create([
                'nombre'             => strtoupper($request->remitente_nombre),
                'primer_apellido'    => strtoupper($request->remitente_apellido_paterno),
                'segundo_apellido'   => strtoupper($request->remitente_apellido_materno),
                'rfc'                => strtoupper($request->remitente_rfc),
                'estatus'            => true,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario'      => $now,
            ]);
            $request->id_cat_remitente = $collectionRemitenteM->getRfc(
                strtoupper($request->remitente_nombre),
                strtoupper($request->remitente_apellido_paterno),
                strtoupper($request->remitente_apellido_materno)
            );
        }

        /* ---------- CREATE ---------- */
        if (!isset($request->id_tbl_correspondencia)) {

            // Oficio obligatorio al crear
            if (
                !$request->hasFile('file_oficio_entrada') ||
                !$request->file('file_oficio_entrada')->isValid()
            ) {
                return redirect()->back()->withInput()->with([
                    'value'   => 'error',
                    'message' => 'Hace falta cargar un oficio (PDF/DOC/IMG).',
                    'estatus' => 'true'
                ]);
            }

            // Validar extensión
            $allowed = ['pdf','doc','docx','jpg','jpeg','png'];
            $ext = strtolower($request->file('file_oficio_entrada')->getClientOriginalExtension());
            if (!in_array($ext, $allowed)) {
                return redirect()->back()->withInput()->with([
                    'value'   => 'error',
                    'message' => 'El oficio debe ser PDF, DOC, DOCX, JPG o PNG.',
                    'estatus' => 'true'
                ]);
            }

            // Num. Turno (regla de máximo/iterator)
            $numTurnoSistemaAux = $request->num_turno_sistema;
            if ($this->getMaxTurno($request->num_turno_sistema) <= $letterM->getMaxNuSistem()) {
                $numTurnoSistemaAux = $this->procesarParametros(
                    $request->num_turno_sistema,
                    $collectionConsecutivoM->noDocumento($request->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'))
                );
            }

            // Normalización de fechas desde el formulario (acepta d/m/Y o Y-m-d)
            $fechaCaptura   = $this->parseDateInput($request->input('fecha_captura'));
            $fechaInicio    = $this->parseDateInput($request->input('fecha_inicio'));
            $fechaFin       = $this->parseDateInput($request->input('fecha_fin'));
            $fechaDocumento = $this->parseDateInput($request->input('fecha_documento'));

            $data = [
                'num_turno_sistema'    => strtoupper($numTurnoSistemaAux),
                'num_documento'        => strtoupper($request->num_documento),
                'fecha_captura'        => $fechaCaptura,
                'fecha_inicio'         => $fechaInicio,
                'fecha_fin'            => $fechaFin,
                'num_flojas'           => 1,
                'num_tomos'            => 0,
                'horas_respuesta'      => $request->horas_respuesta,
                'id_cat_entidad'       => $request->id_cat_entidad,
                'asunto'               => strtoupper($request->asunto),
                'observaciones'        => strtoupper($request->observaciones),
                'id_cat_area'          => $request->id_cat_area,
                'id_usuario_area'      => $request->id_usuario_area,
                'id_usuario_enlace'    => $request->id_usuario_enlace,
                'id_cat_estatus'       => $request->id_cat_estatus,
                'id_cat_remitente'     => $request->id_cat_remitente,
                'id_cat_anio'          => $request->id_cat_anio,
                'id_cat_tramite'       => $request->id_cat_tramite,
                'id_cat_clave'         => $request->id_cat_clave,
                'id_cat_unidad'        => $request->id_cat_unidad,
                'id_cat_coordinacion'  => $request->id_cat_coordinacion,
                'puesto_remitente'     => strtoupper($request->puesto_remitente),
                'folio_gestion'        => strtoupper($request->folio_gestion),
                'es_doc_fisico'        => $es_doc_fisico,
                'son_mas_remitentes'   => $son_mas_remitentes,
                'remitente'            => strtoupper($request->remitente),
                'fecha_documento'      => $fechaDocumento,
                'id_usuario_sistema'   => Auth::user()->id,
                'fecha_usuario'        => $now,
                'id_usuario_captura'   => Auth::user()->id,
                'fecha_usuario_captura'=> $now,
            ];

            $created = LetterM::create($data);

            $logC->add('correspondencia.tbl_correspondencia', $data);
            $collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'));

            $collectionLetterLogM::create([
                'estatus'                => 'AGREGAR',
                'num_documento'          => strtoupper($request->num_documento),
                'folio_gestion'          => strtoupper($request->folio_gestion),
                'asunto'                 => strtoupper($request->asunto),
                'observaciones'          => strtoupper($request->observaciones),
                'id_cat_area'            => $request->id_cat_area,
                'id_cat_estatus'         => $request->id_cat_estatus,
                'id_tbl_correspondencia' => $created->id_tbl_correspondencia,
                'fecha_usuario_captura'  => $now,
                'id_usuario_captura'     => Auth::user()->id,
            ]);

            // Subir a Alfresco si vienen archivos
            $this->uploadFilesIfAny($request, $created->id_tbl_correspondencia);

            return $messagesC->messageSuccessRedirect('letter.list', 'Elemento agregado con éxito.');
        }

        /* ---------- UPDATE (total) ---------- */
        if (in_array($ADM_TOTAL, $roleUserArray) || in_array($COR_TOTAL, $roleUserArray)) {

            // Normalización de fechas
            $fechaCaptura   = $this->parseDateInput($request->input('fecha_captura'));
            $fechaInicio    = $this->parseDateInput($request->input('fecha_inicio'));
            $fechaFin       = $this->parseDateInput($request->input('fecha_fin'));
            $fechaDocumento = $this->parseDateInput($request->input('fecha_documento'));

            $data = [
                'num_turno_sistema'    => strtoupper($request->num_turno_sistema),
                'num_documento'        => strtoupper($request->num_documento),
                'fecha_captura'        => $fechaCaptura,
                'fecha_inicio'         => $fechaInicio,
                'fecha_fin'            => $fechaFin,
                'num_flojas'           => 1,
                'num_tomos'            => 0,
                'horas_respuesta'      => $request->horas_respuesta,
                'id_cat_entidad'       => $request->id_cat_entidad,
                'asunto'               => strtoupper($request->asunto),
                'observaciones'        => strtoupper($request->observaciones),
                'id_cat_area'          => $request->id_cat_area,
                'id_cat_area_1'        => $request->id_cat_area_1,
                'id_cat_area_2'        => $request->id_cat_area_2,
                'id_usuario_area'      => $request->id_usuario_area,
                'id_usuario_enlace'    => $request->id_usuario_enlace,
                'id_cat_estatus'       => $request->id_cat_estatus,
                'id_cat_remitente'     => $request->id_cat_remitente,
                'id_cat_anio'          => $request->id_cat_anio,
                'id_cat_tramite'       => $request->id_cat_tramite,
                'id_cat_clave'         => $request->id_cat_clave,
                'id_cat_unidad'        => $request->id_cat_unidad,
                'id_cat_coordinacion'  => $request->id_cat_coordinacion,
                'puesto_remitente'     => strtoupper($request->puesto_remitente),
                'folio_gestion'        => strtoupper($request->folio_gestion),
                'es_doc_fisico'        => $es_doc_fisico,
                'son_mas_remitentes'   => $son_mas_remitentes,
                'remitente'            => strtoupper($request->remitente),
                'fecha_documento'      => $fechaDocumento,
                'id_usuario_sistema'   => Auth::user()->id,
                'fecha_usuario'        => $now,
            ];

            LetterM::where('id_tbl_correspondencia', $request->id_tbl_correspondencia)->update($data);

            // Guardado de archivos locales (oficio/anexos) si se cargan en edición
            $this->handleUploads((int)$request->id_tbl_correspondencia, $request);

            // Subidas a Alfresco en edición (si vienen campos *_entrada)
            $this->uploadFilesIfAny($request, (int)$request->id_tbl_correspondencia);

            $data['id_tbl_correspondencia'] = $request->id_tbl_correspondencia;
            $logC->edit('correspondencia.tbl_correspondencia', $data);

            $collectionLetterLogM::create([
                'estatus'                => 'MODIFICAR',
                'num_documento'          => strtoupper($request->num_documento),
                'folio_gestion'          => strtoupper($request->folio_gestion),
                'asunto'                 => strtoupper($request->asunto),
                'observaciones'          => strtoupper($request->observaciones),
                'id_cat_area'            => $request->id_cat_area,
                'id_cat_estatus'         => $request->id_cat_estatus,
                'id_tbl_correspondencia' => $request->id_tbl_correspondencia,
                'fecha_usuario_captura'  => $now,
                'id_usuario_captura'     => Auth::user()->id,
            ]);

            return $messagesC->messageSuccessRedirect('letter.list', 'Elemento modificado con éxito.');
        }

        /* ---------- UPDATE restringido (estatus/observaciones) ---------- */
        $collectionRolAreaM = new CollectionRolAreaM();
        if (!in_array($request->id_cat_area, $collectionRolAreaM->getListArea())) {
            return redirect()->back()->with([
                'value'   => 'error',
                'message' => 'No se han configurado permisos para este usuario.',
                'estatus' => 'true'
            ]);
        }

        $data = [
            'observaciones'      => strtoupper($request->observaciones),
            'id_cat_estatus'     => $request->id_cat_estatus,
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario'      => $now,
        ];

        LetterM::where('id_tbl_correspondencia', $request->id_tbl_correspondencia)->update($data);

        $data['id_tbl_correspondencia'] = $request->id_tbl_correspondencia;
        $logC->edit('correspondencia.tbl_correspondencia', $data);

        $collectionLetterLogM::create([
            'estatus'                => 'MODIFICAR',
            'num_documento'          => strtoupper($request->num_documento),
            'folio_gestion'          => strtoupper($request->folio_gestion),
            'asunto'                 => strtoupper($request->asunto),
            'observaciones'          => strtoupper($request->observaciones),
            'id_cat_area'            => $request->id_cat_area,
            'id_cat_estatus'         => $request->id_cat_estatus,
            'id_tbl_correspondencia' => $request->id_tbl_correspondencia,
            'fecha_usuario_captura'  => $now,
            'id_usuario_captura'     => Auth::user()->id,
        ]);

        return $messagesC->messageSuccessRedirect('letter.list', 'Elemento modificado con éxito.');
    }

    /* ======================== ÁREAS DEPENDIENTES (AJAX) ======================== */
    public function collectionArea(Request $request)
    {
        try {
            $by    = $request->input('by');    // compat anterior
            $scope = $request->input('scope'); // nuevo para deps-areas.js

            // ===== flujo anterior =====
            if ($by === 'area2_by_area1') {
                $area1Id = (int) $request->input('id_cat_area_1');
                $rows = (new LetterM())->getArea2OptionsByArea1($area1Id)
                    ->map(fn($r) => ['id' => $r->id, 'label' => $r->descripcion])
                    ->values();
                return response()->json(['ok' => true, 'value' => $rows]);
            }

            if ($by === 'area3_by_area2') {
                $area2Id         = (int) $request->input('id_cat_area_2');
                $includeInactive = filter_var($request->input('include_inactive', false), FILTER_VALIDATE_BOOLEAN);

                $q = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
                    ->join('correspondencia.rel_cat_area_jerarquia_1 as r1', 'r2.id_cat_area_1', '=', 'r1.id_cat_area_2')
                    ->join('correspondencia.cat_area as ca', 'r2.id_cat_area_2', '=', 'ca.id_cat_area')
                    ->where('r1.id_cat_area_2', $area2Id);

                if (!$includeInactive) {
                    $q->where('ca.estatus', true);
                }

                $rows = $q->select('ca.id_cat_area as id', DB::raw('UPPER(ca.descripcion) AS label'))
                          ->distinct()
                          ->orderBy('label')
                          ->get();

                return response()->json(['ok' => true, 'value' => $rows]);
            }

            // ===== nuevo flujo por scope =====
            $buildDependents = function (int $areaId) {
                $collectionRelUsuarioM   = new CollectionRelUsuarioM();
                $collectionRelEnlaceM    = new CollectionRelEnlaceM();
                $collectionUnidadM       = new CollectionUnidadM();
                $collectionTramiteM      = new CollectionTramiteM();

                $selectUsuario = $collectionRelUsuarioM->idUsuarioByAreaNewX($areaId, null) ?? [];
                $selectEnlace  = $collectionRelEnlaceM->idUsuarioByAreaNewX($areaId, null) ?? [];
                $selectUnidad  = $collectionUnidadM->listEdit() ?? [];
                $selectCoor    = []; // se llena con /collectionUnidad al elegir unidad
                $selectTramite = $collectionTramiteM->listEdit($areaId) ?? [];

                return [
                    'ok'            => true,
                    'selectUsuario' => $selectUsuario,
                    'selectEnlace'  => $selectEnlace,
                    'selectUnidad'  => $selectUnidad,
                    'selectCoor'    => $selectCoor,
                    'selectTramite' => $selectTramite,
                    'clave'         => '-', // reset hasta elegir clave
                ];
            };

            if ($scope === 'dependents_by_area3') {
                $areaId = (int) $request->input('id_cat_area');
                if ($areaId <= 0) return response()->json(['ok' => false, 'message' => 'id_cat_area requerido'], 422);
                return response()->json($buildDependents($areaId));
            }

            if ($scope === 'dependents_by_area2') {
                $area2Id = (int) $request->input('id_cat_area_2');
                if ($area2Id <= 0) return response()->json(['ok' => false, 'message' => 'id_cat_area_2 requerido'], 422);

                $a3 = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
                    ->join('correspondencia.rel_cat_area_jerarquia_1 as r1', 'r2.id_cat_area_1', '=', 'r1.id_cat_area_2')
                    ->where('r1.id_cat_area_2', $area2Id)
                    ->pluck('r2.id_cat_area_2')
                    ->unique()
                    ->values();

                if ($a3->count() === 1) {
                    return response()->json($buildDependents((int)$a3[0]));
                }

                return response()->json([
                    'ok' => true,
                    'selectUsuario' => [],
                    'selectEnlace'  => [],
                    'selectUnidad'  => (new CollectionUnidadM())->listEdit() ?? [],
                    'selectCoor'    => [],
                    'selectTramite' => [],
                    'clave'         => '-',
                ]);
            }

            if ($scope === 'dependents_by_area1') {
                $area1Id = (int) $request->input('id_cat_area_1');
                if ($area1Id <= 0) return response()->json(['ok' => false, 'message' => 'id_cat_area_1 requerido'], 422);

                $a2 = DB::table('correspondencia.rel_cat_area_jerarquia_1')
                    ->where('id_cat_area_1', $area1Id)
                    ->pluck('id_cat_area_2')
                    ->unique()
                    ->values();

                if ($a2->isEmpty()) {
                    return response()->json([
                        'ok' => true,
                        'selectUsuario' => [],
                        'selectEnlace'  => [],
                        'selectUnidad'  => (new CollectionUnidadM())->listEdit() ?? [],
                        'selectCoor'    => [],
                        'selectTramite' => [],
                        'clave'         => '-',
                    ]);
                }

                $a3 = DB::table('correspondencia.rel_cat_area_jerarquia_2 as r2')
                    ->join('correspondencia.rel_cat_area_jerarquia_1 as r1', 'r2.id_cat_area_1', '=', 'r1.id_cat_area_2')
                    ->whereIn('r1.id_cat_area_2', $a2)
                    ->pluck('r2.id_cat_area_2')
                    ->unique()
                    ->values();

                if ($a3->count() === 1) {
                    return response()->json($buildDependents((int)$a3[0]));
                }

                return response()->json([
                    'ok' => true,
                    'selectUsuario' => [],
                    'selectEnlace'  => [],
                    'selectUnidad'  => (new CollectionUnidadM())->listEdit() ?? [],
                    'selectCoor'    => [],
                    'selectTramite' => [],
                    'clave'         => '-',
                ]);
            }

            return response()->json(['ok' => false, 'message' => 'Parámetro inválido'], 422);
        } catch (\Throwable $e) {
            Log::error('LETTER_COLLECTION_AREA_ERROR: '.$e->getMessage(), ['ex' => $e]);
            return response()->json(['ok' => false, 'message' => 'Error interno'], 500);
        }
    }

    /* ======================== COPIAS ======================== */
    public function validateCopy(Request $request)
    {
        $letterM = new LetterM();
        $result = $letterM->getValue($request->id_tbl_correspondencia, $request->id_cat_area);
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
        $bool = $result>0;

        return response()->json(['value'=>$bool]);
    }

    /* ======================== REPLY (CREAR OFICIO SIN ARCHIVOS) ======================== */
   /* ======================== REPLY (CREAR OFICIO SIN ARCHIVOS) ======================== */
/* ======================== REPLY (CREAR OFICIO + subida opcional a Alfresco) ======================== */
public function replySave(Request $request)
{
    try {
        $request->validate([
            'id_tbl_correspondencia' => 'required|integer',
            'fecha_inicio'           => 'required|string',
            'fecha_fin'              => 'nullable|string',
            'asunto'                 => 'required|string|max:250',
            'observaciones'          => 'nullable|string|max:500',
            // archivos OPCIONALES en el reply
            'file_oficio_entrada'    => 'nullable|file|max:20480',
            'file_anexo_entrada.*'   => 'nullable|file|max:20480',
        ]);

        $idCorr = (int) $request->input('id_tbl_correspondencia');

        // Traer datos base desde correspondencia
        $corr = DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'id_tbl_correspondencia',
                'id_cat_anio',
                'id_cat_area',
                'id_usuario_area',
                'id_usuario_enlace',
                'observaciones',
                'folio_gestion'
            )
            ->where('id_tbl_correspondencia', $idCorr)
            ->first();

        if (!$corr) {
            return response()->json(['ok' => false, 'message' => 'Correspondencia no encontrada.'], 404);
        }

        // Normalización de fechas del modal
        $fechaInicio = $this->parseDateInput($request->input('fecha_inicio'));
        $fechaFin    = $this->parseDateInput($request->input('fecha_fin'));
        if (!$fechaInicio) {
            return response()->json(['ok' => false, 'message' => 'Fecha inicio inválida.'], 422);
        }

        // Consecutivo para num_turno_sistema del OFICIO (por año de la correspondencia)
        $consec = new CollectionConsecutivoM();
        $numTurnoOficio = $consec->noDocumento($corr->id_cat_anio, config('custom_config.CP_TABLE_OFICIO'));

        DB::beginTransaction();

        // ========= 1) Crear registro en tbl_oficio =========
        $oficioObs  = strtoupper((string)$request->input('observaciones', ''));
        $oficioData = [
            'num_turno_sistema'      => strtoupper($numTurnoOficio),
            'fecha_captura'          => now()->format('Y-m-d'),
            'fecha_inicio'           => $fechaInicio,
            'fecha_fin'              => $fechaFin,
            'asunto'                 => strtoupper((string)$request->input('asunto')),
            'observaciones'          => $oficioObs,
            'id_tbl_correspondencia' => $corr->id_tbl_correspondencia,
            'id_cat_anio'            => $corr->id_cat_anio,

            // Se liga a la correspondencia (no es por área)
            'es_por_area'            => 0,
            'num_documento_area'     => null,
            'id_cat_area_documento'  => null,
            'id_usuario_area'        => $corr->id_usuario_area,
            'id_usuario_enlace'      => $corr->id_usuario_enlace,
            'id_cat_area'            => $corr->id_cat_area,

            // Auditoría
            'id_usuario_sistema'     => Auth::user()->id,
            'id_usuario_captura'     => Auth::user()->id,
            'fecha_usuario'          => now(),
        ];

        $created = OfficeM::create($oficioData);

        // Iterar consecutivo del oficio
        $consec->iteratorConsecutivo($corr->id_cat_anio, config('custom_config.CP_TABLE_OFICIO'));

        // ========= 2) Actualizar tbl_correspondencia =========
        $newObs = $corr->observaciones ?? '';
        if ($oficioObs !== '') {
            $newObs = trim($newObs) === '' ? $oficioObs : ($newObs . '  //  ' . $oficioObs);
        }

        DB::table('correspondencia.tbl_correspondencia')
            ->where('id_tbl_correspondencia', $idCorr)
            ->update([
                'id_cat_estatus'     => 4,
                'observaciones'      => $newObs,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario'      => now(),
            ]);

        // ========= Logs =========
        $logC = new LogC();
        $logC->add('correspondencia.tbl_oficio', $oficioData);
        $logC->edit('correspondencia.tbl_correspondencia', [
            'id_tbl_correspondencia' => $idCorr,
            'folio_gestion'          => $corr->folio_gestion,
            'id_cat_estatus'         => 4,
            'observaciones'          => $newObs,
        ]);

        DB::commit();

        /* ========== 3) Subida a Alfresco (OPCIONAL) + INSERTS EN 4 TABLAS ========== */
        try {
            $hasOficio = $request->hasFile('file_oficio_entrada') && $request->file('file_oficio_entrada')->isValid();

            // Manejo robusto de anexos (name="file_anexo_entrada[]")
            $anexoInput = $request->file('file_anexo_entrada');
            $hasAnexos  = is_array($anexoInput) && count(array_filter($anexoInput)) > 0;

            if ($hasOficio || $hasAnexos) {
                $alfrescoC    = new \App\Http\Controllers\Cloud\AlfrescoC();
                $cloudConfigM = new \App\Models\Letter\Cloud\CloudConfigM();

                // Intento “completo” (area, entrada, tipo)
                $uidRow = $cloudConfigM->getUid(
                    $corr->id_cat_area,
                    $request->input('id_cat_entrada'),
                    $request->input('id_cat_tipo_oficio')
                );

                // Fallback: por área (primer registro activo con uid)
                if (!$uidRow) {
                    $uidRow = DB::table('correspondencia.cat_config_cloud')
                        ->where('id_cat_area', $corr->id_cat_area)
                        ->where('estatus', true)
                        ->whereNotNull('uid')
                        ->orderBy('id_cat_config_cloud')
                        ->first();
                }

                $folderId = $uidRow && !empty($uidRow->uid) ? $this->normalizeFolderId($uidRow->uid) : null;

                // <<< AQUÍ LO FIJAMOS EN 1 COMO PEDISTE >>>
                $tipoDocCloudBase = 1;

                \Log::info('[REPLY_UPLOAD] folderId/tipoDocCloud', [
                    'area'         => $corr->id_cat_area,
                    'uid'          => $uidRow->uid ?? null,
                    'folderId'     => $folderId,
                    'tipoDocCloud' => $tipoDocCloudBase
                ]);

                if ($folderId) {
                    // ===== OFICIO (1 archivo) =====
                    if ($hasOficio) {
                        $file = $request->file('file_oficio_entrada');

                        \Log::info('[REPLY_UPLOAD] oficio file', [
                            'name' => $file->getClientOriginalName(),
                            'size' => $file->getSize(),
                            'mime' => $file->getMimeType()
                        ]);

                        $uploadedUid = $alfrescoC->addFile($file, $folderId, 1); // 1 => prefijo OFICIO_
                        \Log::info('[REPLY_UPLOAD] oficio uploaded UID', ['uid' => $uploadedUid]);

                        if ($uploadedUid) {
                            $nombre = 'OFICIO_' . $file->getClientOriginalName();

                            // a) ctrl_correspondencia_oficio (modelo existente)
                            \App\Models\Letter\Letter\CloudOficiosM::create([
                                'uid'                   => $uploadedUid,
                                'nombre'                => $nombre,
                                'estatus'               => true,
                                'fecha_usuario'         => now(),
                                'id_tbl_correspondencia'=> $idCorr,
                                'id_usuario_sistema'    => Auth::user()->id,
                                'id_cat_tipo_doc_cloud' => $tipoDocCloudBase, // 1
                            ]);

                            // b) ctrl_oficio_oficio (con FK id_tbl_oficio)
                            try {
                                DB::table('correspondencia.ctrl_oficio_oficio')->insert([
                                    'uid'                  => $uploadedUid,
                                    'nombre'               => $nombre,
                                    'estatus'              => true,
                                    'fecha_usuario'        => now(),
                                    'id_tbl_oficio'        => $created->id_tbl_oficio,
                                    'id_usuario_sistema'   => Auth::user()->id,
                                    'id_cat_tipo_doc_cloud'=> $tipoDocCloudBase, // 1
                                ]);
                                \Log::info('[REPLY_UPLOAD] ctrl_oficio_oficio insert OK', [
                                    'uid'    => $uploadedUid,
                                    'oficio' => $created->id_tbl_oficio,
                                    'tipo'   => $tipoDocCloudBase
                                ]);
                            } catch (\Throwable $e) {
                                \Log::error('[REPLY_UPLOAD] ctrl_oficio_oficio insert ERROR: '.$e->getMessage(), [
                                    'uid' => $uploadedUid,
                                    'oficio' => $created->id_tbl_oficio
                                ]);
                            }
                        }
                    }

                    // ===== ANEXOS (0..n) =====
                    if ($hasAnexos) {
                        foreach ($anexoInput as $idx => $file) {
                            if (!$file instanceof \Illuminate\Http\UploadedFile || !$file->isValid()) {
                                \Log::warning('[REPLY_UPLOAD] anexo inválido', ['idx'=>$idx]);
                                continue;
                            }

                            \Log::info('[REPLY_UPLOAD] anexo file', [
                                'idx'  => $idx,
                                'name' => $file->getClientOriginalName(),
                                'size' => $file->getSize(),
                                'mime' => $file->getMimeType()
                            ]);

                            $uploadedUid = $alfrescoC->addFile($file, $folderId, 0); // 0 => prefijo ANEXO_
                            \Log::info('[REPLY_UPLOAD] anexo uploaded UID', ['uid' => $uploadedUid]);

                            if ($uploadedUid) {
                                $nombre = 'ANEXO_' . $file->getClientOriginalName();

                                // a) ctrl_correspondencia_anexo (modelo existente)
                                \App\Models\Letter\Letter\CloudAnexosM::create([
                                    'uid'                   => $uploadedUid,
                                    'nombre'                => $nombre,
                                    'estatus'               => true,
                                    'fecha_usuario'         => now(),
                                    'id_tbl_correspondencia'=> $idCorr,
                                    'id_usuario_sistema'    => Auth::user()->id,
                                    'id_cat_tipo_doc_cloud' => $tipoDocCloudBase, // 1
                                ]);

                                // b) ctrl_oficio_anexo (con FK id_tbl_oficio)
                                try {
                                    DB::table('correspondencia.ctrl_oficio_anexo')->insert([
                                        'uid'                  => $uploadedUid,
                                        'nombre'               => $nombre,
                                        'estatus'              => true,
                                        'fecha_usuario'        => now(),
                                        'id_tbl_oficio'        => $created->id_tbl_oficio,
                                        'id_usuario_sistema'   => Auth::user()->id,
                                        'id_cat_tipo_doc_cloud'=> $tipoDocCloudBase, // 1
                                    ]);
                                    \Log::info('[REPLY_UPLOAD] ctrl_oficio_anexo insert OK', [
                                        'uid'    => $uploadedUid,
                                        'oficio' => $created->id_tbl_oficio,
                                        'tipo'   => $tipoDocCloudBase
                                    ]);
                                } catch (\Throwable $e) {
                                    \Log::error('[REPLY_UPLOAD] ctrl_oficio_anexo insert ERROR: '.$e->getMessage(), [
                                        'uid' => $uploadedUid,
                                        'oficio' => $created->id_tbl_oficio
                                    ]);
                                }
                            }
                        }
                    }
                } else {
                    \Log::error('[REPLY_UPLOAD] No se encontró carpeta Alfresco para el área', [
                        'area' => $corr->id_cat_area
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Log::error('[REPLY_UPLOAD] error: ' . $e->getMessage(), ['ex' => $e]);
            // No interrumpimos la respuesta si la subida falla
        }

        return response()->json([
            'ok'        => true,
            'message'   => 'Oficio creado; estatus actualizado, observaciones concatenadas y archivos subidos (si hubo).',
            'id_oficio' => $created->id_tbl_oficio,
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('LETTER_REPLY_SAVE_ERROR: '.$e->getMessage(), ['ex' => $e]);
        return response()->json(['ok' => false, 'message' => 'Error al guardar la respuesta.'], 500);
    }
}





    /* ======================== CRUD AUX ======================== */
    public function delete($id)
    {
        $messagesC = new MessagesC();
        LetterM::destroy($id);
        return $messagesC->messageSuccessRedirect('letter.list','Elemento eliminado con éxito.');
    }

    public function validateUnique(Request $request)
    {
        try {
            $type      = (string) $request->input('type', '');
            $id        = $request->input('id'); // id_tbl_correspondencia (opcional para excluir)
            $value     = trim((string)$request->input('value', ''));
            $attribute = (string) $request->input('attribute', ''); // opcional

            if ($value === '') {
                return response()->json(['ok' => true, 'exists' => false]);
            }

            $letterM = new LetterM();
            $exists  = false;

            switch ($type) {
                case 'folio':
                    $exists = (bool) $letterM->uniqueNoDocument($id, $value, 'folio_gestion');
                    break;
                case 'num_documento':
                default:
                    $col    = $attribute ?: 'num_documento';
                    $exists = (bool) $letterM->uniqueNoDocument($id, $value, $col);
                    break;
            }

            return response()->json(['ok' => true, 'exists' => $exists]);
        } catch (\Throwable $e) {
            Log::error('LETTER_VALIDATE_UNIQUE_ERROR: '.$e->getMessage(), ['ex' => $e]);
            return response()->json(['ok' => false, 'message' => 'Error interno'], 500);
        }
    }

    /* ======================== HELPERS ======================== */

    // Acepta UUID puro, nodeRef "workspace://SpacesStore/<uuid>" o URL de Share con ?nodeRef=...
    private function normalizeFolderId(?string $value): ?string
    {
        if (!$value) return null;

        // 1) UUID
        if (preg_match('/^[0-9a-fA-F-]{36}$/', $value)) return $value;

        // 2) nodeRef
        if (preg_match('#workspace://SpacesStore/([0-9a-fA-F-]{36})#', $value, $m)) return $m[1];

        // 3) Share URL con nodeRef
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $parts = parse_url($value);
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $qs);
                if (!empty($qs['nodeRef']) && preg_match('#workspace://SpacesStore/([0-9a-fA-F-]{36})#', $qs['nodeRef'], $m2)) {
                    return $m2[1];
                }
            }
        }

        // 4) Primer UUID que aparezca
        if (preg_match('/([0-9a-fA-F-]{36})/', $value, $m3)) return $m3[1];

        return null;
    }

    private function getMaxTurno($numTurno)
    {
        if (preg_match('/\/([0-9]{5})\//', $numTurno, $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    private function procesarParametros($param1, $param2)
    {
        preg_match('/^([A-Za-z]+)/', $param1, $coincidencias1);
        $letras1 = $coincidencias1[1] ?? '';
        preg_match('/\/(\d+)\//', $param2, $coincidencias2);
        $numeros2 = $coincidencias2[1] ?? '00000';
        return $letras1 . '/' . $numeros2 . '/2025';
    }

    /**
     * Normaliza un valor de fecha recibido desde el formulario.
     * - Acepta: "d/m/Y", "d/m/Y H:i", "Y-m-d", "Y-m-d H:i:s"
     * - Devuelve: "Y-m-d" o "Y-m-d H:i:s" según traiga hora; null si vacío.
     */
    private function parseDateInput(?string $value): ?string
    {
        $v = trim((string)$value);
        if ($v === '') return null;

        // d/m/Y H:i
        try {
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2}$/', $v)) {
                return Carbon::createFromFormat('d/m/Y H:i', $v)->format('Y-m-d H:i:s');
            }
        } catch (\Throwable $e) {}

        // d/m/Y
        try {
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $v)) {
                return Carbon::createFromFormat('d/m/Y', $v)->format('Y-m-d');
            }
        } catch (\Throwable $e) {}

        // Y-m-d H:i:s
        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}$/', $v)) {
                return Carbon::parse($v)->format('Y-m-d H:i:s');
            }
        } catch (\Throwable $e) {}

        // Y-m-d
        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
                return Carbon::parse($v)->format('Y-m-d');
            }
        } catch (\Throwable $e) {}

        // Intento final (si llega un DateTime serializado por el browser, etc.)
        try {
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable $e) {
            Log::warning('[parseDateInput] No se pudo parsear la fecha', ['value' => $value]);
            return null;
        }
    }

    /* ===== Subidas a Alfresco si vienen campos file_* de entrada ===== */
    private function uploadFilesIfAny(Request $request, int $idCorrespondencia): void
    {
        try {
            Log::info('[UPLOAD] init', [
                'correspondencia' => $idCorrespondencia,
                'has_oficio'      => $request->hasFile('file_oficio_entrada'),
                'has_anexos'      => $request->hasFile('file_anexo_entrada'),
                'area'            => $request->id_cat_area,
                'entrada_salida'  => $request->id_cat_entrada,
                'tipo_oficio'     => $request->id_cat_tipo_oficio,
            ]);

            $hasOficio = $request->hasFile('file_oficio_entrada') && $request->file('file_oficio_entrada')->isValid();
            $hasAnexos = $request->hasFile('file_anexo_entrada') && is_array($request->file('file_anexo_entrada'));

            if (!$hasOficio && !$hasAnexos) { return; }

            $alfrescoC    = new AlfrescoC();
            $cloudConfigM = new CloudConfigM();

            // Puede venir como UUID, nodeRef o Share URL
            $rawFolder = optional($cloudConfigM->getUid($request->id_cat_area, $request->id_cat_entrada, $request->id_cat_tipo_oficio))->uid ?? null;
            $folderId  = $this->normalizeFolderId($rawFolder);

            Log::info('[UPLOAD] folderId normalize', ['raw' => $rawFolder, 'folderId' => $folderId]);
            if (!$folderId) { Log::error('[UPLOAD] carpeta inválida'); return; }

            // Oficio
            if ($hasOficio) {
                $file = $request->file('file_oficio_entrada');
                Log::info('[UPLOAD] oficio', ['name' => $file->getClientOriginalName(), 'size' => $file->getSize(), 'mime' => $file->getMimeType()]);
                $uploadedUid = $alfrescoC->addFile($file, $folderId, 1); // 1 => OFICIO_
                Log::info('[UPLOAD] oficio uid', ['uid' => $uploadedUid]);

                if ($uploadedUid) {
                    $now = Carbon::now();
                    $filename = 'OFICIO_' . $file->getClientOriginalName();
                    CloudOficiosM::create([
                        'uid'                   => $uploadedUid,
                        'nombre'                => $filename,
                        'estatus'               => true,
                        'fecha_usuario'         => $now,
                        'id_tbl_correspondencia'=> $idCorrespondencia,
                        'id_usuario_sistema'    => Auth::user()->id,
                        'id_cat_tipo_doc_cloud' => $request->id_cat_entrada,
                    ]);
                }
            }

            // Anexos
            if ($hasAnexos) {
                $now = Carbon::now();
                foreach ($request->file('file_anexo_entrada') as $file) {
                    if (!$file || !$file->isValid()) { continue; }

                    Log::info('[UPLOAD] anexo', ['name' => $file->getClientOriginalName(), 'size' => $file->getSize(), 'mime' => $file->getMimeType()]);
                    $uploadedUid = $alfrescoC->addFile($file, $folderId, 0); // 0 => ANEXO_
                    Log::info('[UPLOAD] anexo uid', ['uid' => $uploadedUid]);

                    if ($uploadedUid) {
                        $filename = 'ANEXO_' . $file->getClientOriginalName();
                        CloudAnexosM::create([
                            'uid'                   => $uploadedUid,
                            'nombre'                => $filename,
                            'estatus'               => true,
                            'fecha_usuario'         => $now,
                            'id_tbl_correspondencia'=> $idCorrespondencia,
                            'id_usuario_sistema'    => Auth::user()->id,
                            'id_cat_tipo_doc_cloud' => $request->id_cat_entrada,
                        ]);
                    }
                }
            }

        } catch (\Throwable $e) {
            Log::error('Error subiendo archivos post-guardar: '.$e->getMessage(), ['ex' => $e]);
        }
    }

    /* ===== Guardado local en edición (opcional a Alfresco) ===== */
    private function handleUploads(int $idCorrespondencia, Request $request): void
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
            Log::error('LETTER_UPLOAD_ERROR: '.$e->getMessage(), ['ex' => $e]);
        }
    }

    private function storeFile($file, string $subdir): array
    {
        $disk = 'public';
        $basePath = 'correspondencia/' . trim($subdir, '/');
        $original = $file->getClientOriginalName();
        $ext = $file->getClientOriginalExtension();
        $uuid = (string) Str::uuid();
        $filename = $uuid . '.' . $ext;

        $path = $file->storeAs($basePath, $filename, $disk);

        return [
            'uuid' => $uuid,
            'path' => $path,
            'name' => $original,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'disk' => $disk,
        ];
    }

    private function insertOficio(int $idCorrespondencia, array $meta): void
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

    private function insertAnexo(int $idCorrespondencia, array $meta): void
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
}

