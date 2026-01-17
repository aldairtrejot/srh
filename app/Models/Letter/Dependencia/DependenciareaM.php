<?php

namespace App\Models\Letter\Dependencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DependenciareaM extends Model
{
    protected $table = 'correspondencia.cat_dependencia_area';
    protected $primaryKey = 'id_cat_dependencia_area';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estatus',
    ];

    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        $query = DB::table('correspondencia.cat_dependencia_area')
        ->select([
            'correspondencia.cat_dependencia_area.id_cat_dependencia_area AS id',
            DB::raw('UPPER(correspondencia.cat_dependencia_area.descripcion) AS descripcion'),
            DB::raw('CASE WHEN correspondencia.cat_dependencia_area.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.cat_dependencia_area.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_dependencia_area.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        $query->orderBy('correspondencia.cat_dependencia_area.id_cat_dependencia_area', 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }

    public function edit(string $id)
    {
        $query = DB::table('correspondencia.cat_dependencia_area')
                  ->where('id_cat_dependencia_area', $id)
                  ->first();

        return $query ?? null;
    }

    public function edittblcourses($id)
    {
        $query = DB::table('correspondencia.cat_dependencia_area')
            ->select([
                'correspondencia.cat_dependencia_area.id_cat_dependencia_area AS id',
                DB::raw('UPPER(correspondencia.cat_dependencia_area.descripcion) AS descripcion')
            ])
            ->where('correspondencia.cat_dependencia_area.id_cat_dependencia_area', '=', $id);

        $result = $query->first();
        return $result;
    }
    public function listdependenciarea()
    {
        $query = DB::table('correspondencia.cat_dependencia_area')
            ->select([
                'correspondencia.cat_dependencia_area.id_cat_dependencia_area AS id',
                DB::raw('UPPER(correspondencia.cat_dependencia_area.descripcion) AS descripcion')
            ])
            ->where('estatus', '=', true)
            ->orderBy('correspondencia.cat_dependencia_area.descripcion', 'ASC');
    
        // Ejecutar la consulta y obtener los resultados
        $results = $query->get();
    
        // Retornar los resultados
        return $results;
    }
    public function editreldepencia(string $id)
    {
        $query = DB::table('correspondencia.cat_dependencia_area')
            ->select([
                'correspondencia.cat_dependencia_area.id_cat_dependencia_area AS id',
                DB::raw('UPPER(correspondencia.cat_dependencia_area.descripcion) AS descripcion')
            ])
            ->where('id_cat_dependencia_area', '=', $id);
    
        $result = $query->first();
    
        return $result;
    }
    


}