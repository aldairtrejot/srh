<?php

namespace App\Models\Letter\File;

use Illuminate\Database\Eloquent\Model;

class CloudAnexosM extends Model
{
    protected $table = 'correspondencia.ctrl_expediente_anexo';
    public $timestamps = false;
    protected $primaryKey = 'id_ctrl_expediente_anexo';
    protected $fillable = [
        'uid',
        'nombre',
        'estatus',
        'fecha_usuario',
        'id_tbl_expediente',
        'id_usuario_sistema',
        'id_cat_tipo_doc_cloud',
    ];
}
