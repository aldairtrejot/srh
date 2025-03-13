<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CollectionRolAreaM extends Model
{
    protected $table = 'correspondencia.ctrl_rol_usuario_area';
    public $timestamps = false;
    protected $primaryKey = 'id_ctrl_rol_usuario_area';
    protected $fillable = [
        'id_usuario',
        'id_cat_area',
        'estatus',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

    // La función obtiene el id_area dependiendo del usuario que ingresa
    public function getIdArea()
    {
        $idArea = DB::table('correspondencia.ctrl_rol_usuario_area')
            ->where('id_usuario', Auth::user()->id)
            ->where('estatus', true)
            ->value('id_cat_area');

        return $idArea ? $idArea : null;
    }

    // La funciónm retorna la lista de areas que esta asignado el usuario
    public function getListArea()
    {
        // Usamos el Query Builder de Laravel para crear la consulta
        $result = DB::table('correspondencia.ctrl_rol_usuario_area')
            ->select('id_cat_area')
            ->where('estatus', true)
            ->where('id_usuario', Auth::user()->id)
            ->get();

        $data = $result->pluck('id_cat_area')->toArray();
        return $data;
    }
}
