<?php

namespace App\Models\Letter\External;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CloudM extends Model
{
    //Lista los anexos
    public function listAnexos($id, $limit, $idTipoDoc)
    {
        $query = DB::table('correspondencia.ctrl_circular_ext_anexo')
            ->select(
                'correspondencia.ctrl_circular_ext_anexo.id_ctrl_circular_ext_anexo AS id',
                'correspondencia.ctrl_circular_ext_anexo.uid AS uid',
                'correspondencia.ctrl_circular_ext_anexo.nombre AS nombre'
            )
            ->where('correspondencia.ctrl_circular_ext_anexo.estatus', true)
            ->where('correspondencia.ctrl_circular_ext_anexo.id_tbl_circular_externa', $id)
            ->where('correspondencia.ctrl_circular_ext_anexo.id_cat_tipo_doc_cloud', $idTipoDoc)
            ->orderBy('correspondencia.ctrl_circular_ext_anexo.nombre', 'asc')
            ->limit($limit)
            ->get();

        return $query;
    }

    //Lista de oficios
    public function listOficios($id, $limit, $idTipoDoc)
    {
        $query = DB::table('correspondencia.ctrl_circular_ext_oficio')
            ->select(
                'correspondencia.ctrl_circular_ext_oficio.id_ctrl_circular_ext_oficio AS id',
                'correspondencia.ctrl_circular_ext_oficio.uid AS uid',
                'correspondencia.ctrl_circular_ext_oficio.nombre AS nombre'
            )
            ->where('correspondencia.ctrl_circular_ext_oficio.estatus', true)
            ->where('correspondencia.ctrl_circular_ext_oficio.id_tbl_circular_externa', $id)
            ->where('correspondencia.ctrl_circular_ext_oficio.id_cat_tipo_doc_cloud', $idTipoDoc)
            ->orderBy('correspondencia.ctrl_circular_ext_oficio.nombre', 'asc')
            ->limit($limit)
            ->get();

        return $query;
    }

    //LA FUNCION RETORNA VERDADERO SI ES QUE EL RESULTADO ES MAYOR O IGUAL A LA CONDICION, FALSO SI AUN SE PUDEN AGREGAR
    public function conditionOficios($limit, $id, $id_cat_tipo_doc_cloud)
    {
        // Consulta SQL utilizando el Query Builder de Laravel
        $result = DB::table('correspondencia.ctrl_circular_ext_oficio')
            ->select(DB::raw('
                        CASE 
                            WHEN COUNT(*) >= ' . (int) $limit . ' THEN TRUE
                            ELSE FALSE
                        END as valor
                    '))
            ->where('id_tbl_circular_externa', $id)
            ->where('id_cat_tipo_doc_cloud', $id_cat_tipo_doc_cloud)
            ->where('estatus', true)
            ->first(); // Usamos 'first()' para obtener solo un resultado

        return $result;
    }

    //LA FUNCION RETORNA VERDADERO SI ES QUE EL RESULTADO ES MAYOR O IGUAL A LA CONDICION, FALSO SI AUN SE PUDEN AGREGAR
    public function conditioAnexos($limit, $id, $id_cat_tipo_doc_cloud)
    {
        // Consulta SQL utilizando el Query Builder de Laravel
        $result = DB::table('correspondencia.ctrl_circular_ext_anexo')
            ->select(DB::raw('
                            CASE 
                                WHEN COUNT(*) >= ' . (int) $limit . ' THEN TRUE
                                ELSE FALSE
                            END as valor
                        '))
            ->where('id_tbl_circular_externa', $id)
            ->where('id_cat_tipo_doc_cloud', $id_cat_tipo_doc_cloud)
            ->where('estatus', true)
            ->first(); // Usamos 'first()' para obtener solo un resultado

        return $result;
    }
}

