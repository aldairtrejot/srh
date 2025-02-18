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

    protected $fillable = ['curp', 'estatus'];

    public function editInstructor($idInstructor)
{
    $query = DB::table('capacitacion.tbl_instructores')
        ->select([
            'capacitacion.tbl_instructores.id_tbl_instructores',
            'capacitacion.tbl_instructores.id_usuario_empleado',
            'capacitacion.tbl_instructores.estatus AS estatus',
            'administration.users.estatus AS estatus_usuario',
            DB::raw("COALESCE(central.rfc, public.rfc, transferidos.rfc) AS rfc"),
            DB::raw("COALESCE(central.curp, public.curp, transferidos.curp) AS curp"),
            DB::raw("COALESCE(central.nombre, public.nombre, transferidos.nombre) AS nombre"),
            DB::raw("COALESCE(central.primer_apellido, public.primer_apellido, transferidos.primer_apellido) AS primer_apellido"),
            DB::raw("COALESCE(central.segundo_apellido, public.segundo_apellido, transferidos.segundo_apellido) AS segundo_apellido")
        ])
        ->join('administration.users', 'capacitacion.tbl_instructores.id_usuario_empleado', '=', 'administration.users.id')
        ->leftJoin('central.tbl_empleados_hraes AS central', 'administration.users.id_tbl_empleados_central', '=', 'central.id_tbl_empleados_hraes')
        ->leftJoin('public.tbl_empleados_hraes AS public', 'administration.users.id_tbl_empleados_hraes', '=', 'public.id_tbl_empleados_hraes')
        ->leftJoin('transferidos.tbl_empleados AS transferidos', 'administration.users.id_tbl_empleados_trasnferidos', '=', 'transferidos.id_tbl_empleados')
        ->where('capacitacion.tbl_instructores.id_tbl_instructores', $idInstructor)
        ->first();

    if (!$query) {
        Log::error("❌ No se encontraron datos para el instructor con ID: $idInstructor");
        return null; // Si no hay datos, devolvemos null
    }

    return (object) [
        'id_tbl_instructores' => $query->id_tbl_instructores,
        'id_usuario_empleado' => $query->id_usuario_empleado,
        'estatus' => $query->estatus,
        'estatus_usuario' => $query->estatus_usuario,
        'rfc' => $query->rfc ?? '',
        'curp' => $query->curp ?? '',
        'nombre' => $query->nombre ?? '',
        'primer_apellido' => $query->primer_apellido ?? '',
        'segundo_apellido' => $query->segundo_apellido ?? ''
    ];
}


public function updateInstructor($idInstructor, $data)
{
    try {
        // Buscar el instructor en la base de datos
        $instructor = self::find($idInstructor);

        if (!$instructor) {
            Log::error("❌ No se encontró el instructor con ID: {$idInstructor}");
            return false;
        }

        // 📌 REGISTRAR EN EL LOG PARA DEPURACIÓN
        Log::info("🔄 Intentando actualizar instructor con ID: {$idInstructor}", $data);

        // Verificar si la CURP ha cambiado
        if (isset($data['curp']) && $data['curp'] !== $instructor->curp) {
            Log::info("🔄 Se detectó un cambio de CURP para el instructor ID: {$idInstructor}");

            // Actualizar CURP en `capacitacion.tbl_instructores`
            DB::table('capacitacion.tbl_instructores')
                ->where('id_tbl_instructores', $idInstructor)
                ->update(['curp' => $data['curp']]);
        }

        // Actualizar estatus
        $instructor->update([
            'estatus' => $data['estatus']
        ]);

        Log::info("✅ Instructor actualizado correctamente con ID: {$idInstructor}");
        return true;
    } catch (\Exception $e) {
        Log::error("🔥 Error en updateInstructor(): " . $e->getMessage());
        return false;
    }
}


}
