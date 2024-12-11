<?php

namespace App\Http\Controllers\Letter\Office;
use App\Models\Letter\Collection\CollectionConfigCloudM;
use App\Models\Letter\Office\CloudM;
use App\Models\Letter\Office\OfficeM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        Log::info('Este es un mensaje informativo');

        // Verificar si el archivo ha sido cargado correctamente
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            // Obtener el archivo cargado
            $archivo = $request->file('file');

            // Obtener el nombre del archivo
            $nombreArchivo = $archivo->getClientOriginalName();

            // Obtener la extensión del archivo
            $extensionArchivo = $archivo->getClientOriginalExtension();

            // Obtener el tamaño del archivo en bytes
            $tamanoArchivoBytes = $archivo->getSize();

            // Convertir el tamaño a megabytes (MB)
            $tamanoArchivoMB = $tamanoArchivoBytes / 1024 / 1024; // Convertir a MB

            // Aquí puedes hacer cualquier acción adicional, como guardar el archivo
           
            $ch = curl_init();
            // Log de la información obtenida
            Log::info("Nombre: $nombreArchivo, Extensión: $extensionArchivo, Tamaño: $tamanoArchivoMB MB");







            $message = "Success";
        } else {
            $message = "Error: No file uploaded or invalid file.";
        }

        return response()->json([
            'message' => $message,
            'status' => true,
        ]);
    }
}
