<?php

namespace App\Http\Controllers\Cloud;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AlfrescoService extends Controller
{
    public function downloadFile($uuid, $customName = null)
    {
        try {
            // URL CORRECTA DEL API DE ALFRESCO
            $baseUrl = env('ALFRESCO_URL').'/alfresco/api/-default-/public/alfresco/versions/1';
            $downloadUrl = $baseUrl."/nodes/{$uuid}/content";

            $client = new Client([
                'verify' => false,
                'timeout' => 30,
                'headers' => [
                    'Accept' => '*/*',
                    'Authorization' => 'Basic '.base64_encode(env('ALFRESCO_USER').':'.env('ALFRESCO_PASS')),
                ],
            ]);

            // PRIMERO OBTENER INFORMACIÓN DEL ARCHIVO
            $infoUrl = $baseUrl."/nodes/{$uuid}";
            $infoResponse = $client->get($infoUrl);
            $fileInfo = json_decode($infoResponse->getBody()->getContents(), true);

            $originalFilename = $fileInfo['entry']['name'] ?? 'documento_'.$uuid;
            $mimeType = $fileInfo['entry']['content']['mimeType'] ?? 'application/octet-stream';

            // LUEGO DESCARGAR EL CONTENIDO
            $response = $client->get($downloadUrl);
            $content = $response->getBody()->getContents();

            if (strlen($content) === 0) {
                return ['success' => false, 'error' => 'Archivo vacío'];
            }

            // GENERAR NOMBRE PERSONALIZADO
            $filename = $this->generateCustomFilename($originalFilename, $customName);

            return [
                'success' => true,
                'content' => $content,
                'filename' => $filename,
                'mime_type' => $mimeType,
            ];

        } catch (RequestException $e) {
            $errorMsg = $e->getMessage();

            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();

                // INTENTAR CON MÉTODO ALTERNATIVO
                return $this->downloadFileAlternative($uuid, $customName);
            }

            return ['success' => false, 'error' => $errorMsg];
        }
    }

    /**
     * Generar nombre personalizado del archivo
     */
    private function generateCustomFilename($originalFilename, $customName)
    {
        // Obtener extensión del archivo original
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);

        // Si no tiene extensión, intentar determinar por MIME type
        if (empty($extension)) {
            $extension = $this->getExtensionFromMimeType($mimeType ?? '');
        }

        // Generar número random
        $randomNumber = random_int(10000, 99999);

        // Construir nombre final
        if ($customName) {
            $filename = "{$customName}_{$randomNumber}";
        } else {
            $baseName = pathinfo($originalFilename, PATHINFO_FILENAME);
            $filename = "{$baseName}_{$randomNumber}";
        }

        // Agregar extensión si no está vacía
        if (! empty($extension)) {
            $filename .= ".{$extension}";
        }

        return $filename;
    }

    /**
     * Obtener extensión desde MIME type
     */
    private function getExtensionFromMimeType($mimeType)
    {
        $mimeMap = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/msword' => 'doc',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'text/plain' => 'txt',
            'application/zip' => 'zip',
        ];

        return $mimeMap[$mimeType] ?? '';
    }

    /**
     * Método alternativo - Descarga directa
     */
    private function downloadFileAlternative($uuid, $customName = null)
    {
        try {
            // URL DIRECTA PARA DESCARGA
            $downloadUrl = env('ALFRESCO_URL')."/alfresco/service/api/node/content/workspace/SpacesStore/{$uuid}";

            $client = new Client([
                'verify' => false,
                'timeout' => 30,
                'headers' => [
                    'Accept' => '*/*',
                    'Authorization' => 'Basic '.base64_encode(env('ALFRESCO_USER').':'.env('ALFRESCO_PASS')),
                ],
            ]);

            $response = $client->get($downloadUrl);
            $content = $response->getBody()->getContents();

            if (strlen($content) === 0) {
                return ['success' => false, 'error' => 'Archivo vacío en método alternativo'];
            }

            // Obtener información del header
            $contentDisposition = $response->getHeader('Content-Disposition');
            $originalFilename = 'documento_'.$uuid;

            if (! empty($contentDisposition)) {
                preg_match('/filename="([^"]+)"/', $contentDisposition[0], $matches);
                $originalFilename = $matches[1] ?? $originalFilename;
            }

            $mimeType = $response->getHeader('Content-Type')[0] ?? 'application/octet-stream';

            // GENERAR NOMBRE PERSONALIZADO
            $filename = $this->generateCustomFilename($originalFilename, $customName);

            return [
                'success' => true,
                'content' => $content,
                'filename' => $filename,
                'mime_type' => $mimeType,
            ];

        } catch (RequestException $e) {
            return ['success' => false, 'error' => 'No se pudo descargar el archivo'];
        }
    }

    public function downloadMultipleFiles($uuidsWithNames)
    {
        $zip = new \ZipArchive;
        $zipFilename = storage_path('app/temp/archivos_'.time().'.zip');

        if (! file_exists(dirname($zipFilename))) {
            mkdir(dirname($zipFilename), 0755, true);
        }

        $archivosAgregados = 0;

        if ($zip->open($zipFilename, \ZipArchive::CREATE) === true) {
            foreach ($uuidsWithNames as $fileData) {
                $uuid = $fileData['uuid'];
                $customName = $fileData['name'] ?? null;

                $result = $this->downloadFile($uuid, $customName);

                if ($result['success']) {
                    $zip->addFromString($result['filename'], $result['content']);
                    $archivosAgregados++;
                }
            }
            $zip->close();

            if (file_exists($zipFilename) && filesize($zipFilename) > 0) {
                return ['success' => true, 'zip_path' => $zipFilename];
            } else {
                if (file_exists($zipFilename)) {
                    unlink($zipFilename);
                }

                return ['success' => false, 'error' => 'ZIP vacío'];
            }
        }

        return ['success' => false, 'error' => 'No se pudo crear el ZIP'];
    }
}
