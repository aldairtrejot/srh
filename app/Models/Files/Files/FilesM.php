<?php

namespace App\Models\Files\Files;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FilesM extends Model
{
   public function buscarEmpleadoHraes($valor)
{
    return DB::table('central.tbl_empleados_hraes AS e')
        ->select([
            DB::raw('UPPER(e.rfc) AS rfc'),
            DB::raw('UPPER(e.curp) AS curp'),
            DB::raw('UPPER(e.nombre) AS nombre'),
            DB::raw('UPPER(e.primer_apellido) AS primer_apellido'),
            DB::raw('UPPER(e.segundo_apellido) AS segundo_apellido'),
            'e.id_tbl_empleados_hraes AS id',
            'lateral_sub.fecha_movimiento AS ultima_fecha_movimiento',
            'm.nombre_movimiento AS nombre_movimiento'
        ])
        ->join(DB::raw('LATERAL (
            SELECT 
                pe.fecha_movimiento, 
                pe.id_tbl_movimientos
            FROM central.tbl_plazas_empleados_hraes pe
            WHERE pe.id_tbl_empleados_hraes = e.id_tbl_empleados_hraes
            ORDER BY pe.fecha_movimiento DESC
            LIMIT 1
        ) AS lateral_sub'), DB::raw('true'), '=', DB::raw('true'))
        ->leftJoin('public.tbl_movimientos AS m', 'm.id_tbl_movimientos', '=', 'lateral_sub.id_tbl_movimientos')
        ->where(function ($query) use ($valor) {
            $query->whereRaw('UPPER(e.curp) LIKE ?', ['%' . strtoupper($valor) . '%'])
                ->orWhereRaw('UPPER(e.rfc) LIKE ?', ['%' . strtoupper($valor) . '%'])
                ->orWhereRaw('UPPER(e.nombre) LIKE ?', ['%' . strtoupper($valor) . '%'])
                ->orWhereRaw('UPPER(e.primer_apellido) LIKE ?', ['%' . strtoupper($valor) . '%'])
                ->orWhereRaw('UPPER(e.segundo_apellido) LIKE ?', ['%' . strtoupper($valor) . '%']);
        })
        ->orderBy('e.nombre')
        ->get(); // <- ahora devuelve varios resultados
}

}



