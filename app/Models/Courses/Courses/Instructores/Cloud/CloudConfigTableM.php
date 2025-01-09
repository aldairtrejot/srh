<?php

namespace App\Models\Courses\Cloud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CloudConfigTableM extends Model
{
    // Hace referencia a la tabla de configuración de la nube
    protected $table = 'cloud_config_table'; // Nombre de la tabla
    public $timestamps = false; // No se utilizan timestamps automáticos
    protected $primaryKey = 'id'; // Clave primaria de la tabla

    /**
     * Retorna el valor de una configuración específica.
     *
     * @param int $id
     * @return mixed|null
     */
    public function getData($id)
    {
        $query = DB::table($this->table)
            ->select('valor')
            ->where('id', $id)
            ->where('estatus', true)
            ->first();

        return $query->valor ?? null;
    }

    /**
     * Retorna el UID de una configuración específica, útil como referencia.
     *
     * @param int $id_cat_area
     * @param int $id_cat_tipo_doc_cloud
     * @param int $id_cat_nombre_oficio
     * @return mixed|null
     */
    public function getUid($id_cat_area, $id_cat_tipo_doc_cloud, $id_cat_nombre_oficio)
    {
        $uid = DB::table('cloud_cat_config')
            ->select('uid')
            ->where('estatus', true)
            ->where('id_cat_area', $id_cat_area)
            ->where('id_cat_tipo_doc_cloud', $id_cat_tipo_doc_cloud)
            ->where('id_cat_nombre_oficio', $id_cat_nombre_oficio)
            ->first();

        return $uid->uid ?? null;
    }
}