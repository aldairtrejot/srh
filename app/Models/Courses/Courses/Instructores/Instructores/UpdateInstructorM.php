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
            DB::raw("COALESCE(central.nombre, public.nombre, transferidos.nombre) AS nombre"),
            DB::raw("COALESCE(central.primer_apellido, public.primer_apellido, transferidos.primer_apellido) AS primer_apellido"),
            DB::raw("COALESCE(central.segundo_apellido, public.segundo_apellido, transferidos.segundo_apellido) AS segundo_apellido"),
            DB::raw("COALESCE(central.rfc, public.rfc, transferidos.rfc) AS rfc"),
            DB::raw("COALESCE(central.curp, public.curp, transferidos.curp) AS curp"),
            DB::raw("
                CASE 
                    WHEN central.curp IS NOT NULL THEN 'central'
                    WHEN public.curp IS NOT NULL THEN 'public'
                    WHEN transferidos.curp IS NOT NULL THEN 'transferidos'
                    ELSE NULL
                END AS fuente_curp
            ")
        ])
        ->join('administration.users', 'capacitacion.tbl_instructores.id_usuario_empleado', '=', 'administration.users.id')
        ->leftJoin('central.tbl_empleados_hraes AS central', 'administration.users.id_tbl_empleados_central', '=', 'central.id_tbl_empleados_hraes')
        ->leftJoin('public.tbl_empleados_hraes AS public', 'administration.users.id_tbl_empleados_hraes', '=', 'public.id_tbl_empleados_hraes')
        ->leftJoin('transferidos.tbl_empleados AS transferidos', 'administration.users.id_tbl_empleados_trasnferidos', '=', 'transferidos.id_tbl_empleados')
        ->where('capacitacion.tbl_instructores.id_tbl_instructores', $idInstructor)
        ->first();

    if (!$query) {
        Log::error("❌ No se encontraron datos para el instructor con ID: $idInstructor");
        return null;
    }

    Log::info("✅ Instructor encontrado: ", (array) $query);

    return (object) [
        'id_tbl_instructores' => $query->id_tbl_instructores,
        'id_usuario_empleado' => $query->id_usuario_empleado,
        'estatus' => $query->estatus,
        'estatus_usuario' => $query->estatus_usuario,
        'nombre' => $query->nombre ?? '',
        'primer_apellido' => $query->primer_apellido ?? '',
        'segundo_apellido' => $query->segundo_apellido ?? '',
        'rfc' => $query->rfc ?? '',
        'curp' => $query->curp ?? '',
        'fuente_curp' => $query->fuente_curp ?? '' 
    ];
}



public function updateInstructor($idInstructor, $data)
{
    try {
        // Actualizar solo el estatus
        $estatusActualizado = DB::table('capacitacion.tbl_instructores')
            ->where('id_tbl_instructores', $idInstructor)
            ->update(['estatus' => $data['estatus']]);

        if ($estatusActualizado === 0) {
            Log::error("❌ No se pudo actualizar el estatus en tbl_instructores para el instructor ID: {$idInstructor}");
        } else {
            Log::info("✅ Estatus actualizado correctamente.");
        }

        return true;
    } catch (\Exception $e) {
        Log::error("🔥 Error en updateInstructor(): " . $e->getMessage());
        return false;
    }
}

public function updateCurpInstructor($idInstructor, $nuevoCurp)
{
    try {
        Log::info("📌 Intentando actualizar CURP: {$nuevoCurp} para Instructor ID: {$idInstructor}");

        // Obtener el usuario asociado al instructor
        $usuario = DB::table('capacitacion.tbl_instructores')
            ->join('administration.users', 'capacitacion.tbl_instructores.id_usuario_empleado', '=', 'administration.users.id')
            ->where('capacitacion.tbl_instructores.id_tbl_instructores', $idInstructor)
            ->select('users.id_tbl_empleados_central', 'users.id_tbl_empleados_hraes', 'users.id_tbl_empleados_transferidos')
            ->first();

        if (!$usuario) {
            Log::error("❌ No se encontró el usuario para el instructor ID: {$idInstructor}");
            return false;
        }

        // Determinar en qué tabla está la CURP y actualizarla
        if ($usuario->id_tbl_empleados_central) {
            $tabla = 'central.tbl_empleados_hraes';
            $columnaId = 'id_tbl_empleados_hraes';
            $idEmpleado = $usuario->id_tbl_empleados_central;
        } elseif ($usuario->id_tbl_empleados_hraes) {
            $tabla = 'public.tbl_empleados_hraes';
            $columnaId = 'id_tbl_empleados_hraes';
            $idEmpleado = $usuario->id_tbl_empleados_hraes;
        } elseif ($usuario->id_tbl_empleados_transferidos) {
            $tabla = 'transferidos.tbl_empleados';
            $columnaId = 'id_tbl_empleados_hraes';
            $idEmpleado = $usuario->id_tbl_empleados_transferidos;
        } else {
            Log::error("❌ No se encontró un empleado relacionado con el instructor ID: {$idInstructor}");
            return false;
        }

        Log::info("📌 Actualizando CURP en la tabla {$tabla} con ID: {$idEmpleado}");

        // Actualizar la CURP en la tabla correspondiente
        $curpActualizado = DB::table($tabla)
            ->where($columnaId, $idEmpleado)
            ->update(['curp' => $nuevoCurp]);

        if ($curpActualizado === 0) {
            Log::error("❌ No se pudo actualizar la CURP para el instructor ID: {$idInstructor}");
            return false;
        }

        Log::info("✅ CURP actualizada correctamente para el instructor ID: {$idInstructor}");
        return true;
    } catch (\Exception $e) {
        Log::error("🔥 Error en updateCurpInstructor(): " . $e->getMessage());
        return false;
    }
}


}
