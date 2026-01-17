<?php

namespace App\Models\Courses\Courses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CoursesM extends Model
{
    protected $table = 'capacitacion.cat_beneficio';
    protected $primaryKey = 'id_cat_beneficio'; // Especifica la clave primaria
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
        'estatus',
        'id_usuario_sistema',
        'fecha_usuario',
    ];


    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        // Preparar la consulta base
        $query = DB::table('capacitacion.cat_beneficio')
        ->select([
            'capacitacion.cat_beneficio.id_cat_beneficio AS id',
            DB::raw('UPPER(capacitacion.cat_beneficio.descripcion) AS descripcion'),
            DB::raw('CASE WHEN capacitacion.cat_beneficio.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]); 
          

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(capacitacion.cat_beneficio.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(capacitacion.cat_beneficio.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('capacitacion.cat_beneficio.id_cat_beneficio', 'ASC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }

    
    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('capacitacion.cat_beneficio')
            ->where('id_beneficio', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }
    public function listbeneficio()
    {
        $query = DB::table('capacitacion.cat_beneficio')
            ->select([
                'capacitacion.cat_beneficio.id_cat_beneficio AS id',
                DB::raw('UPPER(capacitacion.cat_beneficio.descripcion) AS descripcion')
            ])
            ->where('estatus', '=', true)
            ->orderBy('capacitacion.cat_beneficio.descripcion', 'ASC');
    
        // Ejecutar la consulta y obtener los resultados
        $results = $query->get();
    
        // Retornar los resultados
        return $results;
    }
}
  
    
   

