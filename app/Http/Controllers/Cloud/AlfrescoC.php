<?php

namespace App\Http\Controllers\Cloud;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlfrescoC extends Controller
{
    /**
     * Sube un archivo a Alfresco (API v1 children).
     * @param \Illuminate\Http\UploadedFile $archivo
     * @param string $folderId  UUID de la carpeta destino
     * @param int $esOficio     1 = prefijo OFICIO_, 0 = ANEXO_
     * @return string|false     nodeId creado o false
     */
    public function addFile($archivo, $folderId, $esOficio)
    {
        $fileName = ($esOficio == 1 ? 'OFICIO_' : 'ANEXO_') . $archivo->getClientOriginalName();
        $filePath = $archivo->getRealPath();

        $username = env('ALFRESCO_USER');
        $password = env('ALFRESCO_PASS');

        $url = str_replace('{folderId}', $folderId, env('ALFRESCO_URL_ADD'));
        $credentials = base64_encode("{$username}:{$password}");

        $headers = [
            "Authorization: Basic {$credentials}",
            "Accept: application/json",
        ];

        // Enviar también name/nodeType/autoRename — recomendado por la API
        $postFields = [
            'filedata'   => new \CURLFile($filePath, $archivo->getMimeType(), $fileName),
            'name'       => $fileName,
            'nodeType'   => 'cm:content',
            'autoRename' => 'true',
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER => true,          // para leer código HTTP y body
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 120,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            Log::error('[ALFRESCO addFile] cURL error: ' . $err);
            return false;
        }

        $status    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSz  = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body      = substr($response, $headerSz);
        curl_close($ch);

        if ($status < 200 || $status >= 300) {
            Log::error('[ALFRESCO addFile] HTTP ' . $status . ' - Body: ' . $body);
            return false;
        }

        $data = json_decode($body, true);
        if (isset($data['entry']['id'])) {
            return $data['entry']['id']; // nodeId de Alfresco
        }

        Log::error('[ALFRESCO addFile] Respuesta sin entry.id: ' . $body);
        return false;
    }

    // Descarga por nodeId usando API v1
    public function download(Request $request)
    {
        $username = env('ALFRESCO_USER');
        $password = env('ALFRESCO_PASS');
        $alfresco_url = env('ALFRESCO_URL_DOWNLOAD');
        $nodeId = $request->uid;

        $url = str_replace('{nodeId}', $nodeId, $alfresco_url);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => true,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $err = curl_error($ch);
            curl_close($ch);
            return response()->json(['estatus' => 'Error de cURL: ' . $err, 'status' => false]);
        }

        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http !== 200) {
            curl_close($ch);
            return redirect()->back()->with([
                'value' => 'error',
                'message' => 'Se produjo un problema al intentar completar la descarga.',
                'estatus' => 'true'
            ]);
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers    = substr($response, 0, $headerSize);
        $body       = substr($response, $headerSize);
        curl_close($ch);

        $fileName    = 'archivo_descargado';
        $contentType = 'application/octet-stream';

        if (preg_match('/Content-Disposition:.*filename="?([^\";]+)"?/i', $headers, $m)) {
            $fileName = $m[1];
        }
        if (preg_match('/Content-Type:\s*([^\s;]+)/i', $headers, $m)) {
            $contentType = $m[1];
        }

        if (ob_get_length()) { ob_end_clean(); }

        return response($body)
            ->header('Content-Type', $contentType)
            ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"')
            ->header('Content-Length', strlen($body))
            ->header('Cache-Control', 'no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    // Visualización inline por UUID legacy (service/api/node/content/...)
    public function see(Request $request)
    {
        $uuid = $request->uid;
        $username = env('ALFRESCO_USER');
        $password = env('ALFRESCO_PASS');
        $url = str_replace('{uuid}', $uuid, env('ALFRESCO_SEE'));

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_HTTPHEADER => ['Accept: application/pdf, image/png, image/jpeg'],
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response    = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($response === false || $http_status != 200) {
            return redirect()->back()->with([
                'value' => 'error',
                'message' => 'Se produjo un error al intentar abrir el documento.',
                'estatus' => 'true'
            ]);
        }

        $ct = 'application/pdf';
        if (strpos($contentType, 'image/png') !== false)  $ct = 'image/png';
        if (strpos($contentType, 'image/jpeg') !== false) $ct = 'image/jpeg';

        header('Content-Type: '.$ct);
        header('Content-Disposition: inline; filename="documento"');
        header('Content-Length: ' . strlen($response));
        echo $response;
    }

    // Elimina nodo por API v1
    public function delete($uuidx)
    {
        $username = env('ALFRESCO_USER');
        $password = env('ALFRESCO_PASS');
        $url = str_replace('{uuid}', $uuidx, env('ALFRESCO_DELETE'));

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_USERPWD => "{$username}:{$password}",
        ]);

        curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return in_array($http, [200, 204], true);
    }

    // Variante simple sin prefijos
    public function add($archivo, $folderId)
    {
        $fileName = $archivo->getClientOriginalName();
        $filePath = $archivo->getRealPath();

        $username = env('ALFRESCO_USER');
        $password = env('ALFRESCO_PASS');

        $url = str_replace('{folderId}', $folderId, env('ALFRESCO_URL_ADD'));
        $credentials = base64_encode("{$username}:{$password}");

        $headers = [
            "Authorization: Basic {$credentials}",
            "Accept: application/json",
        ];

        $postFields = [
            'filedata'   => new \CURLFile($filePath, $archivo->getMimeType(), $fileName),
            'name'       => $fileName,
            'nodeType'   => 'cm:content',
            'autoRename' => 'true',
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 120,
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            Log::error('[ALFRESCO add] cURL error: '.curl_error($ch));
            curl_close($ch);
            return false;
        }

        $status    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSz  = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body      = substr($response, $headerSz);
        curl_close($ch);

        if ($status < 200 || $status >= 300) {
            Log::error('[ALFRESCO add] HTTP '.$status.' - Body: '.$body);
            return false;
        }

        $data = json_decode($body, true);
        return $data['entry']['id'] ?? false;
    }
}
