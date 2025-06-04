<?php

namespace App\Models\Letter\Estatus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EstatusM extends Model
{
    protected $table = 'correspondencia.cat_estatus';
    protected $primaryKey = 'id_cat_estatus';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'estatus',
    ];

    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        $query = DB::table('correspondencia.cat_estatus')
        ->select([
            'correspondencia.cat_estatus.id_cat_estatus AS id',
            DB::raw('UPPER(correspondencia.cat_estatus.descripcion) AS descripcion'),
            DB::raw('CASE WHEN correspondencia.cat_estatus.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.cat_estatus.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_estatus.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        $query->orderBy('correspondencia.cat_estatus.id_cat_estatus', 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }

    public function edit(string $id)
    {
        $query = DB::table('correspondencia.cat_estatus')
                  ->where('id_cat_estatus', $id)
                  ->first();

        return $query ?? null;
    }

    public function edittblcourses($id)
    {
        $query = DB::table('correspondencia.cat_estatus')
            ->select([
                'correspondencia.cat_estatus.id_cat_estatus AS id',
                DB::raw('UPPER(correspondencia.cat_estatus.descripcion) AS descripcion')
                
            ])
            ->where('correspondencia.cat_estatus.id_cat_estatus', '=', $id);

        $result = $query->first();
        return $result;
    }
}