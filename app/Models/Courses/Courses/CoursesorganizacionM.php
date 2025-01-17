<?php

namespace App\Models\Courses\Courses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CoursesorganizacionM extends Model
{
    protected $table = 'capacitacion.cat_organizacion';
    protected $primaryKey = 'id_cat_organizacion'; // Especifica la clave primaria
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
        $query = DB::table('capacitacion.cat_organizacion')
            ->where('id_cat_organizacion', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }
    public function list($iterator, $searchValue )
    {
        // Preparar la consulta base
        $query = DB::table('capacitacion.cat_organizacion')
        ->select([
            'capacitacion.cat_organizacion.id_cat_organizacion AS id',
            DB::raw('UPPER(capacitacion.cat_organizacion.descripcion) AS descripcion'),
            DB::raw('CASE WHEN capacitacion.cat_organizacion.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]); 

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(capacitacion.cat_organizacion.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(capacitacion.cat_organizacion.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('capacitacion.cat_organizacion.id_cat_organizacion', 'ASC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }
    public function listorganizacion()
    {
        $query = DB::table('capacitacion.cat_organizacion')
            ->select([
                'capacitacion.cat_organizacion.id_cat_organizacion AS id',
                DB::raw('UPPER(capacitacion.cat_organizacion.descripcion) AS descripcion')
            ])
            ->where('estatus', '=', true)
            ->orderBy('capacitacion.cat_organizacion.descripcion', 'ASC');
    
        // Ejecutar la consulta y obtener los resultados
        $results = $query->get();
    
        // Retornar los resultados
        return $results;
    }
}