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
 
 
 public function obtenerOcrearUsuarioPorCurp($curp, $idCursos)
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
        return $this->guardarEnTblEmpleadoCursos($usuario->id, $idCursos); // 🔹 Ahora se pasa id_cursos
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
            'es_por_nomina' => false,
            'estatus' => true,
            'id_usuario' => Auth::id(),
            'fecha_usuario' => now(),
            'id_cat_tipo_schema' => $persona->id_schema
        ]);

        DB::commit();
        Log::info('✅ Usuario creado con ID: ' . $idUsuario);

        return $this->guardarEnTblEmpleadoCursos($idUsuario, $idCursos); // 🔹 Se pasa id_cursos
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('🔥 Error al insertar usuario en administration.users: ' . $e->getMessage());
        return null;
    }
}

     public function guardarEnTblEmpleadoCursos($idUsuario, $idCursos = null)
     {
         Log::info("💾 Intentando insertar usuario con ID: $idUsuario en tbl_empleado_cursos con ID de curso: " . ($idCursos ?? 'Ninguno'));
     
         // 🔹 Si no hay curso, verifica si el usuario ya está en la tabla SIN curso
         if (!$idCursos) {
             Log::info("🛑 No se asignará un curso a este usuario en este módulo.");
     
             $existeRegistroSinCurso = DB::table('capacitacion.tbl_empleado_cursos')
                 ->where('id_usuarios', $idUsuario)
                 ->whereNull('id_cursos') // 🔹 Verificamos registros sin curso
                 ->exists();
     
             if ($existeRegistroSinCurso) {
                 Log::info("⚠️ El usuario YA ESTÁ REGISTRADO en tbl_empleado_cursos SIN curso. No se duplica.");
                 return $idUsuario;
             }
     
             // Si no existe, insertamos el usuario sin curso
             DB::beginTransaction();
             try {
                 DB::table('capacitacion.tbl_empleado_cursos')->insert([
                     'id_usuarios' => $idUsuario,
                     'id_cursos' => null, // Se guarda NULL en id_cursos
                     'id_calificacion' => null,
                     'estatus' => true,
                     'id_usuario_sistema' => Auth::id(),
                     'fecha_usuario' => now()
                 ]);
     
                 DB::commit();
                 Log::info("✅ Usuario insertado correctamente en tbl_empleado_cursos SIN curso.");
                 return $idUsuario;
             } catch (\Exception $e) {
                 DB::rollBack();
                 Log::error("🔥 Error al insertar usuario en tbl_empleado_cursos sin curso: " . $e->getMessage());
                 return null;
             }
         }
     
         // 🔹 Si hay curso, verifica si ya está registrado antes de insertarlo
         $existe = DB::table('capacitacion.tbl_empleado_cursos')
             ->where('id_usuarios', $idUsuario)
             ->where('id_cursos', $idCursos)
             ->exists();
     
         if ($existe) {
             Log::info("✅ El usuario ya está registrado en tbl_empleado_cursos con este curso.");
             return $idUsuario;
         }
     
         // Si no está registrado con el curso, se inserta
         DB::beginTransaction();
         try {
             DB::table('capacitacion.tbl_empleado_cursos')->insert([
                 'id_usuarios' => $idUsuario,
                 'id_cursos' => $idCursos,
                 'id_calificacion' => null,
                 'estatus' => true,
                 'id_usuario_sistema' => Auth::id(),
                 'fecha_usuario' => now()
             ]);
     
             DB::commit();
             Log::info("✅ Usuario insertado correctamente en tbl_empleado_cursos con curso.");
             return $idUsuario;
         } catch (\Exception $e) {
             DB::rollBack();
             Log::error("🔥 Error al insertar usuario en tbl_empleado_cursos con curso: " . $e->getMessage());
             return null;
         }
     }
     
     
     public function obtenerAlumno($curp, $estatus, $idCursos = null)
{
    Log::info("🔎 Iniciando búsqueda y creación de Alumno con CURP: " . $curp);

    // 🔹 Llamar a `obtenerOcrearUsuarioPorCurp()` con ambos argumentos
    $idUsuario = $this->obtenerOcrearUsuarioPorCurp($curp, $idCursos ?? null);

    if (!$idUsuario) {
        Log::error("❌ No se encontró usuario válido para CURP: " . $curp);
        return null;
    }

    // 🔹 Si `id_cursos` es `null`, no se asigna curso
    if (!$idCursos) {
        Log::info("⚠️ Usuario registrado sin curso.");
        return $idUsuario;
    }

    // Si hay curso, verifica si ya existe antes de guardarlo
    $alumno = DB::table('capacitacion.tbl_empleado_cursos')
        ->where('id_usuarios', $idUsuario)
        ->where('id_cursos', $idCursos)
        ->first();

    if ($alumno) {
        Log::info("✅ Alumno YA existe con ID: " . $alumno->id_empleado_cursos);
        return $alumno->id_empleado_cursos;
    }

    Log::info("🆕 Alumno NO existe. Procediendo a registrarlo.");
    return $this->guardarEnTblEmpleadoCursos($idUsuario, $idCursos);
}

public function obtenerCursosConDetallesPorEmpleado($idEmpleadoCursos)
{
    Log::info("🔎 Buscando cursos para el ID de empleado: " . ($idEmpleadoCursos ?? 'No definido'));

    $cursos = DB::table('capacitacion.tbl_empleado_cursos as ec')
        ->select([
            'ec.id_empleado_cursos',
            'ec.id_cursos',
            'c.programa_proyecto',
            'c.fecha_inicio',
            'c.fecha_fin',
            'c.horas',
            'c.id_cat_tipo_cursos',
            DB::raw('UPPER(ct.descripcion) AS tipo_curso'),
            'c.estatus'
        ])
        ->join('capacitacion.tbl_cursos as c', 'ec.id_cursos', '=', 'c.id_tbl_cursos') // ✅ Relación corregida
        ->join('capacitacion.cat_tipo_cursos as ct', 'c.id_cat_tipo_cursos', '=', 'ct.id_cat_tipo_cursos')
        ->where('ec.id_empleado_cursos', '=', $idEmpleadoCursos)
        ->whereNotNull('ec.id_cursos') // Evita registros sin curso asignado
        ->get();

    if ($cursos->isEmpty()) {
        Log::warning("⚠️ No se encontraron cursos para el ID de empleado: $idEmpleadoCursos");
    } else {
        Log::info("✅ Cursos obtenidos para el ID de empleado: $idEmpleadoCursos - " . json_encode($cursos));
    }

    return $cursos;
}

public function getDataReport($id)
{
    Log::info("🔍 Buscando información del curso para el ID: " . $id);

    $query = DB::table('capacitacion.tbl_empleado_cursos AS ec')
        ->join('capacitacion.tbl_cursos AS c', 'ec.id_cursos', '=', 'c.id_tbl_cursos')
        ->join('administration.users AS u', 'ec.id_usuarios', '=', 'u.id')
        ->leftJoin('central.tbl_empleados_hraes AS central', 'u.id_tbl_empleados_central', '=', 'central.id_tbl_empleados_hraes')
        ->leftJoin('transferidos.tbl_empleados AS transferidos', 'u.id_tbl_empleados_transferidos', '=', 'transferidos.id_tbl_empleados')
        ->leftJoin('public.tbl_empleados_hraes AS public', 'u.id_tbl_empleados_hraes', '=', 'public.id_tbl_empleados_hraes')
        ->select([
            'ec.id_empleado_cursos',
            'ec.id_cursos',
            'c.programa_proyecto AS curso',
            'c.fecha_inicio',
            'c.fecha_fin',
            'c.horas',
            'c.id_cat_tipo_cursos',
            DB::raw('UPPER(ct.descripcion) AS tipo_curso'),
            'ec.estatus',
            DB::raw("
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(central.curp)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(public.curp)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(transferidos.curp)
                END AS curp
            "),
            DB::raw("
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(central.nombre)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(public.nombre)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(transferidos.nombre)
                END AS nombre
            "),
            DB::raw("
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(central.primer_apellido)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(public.primer_apellido)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(transferidos.primer_apellido)
                END AS primer_apellido
            "),
            DB::raw("
                CASE
                    WHEN u.id_cat_tipo_schema = 1 THEN UPPER(central.segundo_apellido)
                    WHEN u.id_cat_tipo_schema = 2 THEN UPPER(public.segundo_apellido)
                    WHEN u.id_cat_tipo_schema = 3 THEN UPPER(transferidos.segundo_apellido)
                END AS segundo_apellido
            "),
            'u.email'
        ])
        ->join('capacitacion.cat_tipo_cursos AS ct', 'c.id_cat_tipo_cursos', '=', 'ct.id_cat_tipo_cursos')
        ->where('ec.id_empleado_cursos', '=', $id);

    Log::info("📝 SQL Query Generado: " . $query->toSql());
    
    $data = $query->first();

    if (!$data) {
        Log::warning("⚠️ No se encontró información del curso para ID: $id");
    } else {
        Log::info("✅ Información encontrada para ID: $id -> " . json_encode($data));
    }

    return $data;
}

 }

    