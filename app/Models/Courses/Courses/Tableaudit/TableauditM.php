<?php

namespace App\Models\Courses\Courses\Tableaudit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TableauditM extends Model
{
    protected $table = 'capacitacion.tbl_auditoria_cursos';
    protected $primaryKey = 'id_tbl_auditoria_cursos'; // Especifica la clave primaria
    public $timestamps = false;
    protected $fillable = [
        'id_cat_auditoria',
        'id_tbl_cursos',
        'estatus',
        'uuid_constancias',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

    // Función para listar auditorías
    public function list($id_tbl_cursos)
    {
        $query = DB::table('capacitacion.tbl_auditoria_cursos')
            ->join('capacitacion.cat_auditoria', 'capacitacion.tbl_auditoria_cursos.id_cat_auditoria', '=', 'capacitacion.cat_auditoria.id_cat_auditoria')
            ->where('capacitacion.tbl_auditoria_cursos.id_tbl_cursos', $id_tbl_cursos)
            ->select('capacitacion.tbl_auditoria_cursos.id_tbl_auditoria_cursos AS id', 'capacitacion.cat_auditoria.descripcion','capacitacion.tbl_auditoria_cursos.estatus','capacitacion.tbl_auditoria_cursos.uuid_constancias AS uuid')
            ->orderBy('capacitacion.tbl_auditoria_cursos.id_tbl_auditoria_cursos','ASC')
            ->orderBy('capacitacion.tbl_auditoria_cursos.id_cat_auditoria','ASC')
            ->get();
    
        return response()->json($query); // Retornar en formato JSON
    }

    // Función para realizar auditoría
    public function auditlist($id_curso)
    {
        // Verifica si ya existe un registro con el mismo id_tbl_cursos y id_cat_auditoria
        $existing = DB::table('capacitacion.tbl_auditoria_cursos')
            ->where('id_tbl_cursos', $id_curso)
            ->whereIn('id_cat_auditoria', function ($query) use ($id_curso) {
                $query->select('id_cat_auditoria')
                    ->from('capacitacion.cat_auditoria')
                    ->where('estatus', true)
                    ->where('id_tbl_cursos', $id_curso);
            })
            ->exists();
    
        if (!$existing) {
            // Si no existe, entonces insertamos los registros
            DB::table('capacitacion.tbl_auditoria_cursos')->insertUsing(
                ['id_cat_auditoria', 'id_tbl_cursos', 'estatus'],
                DB::table('capacitacion.cat_auditoria')
                    ->select('id_cat_auditoria', DB::raw($id_curso), DB::raw('true'))
                    ->where('estatus', true)
            );
        }
    }
    

    // Función para obtener el UUID de la carpeta de Constancias
    public function getConstanciaUuid()
    {
        try {
            $uuid = DB::table('capacitacion.cat_tipo_uid_cloud AS uid')
                ->join('capacitacion.cat_tipo_doc_cloud AS doc', 'uid.id_cat_tipo_doc_cloud', '=', 'doc.id_cat_tipo_doc_cloud')
                ->where('doc.id_cat_tipo_doc_cloud', 6) // 6 es el ID para Constancias
                ->value('uid.uuid');

            if (!$uuid) {
                Log::warning("⚠️ No se encontró el UID de la carpeta para Constancias (ID: 6)");
                return null;
            }

            Log::info("✅ UID de carpeta para Constancias obtenido: {$uuid}");
            return $uuid;
        } catch (\Exception $e) {
            Log::error("❌ Error en getConstanciaUuid(): " . $e->getMessage());
            return null;
        }
    }

    // Función para actualizar el campo uuid_constancias
    public function updateUuidConstancia($id_tbl_auditoria_cursos, $uuid)
    {
        try {
            // Realizar la actualización de la tabla
            $affected = DB::table('capacitacion.tbl_auditoria_cursos')
                ->where('id_tbl_auditoria_cursos', $id_tbl_auditoria_cursos)
                ->update([
                    'uuid_constancias' => $uuid,
                    'id_usuario_sistema' => auth(), // Si deseas registrar al usuario que realiza la acción
                    'fecha_usuario' => now(),
                ]);

            if ($affected) {
                Log::info("✅ La columna uuid_constancias ha sido actualizada para el ID: {$id_tbl_auditoria_cursos}");
                return true;
            } else {
                Log::warning("⚠️ No se encontró el ID: {$id_tbl_auditoria_cursos} para actualizar.");
                return false;
            }
        } catch (\Exception $e) {
            Log::error("❌ Error al actualizar uuid_constancias: " . $e->getMessage());
            return false;
        }
    }
}
