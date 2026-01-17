<?php

namespace App\Models\Letter\Letter;

use Illuminate\Database\Eloquent\Model;

class CloudAnexosM extends Model
{
    protected $table = 'correspondencia.ctrl_correspondencia_anexo';
    public $timestamps = false;
    protected $primaryKey = 'id_ctrl_correspondencia_anexo';
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
