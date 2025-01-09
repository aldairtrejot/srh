<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CloudCvM extends Model
{
    // Hace referencia a la tabla de CVs donde se guardarán los datos
    protected $table = 'cloud_cvs'; // Nombre de la tabla
    public $timestamps = false; // No se utilizan timestamps automáticos
    protected $primaryKey = 'id'; // Clave primaria de la tabla

    protected $fillable = [
        'uid',                 // Identificador único generado por Alfresco
        'nombre',              // Nombre del archivo
        'estatus',             // Estatus del archivo (activo/inactivo)
        'fecha_usuario',       // Fecha de última modificación
        'id_instructor',       // Relación con la tabla de instructores
        'id_usuario_sistema',  // Usuario que realizó la última modificación
        'id_cat_tipo_doc_cloud', // Tipo de documento (si aplica)
    ];
}