<?php

namespace App\Models\Letter\Collection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CollectionConfigCloudInternoM extends Model
{
    // La función retorna el valor del uuid para que se almacene en alfresco
    public function getUuid($idAnio, $idDocumento)
    {
        $query = DB::table('correspondencia.cat_config_cloud_interno')
            ->where('correspondencia.cat_config_cloud_interno.id_cat_anio', $idAnio)
            ->where('correspondencia.cat_config_cloud_interno.id_cat_tipo_documento', $idDocumento)
            ->select('correspondencia.cat_config_cloud_interno.uuid as uuid')
            ->first();

        return $query->uuid;
    }

}
