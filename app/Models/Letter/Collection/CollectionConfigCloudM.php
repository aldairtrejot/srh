<?php

namespace App\Models\Letter\Collection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CollectionConfigCloudM extends Model
{
    //Apartir de la funcion se retorna el valor de la configuracion de cloud, pasando como parametro el id de la llave primaria
    public function getValue($id)
    {
        // Realizamos la consulta usando el Query Builder de Laravel
        $query = DB::table('correspondencia.config_cloud')
            ->select('valor') // Seleccionamos solo la columna 'valor'
            ->where('id_config_cloud', $id) // Filtramos por el 'id_config_cloud'
            ->where('estatus', true) // Aseguramos que el 'estatus' sea true
            ->first(); // Usamos 'first' ya que esperamos un único resultado

        // Retornamos el valor del campo 'valor' si existe, o null si no existe
        return $query ? $query->valor : null;
    }
}
