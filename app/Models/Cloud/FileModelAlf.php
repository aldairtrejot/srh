<?php

namespace App\Models\Cloud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FileModelAlf extends Model
{
    public function fileModelAlf()
    {
        $query1 = DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia as id',
                DB::raw("'of_' || correspondencia.tbl_correspondencia.folio_gestion as name"),
                'correspondencia.ctrl_correspondencia_oficio.uid as uuid'
            )
            ->leftJoin('correspondencia.ctrl_correspondencia_oficio', function ($join) {
                $join->on('correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.ctrl_correspondencia_oficio.id_tbl_correspondencia')
                    ->where('correspondencia.ctrl_correspondencia_oficio.estatus', true);
            })
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', '<=', 100)
            ->where('correspondencia.ctrl_correspondencia_oficio.estatus', true);

        $query2 = DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia as id',
                DB::raw("'ax_' || correspondencia.tbl_correspondencia.folio_gestion as name"),
                'correspondencia.ctrl_correspondencia_anexo.uid as uuid'
            )
            ->leftJoin('correspondencia.ctrl_correspondencia_anexo', function ($join) {
                $join->on('correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.ctrl_correspondencia_anexo.id_tbl_correspondencia')
                    ->where('correspondencia.ctrl_correspondencia_anexo.estatus', true);
            })
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', '<=', 100)
            ->where('correspondencia.ctrl_correspondencia_anexo.estatus', true);

        $query3 = DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia as id',
                DB::raw("'rep_ax_' || correspondencia.tbl_correspondencia.folio_gestion as name"),
                'correspondencia.ctrl_oficio_anexo.uid as uuid'
            )
            ->join('correspondencia.tbl_oficio', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.tbl_oficio.id_tbl_correspondencia')
            ->join('correspondencia.ctrl_oficio_anexo', function ($join) {
                $join->on('correspondencia.tbl_oficio.id_tbl_oficio', '=', 'correspondencia.ctrl_oficio_anexo.id_tbl_oficio')
                    ->where('correspondencia.ctrl_oficio_anexo.estatus', true);
            })
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', '<=', 100);

        $query4 = DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia as id',
                DB::raw("'rep_of_' || correspondencia.tbl_correspondencia.folio_gestion as name"),
                'correspondencia.ctrl_oficio_oficio.uid as uuid'
            )
            ->join('correspondencia.tbl_oficio', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.tbl_oficio.id_tbl_correspondencia')
            ->join('correspondencia.ctrl_oficio_oficio', function ($join) {
                $join->on('correspondencia.tbl_oficio.id_tbl_oficio', '=', 'correspondencia.ctrl_oficio_oficio.id_tbl_oficio')
                    ->where('correspondencia.ctrl_oficio_oficio.estatus', true);
            })
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', '<=', 100);

        return $query1->unionAll($query2)
            ->unionAll($query3)
            ->unionAll($query4)
            ->get();
    }
}
