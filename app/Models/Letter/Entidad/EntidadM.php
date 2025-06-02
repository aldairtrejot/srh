<?php

namespace App\Models\Letter\Entidad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EntidadM extends Model
{
    protected $table = 'correspondencia.cat_entidad';
    protected $primaryKey = 'id_cat_entidad';
    public $timestamps = false;

    protected $fillable = [
        'clave',
        'descripcion',
        'estatus',
    ];

    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        $query = DB::table('correspondencia.cat_entidad')
        ->select([
            'correspondencia.cat_entidad.id_cat_entidad AS id',
            DB::raw('UPPER(correspondencia.cat_entidad.clave) AS clave'),
            DB::raw('UPPER(correspondencia.cat_entidad.descripcion) AS descripcion'),
            DB::raw('CASE WHEN correspondencia.cat_entidad.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.cat_entidad.clave)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_entidad.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_entidad.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        $query->orderBy('correspondencia.cat_entidad.id_cat_entidad', 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }

    public function edit(string $id)
    {
        $query = DB::table('correspondencia.cat_entidad')
                  ->where('id_cat_entidad', $id)
                  ->first();

        return $query ?? null;
    }

    public function edittblcourses($id)
    {
        $query = DB::table('correspondencia.cat_entidad')
            ->select([
                'correspondencia.cat_entidad.id_cat_entidad AS id',
                DB::raw('UPPER(correspondencia.cat_entidad.clave) AS clave'),
                DB::raw('UPPER(correspondencia.cat_entidad.descripcion) AS descripcion')
                
            ])
            ->where('correspondencia.cat_entidad.id_cat_entidad', '=', $id);

        $result = $query->first();
        return $result;
    }
}
