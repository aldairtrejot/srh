<?php

namespace App\Http\Controllers\Cloud;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlfrescoC extends Controller
{
    //La funcion agrega un archivo a alfresco
    public function addFile($archivo, $folderId)
    {
        $fileName = 'ANEXO_' . $archivo->getClientOriginalName(); // Nombre del archivo
        $filePath = $archivo->getRealPath();// Ruta del archivo temporal

        // Credenciales para la autenticación básica
        $username = env('ALFRESCO_USER');
        $password = env('ALFRESCO_PASS');
        $alfrescoUrlBase = env('ALFRESCO_URL_ADD');

        // Reemplazar el marcador de posición {folderId} por el ID de la carpeta
        $url = str_replace('{folderId}', $folderId, $alfrescoUrlBase);

        $credentials = base64_encode("{$username}:{$password}");// Codificar las credenciales en base64

        $headers = [// Configuración de las cabeceras HTTP
            "Authorization: Basic {$credentials}"
        ];

        $ch = curl_init();// Inicializar cURL
        curl_setopt($ch, CURLOPT_URL, $url);// Configurar la solicitud cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [// Enviar el archivo como parte de la solicitud
            'filedata' => new \CURLFile($filePath, $archivo->getMimeType(), $fileName)
        ]);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Agregar las cabeceras a la solicitud
        $response = curl_exec($ch);// Ejecutar la solicitud

        if (curl_errno($ch)) {// Verificar si hubo un error durante la ejecución de cURL
            curl_close($ch); // Si hay un error, devolver el error
            return false;
        }

        curl_close($ch); // Cerrar la conexión cURL

        $responseData = json_decode($response, true);

        // Verificar si el archivo se subió correctamente
        if (isset($responseData['entry']['id'])) {
            // Si el archivo fue subido exitosamente, obtener el UID del archivo
            $fileUid = $responseData['entry']['id'];
            return $fileUid;
            // Devolver el UID del archivo
            /*
            return response()->json([
                'message' => 'Archivo subido con éxito',
                'file_uid' => $fileUid // Retornar el UID del archivo subido
            ], 200);*/
        } else {
            // Si hubo un error al subir el archivo, devolver la respuesta de Alfresco
            //return response()->json(['error' => 'Error al subir el archivo', 'details' => $responseData], 500);
            return false;
        }
    }
}
