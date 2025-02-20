<?php

namespace App\Http\Controllers\Cloud;
use Illuminate\Support\Facades\Log;
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
                return redirect()->back()->with([
                    'value' => 'error', //VALUE_IS(error, warning, success)
                    'message' => 'Se produjo un problema al intentar completar la descarga.',
                    'estatus' => 'true'
                ]);
                /*
                return response()->json([
                    'estatus' => "Error: No se pudo obtener el archivo. Código de respuesta: $http_code",
                    'status' => false,
                ]);
                */
            }
        }
    }

    public function see(Request $request)
    {
        // UUID del archivo en Alfresco
        $uuid = $request->uid;

        // Configuración de autenticación
        $username = env('ALFRESCO_USER');
        $password = env('ALFRESCO_PASS');

        // Construye la URL para descargar el archivo
        $urlSee = env('ALFRESCO_SEE');
        $url = str_replace('{uuid}', $uuid, $urlSee);

        // Inicializa cURL para descargar el archivo
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/pdf, image/png, image/jpeg'));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // Ejecuta la solicitud
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE); // Obtener tipo de contenido

        curl_close($ch);

        // Verifica si hubo un error
        if ($response === FALSE || $http_status != 200) {
            return redirect()->back()->with([
                'value' => 'error', //VALUE_IS(error, warning, success)
                'message' => 'Se produjo un error al intentar abrir el documento.',
                'estatus' => 'true'
            ]);
        } else {
            // Detecta el tipo de archivo y ajusta los encabezados apropiadamente
            if (strpos($content_type, 'application/pdf') !== false) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="documento.pdf"');
            } elseif (strpos($content_type, 'image/png') !== false) {
                header('Content-Type: image/png');
                header('Content-Disposition: inline; filename="documento.png"');
            } elseif (strpos($content_type, 'image/jpeg') !== false) {
                header('Content-Type: image/jpeg');
                header('Content-Disposition: inline; filename="documento.jpg"');
            } else {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="documento.pdf"');
            }

            // Establece la longitud del contenido
            header('Content-Length: ' . strlen($response));

            // Envía el contenido del archivo al navegador
            echo $response;
        }
    }

    // La funcion elimina un archivo de alfresco, espera como parametro el uuid y retorna true si se elimino, y falso si no
    public function delete($uuidx)
    {
        // Obtener las credenciales y la URL base desde el archivo .env
        $username = env('ALFRESCO_USER');    // Usuario de Alfresco
        $password = env('ALFRESCO_PASS');    // Contraseña de Alfresco

        // UUID del archivo a eliminar (fijo en este caso)
        $uuid = $uuidx;  // UUID fijo

        // Obtener la URL para la eliminación desde .env y reemplazar el placeholder {uuid}
        $urlDelete = env('ALFRESCO_DELETE');
        $url = str_replace('{uuid}', $uuid, $urlDelete);

        // Inicializar cURL
        $ch = curl_init();

        // Configuración de cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");  // Método DELETE para eliminar el archivo
        curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");  // Autenticación básica

        // Ejecutar la petición cURL
        $response = curl_exec($ch);

        // Obtener el código de respuesta HTTP
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Cerrar la sesión de cURL
        curl_close($ch);

        // Verificar si la respuesta fue exitosa (200 OK o 204 No Content)
        if ($httpCode == 200 || $httpCode == 204) {
            //Log::info("Archivo con UUID {$uuid} eliminado correctamente.");
            //return response()->json(['message' => 'Archivo eliminado correctamente'], 200);
            return true; // Exito
        } /*elseif ($httpCode == 404) {
      //Log::error("Archivo con UUID {$uuid} no encontrado.");
      //return response()->json(['message' => 'Archivo no encontrado en el servidor'], 404);
      return false;
  } elseif ($httpCode == 401 || $httpCode == 403) {
      //Log::error("Error de autenticación al eliminar el archivo con UUID {$uuid}. Código de respuesta: {$httpCode}");
      //return response()->json(['message' => 'Error de autenticación'], 401);
  } */ else {
            //Log::error("Error al eliminar el archivo con UUID {$uuid}. Código de respuesta HTTP: {$httpCode}. Respuesta del servidor: {$response}");
            //return response()->json(['message' => 'No se pudo eliminar el archivo'], 400);
            return false; //Error
        }
    }

    // La función agrega un archivo, esperando como parametro el archivo y el uuid de la carpeta donde se va guardar
    // retorna el uuid si se agrega correctamente el archivo (uuid de archivo que se subio) o falso si no se agrega nada
    public function add($archivo, $folderId)
    {
        $fileName =  $archivo->getClientOriginalName(); // Nombre del archivo
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
}
