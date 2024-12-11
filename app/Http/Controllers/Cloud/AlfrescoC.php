<?php

namespace App\Http\Controllers\Cloud;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlfrescoC extends Controller
{
    //La funcion agrega un archivo a alfresco
    public function addFile($archivo, $folderId)
    {
        $fileName = $archivo->getClientOriginalName(); // Nombre del archivo
        $filePath = $archivo->getRealPath();// Ruta del archivo temporal

        // Credenciales para la autenticación básica
        $username = env('ALFRESCO_USER');
        $password = env('ALFRESCO_PASS');
        $url = "http://172.16.17.12:8080/alfresco/api/-default-/public/alfresco/versions/1/nodes/{$folderId}/children";

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
            return true;
        }

        curl_close($ch); // Cerrar la conexión cURL
        return true;
    }
}
