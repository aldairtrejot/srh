<?php

namespace App\Models\Courses\Courses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CoursesprogramaM extends Model
{
    protected $table = 'capacitacion.cat_programa_institucional';
    protected $primaryKey = 'id_cat_programa_institucional'; // Especifica la clave primaria
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
        'estatus',
        'id_usuario_sistema',
        'fecha_usuario',
        'nombre',
    ];

    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('capacitacion.cat_programa')
            ->where('id_programa', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }
    public function list($iterator, $searchValue )
    {
        // Preparar la consulta base
        $query = DB::table('capacitacion.cat_programa_institucional')
        ->select([
            'capacitacion.cat_programa_institucional.id_cat_programa_institucional AS id',
            DB::raw('UPPER(capacitacion.cat_programa_institucional.descripcion) AS descripcion'),
            DB::raw('CASE WHEN capacitacion.cat_programa_institucional.estatus = 1 THEN TRUE ELSE FALSE END AS estatus'),
            DB::raw('UPPER(capacitacion.cat_programa_institucional.nombre) AS nombre'),
        ]); 

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(capacitacion.cat_programa_institucional.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(capacitacion.cat_programa_institucional.estatus)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(capacitacion.cat_programa_institucional.nombre)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('capacitacion.cat_programa_institucional.id_cat_programa_institucional', 'ASC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }
    public function listprograma()
    {
        $query = DB::table('capacitacion.cat_programa_institucional')
            ->select([
                'capacitacion.cat_programa_institucional.id_cat_programa_institucional AS id',
                DB::raw("UPPER(capacitacion.cat_programa_institucional.nombre || '-' || capacitacion.cat_programa_institucional.descripcion) AS descripcion")
            ])
            ->where('estatus', '=', true)
            ->orderBy('capacitacion.cat_programa_institucional.descripcion', 'ASC');
    
        // Ejecutar la consulta y obtener los resultados
        $results = $query->get();
    
        // Retornar los resultados
        return $results;
    }
}