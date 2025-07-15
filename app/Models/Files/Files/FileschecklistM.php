<?php

namespace App\Models\Files\Files;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FileschecklistM extends Model
{
    public function obtenerEmpleadoPorId($id)
    {
        return DB::table('central.tbl_empleados_hraes AS e')
            ->leftJoin('central.tbl_plazas_empleados_hraes AS pe', 'e.id_tbl_empleados_hraes', '=', 'pe.id_tbl_empleados_hraes')
            ->leftJoin('central.tbl_control_plazas_hraes AS cp', 'pe.id_tbl_control_plazas_hraes', '=', 'cp.id_tbl_control_plazas_hraes')
            ->leftJoin('public.cat_unidad AS u', 'cp.id_cat_unidad', '=', 'u.id_cat_unidad')
            ->leftJoin('public.cat_coordinacion AS c', 'cp.id_cat_coordinacion', '=', 'c.id_cat_coordinacion')
            ->where('e.id_tbl_empleados_hraes', $id)
            ->select([
                'e.nombre',
                'e.primer_apellido',
                'e.segundo_apellido',
                'e.rfc',
                'e.curp',
                'u.nombre AS nombre_unidad',
                'c.nombre AS nombre_coordinacion'
            ])
            ->first(); // Solo un registro
    }
}
