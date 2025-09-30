<?php

namespace App\Models\Letter\Dashboard;

use Illuminate\Database\Eloquent\Model;

class CountReportModel extends Model
{
    public function countReportModel()
    {

        $query = 'SELECT COUNT (correspondencia.tbl_correspondencia.id_tbl_correspondencia)
FROM correspondencia.tbl_correspondencia';

        if ( // Código de no administradores
            ! in_array(1, session('SESSION_ROLE_USER', [])) &&
            ! in_array(2, session('SESSION_ROLE_USER', []))
        ) {

        }
        /*
        else { // solo administradores estatus ok
            $query->when(! empty($idArea), function ($query) use ($idArea) {
                return $query->where('correspondencia.tbl_correspondencia.id_cat_area', '=', $idArea);
            });
        }
            */
    }
}
