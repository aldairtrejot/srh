<?php
namespace App\Http\Controllers\Cloud;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
class ConnectionC extends Controller
{

    public function list()
    {
        return view('letter/oficio/cloudList');
    }
    public function connection()
    {
        // URL de la API de Alfresco y el nodo específico para obtener el archivo
        $url = "http://172.16.17.12:8080/alfresco/api/-default-/public/alfresco/versions/1/nodes/";
        $getfile = $url . 'ae5327dc-5c95-4d72-b648-1d77176bf21c/content'; // Asegúrate de tener la ruta correcta para obtener el contenido del archivo

        $username = 'admin';
        $password = 'admin';

        // Codificar las credenciales como usuario:contraseña en base64
        $credentials = base64_encode("{$username}:{$password}");

        // Realizar la solicitud GET con la cabecera de autorización básica
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials
        ])->get($getfile);

        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            // Obtener el contenido del archivo
            $fileContent = $response->body();

            // Definir el nombre del archivo (ajústalo según lo necesario)
            $fileName = 'alfresco_file.txt'; // Cambiar según el tipo de archivo, por ejemplo, PDF, JPG, etc.

            // Verificar si el contenido es un archivo descargable
            $headers = [
                'Content-Type' => $response->header('Content-Type'),
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ];

            // Devolver el archivo al navegador en una nueva pestaña (sin forzar la descarga)
            return response($fileContent, 200, $headers);
        } else {
            // Manejar el error si la solicitud falla
            Log::error('Error al obtener el archivo de Alfresco.', [
                'status' => $response->status(),
                'error_message' => $response->body()
            ]);

            return response()->json([
                'error' => 'Error en la solicitud',
                'status' => $response->status(),
                'message' => $response->body()
            ], $response->status());
        }
    }
}