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
}
