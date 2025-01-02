<?php

namespace App\Http\Controllers\Letter\File;
use App\Models\Letter\Letter\LetterM;
use App\Models\Letter\File\FileM;
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
use App\Models\Letter\Collection\CollectionReportM;
class FileC extends Controller
{
    //La funcion retorna la vista principal de la tabla
    public function list()
    {
        return view('letter/file/list');
    }

    public function cloud($id)
    {
        $collectionReportM = new CollectionReportM();
        $object = new FileM();
        $item = $object->edit($id);
        $id_cat_area = $collectionReportM->getIdArea($id, 'correspondencia.tbl_expediente', 'id_tbl_expediente');
        return view('letter/file/cloud', compact('item', 'id_cat_area', 'id'));

    }

    //La funcion crea ta tabla dependiedp de los roles que se han ingreado
    public function table(Request $request)
    {
        try {
            $model = new FileM();
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
                $value = $model->list($iterator, $searchValue, null);
            } else {
                // Llamamos al método list() con los parámetros necesarios
                $value = $model->list($iterator, $searchValue, Auth::id());
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
        $item = new FileM();
        $collectionDateM = new CollectionDateM();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionAreaM = new CollectionAreaM();
        $collectionRemitenteM = new CollectionRemitenteM();

        $item->fecha_captura = now()->format('d/m/Y'); // Formato de fecha: día/mes/año
        $item->id_cat_anio = $collectionDateM->idYear();
        $item->num_turno_sistema = $collectionConsecutivoM->noDocumento($item->id_cat_anio, config('custom_config.CP_TABLE_EXPEDIENTE'));
        $item->es_por_area = false;

        $noLetter = "";//No de oficio se inicializa en vacio

        $selectAreaAux = $collectionAreaM->list(); //Catalogo de area
        $selectAreaEditAux = []; //catalogo de area null

        return view('letter/file/form', compact('selectAreaEditAux', 'selectAreaAux', 'noLetter', 'item'));
    }

    public function edit(string $id)
    {
        $object = new FileM();
        $collectionAreaM = new CollectionAreaM();
        $collectionRelUsuarioM = new CollectionRelUsuarioM();
        $collectionRelEnlaceM = new CollectionRelEnlaceM();
        $collectionRemitenteM = new CollectionRemitenteM();
        $letterM = new LetterM();

        $item = $object->edit($id); // Obtener el elemento con el ID pasado
        $noLetter = $letterM->getTurno($item->id_tbl_correspondencia);

        $selectAreaAux = $collectionAreaM->list(); //Catalogo de area
        $selectAreaEditAux = isset($item->id_cat_area_documento) ? $collectionAreaM->edit($item->id_cat_area_documento) : []; //catalogo de area null

        return view('letter/file/form', compact('selectAreaEditAux', 'selectAreaAux', 'noLetter', 'item'));
    }

    public function save(Request $request)
    {
        $object = new FileM();
        $messagesC = new MessagesC();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $letterM = new LetterM();
        $collectionAreaM = new CollectionAreaM();
        //USER_ROLE
        $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray(); // Array con roles de usuario
        $ADM_TOTAL = config('custom_config.ADM_TOTAL'); // Acceso completo
        $COR_TOTAL = config('custom_config.COR_TOTAL'); // Acceso completo a correspondencia
        //Autorizacion solo administracion

        $now = Carbon::now(); //Hora y fecha actual
        //Validacion de documento unico
        $id_tbl_correspondencia = $letterM->validateNoTurno($request->num_correspondencia);
        $es_por_area = isset($request->es_por_area) ? 1 : 0; //Se condiciona el valor del check

        if (!isset($request->id_tbl_expediente)) { // || empty($request->id_tbl_correspondencia)) { // Creación de nuevo nuevo elemento
            //Agregar elementos

            $id_area_aux = $letterM->validateNoTurnoArea($request->num_correspondencia);
            if ($es_por_area == 1) {
                if ($request->id_cat_area_documento == 2) {
                    $idusuario = 7;
                    $idEnlace = 8;
                    $idArea = 2;
                } else if ($request->id_cat_area_documento == 4) {
                    $idusuario = 9;
                    $idEnlace = 10;
                    $idArea = 4;
                } else if ($request->id_cat_area_documento == 5) {
                    $idusuario = 6;
                    $idEnlace = 4;
                    $idArea = 5;
                } else if ($request->id_cat_area_documento == 6) {
                    $idusuario = 13;
                    $idEnlace = 14;
                    $idArea = 6;
                }
            } else {
                if ($id_area_aux == 2) {
                    $idusuario = 7;
                    $idEnlace = 8;
                    $idArea = 2;
                } else if ($id_area_aux == 4) {
                    $idusuario = 9;
                    $idEnlace = 10;
                    $idArea = 4;
                } else if ($id_area_aux == 5) {
                    $idusuario = 6;
                    $idEnlace = 4;
                    $idArea = 5;
                } else if ($id_area_aux == 6) {
                    $idusuario = 13;
                    $idEnlace = 14;
                    $idArea = 6;
                }
            }

            $object::create([
                'num_turno_sistema' => $request->num_turno_sistema,
                'fecha_captura' => Carbon::createFromFormat('d/m/Y', $request->fecha_captura)->format('Y-m-d'),
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_tbl_correspondencia' => $id_tbl_correspondencia,
                'id_cat_anio' => $request->id_cat_anio,
                'es_por_area' => $es_por_area,
                'num_documento_area' => $request->num_documento_area,
                'id_cat_area_documento' => $request->id_cat_area_documento,

                'id_usuario_area' => $idusuario,
                'id_usuario_enlace' => $idEnlace,
                'id_cat_area' => $idArea,
                
                //DATA_SYSTEM
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ]);

            //se itera el consevutivo
            $collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_EXPEDIENTE'));
            $collectionAreaM->iteratorConsecutivo($request->id_cat_anio, $request->id_cat_area_documento);

            return $messagesC->messageSuccessRedirect('file.list', 'Elemento agregado con éxito.');

        } else { //modificar elemento 

            if (in_array($ADM_TOTAL, $roleUserArray) || in_array($COR_TOTAL, $roleUserArray)) {

                $object::where('id_tbl_expediente', $request->id_tbl_expediente)
                    ->update([
                        'fecha_inicio' => $request->fecha_inicio,
                        'fecha_fin' => $request->fecha_fin,
                        'asunto' => strtoupper($request->asunto),
                        'observaciones' => strtoupper($request->observaciones),
                        'id_tbl_correspondencia' => $id_tbl_correspondencia,
                        'es_por_area' => $es_por_area,
                        'num_documento_area' => $request->num_documento_area,
                        'id_cat_area_documento' => $request->id_cat_area_documento,
                        'id_usuario_sistema' => Auth::user()->id,
                        'fecha_usuario' => $now,
                    ]);

                return $messagesC->messageSuccessRedirect('file.list', 'Elemento modificado con éxito.');
            } else {
                $object::where('id_tbl_expediente', $request->id_tbl_expediente)
                    ->update([
                        'observaciones' => strtoupper($request->observaciones),
                        'id_usuario_sistema' => Auth::user()->id,
                        'fecha_usuario' => $now,
                    ]);

                return $messagesC->messageSuccessRedirect('file.list', 'Elemento modificado con éxito.');
            }

        }
    }
}
