<?php

namespace App\Models\Letter\Round;

use Illuminate\Database\Eloquent\Model;

class CloudAnexosM extends Model
{
    protected $table = 'correspondencia.ctrl_circular_anexo';
    public $timestamps = false;
    protected $primaryKey = 'id_ctrl_circular_anexo';
    protected $fillable = [
        'uid',
        'nombre',
        'estatus',
        'fecha_usuario',
        'id_tbl_circular',
        'id_usuario_sistema',
        'id_cat_tipo_doc_cloud',
    ];
}
