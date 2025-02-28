<?php

namespace App\Models\Courses\Courses\Instructores\Instructores;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;
class InstructorM extends Model
{
    protected $table = 'capacitacion.tbl_instructores';
    protected $primaryKey = 'id_tbl_instructores';
    public $timestamps = false;
    protected $fillable = [
        'id_usuario_empleado',
        'estatus',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

   
    public function list($iterator, $searchValue)
    {
        $query = DB::table('capacitacion.tbl_instructores')
            ->select([
                'capacitacion.tbl_instructores.id_tbl_instructores',
                DB::raw("
                    CASE
                        WHEN administration.users.id_cat_tipo_schema = 1 THEN UPPER(central.curp)
                        WHEN administration.users.id_cat_tipo_schema = 2 THEN UPPER(public.curp)
                        WHEN administration.users.id_cat_tipo_schema = 3 THEN UPPER(transferidos.curp)
                    END AS curp
                "),
                DB::raw("
                    CASE
                        WHEN administration.users.id_cat_tipo_schema = 1 THEN UPPER(central.nombre || ' ' || central.primer_apellido || ' ' || central.segundo_apellido)
                        WHEN administration.users.id_cat_tipo_schema = 2 THEN UPPER(public.nombre || ' ' || public.primer_apellido || ' ' || public.segundo_apellido)
                        WHEN administration.users.id_cat_tipo_schema = 3 THEN UPPER(transferidos.nombre || ' ' || transferidos.primer_apellido || ' ' || transferidos.segundo_apellido)
                    END AS nombre_completo
                "),
                DB::raw("
                    CASE
                        WHEN CAST(capacitacion.tbl_instructores.estatus AS BOOLEAN) = TRUE THEN 'ACTIVO'
                        ELSE 'INACTIVO'
                    END AS estatus_instructor
                ")
            ])
            ->join('administration.users', 'capacitacion.tbl_instructores.id_usuario_empleado', '=', 'administration.users.id')
            ->leftJoin('central.tbl_empleados_hraes AS central', 'administration.users.id_tbl_empleados_central', '=', 'central.id_tbl_empleados_hraes')
            ->leftJoin('transferidos.tbl_empleados AS transferidos', 'administration.users.id_tbl_empleados_transferidos', '=', 'transferidos.id_tbl_empleados')
            ->leftJoin('public.tbl_empleados_hraes AS public', 'administration.users.id_tbl_empleados_hraes', '=', 'public.id_tbl_empleados_hraes');
    
        // 🔍 Agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(central.curp) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(public.curp) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(transferidos.curp) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(central.nombre || ' ' || central.primer_apellido || ' ' || central.segundo_apellido) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(public.nombre || ' ' || public.primer_apellido || ' ' || public.segundo_apellido) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("UPPER(transferidos.nombre || ' ' || transferidos.primer_apellido || ' ' || transferidos.segundo_apellido) LIKE ?", ['%' . $searchValue . '%'])
                      ->orWhereRaw("
                            CASE
                                WHEN CAST(capacitacion.tbl_instructores.estatus AS BOOLEAN) = TRUE THEN 'ACTIVO'
                                ELSE 'INACTIVO'
                            END LIKE ?
                      ", ['%' . $searchValue . '%']);
            });
        }
    
        return $query->paginate(5, ['*'], 'page', $iterator);
    }
    

    // BUSQUEDA DE CURP 
    public function centralCurp($curp)
    {
        return DB::table('central.tbl_empleados_hraes')
            ->select([
                DB::raw('UPPER(central.tbl_empleados_hraes.rfc) AS RFC'),
                DB::raw('UPPER(central.tbl_empleados_hraes.curp) AS CURP'),
                DB::raw('UPPER(central.tbl_empleados_hraes.nombre) AS NOMBRE'),
                DB::raw('UPPER(central.tbl_empleados_hraes.primer_apellido) AS PRIMER_APELLIDO'),
                DB::raw('UPPER(central.tbl_empleados_hraes.segundo_apellido) AS SEGUNDO_APELLIDO'),
                'central.tbl_empleados_hraes.id_tbl_empleados_hraes AS id' ,
                'ts.id_cat_tipo_schema AS id_schema',
            ])
            ->leftJoin('catalogos.cat_tipo_schema as ts', DB::raw('UPPER(ts.schema)'), '=', DB::raw("'CENTRAL'"))
            ->where('central.tbl_empleados_hraes.curp', '=', $curp)
            ->first(); // Devuelve null si no encuentra un registro
    }
    

    public function buscarEmpleadoHRAES($curp)
{
    return DB::table('public.tbl_empleados_hraes')
        ->select([
            DB::raw('UPPER(public.tbl_empleados_hraes.rfc) AS RFC'),
            DB::raw('UPPER(public.tbl_empleados_hraes.curp) AS CURP'),
            DB::raw('UPPER(public.tbl_empleados_hraes.nombre) AS NOMBRE'),
            DB::raw('UPPER(public.tbl_empleados_hraes.primer_apellido) AS PRIMER_APELLIDO'),
            DB::raw('UPPER(public.tbl_empleados_hraes.segundo_apellido) AS SEGUNDO_APELLIDO'),
            'public.tbl_empleados_hraes.id_tbl_empleados_hraes AS id',
            'ts.id_cat_tipo_schema AS id_schema',
        ])
        ->leftJoin('catalogos.cat_tipo_schema as ts', DB::raw('UPPER(ts.schema)'), '=', DB::raw("'PUBLIC'"))
        ->where('public.tbl_empleados_hraes.curp', '=', $curp)
            ->first(); // Devuelve null si no encuentra un registro
}

public function buscarEmpleadoTransferidos($curp)
{
    return DB::table('transferidos.tbl_empleados')
        ->select([
            DB::raw('UPPER(transferidos.tbl_empleados.rfc) AS RFC'),
            DB::raw('UPPER(transferidos.tbl_empleados.curp) AS CURP'),
            DB::raw('UPPER(transferidos.tbl_empleados.nombre) AS NOMBRE'),
            DB::raw('UPPER(transferidos.tbl_empleados.primer_apellido) AS PRIMER_APELLIDO'),
            DB::raw('UPPER(transferidos.tbl_empleados.segundo_apellido) AS SEGUNDO_APELLIDO'),
            'transferidos.tbl_empleados.id_tbl_empleados AS id',
            'ts.id_cat_tipo_schema AS id_schema',
        ])
        ->leftJoin('catalogos.cat_tipo_schema as ts', DB::raw('UPPER(ts.schema)'), '=', DB::raw("'TRANSFERIDOS'"))
        ->where('transferidos.tbl_empleados.curp', '=', $curp)
            ->first(); // Devuelve null si no encuentra un registro
}


public function obtenerOcrearUsuarioPorCurp($curp)
    {
        Log::info('🔎 Iniciando búsqueda de CURP: ' . $curp);

        // Buscar la persona en las bases de datos
        $persona = $this->centralCurp($curp) ?? 
                   $this->buscarEmpleadoHRAES($curp) ?? 
                   $this->buscarEmpleadoTransferidos($curp);

        if (!$persona) {
            Log::error('❌ No se encontró información para CURP: ' . $curp);
            return null;
        }

        Log::info('✅ Persona encontrada: ' . json_encode($persona));

        $idEmpleado = $persona->id;
        $schema = $persona->id_schema; // 1 = Central, 2 = HRAES, 3 = Transferidos

        // Determinar en qué columna se debe registrar el ID del empleado
        $idCentral = ($schema == 1) ? $idEmpleado : null;
        $idHRAES = ($schema == 2) ? $idEmpleado : null;
        $idTransferidos = ($schema == 3) ? $idEmpleado : null;

        Log::info("🛠️ Buscando usuario en administration.users con: id_tbl_empleados_central = {$idCentral}, id_tbl_empleados_hraes = {$idHRAES}, id_tbl_empleados_transferidos = {$idTransferidos}");

        // Buscar si ya existe en administration.users
        $usuario = DB::table('administration.users')
            ->where(function ($query) use ($idCentral, $idHRAES, $idTransferidos) {
                if (!is_null($idCentral)) {
                    $query->orWhere('id_tbl_empleados_central', $idCentral);
                }
                if (!is_null($idHRAES)) {
                    $query->orWhere('id_tbl_empleados_hraes', $idHRAES);
                }
                if (!is_null($idTransferidos)) {
                    $query->orWhere('id_tbl_empleados_transferidos', $idTransferidos);
                }
            })
            ->first();

        if ($usuario) {
            Log::info('✅ Usuario EXISTE en administration.users con ID: ' . $usuario->id);
            return $usuario->id;
        }

        Log::info('🆕 Usuario NO existe. Procediendo a crearlo.');

        // Manejo de nombres asegurando compatibilidad con bases de datos
        $nombre = $persona->NOMBRE ?? $persona->nombre ?? '';
        $apellido = $persona->PRIMER_APELLIDO ?? $persona->primer_apellido ?? '';
        $segundo_apellido = $persona->SEGUNDO_APELLIDO ?? $persona->segundo_apellido ?? '';

        // Generar un email único basado en el nombre y apellido
        $nombreCorreo = strtolower(str_replace(' ', '', $nombre));
        $apellidoCorreo = strtolower(str_replace(' ', '', $apellido));
        $email = "{$nombreCorreo}.{$apellidoCorreo}@correo.com";

        // Verificar que el email sea único
        $contador = 1;
        while (DB::table('administration.users')->where('email', $email)->exists()) {
            $email = "{$nombreCorreo}.{$apellidoCorreo}{$contador}@correo.com";
            $contador++;
        }

        Log::info('📧 Email generado: ' . $email);

        // Generar una contraseña segura
        $passwordGenerica = bcrypt('Instructor2024!');

        // Insertar nuevo usuario en administration.users con transacción segura
        DB::beginTransaction();
        try {
            $idUsuario = DB::table('administration.users')->insertGetId([
                'name' => "{$nombre} {$apellido} {$segundo_apellido}",
                'email' => $email,
                'email_verified_at' => now(),
                'password' => $passwordGenerica,
                'created_at' => now(),
                'updated_at' => now(),
                'id_tbl_empleados_central' => $idCentral,
                'id_tbl_empleados_hraes' => $idHRAES,
                'id_tbl_empleados_transferidos' => $idTransferidos,
                'es_por_nomina' => false,
                'estatus' => true,
                'id_usuario' => Auth::id(),
                'fecha_usuario' => now(),
                'id_cat_tipo_schema' => $schema, // Asignar correctamente el tipo de schema
            ]);

            DB::commit();
            Log::info('✅ Usuario creado con ID: ' . $idUsuario);
            return $idUsuario;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('🔥 Error al insertar usuario en administration.users: ' . $e->getMessage());
            return null;
        }
    }
    
    public function obtenerOcrearInstructor($curp, $estatus)
    {
        \Log::info("🔎 Iniciando búsqueda y creación de instructor con CURP: " . $curp);
    
        // Obtener usuario registrado en `administration.users`
        $idUsuario = $this->obtenerOcrearUsuarioPorCurp($curp);
    
        if (!$idUsuario) {
            \Log::error("❌ No se encontró usuario válido para CURP: " . $curp);
            return null;
        }
    
        // Buscar si el instructor ya está registrado en `capacitacion.tbl_instructores`
        $instructor = DB::table('capacitacion.tbl_instructores')
            ->where('id_usuario_empleado', $idUsuario)
            ->first();
    
        if ($instructor) {
            \Log::info("✅ Instructor YA existe con ID: " . $instructor->id_tbl_instructores);
    
            // Actualizar el estatus si es diferente
            if ($instructor->estatus != $estatus) {
                DB::table('capacitacion.tbl_instructores')
                    ->where('id_tbl_instructores', $instructor->id_tbl_instructores)
                    ->update(['estatus' => ($estatus == 1)]); // Forzamos a booleano
    
                \Log::info("🔄 Estatus actualizado a: " . (($estatus == 1) ? 'Activo' : 'Inactivo'));
            }
    
            return $instructor->id_tbl_instructores;
        }
    
        \Log::info("🆕 Instructor NO existe. Procediendo a registrarlo.");
    
        // Insertar instructor en `capacitacion.tbl_instructores`
        DB::beginTransaction();
        try {
            $instructorId = DB::table('capacitacion.tbl_instructores')->insertGetId([
                'id_usuario_empleado' => $idUsuario,
                'estatus' => ($estatus == 1), // Convertimos 1 en TRUE y 0 en FALSE
                'id_usuario_sistema' => Auth::id(),
                'fecha_usuario' => now(),
            ], 'id_tbl_instructores');
    
            DB::commit();
            \Log::info("✅ Instructor creado con ID: " . $instructorId);
            return $instructorId;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("🔥 Error al insertar instructor en capacitacion.tbl_instructores: " . $e->getMessage());
            return null;
        }
    }
    
}