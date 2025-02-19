<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
}
