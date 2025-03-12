<?php

namespace App\Models\Courses\Courses\Assignedcourse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class AssignedcourseM extends Model
{
    protected $table = 'capacitacion.tbl_empleado_cursos';
    protected $primaryKey = 'id_empleado_cursos';
    public $timestamps = false;
    protected $fillable = [
        'id_usuarios',
        'id_cursos',
        'id_calificacion',
        'estatus',
        'uuid_constancia',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

    public function list($iterator, $searchValue)
    {
        $query = DB::table('capacitacion.tbl_empleado_cursos AS e')
            ->join('administration.users AS u', 'e.id_usuarios', '=', 'u.id')
            ->leftJoin('central.tbl_empleados_hraes AS c', 'u.id_tbl_empleados_central', '=', 'c.id_tbl_empleados_hraes')
            ->leftJoin('transferidos.tbl_empleados AS t', 'u.id_tbl_empleados_transferidos', '=', 't.id_tbl_empleados')
            ->leftJoin('public.tbl_empleados_hraes AS p', 'u.id_tbl_empleados_hraes', '=', 'p.id_tbl_empleados_hraes')
            ->selectRaw("
                e.id_empleado_cursos,
                e.id_cursos,
                e.id_calificacion,
                e.uuid_constancia,
                e.fecha_usuario,
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.curp)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.curp)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.curp)
                END AS curp,
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.nombre)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.nombre)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.nombre)
                END AS nombre,
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.primer_apellido)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.primer_apellido)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.primer_apellido)
                END AS primer_apellido,
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.segundo_apellido)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.segundo_apellido)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.segundo_apellido)
                END AS segundo_apellido,
                CASE 
                    WHEN e.estatus = TRUE THEN 'ACTIVO' 
                    ELSE 'INACTIVO' 
                END AS estatus_curso
            ")
            ->when(!empty($searchValue), function ($q) use ($searchValue) {
                $searchValue = strtoupper(trim($searchValue));
                $q->whereRaw("UPPER(c.curp) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(p.curp) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(t.curp) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(e.uuid_constancia) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(e.uuid_constancia) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(e.uuid_constancia) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(c.nombre) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(p.nombre) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(t.nombre) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(c.primer_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(p.primer_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(t.primer_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(c.segundo_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(p.segundo_apellido) LIKE ?", ['%' . $searchValue . '%'])
                  ->orWhereRaw("UPPER(t.segundo_apellido) LIKE ?", ['%' . $searchValue . '%']);

            });

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
     Log::info('🔎 Buscando usuario con CURP: ' . $curp);
 
     // Buscar el usuario en las diferentes bases de datos
     $persona = $this->centralCurp($curp) ?? 
                $this->buscarEmpleadoHRAES($curp) ?? 
                $this->buscarEmpleadoTransferidos($curp);
 
     if (!$persona) {
         Log::error('❌ No se encontró información para CURP: ' . $curp);
         return null;
     }
 
     Log::info('✅ Persona encontrada: ' . json_encode($persona));
 
     // Verificar si el usuario ya existe en `administration.users`
     $usuario = DB::table('administration.users')
         ->where('id_tbl_empleados_central', $persona->id)
         ->orWhere('id_tbl_empleados_hraes', $persona->id)
         ->orWhere('id_tbl_empleados_transferidos', $persona->id)
         ->first();
 
     if ($usuario) {
         Log::info('✅ Usuario EXISTE en administration.users con ID: ' . $usuario->id);
         return $this->guardarEnTblEmpleadoCursos($usuario->id);
     }
 
     // Si no existe, crear usuario
     Log::info('🆕 Usuario NO existe. Creando nuevo usuario.');
 
     // Generar email único
     $baseEmail = strtolower(str_replace(' ', '', $persona->nombre)) . ".correo@example.com";
     $email = $baseEmail;
     $contador = 1;
 
     while (DB::table('administration.users')->where('email', $email)->exists()) {
         $email = strtolower(str_replace(' ', '', $persona->nombre)) . $contador . ".correo@example.com";
         $contador++;
     }
 
     DB::beginTransaction();
     try {
         $idUsuario = DB::table('administration.users')->insertGetId([
             'name' => "{$persona->nombre} {$persona->primer_apellido} {$persona->segundo_apellido}",
             'email' => $email,
             'password' => bcrypt('Alumno2025!'),
             'created_at' => now(),
             'updated_at' => now(),
             'id_tbl_empleados_central' => ($persona->id_schema == 1) ? $persona->id : null,
             'id_tbl_empleados_hraes' => ($persona->id_schema == 2) ? $persona->id : null,
             'id_tbl_empleados_transferidos' => ($persona->id_schema == 3) ? $persona->id : null,
             'es_por_nomina' => false,  // 🔹 Se añade para evitar errores de `NOT NULL`
             'estatus' => true,
             'id_usuario' => Auth::id(),
             'fecha_usuario' => now(),
             'id_cat_tipo_schema' => $persona->id_schema
         ]);
 
         DB::commit();
         Log::info('✅ Usuario creado con ID: ' . $idUsuario);
 
         return $this->guardarEnTblEmpleadoCursos($idUsuario);
     } catch (\Exception $e) {
         DB::rollBack();
         Log::error('🔥 Error al insertar usuario en administration.users: ' . $e->getMessage());
         return null;
     }
 }
 


     
 public function obtenerAlumno($curp, $estatus)
 {
     Log::info("🔎 Iniciando búsqueda y creación de Alumno con CURP: " . $curp);
 
     // Obtener usuario registrado en `administration.users`
     $idUsuario = $this->obtenerOcrearUsuarioPorCurp($curp);
 
     if (!$idUsuario) {
         Log::error("❌ No se encontró usuario válido para CURP: " . $curp);
         return null;
     }
 
     // Buscar si el Alumno ya está registrado en `capacitacion.tbl_empleado_cursos`
     $alumno = DB::table('capacitacion.tbl_empleado_cursos')
         ->where('id_usuarios', $idUsuario)
         ->first();
 
     if ($alumno) {
         Log::info("✅ Alumno YA existe con ID: " . $alumno->id_empleado_cursos);
 
         // Si el estatus es diferente, lo actualiza
         if ($alumno->estatus != $estatus) {
             DB::table('capacitacion.tbl_empleado_cursos')
                 ->where('id_empleado_cursos', $alumno->id_empleado_cursos)
                 ->update(['estatus' => ($estatus == 1)]);
 
             Log::info("🔄 Estatus actualizado a: " . (($estatus == 1) ? 'Activo' : 'Inactivo'));
         }
 
         return $alumno->id_empleado_cursos;
     }
 
     Log::info("🆕 Alumno NO existe. Procediendo a registrarlo.");
 
     // Insertar Alumno en `capacitacion.tbl_empleado_cursos`
     DB::beginTransaction();
     try {
         $AlumnoId = DB::table('capacitacion.tbl_empleado_cursos')->insertGetId([
             'id_usuarios' => $idUsuario,
             'estatus' => ($estatus == 1), // Convertimos 1 en TRUE y 0 en FALSE
             'id_usuario_sistema' => Auth::id(),
             'fecha_usuario' => now(),
         ]);
 
         DB::commit();
         Log::info("✅ Alumno creado con ID: " . $AlumnoId);
         return $AlumnoId;
     } catch (\Exception $e) {
         DB::rollBack();
         Log::error("🔥 Error al insertar Alumno en capacitacion.tbl_empleado_cursos: " . $e->getMessage());
         return null;
     }
 }
 
 
     public function getDataReport($id)
     {
         return DB::table('capacitacion.tbl_empleado_cursos AS e')
             ->select([
                 'e.id_empleado_cursos',
                 DB::raw("
                     CASE
                         WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.curp)
                         WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.curp)
                         WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.curp)
                     END AS curp
                 "),
                 DB::raw("
                     CASE
                         WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.nombre)
                         WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.nombre)
                         WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.nombre)
                     END AS nombre
                 "),
                 DB::raw("
                     CASE
                         WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.primer_apellido)
                         WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.primer_apellido)
                         WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.primer_apellido)
                     END AS primer_apellido
                 "),
                 DB::raw("
                     CASE
                         WHEN u.id_cat_tipo_schema = 1 THEN UPPER(c.segundo_apellido)
                         WHEN u.id_cat_tipo_schema = 2 THEN UPPER(p.segundo_apellido)
                         WHEN u.id_cat_tipo_schema = 3 THEN UPPER(t.segundo_apellido)
                     END AS segundo_apellido
                 "),
                 DB::raw("
                     CASE
                         WHEN e.estatus = TRUE THEN 'ACTIVO'
                         ELSE 'INACTIVO'
                     END AS estatus_curso
                 "),
                 'u.email',
                 'u.name AS usuario_sistema',
                 'e.fecha_usuario'
             ])
             ->join('administration.users AS u', 'e.id_usuarios', '=', 'u.id')
             ->leftJoin('central.tbl_empleados_hraes AS c', 'u.id_tbl_empleados_central', '=', 'c.id_tbl_empleados_hraes')
             ->leftJoin('transferidos.tbl_empleados AS t', 'u.id_tbl_empleados_transferidos', '=', 't.id_tbl_empleados')
             ->leftJoin('public.tbl_empleados_hraes AS p', 'u.id_tbl_empleados_hraes', '=', 'p.id_tbl_empleados_hraes')
             ->where('e.id_empleado_cursos', '=', $id)
             ->first();
     }

     private function guardarEnTblEmpleadoCursos($idUsuario)
{
    Log::info("💾 Insertando usuario con ID: $idUsuario en tbl_empleado_cursos");

    // Verificar si ya existe
    $existe = DB::table('capacitacion.tbl_empleado_cursos')
        ->where('id_usuarios', $idUsuario)
        ->exists();

    if ($existe) {
        Log::info("✅ El usuario ya está registrado en tbl_empleado_cursos.");
        return $idUsuario;
    }

    // Insertar el usuario en la tabla
    DB::beginTransaction();
    try {
        DB::table('capacitacion.tbl_empleado_cursos')->insert([
            'id_usuarios' => $idUsuario,
            'estatus' => true,
            'id_usuario_sistema' => Auth::id(),
            'fecha_usuario' => now()
        ]);

        DB::commit();
        Log::info("✅ Usuario insertado correctamente en tbl_empleado_cursos con ID: $idUsuario");
        return $idUsuario;
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("🔥 Error al insertar en tbl_empleado_cursos: " . $e->getMessage());
        return null;
    }
}

    }