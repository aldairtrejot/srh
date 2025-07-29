<?php

namespace App\Models\Letter\Returned;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;




class ReturnedM extends Model
{
    protected $table = 'correspondencia.cat_area';
    protected $primaryKey = 'id_cat_area';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'clave',
        'estatus',
    ];

    public function list($iterator, $searchValue)
    {
        $query = DB::table($this->table)
            ->select([
                'id_cat_area AS id',
                DB::raw('UPPER(descripcion) AS descripcion'),
                DB::raw('clave'),
                DB::raw('CASE WHEN estatus = true THEN \'ACTIVO\' ELSE \'INACTIVO\' END AS estatus')
            ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($q) use ($searchValue) {
                $q->whereRaw("UPPER(TRIM(descripcion)) LIKE ?", ["%$searchValue%"])
                  ->orWhereRaw("UPPER(TRIM(clave)) LIKE ?", ["%$searchValue%"]);
            });
        }

        return $query->orderBy('id_cat_area', 'ASC')
            ->offset($iterator)
            ->limit(5)
            ->get();
    }

    public function edit(string $id)
    {
        return DB::table($this->table)
            ->where('id_cat_area', $id)
            ->first();
    }
public function obtenerSubareas($idCatArea)
{
    return DB::table('correspondencia.sub_area')
        ->select('id_sub_area', 'description') // ← debe decir 'description' según tu tabla
        ->where('id_cat_area', $idCatArea)
        ->orderBy('description')
        ->get();
}


}