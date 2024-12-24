<?php

namespace App\Http\Controllers\Letter\Office;
use App\Models\Letter\Collection\CollectionReportM;
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
        $collectionReportM = new CollectionReportM();
        $officeM = new OfficeM();
        $item = $officeM->edit($id_tbl_oficio);
        $id_cat_area = $collectionReportM->getIdArea($id_tbl_oficio, 'correspondencia.tbl_oficio', 'id_tbl_oficio');
        return view('letter/office/cloud', compact('id_cat_area', 'item', 'id_tbl_oficio'));

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
        $item->es_por_area = false; //Iniciamos la variable en falso para asociar con el nuevo no de documento

        $noLetter = "";//No de oficio se inicializa en vacio


        $selectAreaAux = $collectionAreaM->list(); //Catalogo de area
        $selectAreaEditAux = []; //catalogo de area null

        return view('letter/office/form', compact('selectAreaEditAux', 'selectAreaAux', 'noLetter', 'item'));
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

        $selectAreaAux = $collectionAreaM->list(); //Catalogo de area
        $selectAreaEditAux = isset($item->id_cat_area_documento) ? $collectionAreaM->edit($item->id_cat_area_documento) : []; //catalogo de area null

        return view('letter/office/form', compact('selectAreaEditAux', 'selectAreaAux', 'noLetter', 'item'));
    }

    public function save(Request $request)
    {
        $officeM = new OfficeM();
        $messagesC = new MessagesC();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionAreaM = new CollectionAreaM();
        $letterM = new LetterM();
        //USER_ROLE
        $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray(); // Array con roles de usuario
        $ADM_TOTAL = config('custom_config.ADM_TOTAL'); // Acceso completo
        $COR_TOTAL = config('custom_config.COR_TOTAL'); // Acceso completo a correspondencia
        //Autorizacion solo administracion

        $now = Carbon::now(); //Hora y fecha actual
        //Validacion de documento unico
        $id_tbl_correspondencia = $letterM->validateNoTurno($request->num_correspondencia);
        $es_por_area = isset($request->es_por_area) ? 1 : 0; //Se condiciona el valor del check
        // aregar



        if (!isset($request->id_tbl_oficio)) { // || empty($request->id_tbl_correspondencia)) { // Creación de nuevo nuevo elemento
           
           
            $id_area_aux = $letterM->validateNoTurnoArea($request->num_correspondencia);
            if ($es_por_area == 1) {
                if ($request->id_cat_area_documento == 2) {
                    $idusuario = 7;
                    $idEnlace = 8;
                } else if ($request->id_cat_area_documento == 4) {
                    $idusuario = 9;
                    $idEnlace = 10;
                } else if ($request->id_cat_area_documento == 5) {
                    $idusuario = 6;
                    $idEnlace = 4;
                }
            } else {
                if ($id_area_aux == 2) {
                    $idusuario = 7;
                    $idEnlace = 8;
                } else if ($id_area_aux == 4) {
                    $idusuario = 9;
                    $idEnlace = 10;
                } else if ($id_area_aux == 5) {
                    $idusuario = 6;
                    $idEnlace = 4;
                }
            }

            $officeM::create([
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

                //DATA_SYSTEM
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ]);

            //se itera el consevutivo
            $collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_OFICIO'));
            $collectionAreaM->iteratorConsecutivo($request->id_cat_anio, $request->id_cat_area_documento);

            return $messagesC->messageSuccessRedirect('office.list', 'Elemento agregado con éxito.');

        } else { //modificar elemento 
            //Validacion por roles
            if (in_array($ADM_TOTAL, $roleUserArray) || in_array($COR_TOTAL, $roleUserArray)) {
                $officeM::where('id_tbl_oficio', $request->id_tbl_oficio)
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
            } else {
                $officeM::where('id_tbl_oficio', $request->id_tbl_oficio)
                    ->update([
                        'observaciones' => strtoupper($request->observaciones),
                        'id_usuario_sistema' => Auth::user()->id,
                        'fecha_usuario' => $now,
                    ]);
            }
            return $messagesC->messageSuccessRedirect('office.list', 'Elemento modificado con éxito.');
        }
    }
}
