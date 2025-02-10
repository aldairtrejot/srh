<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionSolicitanteM extends Model
{

    protected $table = 'correspondencia.cat_solicitante';
    public $timestamps = false;
    protected $primaryKey = 'id_cat_solicitante';
    protected $fillable = [
        'nombre',
        'rfc',
        'estatus',
        'primer_apellido',
        'segundo_apellido',
        'profesion',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

    // LA función lista todos los solicitantes activos, para los catalogos
    public function list()
    {
        $result = DB::table('correspondencia.cat_solicitante')
            ->selectRaw('correspondencia.cat_solicitante.id_cat_solicitante AS id, 
                         UPPER(correspondencia.cat_solicitante.nombre) || \' \' || 
                         UPPER(correspondencia.cat_solicitante.primer_apellido) || \' \' || 
                         UPPER(correspondencia.cat_solicitante.segundo_apellido) AS descripcion')
            ->where('correspondencia.cat_solicitante.estatus', true)
            ->orderBy('correspondencia.cat_solicitante.nombre', 'asc')
            ->get();

        return $result;
    }

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_solicitante')
            ->select([
                'correspondencia.cat_solicitante.id_cat_solicitante AS id',
                DB::raw('correspondencia.cat_solicitante.id_cat_solicitante AS id, 
                         UPPER(correspondencia.cat_solicitante.nombre) || \' \' || 
                         UPPER(correspondencia.cat_solicitante.primer_apellido) || \' \' || 
                         UPPER(correspondencia.cat_solicitante.segundo_apellido) AS descripcion')
            ])
            ->where('correspondencia.cat_solicitante.id_cat_solicitante', '=', $id);

        // Usar first() para obtener un único resultado
        $result = $query->first();
        return $result;
    }

    //LA funcion obtiene solo el solicitante por nombre, para mostrar en el catalogo
    public function editByName($name)
    {
        $result = DB::table('correspondencia.cat_solicitante')
            ->selectRaw('correspondencia.cat_solicitante.id_cat_solicitante AS id, 
                     UPPER(correspondencia.cat_solicitante.nombre) || \' \' || 
                     UPPER(correspondencia.cat_solicitante.primer_apellido) || \' \' || 
                     UPPER(correspondencia.cat_solicitante.segundo_apellido) AS descripcion')
            ->whereRaw('TRIM(UPPER(correspondencia.cat_solicitante.nombre)) = TRIM(UPPER(?))', [$name])
            ->get();

        return $result;
    }

    public function equalName($name)
    {
        $result = DB::table('correspondencia.cat_solicitante')
            ->whereRaw('TRIM(UPPER(correspondencia.cat_solicitante.nombre)) = TRIM(UPPER(?))', [$name])
            ->value('correspondencia.cat_solicitante.id_cat_solicitante');

        return $result ? true : false;
    }
}
