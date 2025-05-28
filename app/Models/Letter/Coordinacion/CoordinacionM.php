<?php

namespace App\Models\Letter\Coordinacion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CoordinacionM extends Model
{
    protected $table = 'correspondencia.cat_coordinacion';
    protected $primaryKey = 'id_cat_coordinacion';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estatus',
    ];

    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        $query = DB::table('correspondencia.cat_coordinacion')
        ->select([
            'correspondencia.cat_coordinacion.id_cat_coordinacion AS id',
            DB::raw('UPPER(correspondencia.cat_coordinacion.descripcion) AS descripcion'),
            DB::raw('CASE WHEN correspondencia.cat_coordinacion.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.cat_coordinacion.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_coordinacion.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        $query->orderBy('correspondencia.cat_coordinacion.id_cat_coordinacion', 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }

    public function edit(string $id)
    {
        $query = DB::table('correspondencia.cat_coordinacion')
                  ->where('id_cat_coordinacion', $id)
                  ->first();

        return $query ?? null;
    }
}