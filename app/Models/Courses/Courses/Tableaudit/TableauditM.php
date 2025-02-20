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

    public function list($id_curso)
    {
        DB::table('capacitacion.tbl_auditoria_cursos')->insertUsing(
            ['id_cat_auditoria', 'id_tbl_cursos', 'estatus'],
            DB::table('capacitacion.cat_auditoria')
                ->select('id_cat_auditoria', DB::raw($id_curso), DB::raw('true'))
                ->where('estatus', true)
        );

    }
    public function listaudit($iterator)
    {
        $query = DB::table('capacitacion.tbl_auditoria_cursos AS aud_cursos')
        ->select([
            'aud_cursos.id_tbl_auditoria_cursos AS id_tbl_auditoria_cursos',
            DB::raw("UPPER(auditoria.descripcion) AS descripcion")
        ])
        ->join('capacitacion.cat_auditoria AS auditoria', 'auditoria.id_cat_auditoria', '=', 'aud_cursos.id_cat_auditoria');

        return $query;
        
        }

        
        
}