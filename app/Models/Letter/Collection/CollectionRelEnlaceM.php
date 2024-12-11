<?php

namespace App\Models\Letter\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionRelEnlaceM extends Model
{
    protected $table = 'correspondencia.rel_enlace_usuario';
    public $timestamps = false;
    protected $fillable = [
        'id_rel_enlace_usuario',
        'estatus',
        'id_cat_area',
        'id_usuario',
    ];
    public function idAreaByUser($idUser)
    {
        // Consulta utilizando el Query Builder de DB
        return DB::table('correspondencia.rel_enlace_usuario') // Especificamos la tabla
            ->where('id_usuario', $idUser)              // Agregamos la condición para el id_usuario
            ->pluck('id_cat_area');                      // Obtenemos los valores de id_cat_area
    }

    //la funcion se utiliza en catalagos, cuando se selecciona el catalogo de area. muestra los enlaces asociados a ese catalogo
    public function idUsuarioByArea($idArea)
    {
        return DB::table('administration.users')
            ->select(DB::raw('id, UPPER(name) as descripcion'))
            ->join('correspondencia.rel_enlace_usuario', 'administration.users.id', '=', 'correspondencia.rel_enlace_usuario.id_usuario')
            ->where('correspondencia.rel_enlace_usuario.id_cat_area', $idArea)
            ->where('correspondencia.rel_enlace_usuario.estatus', true)
            ->get();
    }

    public function idUsuarioByAreaEdit($idUsuario)
    {
        $query = DB::table('administration.users')
            ->select([
                'administration.users.id AS id',
                DB::raw('UPPER(administration.users.name) AS descripcion')
            ])
            ->where('administration.users.id', '=', $idUsuario);
        // Usar first() para obtener un único resultado
        $result = $query->first();
        return $result;
    }
}
