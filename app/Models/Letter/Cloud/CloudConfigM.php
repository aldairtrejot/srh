<?php

namespace App\Models\Letter\Cloud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CloudConfigM extends Model
{
    //La funcion retorna los valores de la consulta de valores de confuguracion cloud
    public function getData($id)
    {
        $query = DB::table('correspondencia.config_cloud')
            ->select('correspondencia.config_cloud.valor')
            ->where('correspondencia.config_cloud.id_config_cloud', $id)
            ->where('correspondencia.config_cloud.estatus', true)
            ->first();
        return $query ?? null;
    }

    //La funcion trae el uid, que sirve como referencia para la inserccion
    public function getUid($id_cat_area, $id_cat_tipo_doc_cloud, $id_cat_nombre_oficio)
    {
        $uid = DB::table('correspondencia.cat_config_cloud')
            ->select('uid')
            ->where('estatus', true)
            ->where('id_cat_area', $id_cat_area)
            ->where('id_cat_tipo_doc_cloud', $id_cat_tipo_doc_cloud)
            ->where('id_cat_nombre_oficio', $id_cat_nombre_oficio)
            ->first();
        return $uid ?? null;
    }
}
