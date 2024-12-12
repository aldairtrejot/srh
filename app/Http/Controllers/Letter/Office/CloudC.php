<?php

namespace App\Http\Controllers\Letter\Office;
use App\Http\Controllers\Cloud\AlfrescoC;
use App\Models\Letter\Cloud\CloudConfigM;
use App\Models\Letter\Collection\CollectionConfigCloudM;
use App\Models\Letter\Office\CloudM;
use App\Models\Letter\Office\OfficeM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\MessagesC;
class CloudC extends Controller
{
    //La funcion obtiene datos para el ebcabezado de la vista cloud
    public function cloudData(Request $request)
    {
        $id_tbl_oficio = $request->id_tbl_oficio;
        $officeM = new OfficeM();
        $value = $officeM->dataCloud($id_tbl_oficio);
        return response()->json([
            'value' => $value,
            'status' => true,
        ]);
    }

    //La funcion lista los documentos y trae la informacion para el cloud
    public function cloudAnexos(Request $request)
    {
        $cloudM = new CloudM();
        $collectionConfigCloudM = new CollectionConfigCloudM();
        //Constantes
        $CAT_TIPO_DOC_ENTRADA = config('custom_config.CAT_TIPO_DOC_ENTRADA');
        $CAT_TIPO_DOC_SALIDA = config('custom_config.CAT_TIPO_DOC_SALIDA');
        $MAX_OFICIOS_ENTRADA = config('custom_config.MAX_OFICIOS_ENTRADA');
        $MAX_ANEXOS_ENTRADA = config('custom_config.MAX_ANEXOS_ENTRADA');
        $MAX_OFICIOS_SALIDA = config('custom_config.MAX_OFICIOS_SALIDA');
        $MAX_ANEXOS_SALIDA = config('custom_config.MAX_ANEXOS_SALIDA');

        $anexosEntrada = $cloudM->listAnexos($request->id_tbl_oficio, $collectionConfigCloudM->getValue($MAX_ANEXOS_ENTRADA), $CAT_TIPO_DOC_ENTRADA);
        $oficosEntrada = $cloudM->listOficios($request->id_tbl_oficio, $collectionConfigCloudM->getValue($MAX_OFICIOS_ENTRADA), $CAT_TIPO_DOC_ENTRADA);
        $anexoSalida = $cloudM->listAnexos($request->id_tbl_oficio, $collectionConfigCloudM->getValue($MAX_ANEXOS_SALIDA), $CAT_TIPO_DOC_SALIDA);
        $oficosSalida = $cloudM->listOficios($request->id_tbl_oficio, $collectionConfigCloudM->getValue($MAX_OFICIOS_SALIDA), $CAT_TIPO_DOC_SALIDA);

        return response()->json([
            'anexosEntrada' => $anexosEntrada,
            'oficosEntrada' => $oficosEntrada,
            'anexoSalida' => $anexoSalida,
            'oficosSalida' => $oficosSalida,
            'status' => true,
        ]);
    }

    public function upload(Request $request)
    {
        //$alfrescoC = new AlfrescoC();
        $cloudConfigM = new CloudConfigM();
        $status = false;
        $messages = 'ok';

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
                
                //return $messagesC->messageErrorBack('Tamaño máximo de archivo admitido: $maxSize MB.');
                /*
                                $iud = '61a24bc5-7569-4dcb-83d4-3055c289d8ea';

                                $status = $alfrescoC->addFile($file, $iud);
                                */


            }
        }

        return response()->json([
            'messages' => $messages,
            'status' => $status,
        ]);
    }

    public function saveDocument()
    {

    }
}
