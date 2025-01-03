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

    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        // Preparar la consulta base
        $query = DB::table('capacitacion.cat_auditoria')
        ->select([
            'capacitacion.cat_auditoria.id_auditoriaAS id',
            DB::raw('UPPER(capacitacion.cat_auditoria.descripcion) AS descripcion'),
            DB::raw('CASE WHEN capacitacion.cat_auditoria.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
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