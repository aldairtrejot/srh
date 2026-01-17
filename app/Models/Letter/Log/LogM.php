<?php

namespace App\Models\Letter\Log;

use Illuminate\Database\Eloquent\Model;

class LogM extends Model
{
    protected $table = 'correspondencia.tbl_movimientos_sistema';
    public $timestamps = false;
    protected $primaryKey = 'id_tbl_movimientos_sistema';
    protected $fillable = [
        'nombre_tabla',
        'accion',
        'valores',
        'fecha',
        'id_usuario',
    ];
}
