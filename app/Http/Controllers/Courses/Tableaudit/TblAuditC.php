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
public function upload(Request $request)
{
    Log::info("📥 Datos recibidos en upload():", $request->all());

    $id_tbl_cv = $request->input('id_tbl_auditoria_cursos');
    $esCv = $request->input('esCv'); // 1 = CV, 0 = Constancia

    if (!$id_tbl_cv || !is_numeric($id_tbl_cv)) {
        Log::error("❌ Error: ID de instructor inválido", ['id_tbl_auditoria_cursos' => $id_tbl_cv]);
        return response()->json(['messages' => 'Error: ID inválido.', 'status' => false]);
    }

    if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
        Log::error("❌ Error: Archivo inválido o no recibido.");
        return response()->json(['messages' => 'Archivo inválido.', 'status' => false]);
    }

    // Determinar la carpeta en Alfresco
    $folderId = $esCv ? TableauditM::getCvUuid() : TableauditM::getConstanciaUuid();

    if (!$folderId) {
        Log::error("❌ Error: No se encontró la carpeta de destino en Alfresco.");
        return response()->json(['messages' => 'Error: No se encontró la carpeta de destino.', 'status' => false]);
    }

    // Obtener el archivo
    $file = $request->file('file');

    // Mover el archivo a una ubicación segura en storage/app/temp/
    $fileName = uniqid() . '_' . $file->getClientOriginalName();
    $tempPath = storage_path('app/temp/' . $fileName);
    $file->move(storage_path('app/temp'), $fileName);

    // Verificar que el archivo realmente se movió
    if (!file_exists($tempPath) || !is_readable($tempPath)) {
        Log::error("❌ Error: No se pudo mover el archivo a la ruta temporal.");
        return response()->json(['messages' => 'Error al procesar archivo.', 'status' => false]);
    }

    // Obtener el MIME Type DESPUÉS de moverlo
    $mimeType = mime_content_type($tempPath);
    Log::info("📄 MIME Type detectado: " . $mimeType);

    // Convertimos el archivo a UploadedFile para pasarlo correctamente a AlfrescoC.php
    $uploadedFile = new \Illuminate\Http\UploadedFile(
        $tempPath,
        $file->getClientOriginalName(),
        $mimeType,
        null,
        true
    );

    // Subir el archivo a Alfresco
    $uid = $this->alfresco->add($uploadedFile, $folderId);

    if (!$uid) {
        Log::error("❌ Error: No se pudo subir el archivo a Alfresco.");
        return response()->json(['messages' => 'Error al subir archivo.', 'status' => false]);
    }

    // Guardar en la base de datos
    $data = [
        'fecha_usuario' => Carbon::now(),
        'id_usuario_sistema' => Auth::id(),
        ($esCv ? 'uid_cv' : 'uuid_constancias') => $uid,

    ];

    TableauditM::updateDocument($id_tbl_cv, $data);

    Log::info("✅ Archivo subido correctamente con UID: " . $uid);
    return response()->json(['messages' => 'Archivo subido correctamente.', 'status' => true]);
}

}

