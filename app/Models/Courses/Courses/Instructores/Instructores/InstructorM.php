<?php

namespace App\Models\Courses\Courses\Instructores\Instructores;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InstructorM extends Model
{
    protected $table = 'capacitacion.tbl_instructores'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_tbl_instructores'; // Clave primaria
    public $timestamps = false; // Desactivar timestamps si no se usan en la tabla
    protected $fillable = [
        'id_tbl_instructores',
        'estatus_instructor',
        'id_usuario_sistema',
        'fecha_usuario',
        'id_usuario_empleado',
    ];

    /**
     * Obtener un instructor por ID.
     *
     * @param string $id
     * @return object|null
     */
    public function edit(string $id)
    {
        return DB::table($this->table)
            ->where('id_tbl_instructores', $id)
            ->first();
    }

    /**
     * Obtener lista de instructores con paginación y búsqueda.
     *
     * @param int $iterator
     * @param string|null $searchValue
     * @return \Illuminate\Support\Collection
     */
    public function list($iterator = 0, $searchValue = null)
    {
        // Construcción de la consulta base
        $query = DB::table($this->table)
            ->select([
                "{$this->table}.id_tbl_instructores AS id",
                "{$this->table}.estatus_instructor",
                "{$this->table}.id_usuario_sistema",
                "{$this->table}.fecha_usuario",
                "{$this->table}.id_usuario_empleado",
            ]);

        // Aplicar filtros de búsqueda si corresponde
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($subquery) use ($searchValue) {
                $subquery->whereRaw("UPPER(TRIM({$this->table}.id_usuario_empleado)) LIKE ?", ['%' . $searchValue . '%'])
                         ->orWhereRaw("UPPER(TRIM({$this->table}.estatus_instructor)) LIKE ?", ['%' . $searchValue . '%'])
                         ->orWhereRaw("UPPER(TRIM({$this->table}.id_usuario_sistema)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar orden y paginación
        $query->orderBy("{$this->table}.id_tbl_instructores", 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }

    public function obtenerInstructoresConDetalles()
    {
        $query = DB::table('capacitacion.tbl_instructores')
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
                    END AS nombre_completo
                "),
            ])
            ->join('administration.users', 'capacitacion.tbl_instructores.id_usuario_empleado', '=', 'administration.users.id')
            ->leftJoin('central.tbl_empleados_hraes', 'administration.users.id_tbl_empleados_central', '=', 'central.tbl_empleados_hraes.id_tbl_empleados_hraes')
            ->leftJoin('transferidos.tbl_empleados', 'administration.users.id_tbl_empleados_central', '=', 'transferidos.tbl_empleados.id_tbl_empleados')
            ->leftJoin('public.tbl_empleados_hraes', 'administration.users.id_tbl_empleados_hraes', '=', 'public.tbl_empleados_hraes.id_tbl_empleados_hraes');
    
        return $query->get();
    }
    
}
      