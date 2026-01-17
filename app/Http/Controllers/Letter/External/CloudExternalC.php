<?php

namespace App\Http\Controllers\Letter\External;
use App\Models\Letter\Collection\CollectionConfigCloudM;
use App\Http\Controllers\Controller;
use App\Models\Letter\External\CloudAnexosM;
use App\Models\Letter\External\CloudM;
use App\Models\Letter\External\CloudOficiosM;
use App\Models\Letter\External\ExternalM;
use Illuminate\Http\Request;
use App\Http\Controllers\Cloud\AlfrescoC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Letter\Log\LogC;
use App\Models\Letter\Cloud\CloudConfigM;

class CloudExternalC extends Controller
{

    // La función retorna la vista principal de cloud, con encabezados de datos
    public function cloud($id)
    {
        $externalM = new ExternalM(); // Class
        $item = $externalM->getDataCloud($id); // Data
        return view('letter/external/cloud', compact('item')); // Views
    }

    // La función lista los oficios y anexos
    public function list(Request $request)
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

        $anexosEntrada = $cloudM->listAnexos($request->id, $collectionConfigCloudM->getValue($MAX_ANEXOS_ENTRADA), $CAT_TIPO_DOC_ENTRADA);
        $oficosEntrada = $cloudM->listOficios($request->id, $collectionConfigCloudM->getValue($MAX_OFICIOS_ENTRADA), $CAT_TIPO_DOC_ENTRADA);
        $anexoSalida = $cloudM->listAnexos($request->id, $collectionConfigCloudM->getValue($MAX_ANEXOS_SALIDA), $CAT_TIPO_DOC_SALIDA);
        $oficosSalida = $cloudM->listOficios($request->id, $collectionConfigCloudM->getValue($MAX_OFICIOS_SALIDA), $CAT_TIPO_DOC_SALIDA);
        $resultOficioEntrada = $cloudM->conditionOficios($collectionConfigCloudM->getValue($MAX_OFICIOS_ENTRADA), $request->id, $CAT_TIPO_DOC_ENTRADA);
        $resultOficioSalida = $cloudM->conditionOficios($collectionConfigCloudM->getValue($MAX_OFICIOS_SALIDA), $request->id, $CAT_TIPO_DOC_SALIDA);
        $resultAnexosEntrada = $cloudM->conditioAnexos($collectionConfigCloudM->getValue($MAX_ANEXOS_ENTRADA), $request->id, $CAT_TIPO_DOC_ENTRADA);
        $resultAnexosSalida = $cloudM->conditioAnexos($collectionConfigCloudM->getValue($MAX_ANEXOS_SALIDA), $request->id, $CAT_TIPO_DOC_SALIDA);

        return response()->json([
            'anexosEntrada' => $anexosEntrada,
            'oficosEntrada' => $oficosEntrada,
            'anexoSalida' => $anexoSalida,
            'oficosSalida' => $oficosSalida,
            'resultOficioEntrada' => $resultOficioEntrada->valor,
            'resultOficioSalida' => $resultOficioSalida->valor,
            'resultAnexosEntrada' => $resultAnexosEntrada->valor,
            'resultAnexosSalida' => $resultAnexosSalida->valor,
            'status' => true,
        ]);
    }

    // LA función sube contenido a Alfresco
    public function upload(Request $request)
    {
        $logC = new LogC();
        $alfrescoC = new AlfrescoC();
        $cloudConfigM = new CloudConfigM();
        $status = false;
        $messages = 'ok';
        $now = Carbon::now(); //Hora y fecha actual

        if ($request->hasFile('file') && $request->file('file')->isValid()) { // Verificar si el archivo ha sido cargado correctamente
            $file = $request->file('file');// Obtener el archivo cargado

            /*
            $fileName = 'ANEXO_' . $file->getClientOriginalName(); // Nombre del archivo
            if ($request->esOficio == 1) { //Validacion de archivo donde 1 se cambia el nombre por oficio si no es anexo
                $fileName = 'OFICIO_' . $file->getClientOriginalName(); // Nombre del archivo
            }
            */

            $nameFile = $file->getClientOriginalName();
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

                $idArea = 3; // Hace referencia a id = 3, porque no se turna a una area
                //La funcion obtiene el id de la carpeta donde se almacenara el archivo
                $uid = $cloudConfigM->getUid(
                    $idArea,
                    $request->id_entrada_salida,
                    $request->id_cat_tipo_oficio
                );

                //Se carga el archivo a alfresco
                $result = $alfrescoC->addFile($file, $uid->uid, $request->esOficio);

                if (!$result) { //Validacion de exito, se cargan las tablas 
                    $messages = "Se produjo un error inesperado al intentar subir el archivo: " . $result;
                } else {//Validacion de mensaje de error

                    $data = [
                        'uid' => $result,
                        'nombre' => $nameFile,
                        'estatus' => true,
                        'fecha_usuario' => $now,
                        'id_tbl_circular_externa' => $request->id,
                        'id_usuario_sistema' => Auth::user()->id,
                        'id_cat_tipo_doc_cloud' => $request->id_entrada_salida,
                    ];

                    if ($request->esOficio == 1) { //Validacion para agregar en la tabla de oficios
                        CloudOficiosM::create($data);
                        $logC->add('correspondencia.ctrl_circular_ext_oficio', $data);
                    } else { //agregar en la tabla de anexos
                        CloudAnexosM::create($data);
                        $logC->add('correspondencia.ctrl_circular_ext_anexo', $data);
                    }
                    $status = true;
                }
            }
        }

        return response()->json([
            'messages' => $messages,
            'status' => $status,
        ]);
    }

    public function delete(Request $request)
    {
        $logC = new LogC();
        $now = Carbon::now(); //Hora y fecha actual
        $cloudAnexosM = new CloudAnexosM(); //aCTUALIACION DE ANEXO POR UID
        $cloudOficiosM = new CloudOficiosM();
        $estatus = false;
        $alfrescoC = new AlfrescoC();

        $alfrescoC->delete($request->uid);

        $data = [
            'estatus' => false,
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario' => $now,
        ];
        //update en base
        $resultAnexos = $cloudAnexosM::where('uid', $request->uid)
            ->update($data);

        $resultOficio = $cloudOficiosM::where('uid', $request->uid)
            ->update($data);

        //UPDATE EN LOG
        $data['uid'] = $request->uid;

        if ($resultAnexos > 0) {
            $logC->edit('correspondencia.ctrl_circular_ext_anexo', $data);
        } else if ($resultOficio > 0) {
            $logC->edit('correspondencia.ctrl_circular_ext_oficio', $data);
        }

        $estatus = ($resultAnexos > 0 || $resultOficio > 0) ? true : false;


        return response()->json([
            'messages' => $estatus,
            'status' => true,
        ]);
    }

}
