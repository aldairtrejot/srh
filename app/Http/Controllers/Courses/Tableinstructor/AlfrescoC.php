<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Controller;

class AlfrescoC extends Controller

{
    public function addFile($file, $folderId, $esCv)
    {
        $fileName = ($esCv ? 'CV_' : 'CONSTANCIA_') . $file->getClientOriginalName();
        $filePath = $file->getRealPath();

        $url = str_replace('{folderId}', $folderId, env('ALFRESCO_URL_ADD'));
        $credentials = base64_encode(env('ALFRESCO_USER') . ":" . env('ALFRESCO_PASS'));

        $headers = ["Authorization: Basic {$credentials}"];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['filedata' => new \CURLFile($filePath)]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response['entry']['id'] ?? false;
    }

    public function delete($uuid)
    {
        $url = str_replace('{uuid}', $uuid, env('ALFRESCO_DELETE'));
        $credentials = base64_encode(env('ALFRESCO_USER') . ":" . env('ALFRESCO_PASS'));

        $headers = ["Authorization: Basic {$credentials}"];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode == 200 || $httpCode == 204;
    }
}