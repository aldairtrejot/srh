<?php

namespace App\Models\Letter\Dependencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DependenciaM extends Model
{
    protected $table = 'correspondencia.cat_dependencia';
    protected $primaryKey = 'id_cat_dependencia';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estatus',
    ];

    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        $query = DB::table('correspondencia.cat_dependencia')
        ->select([
            'correspondencia.cat_dependencia.id_cat_dependencia AS id',
            DB::raw('UPPER(correspondencia.cat_dependencia.descripcion) AS descripcion'),
            DB::raw('CASE WHEN correspondencia.cat_dependencia.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.cat_dependencia.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_dependencia.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        $query->orderBy('correspondencia.cat_dependencia.id_cat_dependencia', 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }

    public function edit(string $id)
    {
        $query = DB::table('correspondencia.cat_dependencia')
                  ->where('id_cat_dependencia', $id)
                  ->first();

        return $query ?? null;
    }

    public function edittblcourses($id)
    {
        $query = DB::table('correspondencia.cat_dependencia')
            ->select([
                'correspondencia.cat_dependencia.id_cat_dependencia AS id',
                DB::raw('UPPER(correspondencia.cat_dependencia.descripcion) AS descripcion')
            ])
            ->where('correspondencia.cat_dependencia.id_cat_dependencia', '=', $id);

        $result = $query->first();
        return $result;
    }
    public function listdependencia()
    {
        $query = DB::table('correspondencia.cat_dependencia')
            ->select([
                'correspondencia.cat_dependencia.id_cat_dependencia AS id',
                DB::raw('UPPER(correspondencia.cat_dependencia.descripcion) AS descripcion')
            ])
            ->where('estatus', '=', true)
            ->orderBy('correspondencia.cat_dependencia.descripcion', 'ASC');
    
        // Ejecutar la consulta y obtener los resultados
        $results = $query->get();
    
        // Retornar los resultados
        return $results;
    }
    public function editreldepencia(string $id)
    {
        $query = DB::table('correspondencia.cat_dependencia')
            ->select([
                'correspondencia.cat_dependencia.id_cat_dependencia AS id',
                DB::raw('UPPER(correspondencia.cat_dependencia.descripcion) AS descripcion')
            ])
            ->where('id_cat_dependencia', '=', $id);
    
        $result = $query->first();
    
        return $result;
    }
    


}