<?php

namespace App\Http\Controllers\Letter\Letter;
use App\Http\Controllers\Cloud\AlfrescoC;
use App\Models\Letter\Letter\CloudAnexosM;
use App\Models\Letter\Cloud\CloudConfigM;
use App\Models\Letter\Letter\CloudOficiosM;
use App\Models\Letter\Collection\CollectionConfigCloudM;
use App\Models\Letter\Letter\LetterM;
use App\Models\Letter\Letter\CloudM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Letter\Log\LogC;

class CloudLetterC extends Controller
{
    //La funcion obtiene datos para el ebcabezado de la vista cloud
    public function cloudData(Request $request)
    {
        $id = $request->id;
        $object = new LetterM();
        $value = $object->dataCloud($id);
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
        $MAX_OFICIOS_ENTRADA = config('custom_config.MAX_OFICIOS_ENTRADA');
        $MAX_ANEXOS_ENTRADA = config('custom_config.MAX_ANEXOS_ENTRADA');

        $anexosEntrada = $cloudM->listAnexos($request->id_tbl_oficio, $collectionConfigCloudM->getValue($MAX_ANEXOS_ENTRADA), $CAT_TIPO_DOC_ENTRADA);
        $oficosEntrada = $cloudM->listOficios($request->id_tbl_oficio, $collectionConfigCloudM->getValue($MAX_OFICIOS_ENTRADA), $CAT_TIPO_DOC_ENTRADA);
        $resultOficioEntrada = $cloudM->conditionOficios($collectionConfigCloudM->getValue($MAX_OFICIOS_ENTRADA), $request->id_tbl_oficio, $CAT_TIPO_DOC_ENTRADA);
        $resultAnexosEntrada = $cloudM->conditioAnexos($collectionConfigCloudM->getValue($MAX_ANEXOS_ENTRADA), $request->id_tbl_oficio, $CAT_TIPO_DOC_ENTRADA);

        return response()->json([
            'anexosEntrada' => $anexosEntrada,
            'oficosEntrada' => $oficosEntrada,
            'resultOficioEntrada' => $resultOficioEntrada->valor,
            'resultAnexosEntrada' => $resultAnexosEntrada->valor,
            'status' => true,
        ]);
    }

    public function upload(Request $request)
    {
        $logC = new LogC;
        $alfrescoC = new AlfrescoC();
        $cloudConfigM = new CloudConfigM();
        $status = false;
        $messages = 'ok';
        $now = Carbon::now(); //Hora y fecha actual

        if ($request->hasFile('file') && $request->file('file')->isValid()) { // Verificar si el archivo ha sido cargado correctamente
            $file = $request->file('file');// Obtener el archivo cargado

            $fileName = 'ANEXO_' . $file->getClientOriginalName(); // Nombre del archivo
            if ($request->esOficio == 1) { //Validacion de archivo donde 1 se cambia el nombre por oficio si no es anexo
                $fileName = 'OFICIO_' . $file->getClientOriginalName(); // Nombre del archivo
            }

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

                //La funcion obtiene el id de la carpeta donde se almacenara el archivo
                $uid = $cloudConfigM->getUid(
                    $request->id_cat_area,
                    $request->id_entrada_salida,
                    $request->id_cat_tipo_oficio
                );

                //Se carga el archivo a alfresco
                $result = $alfrescoC->addFile($file, $uid->uid, $request->esOficio);

                if (!$result) { //Validacion de exito, se cargan las tablas 
                    $messages = "Se produjo un error inesperado al intentar subir el archivo: " . $result;
                } else {//Validacion de mensaje de error
                    if ($request->esOficio == 1) { //Validacion para agregar en la tabla de oficios
                        $data = [
                            'uid' => $result,
                            'nombre' => $fileName,
                            'estatus' => true,
                            'fecha_usuario' => $now,
                            'id_tbl_correspondencia' => $request->id,
                            'id_usuario_sistema' => Auth::user()->id,
                            'id_cat_tipo_doc_cloud' => $request->id_entrada_salida,
                        ];

                        CloudOficiosM::create($data);
                        $logC->add('correspondencia.ctrl_correspondencia_oficio', $data);

                    } else { //agregar en la tabla de anexos
                        $data = [
                            'uid' => $result,
                            'nombre' => $fileName,
                            'estatus' => true,
                            'fecha_usuario' => $now,
                            'id_tbl_correspondencia' => $request->id,
                            'id_usuario_sistema' => Auth::user()->id,
                            'id_cat_tipo_doc_cloud' => $request->id_entrada_salida,
                        ];

                        CloudAnexosM::create($data);
                        $logC->add('correspondencia.ctrl_correspondencia_anexo', $data);
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

    //LA funcion actualiza/elimina los registros para que no aparescan en la pantalla de vista de cloud
    public function delete(Request $request)
    {
        $logC = new LogC;
        $now = Carbon::now(); //Hora y fecha actual
        $cloudAnexosM = new CloudAnexosM(); //aCTUALIACION DE ANEXO POR UID
        $cloudOficiosM = new CloudOficiosM();
        $estatus = false;

        $alfrescoC = new AlfrescoC();

        //Borrado de alfresco
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
            $logC->edit('correspondencia.ctrl_correspondencia_anexo', $data);
        } else if ($resultOficio > 0) {
            $logC->edit('correspondencia.ctrl_correspondencia_oficio', $data);
        }


        $estatus = ($resultAnexos > 0 || $resultOficio > 0) ? true : false;



        return response()->json([
            'messages' => $estatus,
            'status' => true,
        ]);
    }


}
