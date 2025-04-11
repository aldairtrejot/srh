<?php

namespace App\Models\Letter\Cloud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConfigcloudM extends Model
{
    protected $table = 'correspondencia.config_cloud';
    protected $primaryKey = 'id_config_cloud';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'valor',
        'estatus',
    ];

    public function list($iterator, $searchValue)
{
    $query = DB::table('correspondencia.config_cloud')
        ->select([
            'id_config_cloud AS id',
            DB::raw('UPPER(nombre) AS nombre'),
            DB::raw('UPPER(descripcion) AS descripcion'),
            DB::raw('UPPER(valor) AS valor'),
            DB::raw('CASE WHEN estatus THEN TRUE ELSE FALSE END AS estatus')
        ]);

    if (!empty($searchValue)) {
        $searchValue = strtoupper(trim($searchValue));
        $query->where(function ($query) use ($searchValue) {
            $query->whereRaw("UPPER(TRIM(nombre)) LIKE ?", ["%$searchValue%"])
                  ->orWhereRaw("UPPER(TRIM(descripcion)) LIKE ?", ["%$searchValue%"])
                  ->orWhereRaw("UPPER(TRIM(valor)) LIKE ?", ["%$searchValue%"])
                  ->orWhereRaw("CAST(estatus AS TEXT) LIKE ?", ["%$searchValue%"]);
        });
    }

    $query->orderBy('id_config_cloud', 'ASC')
          ->offset($iterator)
          ->limit(5);

    return $query->get();
}


    public function edit(string $id)
    {
        return DB::table($this->table)
            ->where('id_config_cloud', $id)
            ->first();
    }

    public function edittblcourses($id)
    {
        return DB::table($this->table)
            ->select([
                'id_config_cloud AS id',
                DB::raw('UPPER(nombre) AS nombre'),
                DB::raw('UPPER(descripcion) AS descripcion'),
                DB::raw('UPPER(valor) AS valor')
            ])
            ->where('id_config_cloud', $id)
            ->first();
    }
}

