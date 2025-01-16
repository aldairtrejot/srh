<?php

namespace App\Models\Courses\Courses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CoursescategoriaM extends Model
{
    protected $table = 'capacitacion.cat_categoria';
    protected $primaryKey = 'id_cat_categoria'; // Especifica la clave primaria
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
        $query = DB::table('capacitacion.cat_categoria')
        ->select([
            'capacitacion.cat_categoria.id_cat_categoria AS id',
            DB::raw('UPPER(capacitacion.cat_categoria.descripcion) AS descripcion'),
            DB::raw('CASE WHEN capacitacion.cat_categoria.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]); 
            //->join('correspondencia.cat_estatus', 'correspondencia.tbl_correspondencia.id_cat_estatus', '=', 'correspondencia.cat_estatus.id_cat_estatus')
            //->join('correspondencia.cat_area', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            //->join('correspondencia.cat_tramite', 'correspondencia.tbl_correspondencia.id_cat_tramite', '=', 'correspondencia.cat_tramite.id_cat_tramite');

        // Filtrar por área si se proporciona el id
      //  if (!empty($idArea)) {
          //  $query->where('correspondencia.tbl_correspondencia.id_cat_area', $idArea);
       // }

        // Filtrar por enlace si se proporciona el id
       // if (!empty($idEnlace)) {
          //  $query->where('correspondencia.tbl_correspondencia.id_usuario_enlace', $idEnlace);
        //}

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(capacitacion.cat_categoria.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(capacitacion.cat_categoria.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('capacitacion.cat_categoria.id_cat_categoria', 'ASC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }
    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('capacitacion.cat_categoria')
            ->where('id_cat_categoria', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }
    public function listcategoria()
    {
        $query = DB::table('capacitacion.cat_categoria')
            ->select([
                'capacitacion.cat_categoria.id_cat_categoria AS id',
                DB::raw('UPPER(capacitacion.cat_categoria.descripcion) AS descripcion')
            ])
            ->where('estatus', '=', true)
            ->orderBy('capacitacion.cat_categoria.descripcion', 'ASC');
    
        // Ejecutar la consulta y obtener los resultados
        $results = $query->get();
    
        // Retornar los resultados
        return $results;
    }
}