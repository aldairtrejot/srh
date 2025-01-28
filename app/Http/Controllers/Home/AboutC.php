<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AboutC extends Controller
{
    public function __invoke()
    {
        return view('home/about');
    }
}

/*// URL del archivo PDF en Alfresco
        $uuid = '362e27d9-48d7-42d8-8630-4f7f2da42f03';
        $file_name = 'ANEXO_FOLIO-21430-24 RP.pdf';

        // Codificar el nombre del archivo para evitar problemas con los espacios u otros caracteres especiales
        $encoded_file_name = urlencode($file_name);

        // Construir la URL completa
        $url = "http://172.16.17.12:8080/alfresco/service/api/node/content/workspace/SpacesStore/{$uuid}/{$encoded_file_name}";

        // Configuración de autenticación (usuario y contraseña)
        $username = 'crh_dsip';
        $password = 'DivS1sp3r.2024';

        // Inicializa cURL
        $ch = curl_init($url);

        // Establece opciones cURL para la autenticación y obtener el contenido
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/pdf'));

        // Habilita la depuración detallada
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        // Verifica si se recibieron correctamente los encabezados y el contenido
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);

        // Ejecuta la solicitud
        $response = curl_exec($ch);

        // Verifica si hubo un error
        if ($response === FALSE) {
            Log::info('Error al obtener el archivo desde Alfresco.');
            echo 'Error al obtener el archivo desde Alfresco: ' . curl_error($ch);
        } else {
            // Establece los encabezados para enviar el archivo PDF al navegador
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            Log::info("Código de estado HTTP: {$http_status}");

            // Si el código de estado es 200, se procedería a mostrar el archivo PDF
            if ($http_status == 200) {
                // Establece los encabezados para enviar el archivo PDF al navegador
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="documento.pdf"');
                header('Content-Length: ' . strlen($response));

                // Loguear que el archivo se está procesando
                Log::info('Archivo PDF encontrado y enviado correctamente.');

                // Envía el contenido del archivo al navegador
                echo $response;
            } else {
                echo "Error: No se pudo obtener el archivo. Código de estado HTTP: " . $http_status;
                Log::error("Error al obtener el archivo desde Alfresco. Código HTTP: {$http_status}");
            }
        }

        // Cierra cURL
        curl_close($ch);
        */