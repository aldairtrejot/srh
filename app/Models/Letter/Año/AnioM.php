<?php

namespace App\Models\Letter\Año;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AnioM extends Model
{
    protected $table = 'correspondencia.cat_anio';
    protected $primaryKey = 'id_cat_anio';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estatus',
    ];

    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        $query = DB::table('correspondencia.cat_anio')
        ->select([
            'correspondencia.cat_anio.id_cat_anio AS id',
            DB::raw('UPPER(correspondencia.cat_anio.descripcion) AS descripcion'),
            DB::raw('CASE WHEN correspondencia.cat_anio.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.cat_anio.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_anio.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        $query->orderBy('correspondencia.cat_anio.id_cat_anio', 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }

    public function edit(string $id)
    {
        $query = DB::table('correspondencia.cat_anio')
                  ->where('id_cat_anio', $id)
                  ->first();

        return $query ?? null;
    }

    public function edittblcourses($id)
    {
        $query = DB::table('correspondencia.cat_anio')
            ->select([
                'correspondencia.cat_anio.id_cat_anio AS id',
                DB::raw('UPPER(correspondencia.cat_anio.descripcion) AS descripcion')
            ])
            ->where('correspondencia.cat_anio.id_cat_anio', '=', $id);

        $result = $query->first();
        return $result;
    }
}
