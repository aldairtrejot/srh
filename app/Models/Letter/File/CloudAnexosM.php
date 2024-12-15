<?php

namespace App\Models\Letter\Inside;

use Illuminate\Database\Eloquent\Model;

class CloudAnexosM extends Model
{
    protected $table = 'correspondencia.ctrl_interno_anexo';
    public $timestamps = false;
    protected $primaryKey = 'id_ctrl_interno_anexo';
    protected $fillable = [
        'uid',
        'nombre',
        'estatus',
        'fecha_usuario',
        'id_tbl_interno',
        'id_usuario_sistema',
        'id_cat_tipo_doc_cloud',
    ];
}
