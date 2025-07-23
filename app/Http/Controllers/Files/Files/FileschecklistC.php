<?php

namespace App\Http\Controllers\Files\Files;

use App\Http\Controllers\Controller;
use App\Models\Files\Files\FileschecklistM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileschecklistC extends Controller
{
    /**
     * Vista principal del checklist del expediente
     */
    public function view($id)
    {
        $model = new FileschecklistM();

        // Obtener información del empleado
        $empleado = $model->obtenerEmpleadoPorId($id);
        if (!$empleado) {
            abort(404, 'Empleado no encontrado');
        }

        // Insertar en tbl_gestion_documentos si no existe
        $model->insertarGestionDocumentoPorEmpleado($id);

        // Obtener los documentos activos para este empleado
        $documentos = $model->obtenerDocumentosChecklistPorEmpleado($id);

        // Obtener estatus actuales (opcional)
        $estatusExistente = $model->obtenerEstatusDocumentosPorEmpleado($id);
        $estatusMap = collect($estatusExistente)->keyBy('id_cat_documento');

        return view('files.fileschecklist.list', [
            'id' => $id,
            'empleado' => $empleado,
            'empleado_json' => json_encode($empleado),
            'documentos' => $documentos,
            'estatusMap' => $estatusMap
        ]);
    }

    /**
     * Guardar o actualizar estatus del checklist
     */
    public function guardarChecklist(Request $request)
    {
        $data = $request->validate([
            'id_cat_documento' => 'required|integer',
            'id_tbl_gestion_documentos' => 'required|integer',
            'estatus' => 'required|boolean'
        ]);

        $now = now();
        $userId = auth()->id() ?? 1; // Fallback si no hay auth

        // Verificar si ya existe la relación
        $existe = DB::table('expediente.rel_gestion_documentos_documento')
            ->where('id_tbl_gestion_documentos', $data['id_tbl_gestion_documentos'])
            ->where('id_cat_documento', $data['id_cat_documento'])
            ->first();

        if ($existe) {
            // Actualizar
            DB::table('expediente.rel_gestion_documentos_documento')
                ->where('id_rel_gestion_documentos_documento', $existe->id_rel_gestion_documentos_documento)
                ->update([
                    'estatus' => $data['estatus'],
                    'id_modificacion' => $userId,
                    'actualizado_en' => $now
                ]);
        } else {
            // Insertar
            DB::table('expediente.rel_gestion_documentos_documento')->insert([
                'id_tbl_gestion_documentos' => $data['id_tbl_gestion_documentos'],
                'id_cat_documento' => $data['id_cat_documento'],
                'estatus' => $data['estatus'],
                'id_usuario_creacion' => $userId,
                'id_modificacion' => $userId,
                'creado_en' => $now,
                'actualizado_en' => $now
            ]);
        }

        return response()->json(['success' => true]);
    }
}
