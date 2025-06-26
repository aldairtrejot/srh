<?php

namespace App\Models\Letter\Nomoficio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NomoficioM extends Model
{
    protected $table = 'correspondencia.cat_nombre_oficio';
    protected $primaryKey = 'id_cat_nombre_oficio';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estatus',
    ];

    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        $query = DB::table('correspondencia.cat_nombre_oficio')
            ->select([
                'id_cat_nombre_oficio AS id',
                DB::raw('UPPER(nombre) AS nombre'),
                DB::raw('UPPER(descripcion) AS descripcion'),
                DB::raw('CASE WHEN estatus IS TRUE THEN TRUE ELSE FALSE END AS estatus')
            ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(nombre)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(descripcion)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        return $query->orderBy('id_cat_nombre_oficio', 'ASC')
                     ->offset($iterator)
                     ->limit(5)
                     ->get();
    }

    public function edit(string $id)
    {
        return DB::table('correspondencia.cat_nombre_oficio')
                 ->where('id_cat_nombre_oficio', $id)
                 ->first();
    }



    public function edittblcourses($id)
{
    return DB::table('correspondencia.cat_nombre_oficio')
        ->select([
            'id_cat_nombre_oficio AS id',
            DB::raw('UPPER(nombre) AS nombre'),
            DB::raw('UPPER(descripcion) AS descripcion')
        ])
        ->where('id_cat_nombre_oficio', $id)
        ->first();
}

}