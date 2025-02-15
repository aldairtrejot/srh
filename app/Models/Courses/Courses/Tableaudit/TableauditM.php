<?php

namespace App\Models\Courses\Courses\Tableaudit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class TableauditM extends Model
{
    protected $table = 'capacitacion.tbl_auditoria_cursos';
    protected $primaryKey = 'id_tbl_auditoria_cursos'; // Especifica la clave primaria
    public $timestamps = false;
    protected $fillable = [
        'id_cat_auditoria',
        'id_tbl_cursos',
        'estatus',
        'uuid_constancias',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

    public function list()
    {
        // Preparar la consulta base
        $query = DB::table('capacitacion.cat_auditoria')
        ->select([
        DB::raw('capacitacion.cat_auditoria.descripcion AS descripcion')
        ])
        ->where('estatus', true);

    }
}