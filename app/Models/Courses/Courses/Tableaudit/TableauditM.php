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
    public function list()
    {
        // Preparar la consulta base
        $query = DB::table('capacitacion.cat_auditoria')
            ->select([
                DB::raw('capacitacion.cat_auditoria.descripcion AS descripcion')
            ])
            ->get(); // Ejecutar la consulta y obtener los resultados
    
        return $query; // Retornar los resultados
    }
    public function auditlist($id_curso)
    {
        DB::table('capacitacion.tbl_auditoria_cursos')->insertUsing(
            ['id_cat_auditoria', 'id_tbl_cursos', 'estatus'],
            DB::table('capacitacion.cat_auditoria')
                ->select('id_cat_auditoria', DB::raw($id_curso), DB::raw('true'))
                ->where('estatus', true)
        );
    }
    public static function getConstanciaUuid()
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
}