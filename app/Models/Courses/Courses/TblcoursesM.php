<?php

namespace App\Models\Courses\Courses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblcoursesM extends Model
{
    protected $table = 'capacitacion.tbl_cursos';
    protected $primaryKey = 'id_tbl_cursos';
    public $timestamps = false;

    protected $fillable = [
        'id_cat_tipo_cursos',
        'id_cat_coordinacion',
        'id_cat_nombre_accion',
        'id_cat_programa_institucional',
        'id_cat_estatuto_organico',
        'programa_proyecto',
        'id_cat_beneficio',
        'id_cat_organizacion',
        'id_cat_tipo_accion',
        'id_cat_modalidad',
        'id_cat_categoria',
        'costo',
        'iva',
        'fecha_inicio',
        'fecha_fin',
        'horas',
        'id_usuario_sistema',
        'nombre_completo',
    ];

    public function list($iterator, $searchValue)
    {
        $query = DB::table('capacitacion.tbl_cursos AS cursos')
            ->select([
                'cursos.id_tbl_cursos AS id_tbl_cursos',
                DB::raw("UPPER(cursos.programa_proyecto) AS nombre_curso"),
                DB::raw("UPPER(beneficio.descripcion) AS categoria_beneficio"),
                DB::raw("UPPER(curso.descripcion) AS categoria_tipo_curso"),
                DB::raw("UPPER(taccion.descripcion) AS categoria_tipo_accion"),
                DB::raw("UPPER(pinstitucional.descripcion) AS categoria_programa_institucional"),
                DB::raw("(cursos.costo + cursos.iva) AS costo_total"),
                DB::raw("TO_CHAR(cursos.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(cursos.fecha_fin, 'DD/MM/YYYY') AS fecha_fin"),
                'cursos.horas AS horas_curso',
                DB::raw("CASE
                    WHEN usr.id_cat_tipo_schema = 1 THEN 
                        UPPER(CONCAT_WS(' ', central.nombre, central.primer_apellido, COALESCE(central.segundo_apellido, '')))
                    WHEN usr.id_cat_tipo_schema = 2 THEN 
                        UPPER(CONCAT_WS(' ', public.nombre, public.primer_apellido, COALESCE(public.segundo_apellido, '')))
                    WHEN usr.id_cat_tipo_schema = 3 THEN 
                        UPPER(CONCAT_WS(' ', transferidos.nombre, transferidos.primer_apellido, COALESCE(transferidos.segundo_apellido, '')))
                    ELSE 
                        NULL
                END AS nombre_completo"),
                'cursos.estatus AS estatus'
            ])
            ->join('capacitacion.rel_cursos_instructor AS rel', 'cursos.id_tbl_cursos', '=', 'rel.id_tbl_cursos')
            ->join('capacitacion.tbl_instructores AS instr', 'rel.id_tbl_instructores', '=', 'instr.id_tbl_instructores')
            ->join('administration.users AS usr', 'instr.id_usuario_empleado', '=', 'usr.id')
            ->leftJoin('central.tbl_empleados_hraes AS central', 'usr.id_tbl_empleados_central', '=', 'central.id_tbl_empleados_hraes')
            ->leftJoin('transferidos.tbl_empleados AS transferidos', 'usr.id_tbl_empleados_central', '=', 'transferidos.id_tbl_empleados')
            ->leftJoin('public.tbl_empleados_hraes AS public', 'usr.id_tbl_empleados_hraes', '=', 'public.id_tbl_empleados_hraes')
            ->join('capacitacion.cat_tipo_cursos AS curso', 'cursos.id_cat_tipo_cursos', '=', 'curso.id_cat_tipo_cursos')
            ->join('capacitacion.cat_beneficio AS beneficio', 'cursos.id_cat_beneficio', '=', 'beneficio.id_cat_beneficio')
            ->join('capacitacion.cat_tipo_accion AS taccion', 'cursos.id_cat_tipo_accion', '=', 'taccion.id_cat_tipo_accion')
            ->join('capacitacion.cat_programa_institucional AS pinstitucional', 'cursos.id_cat_programa_institucional', '=', 'pinstitucional.id_cat_programa_institucional');

        // Agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(cursos.programa_proyecto) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(beneficio.descripcion) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(curso.descripcion) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(taccion.descripcion) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(pinstitucional.descripcion) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("TO_CHAR(cursos.fecha_inicio, 'DD/MM/YYYY') LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("TO_CHAR(cursos.fecha_fin, 'DD/MM/YYYY') LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("
                        CASE
                            WHEN cursos.estatus IS TRUE THEN 'ACTIVO'
                            ELSE 'INACTIVO'
                        END LIKE ?
                  ", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(CONCAT_WS(' ', central.nombre, central.primer_apellido, COALESCE(central.segundo_apellido, ''))) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(CONCAT_WS(' ', public.nombre, public.primer_apellido, COALESCE(public.segundo_apellido, ''))) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(CONCAT_WS(' ', transferidos.nombre, transferidos.primer_apellido, COALESCE(transferidos.segundo_apellido, ''))) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        return $query->paginate(5, ['*'], 'page', $iterator);
    }
}

