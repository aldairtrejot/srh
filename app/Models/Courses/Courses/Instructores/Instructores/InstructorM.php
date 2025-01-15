<?php

namespace App\Models\Courses\Courses\Instructores\Instructores;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InstructorM extends Model
{
    protected $table = 'capacitacion.tbl_instructores';
    protected $primaryKey = 'id_tbl_instructores';
    public $timestamps = false;
    protected $fillable = [
        'id_usuario_empleado',
        'estatus',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

    public function list($iterator, $searchValue)
    {
        // Preparar la consulta base
        $query = DB::table($this->table)
            ->select([
                'capacitacion.tbl_instructores.id_tbl_instructores',
                DB::raw("
                    CASE
                        WHEN administration.users.id_cat_tipo_schema = 1 THEN UPPER(central.tbl_empleados_hraes.curp)
                        WHEN administration.users.id_cat_tipo_schema = 2 THEN UPPER(public.tbl_empleados_hraes.curp)
                        WHEN administration.users.id_cat_tipo_schema = 3 THEN UPPER(transferidos.tbl_empleados.curp)
                    END AS curp
                "),
                DB::raw("
                    CASE
                        WHEN administration.users.id_cat_tipo_schema = 1 THEN UPPER(central.tbl_empleados_hraes.nombre || ' ' || central.tbl_empleados_hraes.primer_apellido || ' ' || central.tbl_empleados_hraes.segundo_apellido)
                        WHEN administration.users.id_cat_tipo_schema = 2 THEN UPPER(public.tbl_empleados_hraes.nombre || ' ' || public.tbl_empleados_hraes.primer_apellido || ' ' || public.tbl_empleados_hraes.segundo_apellido)
                        WHEN administration.users.id_cat_tipo_schema = 3 THEN UPPER(transferidos.tbl_empleados.nombre || ' ' || transferidos.tbl_empleados.primer_apellido || ' ' || transferidos.tbl_empleados.segundo_apellido)
                    END AS nombre
                "),
                DB::raw("
                    CASE
                        WHEN capacitacion.tbl_instructores.estatus IS TRUE THEN 'ACTIVO'
                        ELSE 'INACTIVO'
                    END AS estatus
                "),
            ])
            ->join('administration.users', 'capacitacion.tbl_instructores.id_usuario_empleado', '=', 'administration.users.id')
            ->leftJoin('central.tbl_empleados_hraes', 'administration.users.id_tbl_empleados_central', '=', 'central.tbl_empleados_hraes.id_tbl_empleados_hraes')
            ->leftJoin('transferidos.tbl_empleados', 'administration.users.id_tbl_empleados_central', '=', 'transferidos.tbl_empleados.id_tbl_empleados')
            ->leftJoin('public.tbl_empleados_hraes', 'administration.users.id_tbl_empleados_hraes', '=', 'public.tbl_empleados_hraes.id_tbl_empleados_hraes');

        // Si se proporciona un valor de búsqueda, agregar condiciones
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->where('capacitacion.tbl_instructores.id_tbl_instructores', 'LIKE', '%' . $searchValue . '%')
                      ->orWhere('capacitacion.tbl_instructores.estatus', 'LIKE', '%' . $searchValue . '%');
            });
        }

        // Validar y aplicar paginación
        $iterator = max(0, (int)$iterator);
        $query->orderBy('capacitacion.tbl_instructores.id_tbl_instructores', 'ASC')
            ->offset($iterator)
            ->limit(5);

        return $query->get();
    }

    public function edit(string $id)
    {
        // Usar Eloquent para obtener el registro
        return self::find($id);
    }
}
