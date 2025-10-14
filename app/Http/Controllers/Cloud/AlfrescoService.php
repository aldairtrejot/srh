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
            $filename = "{$customName}_x{$randomNumber}";
        } else {
            $baseName = pathinfo($originalFilename, PATHINFO_FILENAME);
            $filename = "{$baseName}_x{$randomNumber}";
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
        $tamañoTotal = 0;
        $limiteTamaño = 50 * 1024 * 1024; // 100 MB

        if ($zip->open($zipFilename, \ZipArchive::CREATE) === true) {
            foreach ($uuidsWithNames as $fileData) {
                $uuid = $fileData['uuid'];
                $customName = $fileData['name'] ?? null;

                $result = $this->downloadFile($uuid, $customName);

                if ($result['success']) {
                    $tamañoArchivo = strlen($result['content']);
                    $tamañoTotal += $tamañoArchivo;

                    // ✅ PRIMERA VALIDACIÓN: Tamaño en memoria ANTES de agregar al ZIP
                    if ($tamañoTotal > $limiteTamaño) {
                        $zip->close();
                        if (file_exists($zipFilename)) {
                            unlink($zipFilename);
                        }

                        // \Log::info("Límite excedido durante descarga - Archivos: {$archivosAgregados}, Tamaño: ".round($tamañoTotal / (1024 * 1024), 2).' MB');

                        return [
                            'success' => false,
                            'error' => 'El tamaño total de los archivos supera el límite de 100 MB. '.
                                      'Archivos agregados: '.$archivosAgregados.'. '.
                                      'Tamaño actual: '.round($tamañoTotal / (1024 * 1024), 2).' MB',
                        ];
                    }

                    $zip->addFromString($result['filename'], $result['content']);
                    $archivosAgregados++;

                    // ✅ SEGUNDA VALIDACIÓN: Tamaño real del ZIP DESPUÉS de agregar archivo
                    $zip->close(); // Cerrar temporalmente para medir tamaño real
                    $tamañoActualZip = file_exists($zipFilename) ? filesize($zipFilename) : 0;

                    // \Log::info("Después de archivo {$archivosAgregados} - ZIP: ".round($tamañoActualZip / (1024 * 1024), 2).' MB, Memoria: '.round($tamañoTotal / (1024 * 1024), 2).' MB');

                    if ($tamañoActualZip > $limiteTamaño) {
                        if (file_exists($zipFilename)) {
                            unlink($zipFilename);
                        }

                        // \Log::info("Límite excedido en ZIP real - Archivos: {$archivosAgregados}, Tamaño ZIP: ".round($tamañoActualZip / (1024 * 1024), 2).' MB');

                        return [
                            'success' => false,
                            'error' => 'El archivo ZIP generado supera el límite de 100 MB. '.
                                      'Tamaño: '.round($tamañoActualZip / (1024 * 1024), 2).' MB',
                        ];
                    }

                    // Reabrir el ZIP para continuar
                    $zip->open($zipFilename, \ZipArchive::CREATE);
                }
            }
            $zip->close();

            // ✅ VALIDACIÓN FINAL
            if (file_exists($zipFilename)) {
                $tamañoFinal = filesize($zipFilename);
                // \Log::info('Tamaño final del ZIP: '.round($tamañoFinal / (1024 * 1024), 2).' MB');

                if ($tamañoFinal > $limiteTamaño) {
                    unlink($zipFilename);
                    // \Log::info('❌ SUPERÓ 100 MB - ZIP eliminado');

                    return [
                        'success' => false,
                        'error' => 'El archivo ZIP generado supera el límite de 100 MB. '.
                                  'Tamaño: '.round($tamañoFinal / (1024 * 1024), 2).' MB',
                    ];
                }
            }

            if (file_exists($zipFilename) && filesize($zipFilename) > 0) {
                // \Log::info('✅ ZIP creado exitosamente - Tamaño: '.round(filesize($zipFilename) / (1024 * 1024), 2).' MB');

                return ['success' => true, 'zip_path' => $zipFilename];
            } else {
                if (file_exists($zipFilename)) {
                    unlink($zipFilename);
                }
                // \Log::error('ZIP vacío o no creado');

                return ['success' => false, 'error' => 'ZIP vacío'];
            }
        }

        // \Log::error('No se pudo crear el ZIP');

        return ['success' => false, 'error' => 'No se pudo crear el ZIP'];
    }
}
