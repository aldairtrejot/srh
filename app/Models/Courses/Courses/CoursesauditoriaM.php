<?php

namespace App\Models\Courses\Courses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CoursesauditoriaM extends Model
{
    protected $table = 'capacitacion.cat_auditoria';
    protected $primaryKey = 'id_auditoria'; // Especifica la clave primaria
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
        'estatus',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

    public function list($iterator, $searchValue,)
    {
        // Preparar la consulta base
        $query = DB::table('capacitacion.cat_auditoria')
        ->select([
            'capacitacion.cat_auditoria.id_auditoriaAS id',
            DB::raw('UPPER(capacitacion.cat_auditoria.descripcion) AS descripcion'),
            DB::raw('CASE WHEN capacitacion.cat_auditoria.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]); 
            
        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(capacitacion.cat_auditoria.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(capacitacion.cat_auditoria.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('capacitacion.cat_auditoria.id_auditoria', 'ASC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }
    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('capacitacion.cat_auditoria')
            ->where('id_auditoria', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }
}