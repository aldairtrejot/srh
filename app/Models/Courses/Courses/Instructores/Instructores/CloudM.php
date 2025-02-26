<?php

namespace App\Models\Courses\Courses\Instructores\Instructores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloudM extends Model
{
    use HasFactory;

    protected $table = 'capacitacion.tbl_instructores'; // Nombre exacto de la tabla
    protected $primaryKey = 'id_tbl_instructores'; // Clave primaria

    public $timestamps = false; // Evita timestamps automáticos

    protected $fillable = [
        'estatus',
        'id_usuario_sistema',
        'fecha_usuario',
        'id_usuario_empleado',
        'uid_constancias',
        'uid_cv',
        'nombre_cv',
        'nombre_constancia',
    ];

    // Obtener información por ID
    public static function getCloudData($id)
    {
        return self::where('id_tbl_instructores', $id)->first();
    }

    // Actualizar documentos
    public static function updateDocument($id, $data)
    {
        if (!$id || !is_numeric($id)) {
            \Log::error("Error en updateDocument(): id_tbl_cv es inválido", ['id' => $id]);
            return false;
        }

        return self::where('id_tbl_instructores', $id)->update($data);
    }
}