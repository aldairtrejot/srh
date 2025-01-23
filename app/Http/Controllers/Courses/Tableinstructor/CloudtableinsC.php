<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Models\Courses\Cloud\CloudConsM;
use App\Models\Courses\Cloud\CloudConfigTableM;
use App\Models\Courses\Courses\Instructores\Instructores\InstructorM;
use App\Models\Courses\Cloud\CloudCvM;
use App\Http\Controllers\Cloud\AlfrescoC;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CloudtableinsC extends Controller
{
    public function cloudData(Request $request)
    {
        $id_tbl_cons = $request->id_tbl_cons;
        $instructorM = new InstructorM();
        $value = $instructorM ->dataCloud($id_tbl_cons);
        return response()->json([
            'value' => $value,
            'status' => true,
        ]);
    }

    //La funcion lista los documentos y trae la informacion para el cloud
    public function cloudCvs(Request $request)
    {
        $cloudConfigTableM = new CloudConfigTableM();
        //$collectionConfigCloudM = new CollectionConfigCloudM();
        //Constantes
        $CAT_TIPO_DOC_ENTRADA = config('custom_config.CAT_TIPO_DOC_ENTRADA');
        $CAT_TIPO_DOC_SALIDA = config('custom_config.CAT_TIPO_DOC_SALIDA');
        $MAX_CONS_ENTRADA = config('custom_config.MAX_CONS_ENTRADA');
        $MAX_CVS_ENTRADA = config('custom_config.MAX_CVS_ENTRADA');
        $MAX_CONS_SALIDA = config('custom_config.MAX_CONS_SALIDA');
        $MAX_CVS_SALIDA = config('custom_config.MAX_CVS_SALIDA');

        //$cvsEntrada = $cloudConfigTableM->listCvs($request->id_tbl_cons, $collectionConfigCloudM->getValue($MAX_CVS_ENTRADA), $CAT_TIPO_DOC_ENTRADA);
        //$consEntrada = $cloudConfigTableM->listCons($request->id_tbl_cons, $collectionConfigCloudM->getValue($MAX_CONS_ENTRADA), $CAT_TIPO_DOC_ENTRADA);
        //$cvSalida = $cloudConfigTableM->listCvs($request->id_tbl_cons, $collectionConfigCloudM->getValue($MAX_CVS_SALIDA), $CAT_TIPO_DOC_SALIDA);
        //$consSalida = $cloudConfigTableM->listCons($request->id_tbl_cons, $collectionConfigCloudM->getValue($MAX_CONS_SALIDA), $CAT_TIPO_DOC_SALIDA);
        //$resultConsEntrada = $cloudConfigTableM->conditionCons($collectionConfigCloudM->getValue($MAX_CONS_ENTRADA), $request->id_tbl_cons, $CAT_TIPO_DOC_ENTRADA);
        //$resultConsSalida = $cloudConfigTableM->conditionCons($collectionConfigCloudM->getValue($MAX_CONS_SALIDA), $request->id_tbl_cons, $CAT_TIPO_DOC_SALIDA);
        //$resultCvsEntrada = $cloudConfigTableM->conditionCvs($collectionConfigCloudM->getValue($MAX_CVS_ENTRADA), $request->id_tbl_cons, $CAT_TIPO_DOC_ENTRADA);
        //$resultCvsSalida = $cloudConfigTableM->conditionCvs($collectionConfigCloudM->getValue($MAX_CVS_SALIDA), $request->id_tbl_cons, $CAT_TIPO_DOC_SALIDA);

        return response()->json([
            //'cvsEntrada' => $cvsEntrada,
            //'consEntrada' => $consEntrada,
            //'cvSalida' => $cvSalida,
            //'consSalida' => $consSalida,
            //'resultConsEntrada' => $resultConsEntrada->valor,
            //'resultConsSalida' => $resultConsSalida->valor,
            //'resultCvsEntrada' => $resultCvsEntrada->valor,
            //'resultCvsSalida' => $resultCvsSalida->valor,
            //'status' => true,
        ]);
    }

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

            $fileName = 'CV_' . $file->getClientOriginalName(); // Nombre del archivo
            if ($request->esCons == 1) { //Validacion de archivo donde 1 se cambia el nombre por cons si no es cv
                $fileName = 'CONS_' . $file->getClientOriginalName(); // Nombre del archivo
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
                    $request->id_cat_tipo_cons
                );

                //Se carga el archivo a alfresco
                $result = $alfrescoC->addFile($file, $uid->uid, $request->esCons);

                if (!$result) { //Validacion de exito, se cargan las tablas 
                    $messages = "Se produjo un error inesperado al intentar subir el archivo: " . $result;
                } else {//Validacion de mensaje de error
                    if ($request->esCons == 1) { //Validacion para agregar en la tabla de cons
                        $data = [
                            'uid' => $result,
                            'nombre' => $fileName,
                            'estatus' => true,
                            'fecha_usuario' => $now,
                            'id_tbl_cons' => $request->id_tbl_cons,
                            'id_usuario_sistema' => Auth::user()->id,
                            'id_cat_tipo_doc_cloud' => $request->id_entrada_salida,
                        ];

                        CloudConsM::create($data);
                        $logC->add('correspondencia.ctrl_cons_cons', $data);
                    } else { //agregar en la tabla de cvs
                        $data = [
                            'uid' => $result,
                            'nombre' => $fileName,
                            'estatus' => true,
                            'fecha_usuario' => $now,
                            'id_tbl_cons' => $request->id_tbl_cons,
                            'id_usuario_sistema' => Auth::user()->id,
                            'id_cat_tipo_doc_cloud' => $request->id_entrada_salida,
                        ];
                        CloudCvM::create($data);
                        $logC->add('correspondencia.ctrl_cons_cv', $data);
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
        $logC = new LogC();
        $now = Carbon::now(); //Hora y fecha actual
        $cloudCvsM = new CloudCvM(); //aCTUALIACION DE CV POR UID
        $cloudConsM = new CloudConsM();
        $estatus = false;

        $data = [
            'estatus' => false,
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario' => $now,
        ];
        //update en base
        $resultCvs = $cloudCvsM::where('uid', $request->uid)
            ->update($data);

        $resultCons = $cloudConsM::where('uid', $request->uid)
            ->update($data);

        //UPDATE EN LOG
        $data['uid'] = $request->uid;

        if ($resultCvs > 0) {
            $logC->edit('correspondencia.ctrl_cons_cv', $data);
        } else if ($resultCons > 0) {
            $logC->edit('correspondencia.ctrl_cons_cons', $data);
        }

        $estatus = ($resultCvs > 0 || $resultCons > 0) ? true : false;


        return response()->json([
            'messages' => $estatus,
            'status' => true,
        ]);
    }
}