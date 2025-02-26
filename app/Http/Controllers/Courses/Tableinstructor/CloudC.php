<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Courses\Tableinstructor\AlfrescoC;
use App\Models\Courses\Courses\Instructores\Instructores\CloudM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
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
        $alfresco = new AlfrescoC();
        $status = false;
        $messages = 'Error en la subida';

        // Obtener valores y validar que no sean nulos
        $id_tbl_cv = $request->input('id_tbl_cv');
        $id_usuario_sistema = Auth::user()->id ?? null;

        if (empty($id_tbl_cv) || !is_numeric($id_tbl_cv)) {
            return response()->json([
                'messages' => 'Error: ID inválido para la subida.',
                'status' => false
            ]);
        }

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $uid = $alfresco->addFile($file, $request->id_cat_area, $request->esCv);

            if ($uid) {
                $data = [
                    'fecha_usuario' => Carbon::now(),
                    'id_usuario_sistema' => $id_usuario_sistema,
                ];

                if ($request->esCv) {
                    $data['uid_cv'] = $uid;
                    $data['nombre_cv'] = $file->getClientOriginalName();
                } else {
                    $data['uid_constancias'] = $uid;
                    $data['nombre_constancia'] = $file->getClientOriginalName();
                }

                CloudM::updateDocument($id_tbl_cv, $data);
                $status = true;
                $messages = 'Documento subido correctamente.';
            }
        }

        return response()->json(['messages' => $messages, 'status' => $status]);
    }

    public function delete(Request $request)
    {
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
        }

        return response()->json(['status' => $status]);
    }
}