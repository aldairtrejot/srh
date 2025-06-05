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
            'correspondencia.cat_nombre_oficio.id_cat_nombre_oficio AS id',
            DB::raw('UPPER(correspondencia.cat_nombre_oficio.nombre) AS clave'),
            DB::raw('UPPER(correspondencia.cat_nombre_oficio.descripcion) AS descripcion'),
            DB::raw('CASE WHEN correspondencia.cat_nombre_oficio.estatus = 1 THEN TRUE ELSE FALSE END AS estatus')
        ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.cat_nombre_oficio.nombre)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_nombre_oficio.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(TRIM(correspondencia.cat_nombre_oficio.estatus)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        $query->orderBy('correspondencia.cat_nombre_oficio.id_cat_nombre_oficio', 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }

    public function edit(string $id)
    {
        $query = DB::table('correspondencia.cat_nombre_oficio')
                  ->where('id_cat_nombre_oficio', $id)
                  ->first();

        return $query ?? null;
    }

    public function edittblcourses($id)
    {
        $query = DB::table('correspondencia.cat_nombre_oficio')
            ->select([
                'correspondencia.cat_nombre_oficio.id_cat_nombre_oficio AS id',
                DB::raw('UPPER(correspondencia.cat_nombre_oficio.nombre) AS clave'),
                DB::raw('UPPER(correspondencia.cat_nombre_oficio.descripcion) AS descripcion')
                
            ])
            ->where('correspondencia.cat_nombre_oficio.id_cat_nombre_oficio', '=', $id);

        $result = $query->first();
        return $result;
    }
}