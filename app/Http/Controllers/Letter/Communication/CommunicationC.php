<?php

namespace App\Http\Controllers\Letter\Communication;

use App\Http\Controllers\Cloud\AlfrescoC;
use App\Models\Administration\UserM;
use App\Models\Letter\Collection\CollectionAreaInternoM;
use App\Models\Letter\Collection\CollectionConfigCloudInternoM;
use App\Models\Letter\Collection\CollectionConsecutivoInternoM;
use App\Models\Letter\Collection\CollectionDateM;
use App\Models\Letter\Collection\CollectionDestinatarioM;
use App\Models\Letter\Collection\CollectionEntidadM;
use App\Http\Controllers\Controller;
use App\Models\Letter\Collection\CollectionSolicitanteM;
use App\Models\Letter\Collection\CollectionTemaM;
use App\Models\Letter\Communication\CommunicationM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Letter\Log\LogC;
use App\Http\Controllers\Admin\MessagesC;
use Carbon\Carbon;
use App\Models\Letter\Cloud\CloudConfigM;


use Illuminate\Support\Facades\Log;
// Hace referencia correspondencia interna
class CommunicationC extends Controller
{
    // Retorna la vista para correspondencia
    public function list()
    {
        return view('letter/communication/list');
    }

    // Retorna la vista de create
    public function create()
    {
        // Class
        $item = new CommunicationM();
        $collectionEntidadM = new CollectionEntidadM();
        $collectionDateM = new CollectionDateM();
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();
        $collectionTemaM = new CollectionTemaM();
        $collectionSolicitanteM = new CollectionSolicitanteM();
        $collectionAreaInternoM = new CollectionAreaInternoM();
        $collectionDestinatarioM = new CollectionDestinatarioM();

        //Definicion de variable de inicializacion
        $item->fecha_asignacion = now()->format('d/m/Y'); // Formato de fecha: día/mes/año
        $nameUser = Auth::user()->name; // Nombre de usuario
        $nomArea = ' _'; // Inicio de variables
        $item->consecutivo = $collectionConsecutivoInternoM->getMaxConsecutivo(config('custom_config.CP_TABLE_CORRESPONDENCIA_INTERNO'), $collectionDateM->idYear())->iterator;

        // Declaración de catalogos
        $selectEntidad = $collectionEntidadM->list();
        $selectEntidadEdit = [];

        $selectTema = $collectionTemaM->list();
        $selectTemaEdit = [];

        $selectSolicitante = $collectionSolicitanteM->list();
        $selectSolicitanteEdit = [];

        $selectArea = $collectionAreaInternoM->list();
        $selectAreaEdit = [];

        $selectDestinatario = $collectionDestinatarioM->list();
        $selectDestinatarioEdit = [];


        return view('letter/communication/form', compact('selectDestinatarioEdit', 'selectDestinatario', 'selectAreaEdit', 'selectArea', 'selectSolicitanteEdit', 'selectSolicitante', 'selectTemaEdit', 'selectTema', 'nomArea', 'nameUser', 'selectEntidadEdit', 'selectEntidad', 'item'));
    }

    // La función modifica
    public function edit($id)
    {
        // Class
        $communicationM = new CommunicationM();
        $collectionEntidadM = new CollectionEntidadM();
        $collectionDateM = new CollectionDateM();
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();
        $collectionTemaM = new CollectionTemaM();
        $collectionSolicitanteM = new CollectionSolicitanteM();
        $collectionAreaInternoM = new CollectionAreaInternoM();
        $collectionDestinatarioM = new CollectionDestinatarioM();
        $userM = new UserM();
        $collectionAreaInternoM = new CollectionAreaInternoM();

        //Definicion de variable de inicializacion
        $item = $communicationM->edit($id);
        $nameUser = $userM->getName($item->id_usuario);
        $nomArea = $collectionAreaInternoM->getClave($item->id_cat_area_interno);


        // Declaración de catalogos
        $selectEntidad = $collectionEntidadM->list();
        $selectEntidadEdit = isset($item->id_cat_entidad) ? $collectionEntidadM->edit($item->id_cat_entidad) : [];

        $selectTema = $collectionTemaM->list();
        $selectTemaEdit = isset($item->id_cat_tema) ? $collectionTemaM->edit($item->id_cat_tema) : [];

        $selectSolicitante = $collectionSolicitanteM->list();
        $selectSolicitanteEdit = isset($item->id_cat_solicitante) ? $collectionSolicitanteM->edit($item->id_cat_solicitante) : [];

        $selectArea = $collectionAreaInternoM->list();
        $selectAreaEdit = isset($item->id_cat_area_interno) ? $collectionAreaInternoM->edit($item->id_cat_area_interno) : [];

        $selectDestinatario = $collectionDestinatarioM->list();
        $selectDestinatarioEdit = isset($item->id_cat_destinatario) ? $collectionDestinatarioM->edit($item->id_cat_destinatario) : [];


        return view('letter/communication/form', compact('selectDestinatarioEdit', 'selectDestinatario', 'selectAreaEdit', 'selectArea', 'selectSolicitanteEdit', 'selectSolicitante', 'selectTemaEdit', 'selectTema', 'nomArea', 'nameUser', 'selectEntidadEdit', 'selectEntidad', 'item'));
    }

    // La función retorna los valores para mostrar la tabla
    public function table(Request $request)
    {
        try {
            // Declaración de variables
            $communicationM = new CommunicationM();
            $iterator = $request->iterator; // OFSET valor de paginador
            $searchValue = $request->searchValue; // Valor de búsqueda
            $value = $communicationM->list($iterator, $searchValue); // Llamamos al método list() con los parámetros necesarios

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

    // LA función guarda los datos 
    public function save(Request $request)
    {
        // Class
        $now = Carbon::now(); //Hora y fecha actual
        $messagesC = new MessagesC(); // Messages
        $logC = new LogC(); // Save data
        $communicationM = new CommunicationM(); // Class Major
        $collectionDateM = new CollectionDateM(); // Date
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();

        if (!isset($request->id_tbl_correspondencia_interno)) { // Agregar elemento

            // Se establece el consecutivo actual
            $request->consecutivo = $collectionConsecutivoInternoM->getMaxConsecutivo(config('custom_config.CP_TABLE_CORRESPONDENCIA_INTERNO'), $collectionDateM->idYear())->iterator;

            $data = [
                'consecutivo' => strtoupper($request->consecutivo),
                'fecha_asignacion' => $request->fecha_asignacion,//Carbon::createFromFormat('d/m/Y', $request->fecha_asignacion)->format('Y-m-d'),
                'cargo_destinatario' => strtoupper($request->cargo_destinatario),
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_usuario' => Auth::user()->id,
                'id_cat_area_interno' => $request->id_cat_area_interno,
                'id_cat_solicitante' => $request->id_cat_solicitante,
                'id_cat_destinatario' => $request->id_cat_destinatario,
                'id_cat_tema' => $request->id_cat_tema,
                'id_cat_entidad' => $request->id_cat_entidad,
                'estatus' => true,

                // Datos del sistema
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,

                // Datos de captura por primera vez
                'id_usuario_captura' => Auth::user()->id,
                'fecha_usuario_captura' => $now,
            ];

            // Crear el registro en la base de datos utilizando el arreglo
            $communicationM::create($data);

            // Opcional: Guardar el log con los valores insertados (si se necesita)
            $logC->add('correspondencia.tbl_correspondencia_interno', $data);
            $collectionConsecutivoInternoM->iteratorConsecutivo($collectionDateM->idYear(), config('custom_config.CP_TABLE_CORRESPONDENCIA_INTERNO'));

            return $messagesC->messageSuccessRedirect('communication.list', 'Elemento agregado con éxito.');
        } else { // Modificar elemento
            $data = [
                'cargo_destinatario' => strtoupper($request->cargo_destinatario),
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_cat_area_interno' => $request->id_cat_area_interno,
                'id_cat_solicitante' => $request->id_cat_solicitante,
                'id_cat_destinatario' => $request->id_cat_destinatario,
                'id_cat_tema' => $request->id_cat_tema,
                'id_cat_entidad' => $request->id_cat_entidad,

                // Datos del sistema
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            // Edit
            $communicationM::where('id_tbl_correspondencia_interno', $request->id_tbl_correspondencia_interno)
                ->update($data);
            $data['id_tbl_correspondencia_interno'] = $request->id_tbl_correspondencia_interno;
            $logC->edit('correspondencia.tbl_correspondencia_interno', $data);

            return $messagesC->messageSuccessRedirect('communication.list', 'Elemento modificado con éxito.');

        }
    }

    // La funcion elimina el archivo de alfresco y actualiza la tabla;
    public function updateOficio(Request $request)
    {
        // Class
        $logC = new LogC(); // Save data
        $alfrescoC = new AlfrescoC();
        $communicationM = new CommunicationM(); // Class Major
        $now = Carbon::now(); //Hora y fecha actual

        // Eliminar doc de alfresco
        // La variable status obtiene verdadero o falso si es que se elimina el archivo por su uuid
        $status = $alfrescoC->delete($request->uuid);

        if ($status) { // Se elimino con éxito por lo tanto se actualiza de la tabla como null
            $data = [
                'uuid_oficio' => NULL,
                // Datos del sistema
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            $communicationM::where('uuid_oficio', $request->uuid)
                ->update($data);
            $data['uuid_oficio'] = $request->uuid;
            $logC->edit('correspondencia.tbl_correspondencia_interno', $data);
            $status = true;
        }

        return response()->json([
            'status' => $status,
        ]);
    }

    // La funcion elimina el archivo de alfresco y actualiza la tabla;
    public function updateAcuse(Request $request)
    {
        // Class
        $logC = new LogC(); // Save data
        $alfrescoC = new AlfrescoC();
        $communicationM = new CommunicationM(); // Class Major
        $now = Carbon::now(); //Hora y fecha actual

        // Eliminar doc de alfresco
        // La variable status obtiene verdadero o falso si es que se elimina el archivo por su uuid
        $status = $alfrescoC->delete($request->uuid);

        if ($status) { // Se elimino con éxito por lo tanto se actualiza de la tabla como null
            $data = [
                'uuid_acuse' => NULL,
                // Datos del sistema
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            $communicationM::where('uuid_acuse', $request->uuid)
                ->update($data);
            $data['uuid_acuse'] = $request->uuid;
            $logC->edit('correspondencia.tbl_correspondencia_interno', $data);
            $status = true;
        }

        return response()->json([
            'status' => $status,
        ]);
    }

    // LA función sube el archivo a alfresco 
    public function addOficio(Request $request)
    {

        $logC = new LogC();
        $alfrescoC = new AlfrescoC();
        $cloudConfigM = new CloudConfigM();
        $communicationM = new CommunicationM(); // Class Major
        $collectionConfigCloudInternoM = new CollectionConfigCloudInternoM();

        //Value
        $now = Carbon::now(); //Hora y fecha actual
        $messages = 'Se presentó un error en el proceso.';
        $status = false;

        if ($request->hasFile('file') && $request->file('file')->isValid()) { // Verificar si el archivo ha sido cargado correctamente
            $file = $request->file('file');// Obtener el archivo cargado

            $extensionArchivo = $file->getClientOriginalExtension();// Obtener la extensión del archivo
            $tamanoArchivoMB = $file->getSize() / 1024 / 1024; // Convertir a MB

            $maxSize = $cloudConfigM->getData(config('custom_config.MAX_SIZE_ARCHIVO'));
            $fileExtension = $cloudConfigM->getData(config('custom_config.EXTENSIONES_VALIDAS'));
            $validExtensions = explode(',', $fileExtension->valor);// Convertimos la cadena de extensiones válidas en un array

            if ($tamanoArchivoMB > $maxSize->valor) { //Validacion por tamaño maximo de archivo
                $messages = 'Tamaño máximo de archivo admitido: ' . $maxSize->valor . ' MB';//. $maxSize . ' MB.';
            } else if (!in_array($extensionArchivo, $validExtensions)) { //Validacion de extensiones
                $messages = 'Las extensiones permitidas son : ' . $fileExtension->valor;
            } else {
                // Agregar archivo, pero se obtienen el uid de la carpeta asi como el año del documento
                $id_anio = $communicationM->getIdAnio($request->id); // Se obtiene el id de anio de archivo
                // Se obtienen el uuid de la carpeta donde se guardara el archivo
                $uuid = $collectionConfigCloudInternoM->getUuid($id_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA_INTERNO'));

                $result = $alfrescoC->add($file, $uuid); // Se sube el archivo a alfresco
                log::info($result);
                //Validacion
                if ($result) {// Manda el uuid para que se agregue a la tabla
                    $data = [
                        'uuid_oficio' => $result,
                        // Datos del sistema
                        'id_usuario_sistema' => Auth::user()->id,
                        'fecha_usuario' => $now,
                    ];

                    $communicationM::where('id_tbl_correspondencia_interno', $request->id)
                        ->update($data);
                    $data['id_tbl_correspondencia_interno'] = $request->id;
                    $logC->edit('correspondencia.tbl_correspondencia_interno', $data);
                    $status = true;

                }
            }
        }


        return response()->json([
            'status' => $status,
            'messages' => $messages,
        ]);
    }

    // LA función sube el archivo a alfresco  -> ACUSE
    public function addAcuse(Request $request)
    {

        $logC = new LogC();
        $alfrescoC = new AlfrescoC();
        $cloudConfigM = new CloudConfigM();
        $communicationM = new CommunicationM(); // Class Major
        $collectionConfigCloudInternoM = new CollectionConfigCloudInternoM();

        //Value
        $now = Carbon::now(); //Hora y fecha actual
        $messages = 'Se presentó un error en el proceso.';
        $status = false;

        if ($request->hasFile('file') && $request->file('file')->isValid()) { // Verificar si el archivo ha sido cargado correctamente
            $file = $request->file('file');// Obtener el archivo cargado

            $extensionArchivo = $file->getClientOriginalExtension();// Obtener la extensión del archivo
            $tamanoArchivoMB = $file->getSize() / 1024 / 1024; // Convertir a MB

            $maxSize = $cloudConfigM->getData(config('custom_config.MAX_SIZE_ARCHIVO'));
            $fileExtension = $cloudConfigM->getData(config('custom_config.EXTENSIONES_VALIDAS'));
            $validExtensions = explode(',', $fileExtension->valor);// Convertimos la cadena de extensiones válidas en un array

            if ($tamanoArchivoMB > $maxSize->valor) { //Validacion por tamaño maximo de archivo
                $messages = 'Tamaño máximo de archivo admitido: ' . $maxSize->valor . ' MB';//. $maxSize . ' MB.';
            } else if (!in_array($extensionArchivo, $validExtensions)) { //Validacion de extensiones
                $messages = 'Las extensiones permitidas son : ' . $fileExtension->valor;
            } else {
                // Agregar archivo, pero se obtienen el uid de la carpeta asi como el año del documento
                $id_anio = $communicationM->getIdAnio($request->id); // Se obtiene el id de anio de archivo
                // Se obtienen el uuid de la carpeta donde se guardara el archivo
                $uuid = $collectionConfigCloudInternoM->getUuid($id_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA_INTERNO'));

                $result = $alfrescoC->add($file, $uuid); // Se sube el archivo a alfresco
                log::info($result);
                //Validacion
                if ($result) {// Manda el uuid para que se agregue a la tabla
                    $data = [
                        'uuid_acuse' => $result,
                        // Datos del sistema
                        'id_usuario_sistema' => Auth::user()->id,
                        'fecha_usuario' => $now,
                    ];

                    $communicationM::where('id_tbl_correspondencia_interno', $request->id)
                        ->update($data);
                    $data['id_tbl_correspondencia_interno'] = $request->id;
                    $logC->edit('correspondencia.tbl_correspondencia_interno', $data);
                    $status = true;

                }
            }
        }


        return response()->json([
            'status' => $status,
            'messages' => $messages,
        ]);
    }
}
