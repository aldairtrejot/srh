<?php

namespace App\Http\Controllers\Letter\Office;
use App\Http\Controllers\Letter\Other\ConsecutivoC;
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
use App\Http\Controllers\Letter\Log\LogC;
use Illuminate\Support\Facades\Log;
use App\Models\Letter\Collection\CollectionRolAreaM;
use Illuminate\Support\Facades\DB;

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
            $collectionRolAreaM = new CollectionRolAreaM();
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
                $value = $officeM->list($iterator, $searchValue, $collectionRolAreaM->getListArea());
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
        $collectionRelUsuarioM = new CollectionRelUsuarioM();
        $collectionRelEnlaceM = new CollectionRelEnlaceM();

        $item->fecha_captura = now()->format('d/m/Y'); // Formato de fecha: día/mes/año
        $item->id_cat_anio = $collectionDateM->idYear();
        $item->num_turno_sistema = $collectionConsecutivoM->noDocumento($item->id_cat_anio, config('custom_config.CP_TABLE_OFICIO'));
        $item->es_por_area = false; //Iniciamos la variable en falso para asociar con el nuevo no de documento

        $noLetter = "";//No de oficio se inicializa en vacio

        $area = ' _';
        $user_name = ' _';
        $user_enlace = ' _';

        $selectAreaAux = $collectionAreaM->list(); //Catalogo de area
        $selectAreaEditAux = []; //catalogo de area null

        $selectUser = [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios
        $selectUserEdit = [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios

        $selectEnlace = [];//Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios
        $selectEnlaceEdit = [];////Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios

        return view('letter/office/form', compact('user_enlace', 'user_name', 'area', 'selectEnlaceEdit', 'selectEnlace', 'selectUserEdit', 'selectUser', 'selectAreaEditAux', 'selectAreaAux', 'noLetter', 'item'));
    }

    public function edit(string $id)
    {
        $officeM = new OfficeM();
        $collectionAreaM = new CollectionAreaM();
        $collectionRelUsuarioM = new CollectionRelUsuarioM();
        $collectionRelEnlaceM = new CollectionRelEnlaceM();
        $collectionRemitenteM = new CollectionRemitenteM();
        $letterM = new LetterM();

        $other = $officeM->getDataFormat($id);
        $area = $other->area;
        $user_name = $other->user_name;
        $user_enlace = $other->user_enlace;


        $item = $officeM->edit($id); // Obtener el elemento con el ID pasado
        $noLetter = $letterM->getTurno($item->id_tbl_correspondencia);

        $selectAreaAux = $collectionAreaM->list(); //Catalogo de area
        $selectAreaEditAux = isset($item->id_cat_area_documento) ? $collectionAreaM->edit($item->id_cat_area_documento) : []; //catalogo de area null

        $selectUser = isset($item->id_cat_area) ? $collectionRelUsuarioM->idUsuarioByArea($item->id_cat_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios
        $selectUserEdit = isset($item->id_cat_area) && isset($item->id_usuario_area) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios

        $selectEnlace = isset($item->id_cat_area) ? $collectionRelEnlaceM->idUsuarioByArea($item->id_cat_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios
        $selectEnlaceEdit = isset($item->id_cat_area) && isset($item->id_usuario_enlace) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_enlace) : [];////Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios

        return view('letter/office/form', compact('user_enlace', 'user_name', 'area', 'selectEnlaceEdit', 'selectEnlace', 'selectUserEdit', 'selectUser', 'selectAreaEditAux', 'selectAreaAux', 'noLetter', 'item'));
    }

    public function save(Request $request)
    {
        $letterM = new LetterM();
        $logC = new LogC();
        $officeM = new OfficeM();
        $messagesC = new MessagesC();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionAreaM = new CollectionAreaM();
        $consecutivoC = new ConsecutivoC();
        $collectionAreaM = new CollectionAreaM();

        $now = Carbon::now(); //Hora y fecha actual
        //Validacion de documento unico
        //$id_tbl_correspondencia = $letterM->validateNoTurno($request->num_correspondencia);
        $es_por_area = isset($request->es_por_area) ? 1 : 0; //Se condiciona el valor del check
        // aregar

        if ($es_por_area == 1) { // validacion que es verdadero
            $idArea = $request->id_cat_area;
            $idUsuario = $request->id_usuario_area;
            $idEnlace = $request->id_usuario_enlace;
        } else {
            $response = $letterM->editFol($request->num_correspondencia);
            $idArea = $response->id_cat_area;
            $idUsuario = $response->id_usuario_area;
            $idEnlace = $response->id_usuario_enlace;
        }

        if (!isset($request->id_tbl_oficio)) { // || empty($request->id_tbl_correspondencia)) { // Creación de nuevo nuevo elemento

            if ($this->getMaxTurno($request->num_turno_sistema) <= $officeM->getMaxNuSistem()) {
                $numTurnoSistemaAux = $this->procesarParametros($request->num_turno_sistema, $collectionConsecutivoM->noDocumento($request->id_cat_anio, config('custom_config.CP_TABLE_OFICIO')));
                //$collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'));
            } else {
                $numTurnoSistemaAux = $request->num_turno_sistema;
            }

            // Validacion de es por area sea unico, de lo contrario se concatena la la variable
            $noDocumentoAreaAux = $request->num_documento_area;
            if ($es_por_area == 1) {
                if ($consecutivoC->getOnlyNo($request->num_documento_area) <= $officeM->getOnly($request->id_cat_area_documento, $request->id_cat_anio)->max_num) {
                    $noDocumentoAreaAux = $consecutivoC->setNoConsecutivo($request->num_documento_area, $collectionAreaM->noDocumentoByAux($request->id_cat_anio, $request->id_cat_area_documento, 'correspondencia.rel_consecutivo_oficio'));
                }
            } else { // Actualizar status de correspondencoa
                if ($request->update_letter) {
                    $data = [
                        'id_cat_estatus' => 4,
                        'observaciones' => DB::raw("CONCAT(observaciones, '  //  ' , '" . $request->observaciones . "')")
                    ];

                    $letterM::where('folio_gestion', $request->num_correspondencia)->update($data);
                    $data['folio_gestion'] = $request->num_correspondencia;
                    $logC->edit('correspondencia.tbl_correspondencia', $data);
                }
            }



            $data = [
                'num_turno_sistema' => strtoupper($numTurnoSistemaAux),
                'fecha_captura' => Carbon::createFromFormat('d/m/Y', $request->fecha_captura)->format('Y-m-d'),
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_tbl_correspondencia' => $request->id_tbl_correspondencia,
                'id_cat_anio' => $request->id_cat_anio,
                'es_por_area' => $es_por_area,
                'num_documento_area' => strtoupper($noDocumentoAreaAux),
                'id_cat_area_documento' => $request->id_cat_area_documento,
                'id_usuario_area' => $idUsuario,
                'id_usuario_enlace' => $idEnlace,
                'id_cat_area' => $idArea,

                // DATA_SYSTEM
                'id_usuario_sistema' => Auth::user()->id,
                'id_usuario_captura' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            $officeM::create($data);
            $logC->add('correspondencia.tbl_oficio', $data);
            //se itera el consevutivo
            $collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_OFICIO'));
            $collectionAreaM->iteratorConsecutivoAux($request->id_cat_anio, $request->id_cat_area_documento, 'correspondencia.rel_consecutivo_oficio');

            return $messagesC->messageSuccessRedirect('office.list', 'Elemento agregado con éxito.');

        } else { //modificar elemento 
            //Array
            $data = [
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_tbl_correspondencia' => $request->id_tbl_correspondencia,
                'es_por_area' => $es_por_area,
                'num_documento_area' => $request->num_documento_area,
                'id_cat_area_documento' => $request->id_cat_area_documento,
                'id_usuario_area' => $idUsuario,
                'id_usuario_enlace' => $idEnlace,
                'id_cat_area' => $idArea,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            //Actualizacion en db
            $officeM::where('id_tbl_oficio', $request->id_tbl_oficio)
                ->update($data);

            //Log app
            $data['id_tbl_oficio'] = $request->id_tbl_oficio;
            $logC->edit('correspondencia.tbl_oficio', $data);

            return $messagesC->messageSuccessRedirect('office.list', 'Elemento modificado con éxito.');
        }
    }

    //La función valida que el folio de gestión sea unico, para los oficios
    public function validateFol(Request $request)
    {
        $officeM = new OfficeM();
        $value = $officeM->uniqueFolGestion($request->id, $request->value);

        return response()->json([
            'value' => $value,
            'status' => true,
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
}
