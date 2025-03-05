<?php

namespace App\Http\Controllers\Courses\Tableaudit;

use App\Http\Controllers\Cloud\AlfrescoC;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Courses\Courses\Tableaudit\TableauditM;
use Illuminate\Support\Carbon;

class TblAuditC extends Controller
{
    protected $alfresco;

    public function __construct()
    {
        $this->alfresco = new AlfrescoC();
    }

    /**
     * 📂 Subir archivos a Alfresco y guardar en la base de datos
     */
    public function storeAudit(Request $request)
    {
        $tableauditm = new TableauditM();
       $result = $tableauditm->auditlist($request->id_courses);

        return response()->json([
            'value' => $request->id_courses,
            'status' => true,
        ]);
    }

    public function getAuditList(Request $request)
{
    try {
        $tableauditm = new TableauditM();
        $result = $tableauditm->list();

        return response()->json([
            'status' => true,
            'data' => $result,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error al obtener la lista de auditoría',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function saveFile(Request $request)
{
    try {
        $alfrescoC = new AlfrescoC();
        $tableauditM = new TableauditM();
        $now = Carbon::now(); // Hora y fecha actual
        $status = false;
        $result = null;

        if ($request->hasFile('file') && $request->file('file')->isValid()) { // Verificar si el archivo ha sido cargado correctamente
            $file = $request->file('file'); // Obtener el archivo cargado
            $id_tbl_auditoria_cursos = $request->input('id'); // Obtener el ID del curso

            if (!$id_tbl_auditoria_cursos) {
                return response()->json([
                    'status' => false,
                    'message' => 'ID del curso no proporcionado.'
                ]);
            }

            // Validar tipo y tamaño del archivo
            $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            if (!in_array($file->getMimeType(), $allowedMimeTypes) || $file->getSize() > 10485760) { // 10MB
                return response()->json([
                    'status' => false,
                    'message' => 'Tipo de archivo no permitido o tamaño excedido.'
                ]);
            }

            // Obtener el UUID de la carpeta para Constancias
            $uuid = $tableauditM->getConstanciaUuid();
            if (!$uuid) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontró el UUID de la carpeta para Constancias.'
                ]);
            }

            // Subir el archivo a Alfresco
            $result = $alfrescoC->add($file, $uuid);

            // Validación
            if ($result) { // Manda el uuid para que se agregue a la tabla
                Log::info("Archivo subido a Alfresco con UUID: {$result}");
                $data = [
                    'uuid_constancias' => $result,
                    'id_usuario_sistema' => Auth::user()->id,
                    'fecha_usuario' => $now,
                ];

                $updateResult = $tableauditM::where('id_tbl_auditoria_cursos', $id_tbl_auditoria_cursos)
                    ->update($data);

                if ($updateResult) {
                    Log::info("Tabla actualizada correctamente para ID: {$id_tbl_auditoria_cursos}");
                    $status = true;
                } else {
                    Log::error("Error al actualizar la tabla para ID: {$id_tbl_auditoria_cursos}");
                }
            } else {
                Log::error("Error al subir el archivo a Alfresco.");
            }
        } else {
            Log::error('Archivo no válido o no cargado.');
        }

        return response()->json([
            'status' => $status,
            'uuid' => $result,
        ]);
    } catch (\Exception $e) {
        Log::error('Error al subir el archivo: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Ocurrió un error al subir el archivo.'
        ]);
    }
}
}