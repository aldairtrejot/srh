<?php

namespace App\Models\Courses\Courses\Instructores\Instructores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblinstructoresM extends Model{
    protected $table = 'capacitacion.tbl_instructores';
    protected $primaryKey = 'id_tbl_instructores';
    public $timestamps = false;

    protected $fillable = [
        'estatus',
        'id_usuario_sistema',
        'fecha_usuario',
        'id_usuario_empleado',
        'uid_constancias',
        'uid_cv',
        'nombre_cv',
        'nombre_constancia',

    ];

    public function create ($id)
    {
        // Realizar la consulta utilizando el query builder de Laravel para editar
        $relinstructor = DB::table('capacitacion.tbl_instructores')
            ->where('id_usuario_sistema', $id)
            ->value('id_usuario_empleado');

        // Si no se encuentra información, retornamos null
        return $relinstructor ?: null;
    }


}