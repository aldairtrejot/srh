<?php

namespace App\Models\Letter\External;

use Illuminate\Database\Eloquent\Model;

class CloudAnexosM extends Model
{
    //HACE REFERENCIA A LA TABLA DE OFICIOS DONDE SE GUARDARAN LOS DATOS
    protected $table = 'correspondencia.ctrl_circular_ext_anexo';
    public $timestamps = false;
    protected $primaryKey = 'id_ctrl_circular_ext_anexo';
    protected $fillable = [
        'uid',
        'nombre',
        'estatus',
        'fecha_usuario',
        'id_tbl_circular_externa',
        'id_usuario_sistema',
        'id_cat_tipo_doc_cloud',
    ];
}
