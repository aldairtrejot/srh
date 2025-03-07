<?php

namespace App\Models\Courses\Courses\Assignedcourse;

use Illuminate\Database\Eloquent\Model;

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
}