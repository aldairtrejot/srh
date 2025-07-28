<?php

namespace App\Models\Files\Files;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FilesgestionM extends Model
{
    protected $table = 'expediente.tbl_gestion_documentos';
    protected $primaryKey = 'id_tbl_gestion_documentos';
    public $timestamps = false;

    protected $fillable = [
        'id_cat_documento',
        'folio_documento',
        'fecha_registro',
        'observaciones',
        'id_ubicacion',
        'archivo',
        'es_fisico',
        'posicion',
        'fecha_ultima_ubicacion',
        'id_empleado_hraes',
        'id_usuario_creacion',
        'id_modificacion',
        'creado_en',
        'actualizado_en',
    ];

    /**
     * Devuelve una lista paginada de registros con búsqueda opcional.
     */
    public function list($offset, $searchValue, $limit = 5)
    {
        $query = DB::table('expediente.tbl_gestion_documentos')
            ->join(
                'central.tbl_empleados_hraes',
                'expediente.tbl_gestion_documentos.id_empleado_hraes',
                '=',
                'central.tbl_empleados_hraes.id_tbl_empleados_hraes'
            )
            ->select([
                'expediente.tbl_gestion_documentos.id_tbl_gestion_documentos',
                'expediente.tbl_gestion_documentos.folio_documento',
                'expediente.tbl_gestion_documentos.fecha_registro',
                'expediente.tbl_gestion_documentos.observaciones',
                'expediente.tbl_gestion_documentos.id_ubicacion',
                'expediente.tbl_gestion_documentos.archivo',
                'expediente.tbl_gestion_documentos.es_fisico',
                'expediente.tbl_gestion_documentos.posicion',
                'expediente.tbl_gestion_documentos.fecha_ultima_ubicacion',
                DB::raw("
                    TRIM(central.tbl_empleados_hraes.nombre) || ' ' ||
                    TRIM(central.tbl_empleados_hraes.primer_apellido) || ' ' ||
                    TRIM(central.tbl_empleados_hraes.segundo_apellido) AS nombre_completo
                ")
            ]);

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));

            $query->where(function ($q) use ($searchValue) {
                $q->whereRaw("UPPER(TRIM(expediente.tbl_gestion_documentos.folio_documento)) LIKE ?", ["%$searchValue%"])
                  ->orWhereRaw("UPPER(TRIM(expediente.tbl_gestion_documentos.observaciones)) LIKE ?", ["%$searchValue%"])
                  ->orWhereRaw("
                        UPPER(
                            TRIM(central.tbl_empleados_hraes.nombre) || ' ' ||
                            TRIM(central.tbl_empleados_hraes.primer_apellido) || ' ' ||
                            TRIM(central.tbl_empleados_hraes.segundo_apellido)
                        ) LIKE ?
                    ", ["%$searchValue%"]);
            });
        }

        return $query
            ->orderBy('expediente.tbl_gestion_documentos.fecha_registro', 'DESC')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }

    /**
     * Devuelve el total de registros que coinciden con la búsqueda.
     */
    public function count($searchValue)
    {
        $query = DB::table('expediente.tbl_gestion_documentos')
            ->join(
                'central.tbl_empleados_hraes',
                'expediente.tbl_gestion_documentos.id_empleado_hraes',
                '=',
                'central.tbl_empleados_hraes.id_tbl_empleados_hraes'
            );

        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));

            $query->where(function ($q) use ($searchValue) {
                $q->whereRaw("UPPER(TRIM(expediente.tbl_gestion_documentos.folio_documento)) LIKE ?", ["%$searchValue%"])
                  ->orWhereRaw("UPPER(TRIM(expediente.tbl_gestion_documentos.observaciones)) LIKE ?", ["%$searchValue%"])
                  ->orWhereRaw("
                        UPPER(
                            TRIM(central.tbl_empleados_hraes.nombre) || ' ' ||
                            TRIM(central.tbl_empleados_hraes.primer_apellido) || ' ' ||
                            TRIM(central.tbl_empleados_hraes.segundo_apellido)
                        ) LIKE ?
                    ", ["%$searchValue%"]);
            });
        }

        return $query->count();
    }
}
