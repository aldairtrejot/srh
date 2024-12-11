<?php

namespace App\Models\Letter\Office;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CloudM extends Model
{
    //Lista los anexos
    public function listAnexos($idOficio, $limit, $idTipoDoc)
    {
        $query = DB::table('correspondencia.ctrl_oficio_anexo')
            ->select(
                'correspondencia.ctrl_oficio_anexo.id_ctrl_oficio_anexo AS id',
                'correspondencia.ctrl_oficio_anexo.uid AS uid',
                'correspondencia.ctrl_oficio_anexo.nombre AS nombre'
            )
            ->where('correspondencia.ctrl_oficio_anexo.estatus', true)
            ->where('correspondencia.ctrl_oficio_anexo.id_tbl_oficio', $idOficio)
            ->where('correspondencia.ctrl_oficio_anexo.id_cat_tipo_doc_cloud', $idTipoDoc)
            ->orderBy('correspondencia.ctrl_oficio_anexo.nombre', 'asc')
            ->limit($limit)
            ->get();

        return $query;
    }

    //Lista de oficios
    public function listOficios($idOficio, $limit, $idTipoDoc)
    {
        $query = DB::table('correspondencia.ctrl_oficio_oficio')
            ->select(
                'correspondencia.ctrl_oficio_oficio.id_ctrl_oficio_oficio AS id',
                'correspondencia.ctrl_oficio_oficio.uid AS uid',
                'correspondencia.ctrl_oficio_oficio.nombre AS nombre'
            )
            ->where('correspondencia.ctrl_oficio_oficio.estatus', true)
            ->where('correspondencia.ctrl_oficio_oficio.id_tbl_oficio', $idOficio)
            ->where('correspondencia.ctrl_oficio_oficio.id_cat_tipo_doc_cloud', $idTipoDoc)
            ->orderBy('correspondencia.ctrl_oficio_oficio.nombre', 'asc')
            ->limit($limit)
            ->get();

        return $query;
    }
}

