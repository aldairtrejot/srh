<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlfrescotableinsC extends Controller
{
    // Subir un archivo a Alfresco (CV o Constancia)
    public function addFile(Request $request)
    {
        $archivo = $request->file('file');
        $folderId = $request->folderId; // ID de la carpeta en Alfresco
        $tipo = $request->tipo; // "cv" o "constancia"

        // Validación del tipo
        if (!in_array($tipo, ['cv', 'constancia'])) {
            return response()->json(['status' => false, 'message' => 'Tipo de archivo inválido.']);
        }

        // Nombrar el archivo basado en el tipo
        $fileName = strtoupper($tipo) . '_' . $archivo->getClientOriginalName();
        $filePath = $archivo->getRealPath();

        $username = env('ALFRESCO_USER');
        $password = env('ALFRESCO_PASS');
        $url = str_replace('{folderId}', $folderId, env('ALFRESCO_URL_ADD'));

        $credentials = base64_encode("{$username}:{$password}");
        $headers = ["Authorization: Basic {$credentials}"];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'filedata' => new \CURLFile($filePath, $archivo->getMimeType(), $fileName)
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return response()->json(['status' => false, 'message' => curl_error($ch)]);
        }

        curl_close($ch);
        $responseData = json_decode($response, true);

        if (isset($responseData['entry']['id'])) {
            return response()->json(['status' => true, 'uid' => $responseData['entry']['id']]);
        }

        return response()->json(['status' => false, 'message' => 'Error al subir el archivo']);
    }

    // Descargar un archivo de Alfresco (CV o Constancia)
    public function download(Request $request)
    {
        $nodeId = $request->uid; // UID del archivo
        $url = str_replace('{node-id}', $nodeId, env('ALFRESCO_URL_DOWNLOAD'));
        $username = env('ALFRESCO_USER');
        $password = env('ALFRESCO_PASS');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return response()->json(['status' => false, 'message' => curl_error($ch)]);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode === 200) {
            $headers = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            return response($response)
                ->header('Content-Type', $headers)
                ->header('Content-Disposition', 'attachment; filename="archivo_descargado"');
        }

        curl_close($ch);
        return response()->json(['status' => false, 'message' => "Error al descargar el archivo: $httpCode"]);
    }

    // Visualizar un archivo de Alfresco en el navegador (CV o Constancia)
    public function see(Request $request)
    {
        $nodeId = $request->uid; // UID del archivo
        $url = str_replace('{node-id}', $nodeId, env('ALFRESCO_URL_VIEW'));
        $username = env('ALFRESCO_USER');
        $password = env('ALFRESCO_PASS');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return response()->json(['status' => false, 'message' => curl_error($ch)]);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode === 200) {
            curl_close($ch);
            return response()->json(['status' => true, 'url' => $url]);
        }

        curl_close($ch);
        return response()->json(['status' => false, 'message' => "Error al obtener el archivo: $httpCode"]);
    }
}
