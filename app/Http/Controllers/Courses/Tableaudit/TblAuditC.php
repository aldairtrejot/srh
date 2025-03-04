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
    $alfrescoC = new AlfrescoC();
    $tableauditM = new TableauditM();
    $now = Carbon::now(); // Hora y fecha actual
    $status = false;
    $result = null;

    if ($request->hasFile('file') && $request->file('file')->isValid()) { // Verificar si el archivo ha sido cargado correctamente
        $file = $request->file('file'); // Obtener el archivo cargado

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
            $data = [
                'uuid_constancias' => $result,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            $tableauditM::where('id_tbl_auditoria_cursos', $tableauditM->id)
                ->update($data);
            $data['id_tbl_auditoria_cursos'] = $tableauditM->id;
            $status = true;
        }
    } else {
        Log::error('Archivo no válido o no cargado.');
    }

    return response()->json([
        'status' => $status,
        'result' => $result,
    ]);
}
}