<?php

namespace App\Http\Controllers\Letter\Inside;
use App\Http\Controllers\Cloud\AlfrescoC;
use App\Models\Letter\Inside\CloudAnexosM;
use App\Models\Letter\Cloud\CloudConfigM;
use App\Models\Letter\Inside\CloudOficiosM;
use App\Models\Letter\Collection\CollectionConfigCloudM;
use App\Models\Letter\Inside\InsideM;
use App\Models\Letter\Inside\CloudM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CloudInsideC extends Controller
{
    //La funcion obtiene datos para el ebcabezado de la vista cloud
    public function cloudData(Request $request)
    {
        $id = $request->id;
        $item = new InsideM();
        $value = $item->dataCloud($id);
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
        $resultOficioEntrada = $cloudM->conditionOficios($collectionConfigCloudM->getValue($MAX_OFICIOS_ENTRADA), $request->id_tbl_oficio, $CAT_TIPO_DOC_ENTRADA);
        $resultOficioSalida = $cloudM->conditionOficios($collectionConfigCloudM->getValue($MAX_OFICIOS_SALIDA), $request->id_tbl_oficio, $CAT_TIPO_DOC_SALIDA);
        $resultAnexosEntrada = $cloudM->conditioAnexos($collectionConfigCloudM->getValue($MAX_ANEXOS_ENTRADA), $request->id_tbl_oficio, $CAT_TIPO_DOC_ENTRADA);
        $resultAnexosSalida = $cloudM->conditioAnexos($collectionConfigCloudM->getValue($MAX_ANEXOS_SALIDA), $request->id_tbl_oficio, $CAT_TIPO_DOC_SALIDA);

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

    public function upload(Request $request)
    {
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
                        CloudOficiosM::create([
                            'uid' => $result,
                            'nombre' => $fileName,
                            'estatus' => true,
                            'fecha_usuario' => $now,
                            'id_tbl_interno' => $request->id,
                            'id_usuario_sistema' => Auth::user()->id,
                            'id_cat_tipo_doc_cloud' => $request->id_entrada_salida,
                        ]);
                    } else { //agregar en la tabla de anexos
                        CloudAnexosM::create([
                            'uid' => $result,
                            'nombre' => $fileName,
                            'estatus' => true,
                            'fecha_usuario' => $now,
                            'id_tbl_interno' => $request->id,
                            'id_usuario_sistema' => Auth::user()->id,
                            'id_cat_tipo_doc_cloud' => $request->id_entrada_salida,
                        ]);
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
        $now = Carbon::now(); //Hora y fecha actual
        $cloudAnexosM = new CloudAnexosM(); //aCTUALIACION DE ANEXO POR UID
        $cloudOficiosM = new CloudOficiosM();
        $estatus = false;

        $resultAnexos = $cloudAnexosM::where('uid', $request->uid)
            ->update([
                'estatus' => false,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ]);
        $resultOficio = $cloudOficiosM::where('uid', $request->uid)
            ->update([
                'estatus' => false,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ]);

        $estatus = ($resultAnexos > 0 || $resultOficio > 0) ? true : false;


        return response()->json([
            'messages' => $estatus,
            'status' => true,
        ]);
    }


}
