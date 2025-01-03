<?php

namespace App\Http\Controllers\Courses\Alfresco;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AlfrescoC extends Controller
{
    // Método para mostrar el formulario de carga de archivos
    public function showUploadForm()
    {
        return view('courses/alfresco.form');  
    }

    // Método para cargar los archivos a Alfresco
    public function uploadFile(Request $request)
    {
        // Validar que ambos archivos fueron subidos
        $request->validate([
            'file_cv' => 'required|file|max:10240', // Máximo 10MB
            'file_constancia' => 'required|file|max:10240', // Máximo 10MB
        ]);

        // Obtener la URL de la API de Alfresco desde el archivo .env
        $alfrescoApiUrl = env('ALFRESCO_API_URL');
        $cvNodeId = env('ALFRESCO_NODE_ID'); // ID de la carpeta para el CV
        $constanciaNodeId = env('ALFRESCO_CONSTANCIA_NODE_ID'); // ID de la carpeta para la constancia
        $username = env('ALFRESCO_USERNAME'); // Nombre de usuario de Alfresco
        $password = env('ALFRESCO_PASSWORD'); // Contraseña de Alfresco

        // Crear una instancia de Guzzle
        $client = new Client();

        // Preparar los archivos a subir
        $files = [
            'cv' => $request->file('file_cv'),
            'constancia' => $request->file('file_constancia')
        ];

        foreach ($files as $type => $file) {
            // Preparar los datos para el archivo
            $multipart = [
                [
                    'name' => 'filedata',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ]
            ];

            // Determinar el nodo para cada archivo
            $nodeId = ($type === 'cv') ? $cvNodeId : $constanciaNodeId;

            // Realizar la solicitud POST a la API de Alfresco
            try {
                $response = $client->post(
                    str_replace('{nodeId}', $nodeId, $alfrescoApiUrl), 
                    [
                        'auth' => [$username, $password],
                        'multipart' => $multipart
                    ]
                );

                // Verificar que la carga fue exitosa
                if ($response->getStatusCode() != 201) {
                    return response()->json(['message' => 'Error al subir el archivo ' . $type, 'status_code' => $response->getStatusCode()], 500);
                }
            } catch (RequestException $e) {
                return response()->json(['message' => 'Error en la solicitud a Alfresco', 'error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'Archivos subidos exitosamente a Alfresco']);
    }
}


