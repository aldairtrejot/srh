<?php

namespace App\Models\Letter\Area;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AreainternoM extends Model
{
    protected $table = 'correspondencia.cat_area_interno';
    protected $primaryKey = 'id_cat_area_interno';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'clave',
        'estatus',
    ];

    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        $query = DB::table('correspondencia.cat_area_interno')
        ->select([
            'correspondencia.cat_area_interno.id_cat_area_interno AS id',
            DB::raw('UPPER(correspondencia.cat_area_interno.descripcion) AS descripcion'),
            DB::raw('UPPER(correspondencia.cat_area_interno.clave) AS clave'),
            DB::raw('CASE WHEN correspondencia.cat_area_interno.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.cat_area_interno.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_area_interno.clave)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_area_interno.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        $query->orderBy('correspondencia.cat_area_interno.id_cat_area_interno', 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }

    public function edit(string $id)
    {
        $query = DB::table('correspondencia.cat_area_interno')
                  ->where('id_cat_area_interno', $id)
                  ->first();

        return $query ?? null;
    }

    public function edittblcourses($id)
    {
        $query = DB::table('correspondencia.cat_area_interno')
            ->select([
                'correspondencia.cat_area_interno.id_cat_area_interno AS id',
                DB::raw('UPPER(correspondencia.cat_area_interno.descripcion) AS descripcion'),
                DB::raw('UPPER(correspondencia.cat_area_interno.clave) AS clave')
            ])
            ->where('correspondencia.cat_area_interno.id_cat_area_interno', '=', $id);

        $result = $query->first();
        return $result;
    }
}
