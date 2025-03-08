<?php

namespace App\Models\Courses\Courses\Assignedcourse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class AssignedcourseM extends Model
{
    protected $table = 'capacitacion.tbl_empleado_cursos';
    protected $primaryKey = 'id_empleado_cursos';
    public $timestamps = false;
    protected $fillable = [
        'id_usuarios',
        'id_cursos',
        'id_calificacion',
        'estatus',
        'uuid_constancia',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

    public function list($iterator, $searchValue)
    {
        $query = DB::table('capacitacion.tbl_empleado_cursos AS e')
            ->join('administration.users AS u', 'e.id_usuarios', '=', 'u.id')
            ->leftJoin('central.tbl_empleados_hraes AS c', 'u.id_tbl_empleados_central', '=', 'c.id_tbl_empleados_hraes')
            ->leftJoin('transferidos.tbl_empleados AS t', 'u.id_tbl_empleados_transferidos', '=', 't.id_tbl_empleados')
            ->leftJoin('public.tbl_empleados_hraes AS p', 'u.id_tbl_empleados_hraes', '=', 'p.id_tbl_empleados_hraes')
            ->selectRaw("
                e.id_empleado_cursos,
                e.id_cursos,
                e.id_calificacion,
                e.uuid_constancia,
                e.fecha_usuario,
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.curp)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.curp)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.curp)
                END AS curp,
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.nombre)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.nombre)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.nombre)
                END AS nombre,
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.primer_apellido)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.primer_apellido)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.primer_apellido)
                END AS primer_apellido,
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.segundo_apellido)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.segundo_apellido)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.segundo_apellido)
                END AS segundo_apellido,
                CASE 
                    WHEN e.estatus = TRUE THEN 'ACTIVO' 
                    ELSE 'INACTIVO' 
                END AS estatus_curso
            ");
    
        // 🔍 Agregar filtro de búsqueda si hay un valor
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($q) use ($searchValue) {
                $q->whereRaw("UPPER(c.curp) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(p.curp) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(t.curp) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(c.nombre) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(p.nombre) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(t.nombre) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(c.primer_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(p.primer_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(t.primer_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(c.segundo_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(p.segundo_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(t.segundo_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(e.uuid_constancia) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("CASE WHEN e.estatus = TRUE THEN 'ACTIVO' ELSE 'INACTIVO' END LIKE ?", ['%' . $searchValue . '%']);
            });
        }
    
        return $query->paginate(5, ['*'], 'page', $iterator);
    }
    

}