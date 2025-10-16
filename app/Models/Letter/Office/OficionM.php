<?php

namespace App\Models\Letter\Office;

use Illuminate\Database\Eloquent\Model;

class OficionM extends Model
{
    protected $table = 'correspondencia.ctrl_oficio_oficio';

    public $timestamps = false;

    protected $primaryKey = 'id_ctrl_oficio_oficio';

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
