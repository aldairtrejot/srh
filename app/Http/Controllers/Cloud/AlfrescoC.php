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
        $username = env('ALFRESCO_USER'); // Credenciales para la autenticación básica
        $password = env('ALFRESCO_PASS'); // Credenciales para la autenticación básica
        $alfresco_url = env('ALFRESCO_URL_DOWNLOAD');// Reemplaza {node-id} con el UID del archivo
        $usuario = $username;
        $contrasena = $password;
        $nodeId = $request->uid;  // Usamos el UID del archivo recibido en la solicitud
        $url = str_replace('{node-id}', $nodeId, $alfresco_url);// Construir la URL completa de la API
        $ch = curl_init();// Inicializar cURL

        curl_setopt($ch, CURLOPT_URL, $url);// Configurar opciones de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$usuario:$contrasena");  // Autenticación básica
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);  // Esto incluirá los encabezados en la respuesta
        curl_setopt($ch, CURLOPT_NOBODY, false); // Para incluir el cuerpo de la respuesta también

        $response = curl_exec($ch);// Ejecutar la solicitud
        if (curl_errno($ch)) {// Comprobar si hubo un error
            return response()->json([
                'estatus' => 'Error de cURL: ' . curl_error($ch),
                'status' => false,
            ]);
        } else {
            // Obtener el código de respuesta HTTP
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_code == 200) {
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // Separar los encabezados y el cuerpo de la respuesta
                $headers = substr($response, 0, $header_size);
                $body = substr($response, $header_size);

                // Buscar el nombre del archivo en el encabezado Content-Disposition
                $fileName = 'archivo_descargado'; // Nombre predeterminado en caso de que no se encuentre el archivo

                if (preg_match('/Content-Disposition:.*filename="([^"]+)"/i', $headers, $matches)) {
                    $fileName = $matches[1]; // El nombre del archivo con extensión
                }
                curl_close($ch); // Cerrar cURL
                return response($body)// Enviar la respuesta al cliente para la descarga
                    ->header('Content-Type', 'application/octet-stream')  // Tipo MIME genérico
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')  // Nombre original del archivo
                    ->header('Content-Length', strlen($body));
            } else {
                curl_close($ch);
                return response()->json([
                    'estatus' => "Error: No se pudo obtener el archivo. Código de respuesta: $http_code",
                    'status' => false,
                ]);
            }
        }
    }

    public function see(Request $request)
    {
        // Obtener el nombre de usuario y la contraseña desde las variables de entorno
        $username = env('ALFRESCO_USER'); // Credenciales para la autenticación básica
        $password = env('ALFRESCO_PASS'); // Credenciales para la autenticación básica

        // El UID del archivo que deseas ver
        $nodeId = $request->uid;

        // Construir la URL completa de la API de Alfresco para obtener el contenido del archivo
        $alfresco_url = 'http://172.16.17.12:8080/alfresco/api/-default-/public/alfresco/versions/1/nodes/{node-id}/content';
        $url = str_replace('{node-id}', $nodeId, $alfresco_url);  // Reemplazar {node-id} por el UID del archivo

        // Inicializar cURL
        $ch = curl_init();

        // Configurar opciones de cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // Para recibir la respuesta como una cadena
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");  // Autenticación básica
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Seguir cualquier redirección
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);  // Aceptar JSON en la respuesta

        // Ejecutar la solicitud
        $response = curl_exec($ch);

        // Comprobar si hubo un error
        if (curl_errno($ch)) {
            // Si ocurre un error en la solicitud cURL
            return response()->json([
                'estatus' => 'Error de cURL: ' . curl_error($ch),
                'status' => false,
            ]);
        } else {
            // Obtener el código de respuesta HTTP
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Si la solicitud fue exitosa (código HTTP 200)
            if ($http_code == 200) {
                curl_close($ch);

                // Devolver la URL para abrir el archivo en una nueva pestaña del navegador
                return response()->json([
                    'url' => $url,  // URL del archivo para abrir en el navegador
                    'status' => true,
                ]);
            } else {
                // En caso de error, devolver el código de error y el mensaje
                curl_close($ch);
                return response()->json([
                    'estatus' => "Error de autenticación o acceso al archivo. Código de respuesta: $http_code",
                    'status' => false,
                ]);
            }
        }
    }
}
