<?php

namespace App\Models\Courses\Courses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class InstructorM extends Model
{
    protected $table = 'capacitacion.tbl_instructores';
    protected $primaryKey = 'id_instructor'; // Especifica la clave primaria
    public $timestamps = false;
    protected $fillable = [
        'id_instructor',
        'id_empleados',
        'uuid_constancia',
        'uuid_cv',
        'estatus_apto',
        'estatus_instructor',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('capacitacion.tbl_instructores')
            ->where('id_instructor', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }
    public function list($iterator, $searchValue)
{
    // Construcción de la consulta base
    $query = DB::table('capacitacion.tbl_instructores')
        ->select([
            'capacitacion.tbl_instructores.id_instructor AS id',
            'capacitacion.tbl_instructores.id_empleados',
            'capacitacion.tbl_instructores.uuid_constancia',
            'capacitacion.tbl_instructores.uuid_cv',
            DB::raw('CASE WHEN capacitacion.tbl_instructores.estatus_apto = 1 THEN TRUE ELSE FALSE END AS estatus_apto'),
            'capacitacion.tbl_instructores.estatus_instructor'
        ]);

    // Si se proporciona un valor de búsqueda, aplicar filtros
    if (!empty($searchValue)) {
        $searchValue = strtoupper(trim($searchValue));

        $query->where(function ($query) use ($searchValue) {
            $query->whereRaw("UPPER(TRIM(capacitacion.tbl_instructores.id_empleados)) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(TRIM(capacitacion.tbl_instructores.uuid_constancia)) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(TRIM(capacitacion.tbl_instructores.uuid_cv)) LIKE ?", ['%' . $searchValue . '%']);
        });
    }

    // Aplicar orden y paginación
    $query->orderBy('capacitacion.tbl_instructores.id_instructor', 'ASC')
          ->offset($iterator)
          ->limit(5);

    // Ejecutar la consulta y retornar resultados
    return $query->get();
}

}