<?php

namespace App\Http\Controllers\Cloud;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlfrescoC extends Controller
{
    //La funcion agrega un archivo a alfresco
    public function addFile($archivo, $folderId, $esOficio)
    {
        $fileName = 'ANEXO_' . $archivo->getClientOriginalName(); // Nombre del archivo
        if ($esOficio == 1) { //Validacion de archivo donde 1 se cambia el nombre por oficio si no es anexo
            $fileName = 'OFICIO_' . $archivo->getClientOriginalName(); // Nombre del archivo
        }

        $filePath = $archivo->getRealPath();// Ruta del archivo temporal

        $username = env('ALFRESCO_USER');// Credenciales para la autenticación básica
        $password = env('ALFRESCO_PASS');// Credenciales para la autenticación básica

        $url = str_replace('{folderId}', $folderId, env('ALFRESCO_URL_ADD')); // Reemplazar el marcador de posición {folderId} en la URL
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

        if (isset($responseData['entry']['id'])) {// Si el archivo fue subido exitosamente, obtener el UID del archivo
            $fileUid = $responseData['entry']['id'];
            return $fileUid;// Devolver el UID del archivo
        } else {
            return false; //Retorno de falso si es que existe un error
        }
    }

    //La funcion descarga un documento de alfresco
    public function download(Request $request)
    {
        $username = env('ALFRESCO_USER');// Credenciales para la autenticación básica
        $password = env('ALFRESCO_PASS');// Credenciales para la autenticación básica

        return response()->json([
            'status' => true,
        ]);
    }
}
