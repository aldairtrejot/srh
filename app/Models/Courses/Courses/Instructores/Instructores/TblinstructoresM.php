<?php

namespace App\Models\Courses\Courses\Instructores\Instructores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TblinstructoresM extends Model
{
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
}