<?php

namespace App\Models\Letter\Area;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AreaM extends Model
{
    protected $table = 'correspondencia.cat_area';
    protected $primaryKey = 'id_cat_area';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'clave',
        'estatus',
    ];

    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        $query = DB::table('correspondencia.cat_area')
        ->select([
            'correspondencia.cat_area.id_cat_area AS id',
            DB::raw('UPPER(correspondencia.cat_area.descripcion) AS descripcion'),
            DB::raw('UPPER(correspondencia.cat_area.clave) AS clave'),
            DB::raw('CASE WHEN correspondencia.cat_area.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.cat_area.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_area.clave)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_area.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        $query->orderBy('correspondencia.cat_area.id_cat_area', 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }

    public function edit(string $id)
    {
        $query = DB::table('correspondencia.cat_area')
                  ->where('id_cat_area', $id)
                  ->first();

        return $query ?? null;
    }

    public function edittblcourses($id)
    {
        $query = DB::table('correspondencia.cat_area')
            ->select([
                'correspondencia.cat_area.id_cat_area AS id',
                DB::raw('UPPER(correspondencia.cat_area.descripcion) AS descripcion'),
                DB::raw('UPPER(correspondencia.cat_area.clave) AS clave')
            ])
            ->where('correspondencia.cat_area.id_cat_area', '=', $id);

        $result = $query->first();
        return $result;
    }
}

