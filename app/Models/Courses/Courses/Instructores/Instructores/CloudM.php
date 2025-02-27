<?php

namespace App\Models\Courses\Courses\Instructores\Instructores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CloudM extends Model
{
    use HasFactory;

    protected $table = 'capacitacion.tbl_instructores';
    protected $primaryKey = 'id_tbl_instructores';
    public $timestamps = false;

    protected $fillable = [
        'estatus', 'id_usuario_sistema', 'fecha_usuario',
        'id_usuario_empleado', 'uid_constancias', 'uid_cv',
        'nombre_cv', 'nombre_constancia',
    ];

    /**
     * 📌 Obtiene el UID de la carpeta donde se subirá el CV.
     */
    public static function getCvUuid()
    {
        try {
            $uuid = DB::table('capacitacion.cat_tipo_uid_cloud AS uid')
                ->join('capacitacion.cat_tipo_doc_cloud AS doc', 'uid.id_cat_tipo_doc_cloud', '=', 'doc.id_cat_tipo_doc_cloud')
                ->where('doc.id_cat_tipo_doc_cloud', 5) // 5 es el ID para CVs
                ->value('uid.uuid');

            if (!$uuid) {
                Log::warning("⚠️ No se encontró el UID de la carpeta para CVs (ID: 5)");
                return null;
            }

            Log::info("✅ UID de carpeta para CVs obtenido: {$uuid}");
            return $uuid;
        } catch (\Exception $e) {
            Log::error("❌ Error en getCvUuid(): " . $e->getMessage());
            return null;
        }
    }

    /**
     * 📌 Obtiene el UID de la carpeta donde se subirá la Constancia.
     */
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

    public static function updateDocument($id, $data)
{
    try {
        if (!is_numeric($id)) {
            Log::error("❌ ID inválido en updateDocument()", ['id' => $id]);
            return false;
        }

        $updated = self::where('id_tbl_instructores', $id)->update($data);

        if ($updated) {
            Log::info("✅ Documento actualizado correctamente en tbl_instructores", ['id' => $id, 'data' => $data]);
        } else {
            Log::warning("⚠️ No se encontró ningún registro para actualizar con ID: $id");
        }

        return $updated;
    } catch (\Exception $e) {
        Log::error("❌ Error en updateDocument(): " . $e->getMessage());
        return false;
    }
}

}
