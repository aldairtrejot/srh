<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Cloud\AlfrescoC;
use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\Instructores\Instructores\CloudM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CloudTableC extends Controller
{
    protected $alfresco;

    public function __construct()
    {
        $this->alfresco = new AlfrescoC();
    }

    /**
     * 📂 Subir archivos a Alfresco y guardar en la base de datos
     */
    public function upload(Request $request)
    {
        Log::info("📥 Datos recibidos en upload():", $request->all());

        $id_tbl_cv = $request->input('id_tbl_cv');
        $esCv = $request->input('esCv'); // 1 = CV, 0 = Constancia

        if (!$id_tbl_cv || !is_numeric($id_tbl_cv)) {
            Log::error("❌ Error: ID de instructor inválido", ['id_tbl_cv' => $id_tbl_cv]);
            return response()->json(['messages' => 'Error: ID inválido.', 'status' => false]);
        }

        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            Log::error("❌ Error: Archivo inválido o no recibido.");
            return response()->json(['messages' => 'Archivo inválido.', 'status' => false]);
        }

        // Determinar la carpeta en Alfresco
        $folderId = $esCv ? CloudM::getCvUuid() : CloudM::getConstanciaUuid();

        if (!$folderId) {
            Log::error("❌ Error: No se encontró la carpeta de destino en Alfresco.");
            return response()->json(['messages' => 'Error: No se encontró la carpeta de destino.', 'status' => false]);
        }

        // Subir el archivo a Alfresco
        $file = $request->file('file');
        $uid = $this->alfresco->add($file, $folderId);

        if (!$uid) {
            Log::error("❌ Error: No se pudo subir el archivo.");
            return response()->json(['messages' => 'Error al subir archivo.', 'status' => false]);
        }

        // Guardar en la base de datos
        $data = [
            'fecha_usuario' => Carbon::now(),
            'id_usuario_sistema' => Auth::id(),
            ($esCv ? 'uid_cv' : 'uid_constancias') => $uid,
            ($esCv ? 'nombre_cv' : 'nombre_constancia') => $file->getClientOriginalName(),
        ];

        CloudM::updateDocument($id_tbl_cv, $data);

        return response()->json(['messages' => 'Archivo subido correctamente.', 'status' => true]);
    }

    /**
     * 📡 Obtener datos de archivos en cloud
     */
    public function cloudData(Request $request)
    {
        Log::info("📡 Obteniendo datos de Cloud para instructor ID: " . $request->id_tbl_cv);

        $cloudData = CloudM::where('id_tbl_instructores', $request->id_tbl_cv)->first();

        if (!$cloudData) {
            return response()->json(['status' => false, 'message' => 'No se encontraron documentos.']);
        }

        return response()->json([
            'status' => true,
            'cvs' => $cloudData->uid_cv ? [['nombre' => $cloudData->nombre_cv, 'uid' => $cloudData->uid_cv]] : [],
            'constancias' => $cloudData->uid_constancias ? [['nombre' => $cloudData->nombre_constancia, 'uid' => $cloudData->uid_constancias]] : [],
        ]);
    }

    /**
     * 📥 Descargar archivo desde Alfresco
     */
    public function download($uuid)
    {
        Log::info("📥 Intentando descargar documento con UUID: " . $uuid);
    
        $request = new Request(['uid' => $uuid]);
        return $this->alfresco->download($request);
    }
    
    /**
     * 🔍 Ver archivo desde Alfresco
     */
    public function see(Request $request)
    {
        if (!$request->has('uid')) {
            Log::error("❌ Error: No se recibió un UID válido para visualizar.");
            return response()->json(['status' => false, 'message' => 'UID inválido.']);
        }

        return $this->alfresco->see($request);
    }

    /**
     * 🗑️ Eliminar archivo de Alfresco
     */
    public function delete(Request $request)
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

        // Buscar y actualizar la base de datos para eliminar la referencia
        $documento = CloudM::where('uid_cv', $uid)->orWhere('uid_constancias', $uid)->first();

        if ($documento) {
            if ($documento->uid_cv == $uid) {
                $documento->uid_cv = null;
                $documento->nombre_cv = null;
            } elseif ($documento->uid_constancias == $uid) {
                $documento->uid_constancias = null;
                $documento->nombre_constancia = null;
            }

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


    /**
     * 📌 Vista de Cloud para el instructor
     */
    public function cloud($id)
    {
        Log::info("📌 Accediendo a Cloud para el instructor ID: " . $id);

        return view('courses.tableinstructor.cloud', [
            'idInstructor' => $id
        ]);
    }
}
