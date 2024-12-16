<?php

namespace App\Models\Letter\Letter;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CloudM extends Model
{
    //Lista los anexos
    public function listAnexos($idOficio, $limit, $idTipoDoc)
    {
        $query = DB::table('correspondencia.ctrl_correspondencia_anexo')
            ->select(
                'correspondencia.ctrl_correspondencia_anexo.id_ctrl_correspondencia_anexo AS id',
                'correspondencia.ctrl_correspondencia_anexo.uid AS uid',
                'correspondencia.ctrl_correspondencia_anexo.nombre AS nombre'
            )
            ->where('correspondencia.ctrl_correspondencia_anexo.estatus', true)
            ->where('correspondencia.ctrl_correspondencia_anexo.id_tbl_correspondencia', $idOficio)
            ->where('correspondencia.ctrl_correspondencia_anexo.id_cat_tipo_doc_cloud', $idTipoDoc)
            ->orderBy('correspondencia.ctrl_correspondencia_anexo.nombre', 'asc')
            ->limit($limit)
            ->get();

        return $query;
    }

    //Lista de oficios
    public function listOficios($idOficio, $limit, $idTipoDoc)
    {
        $query = DB::table('correspondencia.ctrl_correspondencia_oficio')
            ->select(
                'correspondencia.ctrl_correspondencia_oficio.id_ctrl_correspondencia_oficio AS id',
                'correspondencia.ctrl_correspondencia_oficio.uid AS uid',
                'correspondencia.ctrl_correspondencia_oficio.nombre AS nombre'
            )
            ->where('correspondencia.ctrl_correspondencia_oficio.estatus', true)
            ->where('correspondencia.ctrl_correspondencia_oficio.id_tbl_correspondencia', $idOficio)
            ->where('correspondencia.ctrl_correspondencia_oficio.id_cat_tipo_doc_cloud', $idTipoDoc)
            ->orderBy('correspondencia.ctrl_correspondencia_oficio.nombre', 'asc')
            ->limit($limit)
            ->get();

        return $query;
    }

    //LA FUNCION RETORNA VERDADERO SI ES QUE EL RESULTADO ES MAYOR O IGUAL A LA CONDICION, FALSO SI AUN SE PUDEN AGREGAR
    public function conditionOficios($limit, $id_tbl_oficio, $id_cat_tipo_doc_cloud)
    {
        // Consulta SQL utilizando el Query Builder de Laravel
        $result = DB::table('correspondencia.ctrl_correspondencia_oficio')
            ->select(DB::raw('
                        CASE 
                            WHEN COUNT(*) >= ' . (int) $limit . ' THEN TRUE
                            ELSE FALSE
                        END as valor
                    '))
            ->where('id_tbl_correspondencia', $id_tbl_oficio)
            ->where('id_cat_tipo_doc_cloud', $id_cat_tipo_doc_cloud)
            ->where('estatus', true)
            ->first(); // Usamos 'first()' para obtener solo un resultado

        return $result;
    }

    //LA FUNCION RETORNA VERDADERO SI ES QUE EL RESULTADO ES MAYOR O IGUAL A LA CONDICION, FALSO SI AUN SE PUDEN AGREGAR
    public function conditioAnexos($limit, $id_tbl_oficio, $id_cat_tipo_doc_cloud)
    {
        // Consulta SQL utilizando el Query Builder de Laravel
        $result = DB::table('correspondencia.ctrl_correspondencia_anexo')
            ->select(DB::raw('
                            CASE 
                                WHEN COUNT(*) >= ' . (int) $limit . ' THEN TRUE
                                ELSE FALSE
                            END as valor
                        '))
            ->where('id_tbl_correspondencia', $id_tbl_oficio)
            ->where('id_cat_tipo_doc_cloud', $id_cat_tipo_doc_cloud)
            ->where('estatus', true)
            ->first(); // Usamos 'first()' para obtener solo un resultado

        return $result;
    }
}
