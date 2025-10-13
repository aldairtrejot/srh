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
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Letter\Log\LogC;

class CloudFileC extends Controller
{
    // ===== Encabezado de la vista cloud =====
    public function cloudData(Request $request)
    {
        $id   = $request->id; // id_tbl_correspondencia
        $item = new FileM();
        $value = $item->dataCloud($id);

        return response()->json([
            'value'  => $value,
            'status' => true,
        ]);
    }

    // ===== Lista documentos/info "Entrada" para cloud =====
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

    // ===== NUEVO: Datos para "Documento de Respuesta" (solo lectura) =====
    public function cloudReply(Request $request)
    {
        $idCorr = (int) $request->id; // id_tbl_correspondencia

        // Último oficio ligado a esta correspondencia
        $oficio = DB::table('correspondencia.tbl_oficio')
            ->where('id_tbl_correspondencia', $idCorr)
            ->orderByDesc('id_tbl_oficio')
            ->first();

        if (!$oficio) {
            return response()->json([
                'asunto'         => null,
                'observaciones'  => null,
                'oficiosSalida'  => [],
                'anexosSalida'   => [],
            ]);
        }

        // Texto principal
        $asunto = $oficio->asunto ?? null;
        $observaciones = $oficio->observaciones ?? null;

        // Archivos de salida (ligados a id_tbl_oficio)
        $oficiosSalida = DB::table('correspondencia.ctrl_oficio_oficio')
            ->select('uid', 'nombre')
            ->where('id_tbl_oficio', $oficio->id_tbl_oficio)
            ->where('estatus', true)
            ->orderBy('id_ctrl_oficio_oficio', 'asc')
            ->get();

        $anexosSalida = DB::table('correspondencia.ctrl_oficio_anexo')
            ->select('uid', 'nombre')
            ->where('id_tbl_oficio', $oficio->id_tbl_oficio)
            ->where('estatus', true)
            ->orderBy('id_ctrl_oficio_anexo', 'asc')
            ->get();

        return response()->json([
            'asunto'         => $asunto,
            'observaciones'  => $observaciones,
            'oficiosSalida'  => $oficiosSalida,
            'anexosSalida'   => $anexosSalida,
        ]);
    }

    // ===== Subida con nombre personalizado (folio + fechaHora sin guion bajo) =====
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

            // Validaciones tamaño/extensiones
            $extensionArchivo = strtolower($file->getClientOriginalExtension());
            $tamanoArchivoMB  = $file->getSize() / 1024 / 1024;

            $maxSize         = $cloudConfigM->getData(config('custom_config.MAX_SIZE_ARCHIVO'));
            $fileExtension   = $cloudConfigM->getData(config('custom_config.EXTENSIONES_VALIDAS'));
            $validExtensions = array_map('strtolower', explode(',', $fileExtension->valor));

            if ($tamanoArchivoMB > (float)$maxSize->valor) {
                $messages = 'Tamaño máximo de archivo admitido: ' . $maxSize->valor . ' MB';
            } elseif (!in_array($extensionArchivo, $validExtensions, true)) {
                $messages = 'Las extensiones permitidas son : ' . $fileExtension->valor;
            } else {

                // Carpeta destino
                $uid = $cloudConfigM->getUid(
                    $request->id_cat_area,
                    $request->id_entrada_salida,
                    $request->id_cat_tipo_oficio
                );

                // === Nombre final: PREFIX + folio + _ + YYYYMMDDHHmmss + .ext ===
                // folio_gestion desde correspondencia
                $folioGestion = DB::table('correspondencia.tbl_correspondencia')
                    ->where('id_tbl_correspondencia', (int)$request->id) // hidden "id" en la vista
                    ->value('folio_gestion');

                if (!$folioGestion) {
                    $folioGestion = 'SIN_FOLIO';
                }

                // limpiar folio para nombre
                $folioSafe = preg_replace('/[^\w\-]+/u', '_', $folioGestion);
                $prefix = ((int)$request->esOficio === 1) ? 'OFICIO_' : 'ANEXO_';
                $stamp  = now()->format('YmdHis'); // << sin guion bajo entre fecha y hora
                $fileName = "{$prefix}{$folioSafe}_{$stamp}.{$extensionArchivo}";

                // Subir a Alfresco con nombre personalizado
                $nodeId = $alfrescoC->addFile($file, $uid->uid, (int)$request->esOficio, $fileName);

                if (!$nodeId) {
                    $messages = "Se produjo un error inesperado al intentar subir el archivo.";
                } else {
                    if ((int)$request->esOficio === 1) {
                        $data = [
                            'uid'                   => $nodeId,
                            'nombre'                => $fileName,
                            'estatus'               => true,
                            'fecha_usuario'         => $now,
                            'id_tbl_expediente'     => $request->id, // se mantiene tu campo
                            'id_usuario_sistema'    => Auth::user()->id,
                            'id_cat_tipo_doc_cloud' => $request->id_entrada_salida,
                        ];
                        CloudOficiosM::create($data);
                        $logC->add('correspondencia.ctrl_expediente_oficio', $data);
                    } else {
                        $data = [
                            'uid'                   => $nodeId,
                            'nombre'                => $fileName,
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

    // ===== Ocultar registros en UI (eliminación lógica) =====
    public function delete(Request $request)
    {
        $logC = new LogC();
        $now = Carbon::now();
        $cloudAnexosM  = new CloudAnexosM();
        $cloudOficiosM = new CloudOficiosM();

        $data = [
            'estatus'            => false,
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario'      => $now,
        ];

        $resultAnexos = $cloudAnexosM::where('uid', $request->uid)->update($data);
        $resultOficio = $cloudOficiosM::where('uid', $request->uid)->update($data);

        $data['uid'] = $request->uid;

        if ($resultAnexos > 0) {
            $logC->edit('correspondencia.ctrl_expediente_anexo', $data);
        } elseif ($resultOficio > 0) {
            $logC->edit('correspondencia.ctrl_expediente_oficio', $data);
        }

        return response()->json([
            'messages' => ($resultAnexos > 0 || $resultOficio > 0),
            'status'   => true,
        ]);
    }
}



