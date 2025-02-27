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
            'id_usuario_sistema' => Auth::user()->id,
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
    Log::info("📥 Descargando documento con UUID: " . $uuid);

    $alfresco = new AlfrescoC();
    return $alfresco->download(new Request(['uid' => $uuid]));
}


    /**
     * 🔍 Ver archivo desde Alfresco
     */
    public function see(Request $request)
    {
        return $this->alfresco->see($request);
    }

    /**
     * 🗑️ Eliminar archivo de Alfresco
     */
    public function delete(Request $request)
    {
        return response()->json(['status' => $this->alfresco->delete($request->uid)]);
    }

    public function cloud($id)
{
    Log::info("📌 Accediendo a Cloud para el instructor ID: " . $id);

    return view('courses.tableinstructor.cloud', [
        'idInstructor' => $id
    ]);
}
}