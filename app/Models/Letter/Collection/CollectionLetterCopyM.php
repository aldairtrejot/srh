<?php

namespace App\Models\Letter\Collection;

use Illuminate\Database\Eloquent\Model;

class CollectionLetterCopyM extends Model
{
    //Class que lleva la tabla correspondencia.ctrl_transcribir_correspondenci

    protected $table = 'correspondencia.ctrl_transcribir_correspondencia';
    public $timestamps = false;

    protected $primaryKey = 'id_ctrl_transcribir_correspondencia';
    protected $fillable = [
        'id_cat_area',
        'id_usuario_area',
        'id_usuario_enlace',
        'id_cat_tramite',
        'id_cat_clave',
        'id_tbl_correspondencia',
        'fecha_usuario',
        'id_usuario_sistema',
    ];
}
