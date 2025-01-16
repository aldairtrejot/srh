<?php

namespace App\Models\Courses\Courses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CoursesnombreaccM extends Model
{
    protected $table = 'capacitacion.cat_nombre_accion';
    protected $primaryKey = 'id_cat_nombre_accion'; // Especifica la clave primaria
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
        $query = DB::table('capacitacion.cat_nombre_accion')
            ->where('id_cat_nombre_accion', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }
    public function list($iterator, $searchValue )
    {
        // Preparar la consulta base
        $query = DB::table('capacitacion.cat_nombre_accion')
        ->select([
            'capacitacion.cat_nombre_accion.id_cat_nombre_accion AS id',
            DB::raw('UPPER(capacitacion.cat_nombre_accion.descripcion) AS descripcion'),
            DB::raw('CASE WHEN capacitacion.cat_nombre_accion.estatus = 1 THEN TRUE ELSE FALSE END AS estatus'),
            DB::raw('UPPER(capacitacion.cat_nombre_accion.nombre) AS nombre'),
        ]); 

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(capacitacion.cat_nombre_accion.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(capacitacion.cat_nombre_accion.estatus)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(capacitacion.cat_nombre_accion.nombre)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('capacitacion.cat_nombre_accion.id_cat_nombre_accion', 'ASC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }
    public function listnomaccion()
    {
        $query = DB::table('capacitacion.cat_nombre_accion')
            ->select([
                'capacitacion.cat_nombre_accion.id_cat_nombre_accion AS id',
                DB::raw("UPPER(capacitacion.cat_nombre_accion.nombre || '-' || capacitacion.cat_nombre_accion.descripcion) AS descripcion")
            ])
            ->where('estatus', '=', true)
            ->orderBy('capacitacion.cat_nombre_accion.descripcion', 'ASC');
    
        // Ejecutar la consulta y obtener los resultados
        $results = $query->get();
    
        // Retornar los resultados
        return $results;
    }
    
}