<?php

namespace App\Models\Courses\Courses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CoursesmodalidadM extends Model
{
    protected $table = 'capacitacion.cat_modalidad';
    protected $primaryKey = 'id_cat_modalidad'; // Especifica la clave primaria
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
        'estatus',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('capacitacion.cat_modalidad')
            ->where('id_cat_modalidad', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }
    public function list($iterator, $searchValue )
    {
        // Preparar la consulta base
        $query = DB::table('capacitacion.cat_modalidad')
        ->select([
            'capacitacion.cat_modalidad.id_cat_modalidad AS id',
            DB::raw('UPPER(capacitacion.cat_modalidad.descripcion) AS descripcion'),
            DB::raw('CASE WHEN capacitacion.cat_modalidad.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]); 

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(capacitacion.cat_modalidad.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(capacitacion.cat_modalidad.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('capacitacion.cat_modalidad.id_cat_modalidad', 'ASC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }
    public function listmodalidad()
    {
        $query = DB::table('capacitacion.cat_modalidad')
            ->select([
                'capacitacion.cat_modalidad.id_cat_modalidad AS id',
                DB::raw('UPPER(capacitacion.cat_modalidad.descripcion) AS descripcion')
            ])
            ->where('estatus', '=', true)
            ->orderBy('capacitacion.cat_modalidad.descripcion', 'ASC');
    
        // Ejecutar la consulta y obtener los resultados
        $results = $query->get();
    
        // Retornar los resultados
        return $results;
    }
}