<?php

namespace App\Models\Letter\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CountReportModel extends Model
{
    public function countReportModel()
    {
        $query = DB::table('correspondencia.tbl_correspondencia');

        // Código para no administradores
        if (
            ! in_array(1, session('SESSION_ROLE_USER', [])) &&
            ! in_array(2, session('SESSION_ROLE_USER', []))
        ) {
            $query->join('correspondencia.ctrl_rol_usuario_area',
                'correspondencia.tbl_correspondencia.id_cat_area',
                '=',
                'correspondencia.ctrl_rol_usuario_area.id_cat_area'
            )->where('correspondencia.ctrl_rol_usuario_area.id_usuario', Auth::id());
        }

        return $query->count();
    }
}
