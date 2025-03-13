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
use Illuminate\Support\Facades\Log;


class LetterC extends Controller
{
    public function __invoke()
    {
        return view('letter/letter/list');
    }

    // Retorna el dashboard
    public function dashboard()
    {
        $letterM = new LetterM();
        $item = [

        ];
        return view('letter/dashboard/dashboard', compact('item'));
    }
    public function cloud($id)
    {
        $object = new LetterM();
        $item = $object->edit($id);
        return view('letter/letter/cloud', compact('item'));

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

        $item->fecha_captura = now()->format('d/m/Y'); // Formato de fecha: día/mes/año
        $item->id_cat_anio = $collectionDateM->idYear();
        $item->num_turno_sistema = $collectionConsecutivoM->noDocumento($item->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'));
        $item->rfc_remitente_bool = false; //Iniciamos la variable en falso para asociar con el nuevo no de documento
        $item->es_doc_fisico = true; // Inicio de variables
        $item->son_mas_remitentes = false; // Inicio de variables

        $item->num_flojas = 1; // Inicio de variables
        $item->num_tomos = 0; // Inicio de variables
        $item->horas_respuesta = 0; // Inicio de variables



        $selectArea = $collectionAreaM->list(); //Catalogo de area
        $selectAreaEdit = []; //catalogo de area null

        $selectUser = []; //Catalogo de Area - usuario, al crear comienza en vacio 
        $selectUserEdit = []; //Catalogo de Area - usuario, al crear comienza en vacio 

        $selectEnlace = []; //Catalogo de Area - enlace, al crear comienza en vacio 
        $selectEnlaceEdit = []; //Catalogo de Area - enlace, al crear comienza en vacio 

        $selectUnidad = [];//Catalogo de unidad
        $selectUnidadEdit = []; //Catalogo de Unidad, al crear comienza en vacio 

        $selectCoordinacion = []; //Catalogos de coordinacion vacios
        $selectCoordinacionEdit = [];//Catalogos de coordinacion vacios

        $selectStatus = $collectionStatusM->list(); //Obtenemos el catalogo de estatus
        $selectStatusEdit = $collectionStatusM->edit(1);//Catalogos debe estar vacio

        $selectTramite = []; //Los catalogos incian vacios
        $selectTramiteEdit = []; //Los catalogos incian vacios

        $selectClave = []; //Los catalogos inician en vacio
        $selectClaveEdit = []; // Los catalogos inician en vaio

        $selectRemitente = $collectionRemitenteM->list(); //Se carga el catalogo de remitente
        $selectRemitenteEdit = []; //LA funcion de editar se inicia en falso

        $selectEntidad = $collectionEntidadM->list();
        $selectEntidadEdit = [];

        return view('letter.letter.form', compact('selectEntidadEdit', 'selectEntidad', 'selectRemitenteEdit', 'selectRemitente', 'selectClaveEdit', 'selectClave', 'selectTramite', 'selectTramiteEdit', 'selectStatusEdit', 'selectStatus', 'selectCoordinacionEdit', 'selectCoordinacion', 'selectUnidadEdit', 'selectUnidad', 'item', 'selectArea', 'selectAreaEdit', 'selectUser', 'selectUserEdit', 'selectEnlace', 'selectEnlaceEdit'));
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

        $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray(); // Array con roles de usuario
        $ADM_TOTAL = config('custom_config.ADM_TOTAL'); // Acceso completo
        $COR_TOTAL = config('custom_config.COR_TOTAL'); // Acceso completo a correspondencia

        $item = $letterM->edit($id); // Obtener el elemento con el ID pasado

        //Validacion de catalogo de estatus
        $selectStatus = $collectionStatusM->listEdit(); //Obtenemos el catalogo de estatus - muestra todos los estatus
        $selectStatusEdit = isset($item->id_cat_estatus) ? $collectionStatusM->edit($item->id_cat_estatus) : [];//Catalogos debe estar vacio
        /*
        if (in_array($ADM_TOTAL, $roleUserArray) || in_array($COR_TOTAL, $roleUserArray)) {
            $selectStatus = $collectionStatusM->list(); //Obtenemos el catalogo de estatus - muestra todos los estatus
        }*/

        $selectArea = $collectionAreaM->listEdit();// Obtener todos los registros del catálogo de áreas
        $selectAreaEdit = isset($item->id_cat_area) ? $collectionAreaM->edit($item->id_cat_area) : []; //Validacion de id_en DB para definir si se poblan los catalogos o son vacios

        $selectUser = isset($item->id_cat_area) ? $collectionRelUsuarioM->idUsuarioByArea($item->id_cat_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios
        $selectUserEdit = isset($item->id_cat_area) && isset($item->id_usuario_area) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios

        $selectEnlace = isset($item->id_cat_area) ? $collectionRelEnlaceM->idUsuarioByArea($item->id_cat_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios
        $selectEnlaceEdit = isset($item->id_cat_area) && isset($item->id_usuario_enlace) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_enlace) : [];////Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios

        $selectUnidad = $collectionUnidadM->listEdit();//Catalogo de unidad
        $selectUnidadEdit = isset($item->id_cat_unidad) ? $collectionUnidadM->edit($item->id_cat_unidad) : [];

        $selectCoordinacion = isset($item->id_cat_unidad) ? $collectionCoordinacionM->listEdit($item->id_cat_unidad) : []; //Catalogos de coordinacion vacios
        $selectCoordinacionEdit = isset($item->id_cat_unidad) && isset($item->id_cat_coordinacion) ? $collectionCoordinacionM->edit($item->id_cat_coordinacion) : [];//Catalogos de coordinacion vacios

        $selectTramite = isset($item->id_cat_area) ? $collectionTramiteM->listEdit($item->id_cat_area) : [];
        $selectTramiteEdit = isset($item->id_cat_area) && isset($item->id_cat_tramite) ? $collectionTramiteM->edit($item->id_cat_tramite) : [];

        $selectClave = isset($item->id_cat_area) && isset($item->id_cat_tramite) ? $collectionClaveM->listEdit($item->id_cat_tramite) : [];
        $selectClaveEdit = isset($item->id_cat_area) && isset($item->id_cat_tramite) && isset($item->id_cat_clave) ? $collectionClaveM->edit($item->id_cat_clave) : [];

        $selectRemitente = $collectionRemitenteM->list();
        $selectRemitenteEdit = isset($item->id_cat_remitente) ? $collectionRemitenteM->edit($item->id_cat_remitente) : [];

        $selectEntidad = $collectionEntidadM->listEdit();
        $selectEntidadEdit = isset($item->id_cat_entidad) ? $collectionEntidadM->edit($item->id_cat_entidad) : [];

        return view('letter.letter.form', compact('selectEntidadEdit', 'selectEntidad', 'selectRemitenteEdit', 'selectRemitente', 'selectClaveEdit', 'selectClave', 'selectTramite', 'selectTramiteEdit', 'selectStatusEdit', 'selectStatus', 'selectCoordinacionEdit', 'selectCoordinacion', 'selectUnidadEdit', 'selectUnidad', 'item', 'selectArea', 'selectAreaEdit', 'selectUser', 'selectUserEdit', 'selectEnlace', 'selectEnlaceEdit'));
    }

    public function table(Request $request)
    {
        try {
            $collectionRelUsuarioM = new CollectionRelUsuarioM();
            $collectionRolAreaM = new CollectionRolAreaM();
            $letterM = new LetterM();

            // Obtener valores de la solicitud
            $iterator = $request->input('iterator'); // OFSET valor de paginador
            $searchValue = $request->input('searchValue'); // Valor de búsqueda
            $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray(); // Array con roles de usuario
            $ADM_TOTAL = config('custom_config.ADM_TOTAL'); // Acceso completo
            $COR_TOTAL = config('custom_config.COR_TOTAL'); // Acceso completo a correspondencia
            $COR_USUARIO = config('custom_config.COR_USUARIO'); // Acceso por área

            // Verificar si el usuario tiene acceso completo
            if (in_array($ADM_TOTAL, $roleUserArray) || in_array($COR_TOTAL, $roleUserArray)) {
                // Si tiene acceso completo, no hay necesidad de filtrar por área o enlace
                // Procesar la tabla con acceso completo si es necesario
                $value = $letterM->list($iterator, $searchValue, null);
            } else {
                // Llamamos al método list() con los parámetros necesarios
                $value = $letterM->list($iterator, $searchValue, $collectionRolAreaM->getListArea());
            }

            // Responder con los resultados
            return response()->json([
                'value' => $value,
                'status' => true,
            ]);

        } catch (\Exception $e) {
            // Manejo de errores en caso de excepciones
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function save(Request $request)
    {
        // Class
        $logC = new LogC();
        $collectionRemitenteM = new CollectionRemitenteM();
        $letterM = new LetterM();
        $messagesC = new MessagesC();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionRolAreaM = new CollectionRolAreaM();
        $now = Carbon::now(); //Hora y fecha actual
        $collectionLetterLogM = new CollectionLetterLogM();
        //USER_ROLE
        $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray(); // Array con roles de usuario
        $ADM_TOTAL = config('custom_config.ADM_TOTAL'); // Acceso completo
        $COR_TOTAL = config('custom_config.COR_TOTAL'); // Acceso completo a correspondencia
        $COR_USUARIO = config('custom_config.COR_USUARIO'); // Acceso por área
        $rfc_remitente_bool = isset($request->rfc_remitente_bool) ? 1 : 0; //Se condiciona el valor del check
        $es_doc_fisico = isset($request->es_doc_fisico) ? 1 : 0; //Se condiciona el valor del check
        $son_mas_remitentes = isset($request->son_mas_remitentes) ? 1 : 0; //Se condiciona el valor del check
        //Autorizacion solo administracion

        if ($rfc_remitente_bool) { //El usuario agrego un remitente
            $collectionRemitenteM::create([
                'nombre' => strtoupper($request->remitente_nombre),
                'primer_apellido' => strtoupper($request->remitente_apellido_paterno),
                'segundo_apellido' => strtoupper($request->remitente_apellido_materno),
                'rfc' => strtoupper($request->remitente_rfc),
                'estatus' => true,

                //DATA_SYSTEM
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ]);
            //Se obtiene el id del rfc ingresado
            $request->id_cat_remitente = $collectionRemitenteM->getRfc(strtoupper($request->remitente_nombre), strtoupper($request->remitente_apellido_paterno), strtoupper($request->remitente_apellido_materno));
        }
        /*
        if ($letterM->validateNoDocument($request->id_tbl_correspondencia, $request->num_documento)) {
                        return $messagesC->messageErrorBack('El número de documento ya está registrado.');
                    }
                        */

        if (!isset($request->id_tbl_correspondencia)) { // || empty($request->id_tbl_correspondencia)) { // Creación de nuevo nuevo elemento
            //Agregar elementos

            /// Validación de no de  turno de sistema
            if ($this->getMaxTurno($request->num_turno_sistema) <= $letterM->getMaxNuSistem()) {
                $numTurnoSistemaAux = $this->procesarParametros($request->num_turno_sistema, $collectionConsecutivoM->noDocumento($request->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA')));
                //$collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'));
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

                // Datos del sistema
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,

                // Datos de captura por primera vez
                'id_usuario_captura' => Auth::user()->id,
                'fecha_usuario_captura' => $now,
            ];

            // Crear el registro en la base de datos utilizando el arreglo
            $letterM = LetterM::create($data);

            // Opcional: Guardar el log con los valores insertados (si se necesita)
            $logC->add('correspondencia.tbl_correspondencia', $data);
            $collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'));

            // Se agrega log a correspondencia
            $collectionLetterLogM::create([
                'estatus' => 'AGREGAR',
                'num_documento' => strtoupper($request->num_documento),
                'folio_gestion' => strtoupper($request->folio_gestion),
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_cat_area' => $request->id_cat_area,
                'id_cat_estatus' => $request->id_cat_estatus,
                'id_tbl_correspondencia' => $letterM->getIdFolGestion($request->folio_gestion)->id,
                'fecha_usuario_captura' => $now,
                'id_usuario_captura' => Auth::user()->id,
            ]);

            return $messagesC->messageSuccessRedirect('letter.list', 'Elemento agregado con éxito.');

        } else { //modificar elemento 

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

                // Realizar la actualización en la base de datos utilizando el arreglo
                $letterM = LetterM::where('id_tbl_correspondencia', $request->id_tbl_correspondencia)
                    ->update($data);

                $data['id_tbl_correspondencia'] = $request->id_tbl_correspondencia;
                $logC->edit('correspondencia.tbl_correspondencia', $data);

                // Se agrega log a correspondencia
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
            } else {
                // Validación para que en el caso que el area no este relacionada con el usuario este no sea capaz de modificar
                // Validacion de que el usuario tenga asociado esa area para modificarlo
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

                // Realizar la actualización en la base de datos utilizando el arreglo
                $letterM = LetterM::where('id_tbl_correspondencia', $request->id_tbl_correspondencia)
                    ->update($data);

                $data['id_tbl_correspondencia'] = $request->id_tbl_correspondencia;
                $logC->edit('correspondencia.tbl_correspondencia', $data);

                // Se agrega log a correspondencia
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

        }
    }

    // La función muestra el catalogo de areas, para el modal de turnar copia
    public function collectionArea()
    {
        // Class
        $collectionAreaM = new CollectionAreaM();
        $result = $collectionAreaM->list(); //Catalogo de area

        return response()->json([
            'result' => $result,
        ]);
    }

    // La función valida que el area y el No Correspondencia no esten asociados
    public function validateCopy(Request $request)
    {
        // Class
        $letterM = new LetterM();
        $result = $letterM->getValue($request->id_tbl_correspondencia, $request->id_cat_area);

        return response()->json([
            'result' => $result,
        ]);
    }

    // LA funcion guarda en la tabla copy correspondencia
    public function saveCopy(Request $request)
    {
        // Class
        $collectionLetterCopyM = new CollectionLetterCopyM();
        $logC = new LogC();
        $now = Carbon::now(); //Hora y fecha actual

        $data = [ // is Array
            'id_cat_area' => $request->id_cat_area,
            'id_usuario_area' => $request->id_usuario_area,
            'id_usuario_enlace' => $request->id_usuario_enlace,
            'id_cat_tramite' => $request->id_cat_tramite,
            'id_cat_clave' => $request->id_cat_clave,
            'id_tbl_correspondencia' => $request->id_tbl_correspondencia,
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario' => $now,
        ];

        $result = $collectionLetterCopyM::create($data);
        // Opcional: Guardar el log con los valores insertados (si se necesita)
        $logC->add('correspondencia.ctrl_transcribir_correspondencia', $data);


        return response()->json([
            'result' => $result,
        ]);
    }


    // La función retorna los valores para mostrar la tablad e copy
    public function tableCopy(Request $request)
    {
        try {
            // Declaración de variables
            $letterM = new LetterM();
            $value = $letterM->tableCopy($request->id); // Llamamos al método list() con los parámetros necesarios

            return response()->json([
                'value' => $value,
                'status' => true,
            ]);

        } catch (\Exception $e) {
            // Manejo de errores en caso de excepciones
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // La función elimina los elementos de copy -> correspondencia
    public function deleteCopy(Request $request)
    {
        // Class
        $collectionLetterCopyM = new CollectionLetterCopyM();
        $logC = new LogC();

        // Eliminacion del elemento
        $data = [ // Log
            'id_ctrl_transcribir_correspondencia' => $request->id
        ];

        $logC->delete('correspondencia.ctrl_transcribir_correspondencia', $data);
        $result = $collectionLetterCopyM::where('id_ctrl_transcribir_correspondencia', $request->id)->delete();

        $bool = false;
        if ($result > 0) {
            $bool = true;
        }

        return response()->json([
            'value' => $bool,
        ]);
    }


    //LA funcion elimina el elemento
    public function delete($id)
    {
        $letterM = new LetterM();
        $messagesC = new MessagesC();
        letterM::destroy($id);
        return $messagesC->messageSuccessRedirect('letter.list', 'Elemento eliminado con éxito.');
    }

    //La funcion que el no de documento y no gestion sean unicos
    public function validateUnique(Request $request)
    {
        $letterM = new LetterM();
        $result = $letterM->uniqueNoDocument($request->id, $request->value, $request->attribute);
        $value = !$result ? false : true; // Validacion de valor 

        // Responder con los resultados
        return response()->json([
            'status' => $value,
        ]);
    }

    //La funcion que el remitente sea unico
    public function uniqueRemitente(Request $request)
    {
        $collectionRemitenteM = new CollectionRemitenteM();
        $result = $collectionRemitenteM->uniqueRemitente($request->value, $request->attribute);
        $value = !$result ? false : true; // Validacion de valor 

        // Responder con los resultados
        return response()->json([
            'status' => $value,
        ]);
    }


    //La funcion que el remitente sea unico, por nombre, primer apellido, segundo apellido,
    public function uniqueRemitenteName(Request $request)
    {
        $collectionRemitenteM = new CollectionRemitenteM();
        $result = $collectionRemitenteM->uniqueRemitenteName($request->name, $request->fistLastName, $request->seconLastName);
        $value = !$result ? false : true; // Validacion de valor 

        // Responder con los resultados
        return response()->json([
            'status' => $value,
        ]);
    }

    public function getletter(Request $request)
    {
        $letterM = new LetterM();
        $value = $letterM->getUserEnlace($request->value);
        // Responder con los resultados
        return response()->json([
            'value' => $value,
        ]);
    }

    // la funcion elimina los espacios para obtener solo los numero de / ***(
    private function getMaxTurno($numTurno)
    {
        // Usamos una expresión regular para capturar los 5 dígitos entre las barras "/"
        if (preg_match('/\/([0-9]{5})\//', $numTurno, $matches)) {
            // $matches[1] contiene los 5 dígitos capturados
            return (int) $matches[1]; // Devolvemos el número como entero
        }

        return null; // Si no encuentra el patrón, devolvemos null
    }

    private function procesarParametros($param1, $param2)
    {
        // Extraemos la parte antes del primer '/'
        preg_match('/^([A-Za-z]+)/', $param1, $coincidencias1);
        $letras1 = $coincidencias1[1];

        // Extraemos la parte entre los '/' de param2
        preg_match('/\/(\d+)\//', $param2, $coincidencias2);
        $numeros2 = $coincidencias2[1];

        // Concatenamos las partes
        return $letras1 . '/' . $numeros2 . '/2025';
    }
    // Incrementacion del no consecutivo
    /*
    private function incrementarConsecutivo($turno)
    {
        // Usamos una expresión regular para extraer el prefijo, el número consecutivo y el sufijo
        if (preg_match('/^(.*\/)(\d{5})(\/\d{4})$/', $turno, $matches)) {
            // Extraemos los componentes
            $prefijo = $matches[1];  // DGP/
            $numeroConsecutivo = $matches[2];  // 01254
            $sufijo = $matches[3];  // /2025

            // Incrementamos el número consecutivo
            $nuevoNumero = str_pad($numeroConsecutivo + 1, 5, '0', STR_PAD_LEFT);  // Aseguramos que tenga 5 dígitos

            // Concatenamos el nuevo número con el prefijo y el sufijo
            $nuevoTurno = $prefijo . $nuevoNumero . $sufijo;

            return $nuevoTurno;
        }

        // Si no coincide con el formato esperado, devolvemos null o un valor de error
        return null;
    }
        */
}


