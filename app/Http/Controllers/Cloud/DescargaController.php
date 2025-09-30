<?php

namespace App\Http\Controllers\Cloud;

use App\Http\Controllers\Controller;
use App\Models\Cloud\FileModelAlf;

class DescargaController extends Controller
{
    public function descargarArchivos()
    {
        try {
            $fileModelAlf = new FileModelAlf;

            // Obtener archivos con UUID y nombre
            $archivos = $fileModelAlf->fileModelAlf();

            if ($archivos->isEmpty()) {
                return response()->json(['error' => 'No hay archivos'], 404);
            }

            $alfrescoService = new AlfrescoService;

            // Preparar datos: array con uuid y name
            $filesData = [];
            foreach ($archivos as $archivo) {
                $filesData[] = [
                    'uuid' => $archivo->uuid,
                    'name' => $archivo->name,
                ];
            }

            // Descargar todos los archivos
            $resultado = $alfrescoService->downloadMultipleFiles($filesData);

            if ($resultado['success']) {

                return response()->download($resultado['zip_path'], 'sirh_file.zip')
                    ->deleteFileAfterSend(true);
            }

            return response()->json(['error' => 'Error al descargar: '.$resultado['error']], 500);

        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
