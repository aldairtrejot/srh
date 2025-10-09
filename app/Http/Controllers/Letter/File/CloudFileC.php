<?php

namespace App\Http\Controllers\Letter\File;

use App\Http\Controllers\Cloud\AlfrescoC;
use App\Models\Letter\File\CloudAnexosM;
use App\Models\Letter\Cloud\CloudConfigM;
use App\Models\Letter\File\CloudOficiosM;
use App\Models\Letter\Collection\CollectionConfigCloudM;
use App\Models\Letter\File\FileM;
use App\Models\Letter\File\CloudM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;       // ⬅️ NUEVO (para leer folio)
use Illuminate\Support\Str;             // ⬅️ NUEVO (sanear nombre)
use App\Http\Controllers\Letter\Log\LogC;

class CloudFileC extends Controller
{
    // Encabezado de la vista cloud
    public function cloudData(Request $request)
    {
        $id = $request->id;
        $item = new FileM();
        $value = $item->dataCloud($id);
        return response()->json([
            'value' => $value,
            'status' => true,
        ]);
    }

    // Lista documentos/info para cloud
    public function cloudAnexos(Request $request)
    {
        $cloudM = new CloudM();
        $collectionConfigCloudM = new CollectionConfigCloudM();

        $CAT_TIPO_DOC_ENTRADA = config('custom_config.CAT_TIPO_DOC_ENTRADA');
        $CAT_TIPO_DOC_SALIDA  = config('custom_config.CAT_TIPO_DOC_SALIDA');
        $MAX_OFICIOS_ENTRADA  = config('custom_config.MAX_OFICIOS_ENTRADA');
        $MAX_ANEXOS_ENTRADA   = config('custom_config.MAX_ANEXOS_ENTRADA');
        $MAX_OFICIOS_SALIDA   = config('custom_config.MAX_OFICIOS_SALIDA');
        $MAX_ANEXOS_SALIDA    = config('custom_config.MAX_ANEXOS_SALIDA');

        $anexosEntrada        = $cloudM->listAnexos($request->id_tbl_oficio, $collectionConfigCloudM->getValue($MAX_ANEXOS_ENTRADA), $CAT_TIPO_DOC_ENTRADA);
        $oficosEntrada        = $cloudM->listOficios($request->id_tbl_oficio, $collectionConfigCloudM->getValue($MAX_OFICIOS_ENTRADA), $CAT_TIPO_DOC_ENTRADA);
        $anexoSalida          = $cloudM->listAnexos($request->id_tbl_oficio, $collectionConfigCloudM->getValue($MAX_ANEXOS_SALIDA), $CAT_TIPO_DOC_SALIDA);
        $oficosSalida         = $cloudM->listOficios($request->id_tbl_oficio, $collectionConfigCloudM->getValue($MAX_OFICIOS_SALIDA), $CAT_TIPO_DOC_SALIDA);
        $resultOficioEntrada  = $cloudM->conditionOficios($collectionConfigCloudM->getValue($MAX_OFICIOS_ENTRADA), $request->id_tbl_oficio, $CAT_TIPO_DOC_ENTRADA);
        $resultOficioSalida   = $cloudM->conditionOficios($collectionConfigCloudM->getValue($MAX_OFICIOS_SALIDA), $request->id_tbl_oficio, $CAT_TIPO_DOC_SALIDA);
        $resultAnexosEntrada  = $cloudM->conditioAnexos($collectionConfigCloudM->getValue($MAX_ANEXOS_ENTRADA), $request->id_tbl_oficio, $CAT_TIPO_DOC_ENTRADA);
        $resultAnexosSalida   = $cloudM->conditioAnexos($collectionConfigCloudM->getValue($MAX_ANEXOS_SALIDA), $request->id_tbl_oficio, $CAT_TIPO_DOC_SALIDA);

        return response()->json([
            'anexosEntrada'       => $anexosEntrada,
            'oficosEntrada'       => $oficosEntrada,
            'anexoSalida'         => $anexoSalida,
            'oficosSalida'        => $oficosSalida,
            'resultOficioEntrada' => $resultOficioEntrada->valor,
            'resultOficioSalida'  => $resultOficioSalida->valor,
            'resultAnexosEntrada' => $resultAnexosEntrada->valor,
            'resultAnexosSalida'  => $resultAnexosSalida->valor,
            'status'              => true,
        ]);
    }

    public function upload(Request $request)
    {
        $logC         = new LogC();
        $alfrescoC    = new AlfrescoC();
        $cloudConfigM = new CloudConfigM();
        $status       = false;
        $messages     = 'ok';
        $now          = Carbon::now();

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');

            // ===== Validaciones tamaño/extensiones
            $extensionArchivo = strtolower($file->getClientOriginalExtension());
            $tamanoArchivoMB  = $file->getSize() / 1024 / 1024;

            $maxSize        = $cloudConfigM->getData(config('custom_config.MAX_SIZE_ARCHIVO'));
            $fileExtension  = $cloudConfigM->getData(config('custom_config.EXTENSIONES_VALIDAS'));
            $validExtensions = array_map('strtolower', explode(',', $fileExtension->valor));

            if ($tamanoArchivoMB > (float) $maxSize->valor) {
                $messages = 'Tamaño máximo de archivo admitido: ' . $maxSize->valor . ' MB';
            } elseif (!in_array($extensionArchivo, $validExtensions, true)) {
                $messages = 'Las extensiones permitidas son: ' . $fileExtension->valor;
            } else {
                // ===== Carpeta destino en Alfresco
                $uid = $cloudConfigM->getUid(
                    $request->id_cat_area,
                    $request->id_entrada_salida,
                    $request->id_cat_tipo_oficio
                );

                // ===== Construir NOMBRE deseado:
                // Prefijo
                $prefix = ((int)$request->esOficio === 1) ? 'OFICIO_' : 'ANEXO_';

                // 1) obtenemos el folio_gestion a partir del id_tbl_oficio (o id fallback)
                $oficioId = $request->id_tbl_oficio ?? $request->id; // admite ambas llaves
                $folioGestion = DB::table('correspondencia.tbl_oficio as o')
                    ->join('correspondencia.tbl_correspondencia as c', 'c.id_tbl_correspondencia', '=', 'o.id_tbl_correspondencia')
                    ->where('o.id_tbl_oficio', $oficioId)
                    ->value('c.folio_gestion');

                if (!$folioGestion) { $folioGestion = 'SIN_FOLIO'; }

                // 2) saneamos el folio para nombre de archivo (evitar caracteres raros)
                //    (permitimos letras/numeros/guion-bajo/guion; reemplazamos lo demás por "_")
                $folioSafe = preg_replace('/[^\w\-]+/u', '_', $folioGestion);

                // 3) timestamp
                $stamp = now()->format('Ymd_His');

                // 4) nombre final
                $fileName = "{$prefix}{$folioSafe}_{$stamp}.{$extensionArchivo}";

                // ===== Subir a Alfresco con nombre personalizado
                // IMPORTANTE: ver nota al final para que AlfrescoC::addFile acepte $fileName
                $result = $alfrescoC->addFile($file, $uid->uid, (int)$request->esOficio, $fileName);

                if (!$result) {
                    $messages = "Se produjo un error inesperado al intentar subir el archivo: " . $result;
                } else {
                    // Guardados en tus tablas (esta versión mantiene las mismas tablas que ya usabas aquí)
                    if ((int)$request->esOficio === 1) {
                        $data = [
                            'uid'                   => $result,
                            'nombre'                => $fileName,           // <-- usamos el nombre nuevo
                            'estatus'               => true,
                            'fecha_usuario'         => $now,
                            'id_tbl_expediente'     => $request->id,        // (mismo campo que ya tenías en este controller)
                            'id_usuario_sistema'    => Auth::user()->id,
                            'id_cat_tipo_doc_cloud' => $request->id_entrada_salida,
                        ];

                        CloudOficiosM::create($data);
                        $logC->add('correspondencia.ctrl_expediente_oficio', $data);
                    } else {
                        $data = [
                            'uid'                   => $result,
                            'nombre'                => $fileName,           // <-- usamos el nombre nuevo
                            'estatus'               => true,
                            'fecha_usuario'         => $now,
                            'id_tbl_expediente'     => $request->id,
                            'id_usuario_sistema'    => Auth::user()->id,
                            'id_cat_tipo_doc_cloud' => $request->id_entrada_salida,
                        ];
                        CloudAnexosM::create($data);
                        $logC->add('correspondencia.ctrl_expediente_anexo', $data);
                    }

                    $status = true;
                }
            }
        }

        return response()->json([
            'messages' => $messages,
            'status'   => $status,
        ]);
    }

    // Ocultar registros en la vista (delete lógico)
    public function delete(Request $request)
    {
        $logC = new LogC();
        $now = Carbon::now();
        $cloudAnexosM  = new CloudAnexosM();
        $cloudOficiosM = new CloudOficiosM();
        $estatus = false;

        $data = [
            'estatus'           => false,
            'id_usuario_sistema'=> Auth::user()->id,
            'fecha_usuario'     => $now,
        ];

        $resultAnexos = $cloudAnexosM::where('uid', $request->uid)->update($data);
        $resultOficio = $cloudOficiosM::where('uid', $request->uid)->update($data);

        $data['uid'] = $request->uid;

        if ($resultAnexos > 0) {
            $logC->edit('correspondencia.ctrl_expediente_anexo', $data);
        } elseif ($resultOficio > 0) {
            $logC->edit('correspondencia.ctrl_expediente_oficio', $data);
        }

        $estatus = ($resultAnexos > 0 || $resultOficio > 0);

        return response()->json([
            'messages' => $estatus,
            'status'   => true,
        ]);
    }
}

