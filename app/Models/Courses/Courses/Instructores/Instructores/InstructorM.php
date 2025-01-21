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
    $query = DB::table('capacitacion.tbl_instructores')
        ->select([
            'capacitacion.tbl_instructores.id_tbl_instructores',
            DB::raw("
                CASE
                    WHEN administration.users.id_cat_tipo_schema = 1 THEN UPPER(central.curp)
                    WHEN administration.users.id_cat_tipo_schema = 2 THEN UPPER(public.curp)
                    WHEN administration.users.id_cat_tipo_schema = 3 THEN UPPER(transferidos.curp)
                END AS curp
            "),
            DB::raw("
                CASE
                    WHEN administration.users.id_cat_tipo_schema = 1 THEN UPPER(central.nombre || ' ' || central.primer_apellido || ' ' || central.segundo_apellido)
                    WHEN administration.users.id_cat_tipo_schema = 2 THEN UPPER(public.nombre || ' ' || public.primer_apellido || ' ' || public.segundo_apellido)
                    WHEN administration.users.id_cat_tipo_schema = 3 THEN UPPER(transferidos.nombre || ' ' || transferidos.primer_apellido || ' ' || transferidos.segundo_apellido)
                END AS nombre_completo
            "),
            DB::raw("
                CASE
                    WHEN capacitacion.tbl_instructores.estatus IS TRUE THEN 'ACTIVO'
                    ELSE 'INACTIVO'
                END AS estatus_instructor
            "),
        ])
        ->join(table: 'administration.users', first: 'capacitacion.tbl_instructores.id_usuario_empleado', operator: '=', second: 'administration.users.id')
        ->leftJoin(table: 'central.tbl_empleados_hraes as central', first: 'administration.users.id_tbl_empleados_central', operator: '=', second: 'central.id_tbl_empleados_hraes')
        ->leftJoin(table: 'transferidos.tbl_empleados as transferidos', first: 'administration.users.id_tbl_empleados_central', operator: '=', second: 'transferidos.id_tbl_empleados')
        ->leftJoin(table: 'public.tbl_empleados_hraes as public', first: 'administration.users.id_tbl_empleados_hraes', operator: '=', second: 'public.id_tbl_empleados_hraes');

    // Agregar condiciones de búsqueda
    if (!empty($searchValue)) {
        $searchValue = strtoupper(trim($searchValue));
        $query->where(function ($query) use ($searchValue) {
            $query->whereRaw("UPPER(central.curp) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(public.curp) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(transferidos.curp) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(central.nombre || ' ' || central.primer_apellido || ' ' || central.segundo_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(public.nombre || ' ' || public.primer_apellido || ' ' || public.segundo_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(transferidos.nombre || ' ' || transferidos.primer_apellido || ' ' || transferidos.segundo_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("
                        CASE
                            WHEN capacitacion.tbl_instructores.estatus IS TRUE THEN 'ACTIVO'
                            ELSE 'INACTIVO'
                        END LIKE ?
                  ", ['%' . $searchValue . '%']);
        });
    }

    // Aplicar paginación y orden
    $query->orderBy('capacitacion.tbl_instructores.id_tbl_instructores', 'ASC')
        ->offset(max(0, (int)$iterator))
        ->limit(5);

    return $query->get();
}

    /*public function edit(string $id)
    {
          // Realizamos la consulta utilizando el Query Builder de Laravel
          $query = DB::table('capacitacion.tbl_instructores')
          ->where('id_tbl_instructores', $id)
          ->first(); // Usamos first() para obtener un único registro

      // Retornamos el usuario o null si no se encuentra
      return $query ?? null;

    }*/


    // BUSQUEDA DE CURP 
    public function centralCurp($curp)
    {
        return DB::table('central.tbl_empleados_hraes')
            ->select([
                DB::raw('UPPER(rfc) AS RFC'),
                DB::raw('UPPER(curp) AS CURP'),
                DB::raw('UPPER(nombre) AS NOMBRE'),
                DB::raw('UPPER(primer_apellido) AS PRIMER_APELLIDO'),
                DB::raw('UPPER(segundo_apellido) AS SEGUNDO_APELLIDO'),
            ])
            ->where('curp', '=', $curp)
            ->first(); // Devuelve null si no encuentra un registro
    }

    public function buscarEmpleadoHRAES($curp)
{
    return DB::table('public.tbl_empleados_hraes')
        ->select([
            DB::raw('UPPER(rfc) AS RFC'),
            DB::raw('UPPER(curp) AS CURP'),
            DB::raw('UPPER(nombre) AS NOMBRE'),
            DB::raw('UPPER(primer_apellido) AS PRIMER_APELLIDO'),
            DB::raw('UPPER(segundo_apellido) AS SEGUNDO_APELLIDO'),
        ])
        ->where('curp', '=', $curp)
        ->first(); // Devuelve null si no encuentra un registro
}

public function buscarEmpleadoTransferidos($curp)
{
    return DB::table('transferidos.tbl_empleados')
        ->select([
            DB::raw('UPPER(rfc) AS RFC'),
            DB::raw('UPPER(curp) AS CURP'),
            DB::raw('UPPER(nombre) AS NOMBRE'),
            DB::raw('UPPER(primer_apellido) AS PRIMER_APELLIDO'),
            DB::raw('UPPER(segundo_apellido) AS SEGUNDO_APELLIDO'),
        ])
        ->where('curp', '=', $curp)
        ->first(); // Devuelve null si no encuentra un registro
}

    /*public function editCurp (string $id)
    {
          // Realizamos la consulta utilizando el Query Builder de Laravel
          $query = DB::table('capacitacion.tbl_instructores')
          ->where('id_tbl_instructores', $id)
          ->first(); // Usamos first() para obtener un único registro

      // Retornamos el usuario o null si no se encuentra
      return $query ?? null;
    }*/


}
