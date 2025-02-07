<?php

namespace App\Models\Courses\Courses\Instructores\Instructores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TblInstructorM extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'administration.users';

    // Clave primaria
    protected $primaryKey = 'id';

    // Marcar timestamps como activos o desactivados
    public $timestamps = true;

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'create_at',
        'update_at',
        'id_tbl_empleados_central',
        'id_tbl_empleados_hraes',
        'id_tbl_empleados_trasnferidos',
        'id_tbl_empleados_aux',
        'es_por_nomina',
        'estatus',
        'id_usuario',
        'fecha_usuario',
        'id_cat_tipo_schema',
    ];

    // Puedes agregar relaciones si es necesario, por ejemplo:
    // Relación con otra tabla (One-to-Many, Many-to-Many, etc.)
    public function empleadoCentral()
    {
        //return $this->belongsTo(EmpleadoCentral::class, 'id_tbl_empleados_central');
    }
}
