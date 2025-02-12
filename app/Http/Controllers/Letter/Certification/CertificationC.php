<?php

namespace App\Http\Controllers\Letter\Certification;

use App\Http\Controllers\Controller;
use App\Models\Letter\Certification\CertificationM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Cloud\AlfrescoC;
use App\Models\Letter\Cloud\CloudConfigM;
use App\Models\Letter\Collection\CollectionConfigCloudInternoM;
use App\Http\Controllers\Letter\Log\LogC;
use App\Http\Controllers\Admin\MessagesC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class CertificationC extends Controller
{
    // Retorna la vista para Notas de requerimiento
    public function list()
    {
        return view('letter/certification/list');
    }
    // La función retorna los valores para mostrar la tabla
    public function table(Request $request)
    {
        try {
            // Declaración de variables
            $certificationM = new CertificationM();
            $iterator = $request->iterator; // OFSET valor de paginador
            $searchValue = $request->searchValue; // Valor de búsqueda
            $value = $certificationM->list($iterator, $searchValue); // Llamamos al método list() con los parámetros necesarios

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

    // LA función sube el archivo a alfresco 
    public function saveFile(Request $request)
    {

        $logC = new LogC();
        $alfrescoC = new AlfrescoC();
        $cloudConfigM = new CloudConfigM();
        $certificationM = new CertificationM(); // Class Major
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
                $id_anio = $certificationM->getIdAnio($request->id); // Se obtiene el id de anio de archivo
                // Se obtienen el uuid de la carpeta donde se guardara el archivo
                $uuid = $collectionConfigCloudInternoM->getUuid($id_anio, config('custom_config.CP_TABLE_CERTIFICACIONES'));

                $result = $alfrescoC->add($file, $uuid); // Se sube el archivo a alfresco
                //Validacion
                if ($result) {// Manda el uuid para que se agregue a la tabla
                    $data = [
                        'uuid_pdf' => $result,
                        // Datos del sistema
                        'id_usuario_sistema' => Auth::user()->id,
                        'fecha_usuario' => $now,
                    ];

                    $certificationM::where('id_tbl_certificaciones', $request->id)
                        ->update($data);
                    $data['id_tbl_certificaciones'] = $request->id;
                    $logC->edit('correspondencia.tbl_certificaciones', $data);
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
        $certificationM = new CertificationM(); // Class Major
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

            $certificationM::where('uuid_pdf', $request->uuid)
                ->update($data);
            $data['uuid_pdf'] = $request->uuid;
            $logC->edit('correspondencia.tbl_certificaciones', $data);
            $status = true;
        }

        return response()->json([
            'status' => $status,
        ]);
    }

}

/*
<?php

namespace App\Http\Controllers\Letter\Informative;

use App\Http\Controllers\Controller;
use App\Models\Letter\Informative\InformativeM;
use Illuminate\Http\Request;
use App\Models\Letter\Collection\CollectionConsecutivoInternoM;
use App\Models\Letter\Collection\CollectionSolicitanteM;
use App\Models\Letter\Collection\CollectionDateM;
use App\Models\Letter\Collection\CollectionDestinatarioM;
use App\Http\Controllers\Letter\Log\LogC;
use App\Http\Controllers\Admin\MessagesC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Cloud\AlfrescoC;
use App\Models\Letter\Cloud\CloudConfigM;
use App\Models\Letter\Collection\CollectionConfigCloudInternoM;
class InformativeC extends Controller
{
    // Retorna la vista para Notas de requerimiento
    public function list()
    {
        return view('letter/informative/list');
    }



    public function create()
    {
        // Class
        $item = new InformativeM();
        $collectionDateM = new CollectionDateM();
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();
        $collectionSolicitanteM = new CollectionSolicitanteM();
        $collectionDestinatarioM = new CollectionDestinatarioM();

        //Definicion de variable de inicializacion
        $item->fecha_asignacion = now()->format('d/m/Y'); // Formato de fecha: día/mes/año
        $item->consecutivo = $collectionConsecutivoInternoM->noDocumento($collectionDateM->idYear(), config('custom_config.CP_TABLE_NOTAS_INTERNO'));

        // Declaración de catalogos
        $selectSolicitante = $collectionSolicitanteM->list();
        $selectSolicitanteEdit = [];

        // Declaración de catalogos
        $selectSolicitante_2 = $collectionSolicitanteM->list();
        $selectSolicitanteEdit_2 = [];

        $selectDestinatario = $collectionDestinatarioM->list();
        $selectDestinatarioEdit = [];

        return view('letter/informative/form', compact('selectDestinatarioEdit', 'selectDestinatario', 'selectSolicitanteEdit_2', 'selectSolicitante_2', 'selectSolicitanteEdit', 'selectSolicitante', 'item'));
    }

    // LA función guarda los datos 
    public function save(Request $request)
    {
        // Class
        $now = Carbon::now(); //Hora y fecha actual
        $messagesC = new MessagesC(); // Messages
        $logC = new LogC(); // Save data
        $informativeM = new InformativeM(); // Class Major
        $collectionDateM = new CollectionDateM(); // Date
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();

        if (!isset($request->id_tbl_notas_interno)) { // Agregar elemento

            // Se establece el consecutivo actual
            $request->consecutivo = $collectionConsecutivoInternoM->noDocumento($collectionDateM->idYear(), config('custom_config.CP_TABLE_NOTAS_INTERNO'));

            $data = [
                'consecutivo' => strtoupper($request->consecutivo),
                'fecha_asignacion' => $request->fecha_asignacion,//Carbon::createFromFormat('d/m/Y', $request->fecha_asignacion)->format('Y-m-d'),
                'fecha_documento' => $request->fecha_documento,
                'asunto' => strtoupper($request->asunto),
                'id_cat_solicitante' => $request->id_cat_solicitante,
                'id_cat_solicitante_2' => $request->id_cat_solicitante_2,
                'id_cat_destinatario' => $request->id_cat_destinatario,
                'estatus' => true,

                // Datos del sistema
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,

                // Datos de captura por primera vez
                'id_usuario_captura' => Auth::user()->id,
                'fecha_usuario_captura' => $now,
            ];

            // Crear el registro en la base de datos utilizando el arreglo
            $informativeM::create($data);

            // Opcional: Guardar el log con los valores insertados (si se necesita)
            $logC->add('correspondencia.tbl_notas_interno', $data);
            $collectionConsecutivoInternoM->iteratorConsecutivo($collectionDateM->idYear(), config('custom_config.CP_TABLE_NOTAS_INTERNO'));

            return $messagesC->messageSuccessRedirect('informative.list', 'Elemento agregado con éxito.');
        } else { // Modificar elemento
            $data = [
                'fecha_documento' => $request->fecha_documento,
                'asunto' => strtoupper($request->asunto),
                'id_cat_solicitante' => $request->id_cat_solicitante,
                'id_cat_solicitante_2' => $request->id_cat_solicitante_2,
                'id_cat_destinatario' => $request->id_cat_destinatario,

                // Datos del sistema
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];
            // Edit
            $informativeM::where('id_tbl_notas_interno', $request->id_tbl_notas_interno)
                ->update($data);
            $data['id_tbl_notas_interno'] = $request->id_tbl_notas_interno;
            $logC->edit('correspondencia.tbl_notas_interno', $data);

            return $messagesC->messageSuccessRedirect('informative.list', 'Elemento modificado con éxito.');

        }
    }

    // La función modifica
    public function edit($id)
    {
        // Class
        $informativeM = new InformativeM();
        $item = $informativeM->edit($id);
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();
        $collectionSolicitanteM = new CollectionSolicitanteM();
        $collectionDestinatarioM = new CollectionDestinatarioM();

        // Declaración de catalogos
        $selectSolicitante = $collectionSolicitanteM->list();
        $selectSolicitanteEdit = isset($item->id_cat_solicitante) ? $collectionSolicitanteM->edit($item->id_cat_solicitante) : [];

        // Declaración de catalogos
        $selectSolicitante_2 = $collectionSolicitanteM->list();
        $selectSolicitanteEdit_2 = isset($item->id_cat_solicitante_2) ? $collectionSolicitanteM->edit($item->id_cat_solicitante_2) : [];

        $selectDestinatario = $collectionDestinatarioM->list();
        $selectDestinatarioEdit = isset($item->id_cat_destinatario) ? $collectionDestinatarioM->edit($item->id_cat_destinatario) : [];

        return view('letter/informative/form', compact('selectDestinatarioEdit', 'selectDestinatario', 'selectSolicitanteEdit_2', 'selectSolicitante_2', 'selectSolicitanteEdit', 'selectSolicitante', 'item'));
    }

    // LA función sube el archivo a alfresco 
    public function saveFile(Request $request)
    {

        $logC = new LogC();
        $alfrescoC = new AlfrescoC();
        $cloudConfigM = new CloudConfigM();
        $informativeM = new InformativeM(); // Class Major
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
                $id_anio = $informativeM->getIdAnio($request->id); // Se obtiene el id de anio de archivo
                // Se obtienen el uuid de la carpeta donde se guardara el archivo
                $uuid = $collectionConfigCloudInternoM->getUuid($id_anio, config('custom_config.CP_TABLE_NOTAS_INTERNO'));

                $result = $alfrescoC->add($file, $uuid); // Se sube el archivo a alfresco
                //Validacion
                if ($result) {// Manda el uuid para que se agregue a la tabla
                    $data = [
                        'uuid_pdf' => $result,
                        // Datos del sistema
                        'id_usuario_sistema' => Auth::user()->id,
                        'fecha_usuario' => $now,
                    ];

                    $informativeM::where('id_tbl_notas_interno', $request->id)
                        ->update($data);
                    $data['id_tbl_notas_interno'] = $request->id;
                    $logC->edit('correspondencia.tbl_notas_interno', $data);
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
        $informativeM = new InformativeM(); // Class Major
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

            $informativeM::where('uuid_pdf', $request->uuid)
                ->update($data);
            $data['uuid_pdf'] = $request->uuid;
            $logC->edit('correspondencia.tbl_notas_interno', $data);
            $status = true;
        }

        return response()->json([
            'status' => $status,
        ]);
    }
}

*/