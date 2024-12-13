<?php

namespace App\Models\Letter\Cloud;

use Illuminate\Database\Eloquent\Model;

class CloudAnexosM extends Model
{
    protected $table = 'correspondencia.ctrl_oficio_anexo';
    public $timestamps = false;
    protected $primaryKey = 'id_ctrl_oficio_anexo';
    protected $fillable = [
        'uid',
        'nombre',
        'estatus',
        'fecha_usuario',
        'id_tbl_oficio',
        'id_usuario_sistema',
        'id_cat_tipo_doc_cloud',
    ];
}
