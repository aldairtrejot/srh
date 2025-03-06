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
        $result = $tableauditm->list($request->id_tbl_cursos);

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

public function uploadFile(Request $request)
{
    // Obtener el archivo y su ID
    $file = $request->file('file');
    $id_tbl_auditoria_cursos = $request->input('id');

    // Verificar que el archivo esté presente
    if (!$file) {
        return response()->json(['status' => false, 'message' => 'No se seleccionó ningún archivo.']);
    }

    // Subir archivo a Alfresco (simulación de proceso de subida)
    // Aquí iría tu código para subir el archivo a Alfresco y obtener el UUID
    // Ejemplo:
    $alfrescoResponse = $this->uploadToAlfresco($file); // Esta función debe devolver el UUID

    // Suponiendo que el UUID es parte de la respuesta de Alfresco
    $uuid = $alfrescoResponse['uuid']; // Asegúrate de que 'uuid' esté presente en la respuesta

    if (!$uuid) {
        return response()->json(['status' => false, 'message' => 'Error al obtener el UUID del archivo.']);
    }

    // Llamar a la función del modelo para actualizar el UUID en la base de datos
    $tableauditModel = new TableauditM();
    $result = $tableauditModel->updateUuidConstancia($id_tbl_auditoria_cursos, $uuid);

    // Verificar si la actualización fue exitosa
    if ($result) {
        return response()->json(['status' => true, 'uuid' => $uuid]);
    } else {
        return response()->json(['status' => false, 'message' => 'Error al actualizar la base de datos']);
    }
}

public function uploadToAlfresco($file)
{
    // Obtener el folderId de alguna manera, puede ser de la base de datos o parámetros de entrada
    $folderId = 'id_del_folder_alfresco'; // Aquí deberías tener el folderId correspondiente

    // Crear una instancia de AlfrescoC para poder usar su método add
    $alfrescoC = new AlfrescoC();

    // Llamar a la función add en el controlador de AlfrescoC para subir el archivo
    $fileUid = $alfrescoC->add($file, $folderId); // Usamos el método add de AlfrescoC para subir el archivo

    if (!$fileUid) {
        // Si no se puede subir el archivo, retornamos un error
        return response()->json(['status' => false, 'message' => 'Error al subir el archivo a Alfresco.']);
    }

    // Si el archivo se subió correctamente, retornamos el UUID
    return $fileUid; // Retornamos el UUID que nos da Alfresco
}


public function seeDocument(Request $request)
{
    $alfresco = new AlfrescoC(); // Crear una instancia del controlador
    return $alfresco->see($request); // Llamar la función see y retornar la respuesta
}
public function downloadDocument(Request $request)
{
    $alfresco = new AlfrescoC(); // Instancia del controlador Alfresco
    return $alfresco->download($request); // Llamar a la función de descarga y devolver la respuesta
}
public function deleteDocument(Request $request)
{
    $uid = $request->uid;

    Log::info("🗑️ Intentando eliminar el documento con UID: " . $uid);

    // Verificar que el UID es válido
    if (!$uid) {
        Log::error("❌ Error: No se recibió un UID válido para eliminar.");
        return response()->json(['status' => false, 'message' => 'UID inválido.']);
    }

    // Llamar a la función de eliminación en Alfresco
    $eliminado = $this->alfresco->delete($uid);

    if ($eliminado) {
        Log::info("✅ Documento eliminado en Alfresco. Procediendo a eliminar en la base de datos.");

        // Buscar y actualizar la base de datos para eliminar la referencia de `uuid_constancias`
        $documento = TableauditM::where('uuid_constancias', $uid)->first();

        if ($documento) {
            // Eliminar la referencia de `uuid_constancias`
            $documento->uuid_constancias = null;

            $documento->save();
            Log::info("✅ Documento eliminado de la base de datos.");

            return response()->json(['status' => true, 'message' => 'Documento eliminado.']);
        } else {
            Log::warning("⚠️ No se encontró el documento en la base de datos.");
            return response()->json(['status' => true, 'message' => 'Documento eliminado de Alfresco, pero no encontrado en la base de datos.']);
        }
    } else {
        Log::error("❌ Error: No se pudo eliminar el documento en Alfresco.");
        return response()->json(['status' => false, 'message' => 'No se pudo eliminar el documento.']);
    }
}




}