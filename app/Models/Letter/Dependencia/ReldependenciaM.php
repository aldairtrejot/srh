<?php

namespace App\Models\Letter\Dependencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReldependenciaM extends Model
{
    protected $table = 'correspondencia.rel_dependencia_area';
    protected $primaryKey = 'id_rel_dependencia_area';
    public $timestamps = false;

    protected $fillable = [
        'id_cat_dependencia',
        'id_cat_dependencia_area',
    ];

    public function list($iterator, $searchValue)
{
    $query = DB::table('correspondencia.rel_dependencia_area AS rda')
        ->join('correspondencia.cat_dependencia AS cd', 'cd.id_cat_dependencia', '=', 'rda.id_cat_dependencia')
        ->join('correspondencia.cat_dependencia_area AS cda', 'cda.id_cat_dependencia_area', '=', 'rda.id_cat_dependencia_area')
        ->select([
            'rda.id_rel_dependencia_area AS id',
            DB::raw('UPPER(cd.descripcion) AS dependencia'),
            DB::raw('UPPER(cda.descripcion) AS area'),
        ]);

    // Filtro por texto
    if (!empty($searchValue)) {
        $searchValue = strtoupper(trim($searchValue));
        $query->where(function ($query) use ($searchValue) {
            $query->whereRaw("UPPER(TRIM(cd.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(TRIM(cda.descripcion)) LIKE ?", ['%' . $searchValue . '%']);
        });
    }

    // Paginación
    $query->orderBy('cd.id_cat_dependencia', 'ASC')
          ->offset($iterator)
          ->limit(5);

    return $query->get();
}

    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('correspondencia.rel_dependencia_area')
            ->where('id_rel_dependencia_area', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }

    

}