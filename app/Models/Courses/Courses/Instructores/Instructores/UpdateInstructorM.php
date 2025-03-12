<?php

namespace App\Models\Courses\Courses\Instructores\Instructores;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateInstructorM extends Model
{
    protected $table = 'capacitacion.tbl_instructores';
    protected $primaryKey = 'id_tbl_instructores';
    public $timestamps = false;

    protected $fillable = ['estatus'];

    public function editInstructor($idInstructor)
    {
        $query = DB::table('capacitacion.tbl_instructores')
            ->select([
                'capacitacion.tbl_instructores.id_tbl_instructores',
                'capacitacion.tbl_instructores.id_usuario_empleado',
                'capacitacion.tbl_instructores.estatus AS estatus',
                'administration.users.estatus AS estatus_usuario',
                DB::raw("COALESCE(central.curp, public.curp, transferidos.curp) AS curp"),
                DB::raw("COALESCE(central.nombre, public.nombre, transferidos.nombre) AS nombre"),
                DB::raw("COALESCE(central.primer_apellido, public.primer_apellido, transferidos.primer_apellido) AS primer_apellido"),
                DB::raw("COALESCE(central.segundo_apellido, public.segundo_apellido, transferidos.segundo_apellido) AS segundo_apellido"),
                DB::raw("COALESCE(central.rfc, public.rfc, transferidos.rfc) AS rfc"),
                DB::raw("CASE 
                            WHEN central.curp IS NOT NULL THEN 'central'
                            WHEN public.curp IS NOT NULL THEN 'public'
                            WHEN transferidos.curp IS NOT NULL THEN 'transferidos'
                            ELSE NULL
                        END AS fuente_curp")
            ])
            ->join('administration.users', 'capacitacion.tbl_instructores.id_usuario_empleado', '=', 'administration.users.id')
            ->leftJoin('central.tbl_empleados_hraes AS central', 'administration.users.id_tbl_empleados_central', '=', 'central.id_tbl_empleados_hraes')
            ->leftJoin('public.tbl_empleados_hraes AS public', 'administration.users.id_tbl_empleados_hraes', '=', 'public.id_tbl_empleados_hraes')
            ->leftJoin('transferidos.tbl_empleados AS transferidos', 'administration.users.id_tbl_empleados_transferidos', '=', 'transferidos.id_tbl_empleados')
            ->where('capacitacion.tbl_instructores.id_tbl_instructores', $idInstructor)
            ->first();
    
        if (!$query) {
            Log::error("❌ No se encontraron datos para el instructor con ID: $idInstructor");
            return null;
        }
    
        return (object) [
            'id_tbl_instructores' => $query->id_tbl_instructores,
            'id_usuario_empleado' => $query->id_usuario_empleado,
            'estatus' => $query->estatus,
            'estatus_usuario' => $query->estatus_usuario,
            'curp' => $query->curp ?? '',
            'nombre' => $query->nombre ?? '',
            'primer_apellido' => $query->primer_apellido ?? '',
            'segundo_apellido' => $query->segundo_apellido ?? '',
            'rfc' => $query->rfc ?? '',
            'fuente_curp' => $query->fuente_curp ?? ''
        ];
    }
    

    public function updateInstructor($idInstructor, $data)
    {
        try {
            Log::info("🔄 Actualizando instructor ID: {$idInstructor}", $data);

            $updated = DB::table('capacitacion.tbl_instructores')
                ->where('id_tbl_instructores', $idInstructor)
                ->update(['estatus' => $data['estatus']]);

            if (!$updated) {
                Log::warning("⚠ No se realizaron cambios en el estatus para instructor ID: {$idInstructor}");
            } else {
                Log::info("✅ Estatus actualizado correctamente para instructor ID: {$idInstructor}");
            }

            return $updated;
        } catch (\Exception $e) {
            Log::error("Error en updateInstructor():" . $e->getMessage());
            return false;
        }
    }
}