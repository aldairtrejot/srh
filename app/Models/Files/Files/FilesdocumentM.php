<?php

namespace App\Models\Files\Files;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class FilesdocumentM extends Model
{
    protected $table = 'expediente.cat_documento';
    protected $primaryKey = 'id_cat_documento'; // Especifica la clave primaria
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
        'estatus',
        'id_usuario_creacion',
        'id_modificacion',
        'creado_en',
        'actualizado_en',
    ];

    public function list($iterator, $searchValue)
    {
        // Preparar la consulta base
        $query = DB::table('expediente.cat_documento')
        ->select([
            'expediente.cat_documento.id_cat_documento AS id',
            DB::raw('UPPER(expediente.cat_documento.descripcion) AS descripcion'),
            DB::raw('CASE WHEN expediente.cat_documento.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]); 
            

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(expediente.cat_documento.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(expediente.cat_documento.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('expediente.cat_documento.id_cat_documento', 'ASC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }
    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('expediente.cat_documento')
            ->where('id_cat_documento', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }
    public function listdocuments()
    {
        $query = DB::table('expediente.cat_documento')
            ->select([
                'expediente.cat_documento.id_cat_documento AS id',
                DB::raw('UPPER(expediente.cat_documento.descripcion) AS descripcion')
            ])
            ->where('estatus', '=', true)
            ->orderBy('expediente.cat_documento.descripcion', 'ASC');
    
        // Ejecutar la consulta y obtener los resultados
        $results = $query->get();
    
        // Retornar los resultados
        return $results;
    }
}