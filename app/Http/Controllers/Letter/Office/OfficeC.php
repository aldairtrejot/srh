<?php

namespace App\Http\Controllers\Letter\Office;
use App\Models\Letter\Letter\LetterM;
use App\Models\Letter\Office\OfficeM;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Letter\Collection\CollectionDateM;
use App\Models\Letter\Collection\CollectionConsecutivoM;
use App\Models\Letter\Collection\CollectionAreaM;
use App\Models\Letter\Collection\CollectionRemitenteM;
use App\Models\Letter\Collection\CollectionRelEnlaceM;
use App\Models\Letter\Collection\CollectionRelUsuarioM;
use Carbon\Carbon;
use App\Http\Controllers\Admin\MessagesC;

class OfficeC extends Controller
{
    //La funcion retorna la vista principal de la tabla
    public function list()
    {
        return view('letter/office/list');
    }

    public function cloud($id_tbl_oficio)
    {
        $officeM = new OfficeM();
        $item = $officeM->edit($id_tbl_oficio);
        return view('letter/office/cloud', compact('item', 'id_tbl_oficio'));

    }

    //La funcion crea ta tabla dependiedp de los roles que se han ingreado
    public function table(Request $request)
    {
        try {
            $officeM = new OfficeM();
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
                $value = $officeM->list($iterator, $searchValue, null);
            } else {
                // Llamamos al método list() con los parámetros necesarios
                $value = $officeM->list($iterator, $searchValue, Auth::id());
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

    public function create()
    {
        $item = new OfficeM();
        $collectionDateM = new CollectionDateM();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionAreaM = new CollectionAreaM();
        $collectionRemitenteM = new CollectionRemitenteM();

        $item->fecha_captura = now()->format('d/m/Y'); // Formato de fecha: día/mes/año
        $item->id_cat_anio = $collectionDateM->idYear();
        $item->num_turno_sistema = $collectionConsecutivoM->noDocumento($item->id_cat_anio, config('custom_config.CP_TABLE_OFICIO'));

        $noLetter = "";//No de oficio se inicializa en vacio
        $selectArea = $collectionAreaM->list(); //Catalogo de area
        $selectAreaEdit = []; //catalogo de area null

        $selectUser = []; //Catalogo de Area - usuario, al crear comienza en vacio 
        $selectUserEdit = []; //Catalogo de Area - usuario, al crear comienza en vacio 

        $selectEnlace = []; //Catalogo de Area - enlace, al crear comienza en vacio 
        $selectEnlaceEdit = []; //Catalogo de Area - enlace, al crear comienza en vacio 

        $selectRemitente = $collectionRemitenteM->list(); //Se carga el catalogo de remitente
        $selectRemitenteEdit = []; //LA funcion de editar se inicia en falso

        return view('letter/office/form', compact('noLetter', 'selectRemitenteEdit', 'selectRemitente', 'selectEnlaceEdit', 'selectEnlace', 'selectUserEdit', 'selectUser', 'selectAreaEdit', 'selectArea', 'item'));
    }

    public function edit(string $id)
    {
        $officeM = new OfficeM();
        $collectionAreaM = new CollectionAreaM();
        $collectionRelUsuarioM = new CollectionRelUsuarioM();
        $collectionRelEnlaceM = new CollectionRelEnlaceM();
        $collectionRemitenteM = new CollectionRemitenteM();
        $letterM = new LetterM();

        $item = $officeM->edit($id); // Obtener el elemento con el ID pasado
        $noLetter = $letterM->getTurno($item->id_tbl_correspondencia);

        $selectArea = $collectionAreaM->list();// Obtener todos los registros del catálogo de áreas
        $selectAreaEdit = isset($item->id_cat_area) ? $collectionAreaM->edit($item->id_cat_area) : []; //Validacion de id_en DB para definir si se poblan los catalogos o son vacios

        $selectUser = isset($item->id_cat_area) ? $collectionRelUsuarioM->idUsuarioByArea($item->id_cat_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios
        $selectUserEdit = isset($item->id_cat_area) && isset($item->id_usuario_area) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios

        $selectEnlace = isset($item->id_cat_area) ? $collectionRelEnlaceM->idUsuarioByArea($item->id_cat_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios
        $selectEnlaceEdit = isset($item->id_cat_area) && isset($item->id_usuario_enlace) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_enlace) : [];////Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios

        $selectRemitente = $collectionRemitenteM->list();
        $selectRemitenteEdit = isset($item->id_cat_remitente) ? $collectionRemitenteM->edit($item->id_cat_remitente) : [];

        return view('letter/office/form', compact('noLetter', 'selectRemitenteEdit', 'selectRemitente', 'selectEnlaceEdit', 'selectEnlace', 'selectUserEdit', 'selectUser', 'selectAreaEdit', 'selectArea', 'item'));
    }

    public function save(Request $request)
    {
        $officeM = new OfficeM();
        $messagesC = new MessagesC();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $letterM = new LetterM();
        //USER_ROLE
        $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray(); // Array con roles de usuario
        $ADM_TOTAL = config('custom_config.ADM_TOTAL'); // Acceso completo
        $COR_TOTAL = config('custom_config.COR_TOTAL'); // Acceso completo a correspondencia
        //Autorizacion solo administracion

        $now = Carbon::now(); //Hora y fecha actual
        //Validacion de documento unico
        $id_tbl_correspondencia = $letterM->validateNoTurno($request->num_correspondencia);

        if (!isset($request->id_tbl_oficio)) { // || empty($request->id_tbl_correspondencia)) { // Creación de nuevo nuevo elemento

            $request->validate([
                'num_correspondencia' => 'required|max:45',
                'fecha_inicio' => 'required',
                'fecha_fin' => 'required',
                'asunto' => 'required|max:80',
                'observaciones' => 'max:80',
                'id_cat_area' => 'required',
                'id_usuario_area' => 'required',
                'id_usuario_enlace' => 'required',
                'id_cat_remitente' => 'required',
            ]);

            if (!$id_tbl_correspondencia) { //Validacion para que exista un id o este vacio
                return $messagesC->messageErrorBack('El No de correspondencia no está asociado a un documento.');
            }

            //Validacion de fecha, de inicio y fin
            if ($request->fecha_inicio >= $request->fecha_fin) {
                return $messagesC->messageErrorBack('La fecha de inicio no puede ser anterior a la fecha de finalización.');
            }

            //Agregar elementos
            $officeM::create([
                'num_turno_sistema' => $request->num_turno_sistema,
                'fecha_captura' => Carbon::createFromFormat('d/m/Y',$request->fecha_captura)->format('Y-m-d'),
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'asunto' => $request->asunto,
                'observaciones' => $request->observaciones,
                'id_cat_area' => $request->id_cat_area,
                'id_usuario_area' => $request->id_usuario_area,
                'id_usuario_enlace' => $request->id_usuario_enlace,
                'id_cat_remitente' => $request->id_cat_remitente,
                'rfc_remitente_bool' => false,
                'id_tbl_correspondencia' => $id_tbl_correspondencia,
                'id_cat_anio' => $request->id_cat_anio,
                //DATA_SYSTEM
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ]);

            //se itera el consevutivo
            $collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_OFICIO'));

            return $messagesC->messageSuccessRedirect('office.list', 'Elemento agregado con éxito.');

        } else { //modificar elemento 

            if (in_array($ADM_TOTAL, $roleUserArray) || in_array($COR_TOTAL, $roleUserArray)) {
                $request->validate([
                    'num_correspondencia' => 'required|max:45',
                    'fecha_inicio' => 'required',
                    'fecha_fin' => 'required',
                    'asunto' => 'required|max:80',
                    'observaciones' => 'max:80',
                    'id_cat_area' => 'required',
                    'id_usuario_area' => 'required',
                    'id_usuario_enlace' => 'required',
                    'id_cat_remitente' => 'required',
                ]);

                //Validacion de documento unico
                if (!$id_tbl_correspondencia) { //Validacion para que exista un id o este vacio
                    return $messagesC->messageErrorBack('El No de correspondencia no está asociado a un documento.');
                }

                //Validacion de fecha, de inicio y fin
                if ($request->fecha_inicio >= $request->fecha_fin) {
                    return $messagesC->messageErrorBack('La fecha de inicio no puede ser anterior a la fecha de finalización.');
                }

                $officeM::where('id_tbl_oficio', $request->id_tbl_oficio)
                    ->update([
                        'fecha_inicio' => $request->fecha_inicio,
                        'fecha_fin' => $request->fecha_fin,
                        'asunto' => $request->asunto,
                        'observaciones' => $request->observaciones,
                        'id_cat_area' => $request->id_cat_area,
                        'id_usuario_area' => $request->id_usuario_area,
                        'id_usuario_enlace' => $request->id_usuario_enlace,
                        'id_cat_remitente' => $request->id_cat_remitente,
                        'rfc_remitente_bool' => false,
                        'id_tbl_correspondencia' => $id_tbl_correspondencia,

                        'id_usuario_sistema' => Auth::user()->id,
                        'fecha_usuario' => $now,
                    ]);

                return $messagesC->messageSuccessRedirect('office.list', 'Elemento modificado con éxito.');
            } else {
                $request->validate([
                    'observaciones' => 'required|max:50',
                    'num_correspondencia' => 'required|max:45',
                ]);

                //Validacion de documento unico
                if (!$id_tbl_correspondencia) { //Validacion para que exista un id o este vacio
                    return $messagesC->messageErrorBack('El No de correspondencia no está asociado a un documento.');
                }

                $officeM::where('id_tbl_oficio', $request->id_tbl_oficio)
                    ->update([
                        'observaciones' => $request->observaciones,
                        'id_tbl_correspondencia' => $id_tbl_correspondencia,
                        'id_usuario_sistema' => Auth::user()->id,
                        'fecha_usuario' => $now,
                    ]);

                return $messagesC->messageSuccessRedirect('office.list', 'Elemento modificado con éxito.');
            }

        }
    }
}
