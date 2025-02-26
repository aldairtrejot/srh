<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Courses\Tableinstructor\AlfrescoC;
use App\Models\Courses\Courses\Instructores\Instructores\CloudM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CloudC extends Controller
{
    public function cloudData(Request $request)
    {
        $cloudData = CloudM::getCloudData($request->id_tbl_cv);
        return response()->json([
            'value' => $cloudData,
            'status' => true,
        ]);
    }

    public function cloudConstancias(Request $request)
    {
        $cloudData = CloudM::getCloudData($request->id_tbl_cv);
        return response()->json([
            'constancias' => $cloudData->uid_constancias,
            'cvs' => $cloudData->uid_cv,
            'status' => true,
        ]);
    }

    public function upload(Request $request)
    {
        Log::info("📥 Datos recibidos en `upload()`:", $request->all()); // 🔍 Debug

        $alfresco = new AlfrescoC();
        $status = false;
        $messages = 'Error en la subida';

        // Obtener valores del request y validarlos
        $id_tbl_cv = $request->input('id_tbl_cv');
        $id_cat_area = $request->input('id_cat_area');
        $esCv = $request->input('esCv');

        if (empty($id_tbl_cv) || !is_numeric($id_tbl_cv) || empty($id_cat_area)) {
            Log::error("❌ Error en upload(): Parámetros inválidos", [
                'id_tbl_cv' => $id_tbl_cv,
                'id_cat_area' => $id_cat_area,
                'esCv' => $esCv
            ]);
            return response()->json([
                'messages' => 'Error: Faltan parámetros obligatorios en la solicitud.',
                'status' => false
            ]);
        }

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $uid = $alfresco->addFile($file, $id_cat_area, $esCv);

            if ($uid) {
                $data = [
                    'fecha_usuario' => Carbon::now(),
                    'id_usuario_sistema' => Auth::user()->id,
                ];

                if ($esCv) {
                    $data['uid_cv'] = $uid;
                    $data['nombre_cv'] = $file->getClientOriginalName();
                } else {
                    $data['uid_constancias'] = $uid;
                    $data['nombre_constancia'] = $file->getClientOriginalName();
                }

                CloudM::updateDocument($id_tbl_cv, $data);
                $status = true;
                $messages = 'Documento subido correctamente.';
            } else {
                Log::error("❌ Error al subir archivo a Alfresco");
            }
        } else {
            Log::error("❌ Error: No se recibió archivo válido.");
        }

        return response()->json(['messages' => $messages, 'status' => $status]);
    }

    public function delete(Request $request)
    {
        Log::info("🗑 Eliminando archivo UID:", ['uid' => $request->uid]);

        $alfresco = new AlfrescoC();
        $status = false;

        if ($alfresco->delete($request->uid)) {
            $data = [
                'fecha_usuario' => Carbon::now(),
                'id_usuario_sistema' => Auth::user()->id,
            ];

            if ($request->esCv) {
                $data['uid_cv'] = null;
                $data['nombre_cv'] = null;
            } else {
                $data['uid_constancias'] = null;
                $data['nombre_constancia'] = null;
            }

            CloudM::updateDocument($request->id_tbl_cv, $data);
            $status = true;
        } else {
            Log::error("❌ Error: No se pudo eliminar el archivo en Alfresco.");
        }

        return response()->json(['status' => $status]);
    }
}