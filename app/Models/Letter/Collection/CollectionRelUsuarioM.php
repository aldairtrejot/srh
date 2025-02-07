<?php

namespace App\Models\Letter\Collection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CollectionRelUsuarioM extends Model
{
    protected $table = 'correspondencia.rel_area_usuario';
    public $timestamps = false;
    protected $fillable = [
        'id_rel_area_usuario',
        'estatus',
        'id_cat_area',
        'id_usuario',
    ];
    public function idAreaByUser($idUser)
    {
        // Consulta utilizando el Query Builder de DB
        return DB::table('correspondencia.rel_area_usuario') // Especificamos la tabla
            ->where('id_usuario', $idUser)              // Agregamos la condición para el id_usuario
            ->pluck('id_cat_area');                      // Obtenemos los valores de id_cat_area
    }

    //la funcion se utiliza en catalagos, cuando se selecciona el catalogo de area. muestra los enlaces asociados a ese catalogo
    public function idUsuarioByArea($idArea)
    {
        return DB::table('administration.users')
            ->select(DB::raw('id, UPPER(name) as descripcion'))
            ->join('correspondencia.rel_area_usuario', 'administration.users.id', '=', 'correspondencia.rel_area_usuario.id_usuario')
            ->where('correspondencia.rel_area_usuario.id_cat_area', $idArea)
            ->where('correspondencia.rel_area_usuario.estatus', true)
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

    // La función obtiene el id de area y id de uaurio, a apartir del enlace
    public function idAreaUser($idEnlace)
    {
        return DB::table('correspondencia.rel_enlace_usuario')
            ->join('correspondencia.cat_area', 'correspondencia.rel_enlace_usuario.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->join('correspondencia.rel_area_usuario', 'correspondencia.cat_area.id_cat_area', '=', 'correspondencia.rel_area_usuario.id_cat_area')
            ->where('correspondencia.rel_enlace_usuario.id_usuario', '=', $idEnlace)
            ->where('correspondencia.rel_area_usuario.estatus', '=', true)  // Asegúrate que 'estatus' sea booleano o compara contra 1 si es necesario
            ->select(
                'correspondencia.rel_enlace_usuario.id_rel_enlace_usuario AS id',
                'correspondencia.rel_enlace_usuario.id_cat_area AS id_cat_area',
                'correspondencia.rel_area_usuario.id_usuario AS id_usuario'
            )
            ->first();  // Retorna solo el primer registro
    }
}
