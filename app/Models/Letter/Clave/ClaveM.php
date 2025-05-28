<?php

namespace App\Models\Letter\Clave;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClaveM extends Model
{
    protected $table = 'correspondencia.cat_clave';
    protected $primaryKey = 'id_cat_clave';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estatus',
    ];

    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        $query = DB::table('correspondencia.cat_clave')
        ->select([
            'correspondencia.cat_clave.id_cat_clave AS id',
            DB::raw('UPPER(correspondencia.cat_clave.descripcion) AS descripcion'),
            DB::raw('CASE WHEN correspondencia.cat_clave.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.cat_clave.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_clave.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        $query->orderBy('correspondencia.cat_clave.id_cat_clave', 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }

    public function edit(string $id)
    {
        $query = DB::table('correspondencia.cat_clave')
                  ->where('id_cat_clave', $id)
                  ->first();

        return $query ?? null;
    }
}