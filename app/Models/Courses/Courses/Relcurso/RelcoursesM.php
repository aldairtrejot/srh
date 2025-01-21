<?php

namespace App\Models\Courses\Courses\Relcurso;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RelcoursesM extends Model{
    protected $table = 'capacitacion.rel_cursos_instructor';
    protected $primaryKey = 'id_rel_cursos_instructor';
    public $timestamps = false;

    protected $fillable = [
        'fecha_usuario',
        'id_usuario_sistema',
        'id_tbl_cursos',
        'id_tbl_instructores', 
    ];


}