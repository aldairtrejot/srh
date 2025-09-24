<?php

namespace App\Http\Controllers\Letter\Letter;

use App\Models\Letter\Collection\CollectionLetterLogM;
use App\Models\Letter\Collection\CollectionRolAreaM;
use App\Http\Controllers\Letter\Log\LogC;
use App\Models\Letter\Collection\CollectionClaveM;
use App\Models\Letter\Collection\CollectionEntidadM;
use App\Models\Letter\Collection\CollectionLetterCopyM;
use App\Models\Letter\Collection\CollectionTramiteM;
use App\Models\Letter\Collection\CollectionCoordinacionM;
use App\Models\Letter\Collection\CollectionConsecutivoM;
use App\Models\Letter\Collection\CollectionDateM;
use App\Models\Letter\Collection\CollectionRelEnlaceM;
use App\Models\Letter\Collection\CollectionRemitenteM;
use App\Models\Letter\Collection\CollectionStatusM;
use App\Models\Letter\Collection\CollectionUnidadM;
use App\Http\Controllers\Controller;
use App\Models\Letter\Collection\CollectionAreaM;
use App\Models\Letter\Collection\CollectionRelUsuarioM;
use App\Models\Letter\Letter\LetterM;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\MessagesC;
use Carbon\Carbon;

class LetterC extends Controller
{
    public function __invoke()
    {
        return view('letter/letter/list');
    }
    public function table(Request $request, LetterM $model)
    {
        try {
            // El JS envía "iterator" = offset y "searchValue".
            // Si tienes lógica de áreas por rol, puedes poblar $idUser con un array de IDs de área.
            // Por ahora lo dejamos vacío para mostrar todo (modo admin).
            $iterator    = (int) $request->get('iterator', 0);
            $searchValue = (string) $request->get('searchValue', '');
            $idUser      = []; // o p.ej. $idUser = auth()->user()->areas_ids ?? [];

            $rows = $model->list($iterator, $searchValue, $idUser);

            return response()->json([
                'value' => $rows,
            ]);
        } catch (\Throwable $e) {
            Log::error('LETTER_TABLE_ERROR: '.$e->getMessage(), ['ex' => $e]);
            return response()->json([
                'value' => [],
                'error' => true,
                'message' => 'Error al cargar la tabla',
            ], 500);
        }
    }

    public function create()
    {
        $item = new LetterM();
        $collectionAreaM = new CollectionAreaM();
        $collectionUnidadM = new CollectionUnidadM();
        $collectionStatusM = new CollectionStatusM();
        $collectionDateM = new CollectionDateM();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionRemitenteM = new CollectionRemitenteM();
        $collectionEntidadM = new CollectionEntidadM();

        $item->fecha_captura = now()->format('d/m/Y');
        $item->id_cat_anio = $collectionDateM->idYear();
        $item->num_turno_sistema = $collectionConsecutivoM->noDocumento($item->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'));
        $item->rfc_remitente_bool = false;
        $item->es_doc_fisico = true;
        $item->son_mas_remitentes = false;
        $item->num_flojas = 1;
        $item->num_tomos = 0;
        $item->horas_respuesta = 0;

        $selectArea   = $collectionAreaM->list();
        $selectAreaEdit = [];

        // NUEVOS selects para Área 1 y Área 2
        $selectArea1 = $collectionAreaM->list();
        $selectArea1Edit = [];
        $selectArea2 = $collectionAreaM->list();
        $selectArea2Edit = [];

        $selectUser = [];
        $selectUserEdit = [];

        $selectEnlace = [];
        $selectEnlaceEdit = [];

        $selectUnidad = [];
        $selectUnidadEdit = [];

        $selectCoordinacion = [];
        $selectCoordinacionEdit = [];

        $selectStatus = $collectionStatusM->list();
        $selectStatusEdit = $collectionStatusM->edit(1);

        $selectTramite = [];
        $selectTramiteEdit = [];

        $selectClave = [];
        $selectClaveEdit = [];

        $selectRemitente = $collectionRemitenteM->list();
        $selectRemitenteEdit = [];

        $selectEntidad = $collectionEntidadM->list();
        $selectEntidadEdit = [];

        return view('letter.letter.form', compact(
            'selectEntidadEdit','selectEntidad','selectRemitenteEdit','selectRemitente',
            'selectClaveEdit','selectClave','selectTramite','selectTramiteEdit',
            'selectStatusEdit','selectStatus','selectCoordinacionEdit','selectCoordinacion',
            'selectUnidadEdit','selectUnidad','item','selectArea','selectAreaEdit',
            'selectUser','selectUserEdit','selectEnlace','selectEnlaceEdit',
            // nuevos:
            'selectArea1','selectArea1Edit','selectArea2','selectArea2Edit'
        ));
    }

    public function edit(string $id)
    {
        $letterM = new LetterM();
        $collectionAreaM = new CollectionAreaM();
        $collectionRelUsuarioM = new CollectionRelUsuarioM();
        $collectionRelEnlaceM = new CollectionRelEnlaceM();
        $collectionUnidadM = new CollectionUnidadM();
        $collectionStatusM = new CollectionStatusM();
        $collectionCoordinacionM = new CollectionCoordinacionM();
        $collectionTramiteM = new CollectionTramiteM();
        $collectionRemitenteM = new CollectionRemitenteM();
        $collectionClaveM = new CollectionClaveM();
        $collectionEntidadM = new CollectionEntidadM();

        $item = $letterM->edit($id);

        $selectStatus = $collectionStatusM->listEdit();
        $selectStatusEdit = isset($item->id_cat_estatus) ? $collectionStatusM->edit($item->id_cat_estatus) : [];

        $selectArea = $collectionAreaM->listEdit();
        $selectAreaEdit = isset($item->id_cat_area) ? $collectionAreaM->edit($item->id_cat_area) : [];

        // NUEVO: catálogos Área 1 y Área 2
        $selectArea1 = $collectionAreaM->listEdit();
        $selectArea1Edit = isset($item->id_cat_area_1) ? $collectionAreaM->edit($item->id_cat_area_1) : [];
        $selectArea2 = $collectionAreaM->listEdit();
        $selectArea2Edit = isset($item->id_cat_area_2) ? $collectionAreaM->edit($item->id_cat_area_2) : [];

        $selectUser = isset($item->id_cat_area) ? $collectionRelUsuarioM->idUsuarioByAreaNewX($item->id_cat_area, $item->id_usuario_area) : [];
        $selectUserEdit = isset($item->id_cat_area) && isset($item->id_usuario_area) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_area) : [];

        $selectEnlace = isset($item->id_cat_area) ? $collectionRelEnlaceM->idUsuarioByAreaNewX($item->id_cat_area, $item->id_usuario_enlace) : [];
        $selectEnlaceEdit = isset($item->id_cat_area) && isset($item->id_usuario_enlace) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_enlace) : [];

        $selectUnidad = $collectionUnidadM->listEdit();
        $selectUnidadEdit = isset($item->id_cat_unidad) ? $collectionUnidadM->edit($item->id_cat_unidad) : [];

        $selectCoordinacion = isset($item->id_cat_unidad) ? $collectionCoordinacionM->listEdit($item->id_cat_unidad) : [];
        $selectCoordinacionEdit = isset($item->id_cat_unidad) && isset($item->id_cat_coordinacion) ? $collectionCoordinacionM->edit($item->id_cat_coordinacion) : [];

        $selectTramite = isset($item->id_cat_area) ? $collectionTramiteM->listEdit($item->id_cat_area) : [];
        $selectTramiteEdit = isset($item->id_cat_area) && isset($item->id_cat_tramite) ? $collectionTramiteM->edit($item->id_cat_tramite) : [];

        $selectClave = isset($item->id_cat_area) && isset($item->id_cat_tramite) ? $collectionClaveM->listEdit($item->id_cat_tramite) : [];
        $selectClaveEdit = isset($item->id_cat_area) && isset($item->id_cat_tramite) && isset($item->id_cat_clave) ? $collectionClaveM->edit($item->id_cat_clave) : [];

        $selectRemitente = $collectionRemitenteM->list();
        $selectRemitenteEdit = isset($item->id_cat_remitente) ? $collectionRemitenteM->edit($item->id_cat_remitente) : [];

        $selectEntidad = $collectionEntidadM->listEdit();
        $selectEntidadEdit = isset($item->id_cat_entidad) ? $collectionEntidadM->edit($item->id_cat_entidad) : [];

        return view('letter.letter.form', compact(
            'selectEntidadEdit','selectEntidad','selectRemitenteEdit','selectRemitente',
            'selectClaveEdit','selectClave','selectTramite','selectTramiteEdit',
            'selectStatusEdit','selectStatus','selectCoordinacionEdit','selectCoordinacion',
            'selectUnidadEdit','selectUnidad','item','selectArea','selectAreaEdit',
            'selectUser','selectUserEdit','selectEnlace','selectEnlaceEdit',
            // nuevos:
            'selectArea1','selectArea1Edit','selectArea2','selectArea2Edit'
        ));
    }

    public function save(Request $request)
    {
        $logC = new LogC();
        $collectionRemitenteM = new CollectionRemitenteM();
        $messagesC = new MessagesC();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionRolAreaM = new CollectionRolAreaM();
        $collectionLetterLogM = new CollectionLetterLogM();
        $now = Carbon::now();

        $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray();
        $ADM_TOTAL = config('custom_config.ADM_TOTAL');
        $COR_TOTAL = config('custom_config.COR_TOTAL');

        $rfc_remitente_bool = isset($request->rfc_remitente_bool) ? 1 : 0;
        $es_doc_fisico = isset($request->es_doc_fisico) ? 1 : 0;
        $son_mas_remitentes = isset($request->son_mas_remitentes) ? 1 : 0;

        if ($rfc_remitente_bool) {
            $collectionRemitenteM::create([
                'nombre' => strtoupper($request->remitente_nombre),
                'primer_apellido' => strtoupper($request->remitente_apellido_paterno),
                'segundo_apellido' => strtoupper($request->remitente_apellido_materno),
                'rfc' => strtoupper($request->remitente_rfc),
                'estatus' => true,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ]);
            $request->id_cat_remitente = $collectionRemitenteM->getRfc(
                strtoupper($request->remitente_nombre),
                strtoupper($request->remitente_apellido_paterno),
                strtoupper($request->remitente_apellido_materno)
            );
        }

        // CREATE
        if (!isset($request->id_tbl_correspondencia)) {

            // aseguro num_turno_sistema coherente (tu lógica original)
            $letterM = new LetterM();
            if ($this->getMaxTurno($request->num_turno_sistema) <= $letterM->getMaxNuSistem()) {
                $numTurnoSistemaAux = $this->procesarParametros(
                    $request->num_turno_sistema,
                    $collectionConsecutivoM->noDocumento($request->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'))
                );
            } else {
                $numTurnoSistemaAux = $request->num_turno_sistema;
            }

            $data = [
                'num_turno_sistema' => strtoupper($numTurnoSistemaAux),
                'num_documento' => strtoupper($request->num_documento),
                'fecha_captura' => Carbon::createFromFormat('d/m/Y', $request->fecha_captura)->format('Y-m-d'),
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'num_flojas' => 1,
                'num_tomos' => 0,
                'horas_respuesta' => $request->horas_respuesta,
                'id_cat_entidad' => $request->id_cat_entidad,
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_cat_area' => $request->id_cat_area,
                'id_cat_area_1' => $request->id_cat_area_1, // NUEVO
                'id_cat_area_2' => $request->id_cat_area_2, // NUEVO
                'id_usuario_area' => $request->id_usuario_area,
                'id_usuario_enlace' => $request->id_usuario_enlace,
                'id_cat_estatus' => $request->id_cat_estatus,
                'id_cat_remitente' => $request->id_cat_remitente,
                'id_cat_anio' => $request->id_cat_anio,
                'id_cat_tramite' => $request->id_cat_tramite,
                'id_cat_clave' => $request->id_cat_clave,
                'id_cat_unidad' => $request->id_cat_unidad,
                'id_cat_coordinacion' => $request->id_cat_coordinacion,
                'puesto_remitente' => strtoupper($request->puesto_remitente),
                'folio_gestion' => strtoupper($request->folio_gestion),
                'es_doc_fisico' => $es_doc_fisico,
                'son_mas_remitentes' => $son_mas_remitentes,
                'remitente' => strtoupper($request->remitente),
                'fecha_documento' => $request->fecha_documento,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
                'id_usuario_captura' => Auth::user()->id,
                'fecha_usuario_captura' => $now,
            ];

            $created = LetterM::create($data);
            $logC->add('correspondencia.tbl_correspondencia', $data);
            $collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'));

            $collectionLetterLogM::create([
                'estatus' => 'AGREGAR',
                'num_documento' => strtoupper($request->num_documento),
                'folio_gestion' => strtoupper($request->folio_gestion),
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_cat_area' => $request->id_cat_area,
                'id_cat_estatus' => $request->id_cat_estatus,
                'id_tbl_correspondencia' => $created->getIdFolGestion($request->folio_gestion)->id,
                'fecha_usuario_captura' => $now,
                'id_usuario_captura' => Auth::user()->id,
            ]);

            return $messagesC->messageSuccessRedirect('letter.list', 'Elemento agregado con éxito.');
        }

        // UPDATE
        if (in_array($ADM_TOTAL, $roleUserArray) || in_array($COR_TOTAL, $roleUserArray)) {

            $data = [
                'num_turno_sistema' => strtoupper($request->num_turno_sistema),
                'num_documento' => $request->num_documento,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'num_flojas' => 1,
                'num_tomos' => 0,
                'horas_respuesta' => $request->horas_respuesta,
                'id_cat_entidad' => $request->id_cat_entidad,
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_cat_area' => $request->id_cat_area,
                'id_cat_area_1' => $request->id_cat_area_1, // NUEVO
                'id_cat_area_2' => $request->id_cat_area_2, // NUEVO
                'id_usuario_area' => $request->id_usuario_area,
                'id_usuario_enlace' => $request->id_usuario_enlace,
                'id_cat_estatus' => $request->id_cat_estatus,
                'id_cat_remitente' => $request->id_cat_remitente,
                'id_cat_anio' => $request->id_cat_anio,
                'id_cat_tramite' => $request->id_cat_tramite,
                'id_cat_clave' => $request->id_cat_clave,
                'id_cat_unidad' => $request->id_cat_unidad,
                'id_cat_coordinacion' => $request->id_cat_coordinacion,
                'puesto_remitente' => strtoupper($request->puesto_remitente),
                'folio_gestion' => strtoupper($request->folio_gestion),
                'es_doc_fisico' => $es_doc_fisico,
                'son_mas_remitentes' => $son_mas_remitentes,
                'remitente' => strtoupper($request->remitente),
                'fecha_documento' => $request->fecha_documento,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            LetterM::where('id_tbl_correspondencia', $request->id_tbl_correspondencia)->update($data);

            $data['id_tbl_correspondencia'] = $request->id_tbl_correspondencia;
            $logC->edit('correspondencia.tbl_correspondencia', $data);

            $collectionLetterLogM::create([
                'estatus' => 'MODIFICAR',
                'num_documento' => strtoupper($request->num_documento),
                'folio_gestion' => strtoupper($request->folio_gestion),
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_cat_area' => $request->id_cat_area,
                'id_cat_estatus' => $request->id_cat_estatus,
                'id_tbl_correspondencia' => $request->id_tbl_correspondencia,
                'fecha_usuario_captura' => $now,
                'id_usuario_captura' => Auth::user()->id,
            ]);

            return $messagesC->messageSuccessRedirect('letter.list', 'Elemento modificado con éxito.');
        }

        // UPDATE restringido (no admin): NO tocamos áreas 1/2 aquí
        $collectionRolAreaM = new CollectionRolAreaM();
        if (!in_array($request->id_cat_area, $collectionRolAreaM->getListArea())) {
            return redirect()->back()->with([
                'value' => 'error',
                'message' => 'No se han configurado permisos para este usuario.',
                'estatus' => 'true'
            ]);
        }

        $data = [
            'observaciones' => strtoupper($request->observaciones),
            'id_cat_estatus' => $request->id_cat_estatus,
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario' => $now,
        ];

        LetterM::where('id_tbl_correspondencia', $request->id_tbl_correspondencia)->update($data);

        $data['id_tbl_correspondencia'] = $request->id_tbl_correspondencia;
        $logC->edit('correspondencia.tbl_correspondencia', $data);

        $collectionLetterLogM::create([
            'estatus' => 'MODIFICAR',
            'num_documento' => strtoupper($request->num_documento),
            'folio_gestion' => strtoupper($request->folio_gestion),
            'asunto' => strtoupper($request->asunto),
            'observaciones' => strtoupper($request->observaciones),
            'id_cat_area' => $request->id_cat_area,
            'id_cat_estatus' => $request->id_cat_estatus,
            'id_tbl_correspondencia' => $request->id_tbl_correspondencia,
            'fecha_usuario_captura' => $now,
            'id_usuario_captura' => Auth::user()->id,
        ]);

        return $messagesC->messageSuccessRedirect('letter.list', 'Elemento modificado con éxito.');
    }

    /* ==== helpers privados originales (sin cambios) ==== */
    private function getMaxTurno($numTurno) {
        if (preg_match('/\/([0-9]{5})\//', $numTurno, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    private function procesarParametros($param1, $param2) {
        preg_match('/^([A-Za-z]+)/', $param1, $coincidencias1);
        $letras1 = $coincidencias1[1];
        preg_match('/\/(\d+)\//', $param2, $coincidencias2);
        $numeros2 = $coincidencias2[1];
        return $letras1 . '/' . $numeros2 . '/2025';
    }
}



