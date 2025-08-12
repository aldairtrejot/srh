<?php

namespace App\Models\Letter\Letter;

use Illuminate\Database\Eloquent\Model;

class CloudOficiosM extends Model
{
    //HACE REFERENCIA A LA TABLA DE OFICIOS DONDE SE GUARDARAN LOS DATOS
    protected $table = 'correspondencia.ctrl_correspondencia_oficio';
    public $timestamps = false;
    protected $primaryKey = 'id_ctrl_correspondencia_oficio';
    protected $fillable = [
        'uid',
        'nombre',
        'estatus',
        'fecha_usuario',
        'id_tbl_correspondencia',
        'id_usuario_sistema',
        'id_cat_tipo_doc_cloud',
    ];
}
