<?php

namespace App\Http\Controllers\Letter\Request;

use App\Models\Letter\Collection\CollectionConsecutivoInternoM;
use App\Models\Letter\Collection\CollectionSolicitanteM;
use App\Http\Controllers\Controller;
use App\Models\Letter\Request\RequestM;
use Illuminate\Http\Request;
use App\Models\Letter\Collection\CollectionDateM;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Letter\Log\LogC;
use App\Http\Controllers\Admin\MessagesC;
use Carbon\Carbon;
use App\Http\Controllers\Cloud\AlfrescoC;
use App\Models\Letter\Cloud\CloudConfigM;
use App\Models\Letter\Collection\CollectionConfigCloudInternoM;
use Illuminate\Support\Facades\Log;
class RequestC extends Controller
{
    // Retorna la vista para Notas de requerimiento
    public function list()
    {
        return view('letter/request/list');
    }

    // La función retorna los valores para mostrar la tabla
    public function table(Request $request)
    {
        try {
            // Declaración de variables
            $requestM = new RequestM();
            $iterator = $request->iterator; // OFSET valor de paginador
            $searchValue = $request->searchValue; // Valor de búsqueda
            $value = $requestM->list($iterator, $searchValue); // Llamamos al método list() con los parámetros necesarios

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
        // Class
        $item = new RequestM();
        $collectionDateM = new CollectionDateM();
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();
        $collectionSolicitanteM = new CollectionSolicitanteM();

        //Definicion de variable de inicializacion
        $item->fecha_asignacion = now()->format('d/m/Y'); // Formato de fecha: día/mes/año
        $item->consecutivo = $collectionConsecutivoInternoM->noDocumento($collectionDateM->idYear(), config('custom_config.CP_TABLE_REQUERIMRNTOS_INTERNO'));

        // Declaración de catalogos
        $selectSolicitante = $collectionSolicitanteM->list();
        $selectSolicitanteEdit = [];

        return view('letter/request/form', compact('selectSolicitanteEdit', 'selectSolicitante', 'item'));
    }

    // La función modifica
    public function edit($id)
    {
        // Class
        $requestM = new RequestM();
        $item = $requestM->edit($id);
        $collectionDateM = new CollectionDateM();
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();
        $collectionSolicitanteM = new CollectionSolicitanteM();

        // Declaración de catalogos
        $selectSolicitante = $collectionSolicitanteM->list();
        $selectSolicitanteEdit = isset($item->id_cat_solicitante) ? $collectionSolicitanteM->edit($item->id_cat_solicitante) : [];

        return view('letter/request/form', compact('selectSolicitanteEdit', 'selectSolicitante', 'item'));
    }
    // LA función guarda los datos 
    public function save(Request $request)
    {
        // Class
        $now = Carbon::now(); //Hora y fecha actual
        $messagesC = new MessagesC(); // Messages
        $logC = new LogC(); // Save data
        $requestM = new RequestM(); // Class Major
        $collectionDateM = new CollectionDateM(); // Date
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();

        if (!isset($request->id_tbl_requerimiento_interno)) { // Agregar elemento

            // Se establece el consecutivo actual
            $request->consecutivo = $collectionConsecutivoInternoM->noDocumento($collectionDateM->idYear(), config('custom_config.CP_TABLE_REQUERIMRNTOS_INTERNO'));

            $data = [
                'consecutivo' => strtoupper($request->consecutivo),
                'fecha_asignacion' => $request->fecha_asignacion,//Carbon::createFromFormat('d/m/Y', $request->fecha_asignacion)->format('Y-m-d'),
                'fecha_documento' => $request->fecha_documento,
                'fecha_termino' => $request->fecha_termino,
                'observaciones' => strtoupper($request->observaciones),
                'asunto' => strtoupper($request->asunto),
                'id_cat_solicitante' => $request->id_cat_solicitante,
                'estatus' => true,

                // Datos del sistema
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,

                // Datos de captura por primera vez
                'id_usuario_captura' => Auth::user()->id,
                'fecha_usuario_captura' => $now,
            ];

            // Crear el registro en la base de datos utilizando el arreglo
            $requestM::create($data);

            // Opcional: Guardar el log con los valores insertados (si se necesita)
            $logC->add('correspondencia.tbl_requerimiento_interno', $data);
            $collectionConsecutivoInternoM->iteratorConsecutivo($collectionDateM->idYear(), config('custom_config.CP_TABLE_REQUERIMRNTOS_INTERNO'));

            return $messagesC->messageSuccessRedirect('request.list', 'Elemento agregado con éxito.');
        } else { // Modificar elemento
            $data = [
                'fecha_documento' => $request->fecha_documento,
                'fecha_termino' => $request->fecha_termino,
                'observaciones' => strtoupper($request->observaciones),
                'asunto' => strtoupper($request->asunto),
                'id_cat_solicitante' => $request->id_cat_solicitante,

                // Datos del sistema
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];
            // Edit
            $requestM::where('id_tbl_requerimiento_interno', $request->id_tbl_requerimiento_interno)
                ->update($data);
            $data['tbl_requerimiento_interno'] = $request->id_tbl_requerimiento_interno;
            $logC->edit('correspondencia.tbl_requerimiento_interno', $data);

            return $messagesC->messageSuccessRedirect('request.list', 'Elemento modificado con éxito.');

        }
    }

    // LA función sube el archivo a alfresco 
    public function saveFile(Request $request)
    {

        $logC = new LogC();
        $alfrescoC = new AlfrescoC();
        $cloudConfigM = new CloudConfigM();
        $requestM = new RequestM(); // Class Major
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
                $id_anio = $requestM->getIdAnio($request->id); // Se obtiene el id de anio de archivo
                Log::info($id_anio);
                // Se obtienen el uuid de la carpeta donde se guardara el archivo
                $uuid = $collectionConfigCloudInternoM->getUuid($id_anio, config('custom_config.CP_TABLE_REQUERIMRNTOS_INTERNO'));

                $result = $alfrescoC->add($file, $uuid); // Se sube el archivo a alfresco
                //Validacion
                if ($result) {// Manda el uuid para que se agregue a la tabla
                    $data = [
                        'uuid_pdf' => $result,
                        // Datos del sistema
                        'id_usuario_sistema' => Auth::user()->id,
                        'fecha_usuario' => $now,
                    ];

                    $requestM::where('id_tbl_requerimiento_interno', $request->id)
                        ->update($data);
                    $data['id_tbl_requerimiento_interno'] = $request->id;
                    $logC->edit('correspondencia.tbl_requerimiento_interno', $data);
                    $status = true;

                }
            }
        }


        return response()->json([
            'status' => $status,
            'messages' => $messages,
        ]);
    }

    // La función elimina el archivo de alfresco
    public function deleteFile(Request $request)
    {
        // Class
        $logC = new LogC(); // Save data
        $alfrescoC = new AlfrescoC();
        $requestM = new RequestM(); // Class Major
        $now = Carbon::now(); //Hora y fecha actual

        // Eliminar doc de alfresco
        // La variable status obtiene verdadero o falso si es que se elimina el archivo por su uuid
        $status = $alfrescoC->delete($request->uuid);

        if ($status) { // Se elimino con éxito por lo tanto se actualiza de la tabla como null
            $data = [
                'uuid_pdf' => NULL,
                // Datos del sistema
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            $requestM::where('uuid_pdf', $request->uuid)
                ->update($data);
            $data['uuid_pdf'] = $request->uuid;
            $logC->edit('correspondencia.tbl_requerimiento_interno', $data);
            $status = true;
        }

        return response()->json([
            'status' => $status,
        ]);
    }
}
