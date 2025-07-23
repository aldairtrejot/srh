<?php

namespace App\Models\Files\Files;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FileschecklistM extends Model
{
    public function obtenerEmpleadoPorId($id)
    {
        return DB::table('central.tbl_empleados_hraes AS e')
            ->leftJoin('central.tbl_plazas_empleados_hraes AS pe', 'e.id_tbl_empleados_hraes', '=', 'pe.id_tbl_empleados_hraes')
            ->leftJoin('central.tbl_control_plazas_hraes AS cp', 'pe.id_tbl_control_plazas_hraes', '=', 'cp.id_tbl_control_plazas_hraes')
            ->leftJoin('public.cat_unidad AS u', 'cp.id_cat_unidad', '=', 'u.id_cat_unidad')
            ->leftJoin('public.cat_coordinacion AS c', 'cp.id_cat_coordinacion', '=', 'c.id_cat_coordinacion')
            ->where('e.id_tbl_empleados_hraes', $id)
            ->select([
                'e.nombre',
                'e.primer_apellido',
                'e.segundo_apellido',
                'e.rfc',
                'e.curp',
                'u.nombre AS nombre_unidad',
                'c.nombre AS nombre_coordinacion'
            ])
            ->first(); // Solo un registro
    }

    public function insertarGestionDocumentoPorEmpleado($idEmpleado)
    {
        // Verifica si ya existe un registro con ese empleado
        $existe = DB::table('expediente.tbl_gestion_documentos')
            ->where('id_empleado_hraes', $idEmpleado)
            ->exists();

        if (!$existe) {
            return DB::table('expediente.tbl_gestion_documentos')->insert([
                'id_empleado_hraes' => $idEmpleado,
                'creado_en' => now(),
                'actualizado_en' => now(),
            ]);
        }

        return false; // No se insertó porque ya existe
    }
    public function obtenerDocumentosChecklistPorEmpleado($idEmpleado)
{
    // Obtener ID de gestión documental (ya lo insertaste al cargar la vista)
    $gestion = DB::table('expediente.tbl_gestion_documentos')
        ->where('id_empleado_hraes', $idEmpleado)
        ->first();

    if (!$gestion) return [];

    return DB::table('expediente.cat_documento AS d')
        ->select([
            'd.id_cat_documento',
            'd.descripcion',
            DB::raw("'" . $gestion->id_tbl_gestion_documentos . "' AS id_tbl_gestion_documentos")
        ])
        ->where('d.estatus', true)
        ->get();
}
public function obtenerEstatusDocumentosPorEmpleado($idEmpleado)
{
    // Primero obtenemos el ID de gestion documentos relacionado al empleado
    $gestion = DB::table('expediente.tbl_gestion_documentos')
        ->where('id_empleado_hraes', $idEmpleado)
        ->first();

    if (!$gestion) return [];

    $idGestion = $gestion->id_tbl_gestion_documentos;

    return DB::table('expediente.cat_documento AS cd')
        ->leftJoin('expediente.rel_gestion_documentos_documento AS rel', function($join) use ($idGestion) {
            $join->on('cd.id_cat_documento', '=', 'rel.id_cat_documento')
                ->where('rel.id_tbl_gestion_documentos', '=', $idGestion);
        })
        ->where('cd.estatus', true)
        ->select([
            'cd.id_cat_documento',
            'cd.descripcion',
            DB::raw('COALESCE(rel.estatus, false) AS estatus'),
            DB::raw("$idGestion AS id_tbl_gestion_documentos")
        ])
        ->get();
}


}

