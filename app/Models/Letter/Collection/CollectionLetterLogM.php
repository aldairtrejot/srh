<?php

namespace App\Models\Letter\Collection;

use Illuminate\Database\Eloquent\Model;

class CollectionLetterLogM extends Model
{
    protected $table = 'correspondencia.ctrl_correspondencia_log';
    public $timestamps = false;
    protected $primaryKey = 'id_ctrl_correspondencia_log';
    protected $fillable = [
        'estatus',
        'num_documento',
        'folio_gestion',
        'asunto',
        'observaciones',
        'id_cat_area',
        'id_cat_estatus',
        'id_tbl_correspondencia',
        'fecha_usuario_captura',
        'id_usuario_captura',
    ];
}
