<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionClaveM extends Model
{
    //La funcion obtiene el catalogo de clave dependiendo del tramite
    public function list($idTramite)
    {
        return DB::table('correspondencia.cat_clave')  // Especificamos la tabla 'cat_clave' en el esquema 'correspondencia'
            ->select(DB::raw('correspondencia.cat_clave.id_cat_clave AS id, 
                UPPER(correspondencia.cat_clave.descripcion) AS descripcion'))
            ->join('correspondencia.rel_tramite_clave', 'correspondencia.cat_clave.id_cat_clave', '=', 'correspondencia.rel_tramite_clave.id_cat_clave')  // Realizamos el INNER JOIN entre 'cat_clave' y 'rel_tramite_clave'
            ->where('correspondencia.rel_tramite_clave.id_cat_tramite', $idTramite)  // Filtramos por el 'id_cat_tramite'
            ->where('correspondencia.cat_clave.estatus', true)  // Filtramos por 'estatus' en la tabla 'cat_clave'
            ->orderBy('correspondencia.cat_clave.descripcion', 'ASC')  // Ordenamos por 'descripcion' en 'cat_clave'
            ->get();  // Ejecutamos y obtenemos los resultados
    }


    public function listEdit($idTramite)
    {
        return DB::table('correspondencia.cat_clave')  // Especificamos la tabla 'cat_clave' en el esquema 'correspondencia'
            ->select(DB::raw('correspondencia.cat_clave.id_cat_clave AS id, 
                UPPER(correspondencia.cat_clave.descripcion) AS descripcion'))
            ->join('correspondencia.rel_tramite_clave', 'correspondencia.cat_clave.id_cat_clave', '=', 'correspondencia.rel_tramite_clave.id_cat_clave')  // Realizamos el INNER JOIN entre 'cat_clave' y 'rel_tramite_clave'
            ->where('correspondencia.rel_tramite_clave.id_cat_tramite', $idTramite)  // Filtramos por el 'id_cat_tramite'
            ->where('correspondencia.cat_clave.estatus', true)  // Filtramos por 'estatus' en la tabla 'cat_clave'
            ->orderBy('correspondencia.cat_clave.descripcion', 'ASC')  // Ordenamos por 'descripcion' en 'cat_clave'
            ->get();  // Ejecutamos y obtenemos los resultados
    }

    //La funcion obtiene todos los resultados de la tabla clave, dependidendo del catalogo de clave que se seleccione
    public function getData($idClave)
    {
        // Usamos el Query Builder para hacer la consulta
        $result = DB::table('correspondencia.cat_clave')
            ->selectRaw('
                    id_cat_clave AS id, 
                    UPPER(descripcion) AS descripcion,
                    UPPER(redaccion) AS redaccion,
                    UPPER(codigo) AS codigo,
                    UPPER(copiar) AS copiar
                ')
            ->where('id_cat_clave', $idClave)
            ->first(); // Usamos first() ya que solo esperamos un único resultado

        if ($result) {
            // Formateamos la respuesta según lo que espera el JavaScript
            return [
                '_labClave' => $result->descripcion ?? '_',
                '_labClaveCodigo' => $result->codigo ?? '_',
                '_labClaveRedaccion' => $result->redaccion ?? '_',
            ];
        } else {
            // Si no encontramos el registro, devolvemos un valor predeterminado
            return [
                '_labClave' => '_',
                '_labClaveCodigo' => '_',
                '_labClaveRedaccion' => '_',
            ];
        }
    }

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_clave')
            ->select([
                'correspondencia.cat_clave.id_cat_clave AS id',
                DB::raw('UPPER(correspondencia.cat_clave.codigo) || \' - \' || UPPER(correspondencia.cat_clave.descripcion) AS descripcion')
            ])
            ->where('correspondencia.cat_clave.id_cat_clave', '=', $id);

        $result = $query->first();
        return $result;
    }
}
