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

    public function relinstructor($id)
    {
        // Realizar la consulta utilizando el query builder de Laravel para editar
        $relinstructor = DB::table('capacitacion.rel_cursos_instructor')
            ->where('id_tbl_cursos', $id)
            ->value('id_tbl_instructores');

        // Si no se encuentra información, retornamos null
        return $relinstructor ?: null;
    }


}