<?php

namespace App\Models\Letter\Letter;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class LetterM extends Model
{
    protected $table = 'correspondencia.tbl_correspondencia';
    public $timestamps = false;
    protected $primaryKey = 'id_tbl_correspondencia';
    protected $fillable = [
        'num_turno_sistema',
        'num_documento',
        'fecha_captura',
        'fecha_inicio',
        'fecha_fin',
        'num_flojas',
        'num_tomos',
        'horas_respuesta',
        'asunto',
        'observaciones',
        'fecha_usuario',
        'id_cat_area',
        'id_usuario_area',
        'id_usuario_enlace',
        'id_cat_estatus',
        'id_cat_remitente',
        'id_cat_anio',
        'id_cat_tramite',
        'id_cat_clave',
        'id_cat_unidad',
        'id_cat_coordinacion',
        'id_usuario_sistema',
        'puesto_remitente',
        'folio_gestion',
        'id_usuario_captura',
        'fecha_usuario_captura',
        'es_doc_fisico',
        'son_mas_remitentes',
        'remitente',
        'fecha_documento',
        'id_cat_entidad',
    ];

    // La función retorna el id de correspondencia, esperando el folio unico de gestión
    public function getIdFolGestion($folGestion)
    {
        return DB::table('correspondencia.tbl_correspondencia')
            ->select('id_tbl_correspondencia as id')
            ->whereRaw('TRIM(UPPER(folio_gestion)) = TRIM(UPPER(?))', [$folGestion])
            ->first();
    }


    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->where('id_tbl_correspondencia', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }



    public function editFol(string $fol)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->whereRaw('UPPER(TRIM(folio_gestion)) = ?', [strtoupper(trim($fol))]) // Parametrizamos la consulta
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el resultado, si no se encuentra, retorna null
        return $query ?? null;
    }

    public function list($iterator, $searchValue, $idUser)
    {
        // Preparar la consulta base
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->select([
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia AS id',
                //DB::raw('UPPER(correspondencia.tbl_correspondencia.num_turno_sistema) AS num_turno_sistema'),
                DB::raw('UPPER(correspondencia.tbl_correspondencia.num_documento) AS num_documento'),
                DB::raw('UPPER(correspondencia.tbl_correspondencia.folio_gestion) AS folio_gestion'), // No es necesario DISTINCT
                DB::raw('UPPER(correspondencia.cat_estatus.descripcion) AS estatus'),
                DB::raw('UPPER(correspondencia.cat_tramite.descripcion) AS tramite'),
                DB::raw('UPPER(correspondencia.cat_area.descripcion) AS area'),
                DB::raw('UPPER(correspondencia.tbl_correspondencia.asunto) AS asunto'),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_captura::date, 'DD/MM/YYYY') AS fecha_captura"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin::date, 'DD/MM/YYYY') AS fecha_fin")
            ])
            ->join('correspondencia.cat_estatus', 'correspondencia.tbl_correspondencia.id_cat_estatus', '=', 'correspondencia.cat_estatus.id_cat_estatus')
            ->join('correspondencia.cat_area', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->join('correspondencia.cat_tramite', 'correspondencia.tbl_correspondencia.id_cat_tramite', '=', 'correspondencia.cat_tramite.id_cat_tramite')
            ->leftJoin('correspondencia.ctrl_transcribir_correspondencia', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.ctrl_transcribir_correspondencia.id_tbl_correspondencia')
            ->groupBy(
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia',
                'correspondencia.tbl_correspondencia.num_documento',
                'correspondencia.tbl_correspondencia.folio_gestion', // Usamos GROUP BY para este campo
                'correspondencia.cat_estatus.descripcion',
                'correspondencia.cat_tramite.descripcion',
                'correspondencia.cat_area.descripcion',
                'correspondencia.tbl_correspondencia.asunto',
                'correspondencia.tbl_correspondencia.fecha_captura',
                'correspondencia.tbl_correspondencia.fecha_fin'
            );

        // Filtrar por área si se proporciona el id
        if (!empty($idUser)) {

            $query->where(function ($query) use ($idUser) {
                $query->whereIn('correspondencia.tbl_correspondencia.id_cat_area', $idUser)
                    ->orWhereIn('correspondencia.ctrl_transcribir_correspondencia.id_cat_area', $idUser);
            });

            $query->where('correspondencia.tbl_correspondencia.id_cat_estatus', '!=', 2);

        }

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas


            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.tbl_correspondencia.num_documento)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_correspondencia.asunto)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.cat_estatus.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_correspondencia.folio_gestion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.cat_area.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(TO_CHAR(correspondencia.tbl_correspondencia.fecha_captura, 'DD/MM/YYYY'))) LIKE ?", ['%' . $searchValue . '%']);
                //->orWhereRaw("UPPER(TRIM(TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin, 'DD/MM/YYYY'))) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        if (!empty($idUser)) { // Ordenamiento por estatus
            $query->orderByRaw('CASE correspondencia.tbl_correspondencia.id_cat_estatus
                                WHEN 1 THEN 1 -- TURNADO
                                WHEN 2 THEN 2 -- CANCELADO
                                WHEN 3 THEN 3 -- EN PROCESO
                                WHEN 4 THEN 4 -- CONCLUIDO
                                WHEN 5 THEN 5 -- VENCIDO
                                WHEN 6 THEN 6 -- RECHAZADO
                                ELSE 7 -- Para cualquier valor no esperado
                            END ASC');
        } else { // Ordenamiento para admin
            $query->orderBy('correspondencia.tbl_correspondencia.id_tbl_correspondencia', 'DESC');
        }

        $query->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }


    //La funcion valida que el no de documento sea unico
    public function validateNoDocument($id, $value)
    {
        // Realizar la consulta a la base de datos, buscando si existe un registro con el valor de documento
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->select('correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->whereRaw('UPPER(TRIM(correspondencia.tbl_correspondencia.num_documento)) = UPPER(TRIM(?))', [trim($value)]);

        // Si el ID está presente, agregar la condición para excluir el ID
        if (isset($id)) {
            $query->whereRaw('correspondencia.tbl_correspondencia.id_tbl_correspondencia <> ?', [$id]);
        }

        // Ejecutar la consulta y verificar si hay resultados
        $result = $query->first();

        // Retornar true si se encuentra algún resultado, de lo contrario false
        return $result;//$result !== null;
    }

    //La funcion obtiene informacon para la impresion de reporte en pdf
    public function getDataReport($id)
    {
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.num_turno_sistema AS num_turno_sistema',
                'correspondencia.tbl_correspondencia.num_documento AS num_documento',
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin, 'DD/MM/YYYY') AS fecha_fin"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_documento, 'DD/MM/YYYY') AS fecha_documento"),
                'correspondencia.tbl_correspondencia.num_flojas AS num_flojas',
                'correspondencia.tbl_correspondencia.num_tomos AS num_tomos',
                'correspondencia.tbl_correspondencia.horas_respuesta AS horas_respuesta',
                'correspondencia.tbl_correspondencia.asunto AS asunto',
                'correspondencia.tbl_correspondencia.folio_gestion AS folio_gestion',
                'correspondencia.tbl_correspondencia.observaciones AS observaciones',
                DB::raw("COALESCE(correspondencia.cat_remitente.nombre, '') || ' ' || 
                            COALESCE(correspondencia.cat_remitente.primer_apellido, '') || ' ' ||
                            COALESCE(correspondencia.cat_remitente.segundo_apellido, '') || ' ' ||
                            ' - ' || COALESCE(correspondencia.cat_remitente.rfc, '') AS remitente"),
                'correspondencia.cat_anio.descripcion AS anio',
                'correspondencia.cat_tramite.descripcion AS tramite',
                'correspondencia.cat_area.descripcion AS area',
                'correspondencia.cat_clave.descripcion AS codigo',
                'correspondencia.cat_clave.redaccion AS clave',
                'correspondencia.cat_unidad.descripcion AS unidad',
                'correspondencia.cat_coordinacion.descripcion AS coordinacion',
                'correspondencia.tbl_correspondencia.puesto_remitente AS puesto_remitente',
                'correspondencia.cat_entidad.descripcion AS entidad',
                'administration.users.name AS user_area'
            )
            ->leftJoin('correspondencia.cat_area', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->leftJoin('correspondencia.cat_remitente', 'correspondencia.tbl_correspondencia.id_cat_remitente', '=', 'correspondencia.cat_remitente.id_cat_remitente')
            ->leftJoin('correspondencia.cat_anio', 'correspondencia.tbl_correspondencia.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->leftJoin('correspondencia.cat_tramite', 'correspondencia.tbl_correspondencia.id_cat_tramite', '=', 'correspondencia.cat_tramite.id_cat_tramite')
            ->leftJoin('correspondencia.cat_clave', 'correspondencia.tbl_correspondencia.id_cat_clave', '=', 'correspondencia.cat_clave.id_cat_clave')
            ->leftJoin('correspondencia.cat_unidad', 'correspondencia.tbl_correspondencia.id_cat_unidad', '=', 'correspondencia.cat_unidad.id_cat_unidad')
            ->leftJoin('correspondencia.cat_coordinacion', 'correspondencia.tbl_correspondencia.id_cat_coordinacion', '=', 'correspondencia.cat_coordinacion.id_cat_coordinacion')
            ->leftJoin('correspondencia.cat_entidad', 'correspondencia.tbl_correspondencia.id_cat_entidad', '=', 'correspondencia.cat_entidad.id_cat_entidad')
            ->leftJoin('administration.users', 'correspondencia.tbl_correspondencia.id_usuario_captura', '=', 'administration.users.id')
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', $id)
            ->first(); // Obtener solo el primer resultado

        return $query;
    }


    //La funcion obtiene el numero de turno, a partir de su id
    public function getTurno($id)
    {
        // Realizar la consulta utilizando el query builder de Laravel
        $turno = DB::table('correspondencia.tbl_correspondencia')
            ->where('id_tbl_correspondencia', $id)
            ->value('folio_gestion');

        // Si no se encuentra información, retornamos null
        return $turno ?: null;
    }

    //Valida el no de turno exista
    public function validateNoTurno($noTurno)
    {
        // Usamos whereRaw con binding para evitar problemas de inyección SQL
        $turno = DB::table('correspondencia.tbl_correspondencia')
            ->whereRaw('UPPER(TRIM(num_turno_sistema)) = UPPER(TRIM(?))', [$noTurno])
            ->orWhereRaw('UPPER(TRIM(folio_gestion)) = UPPER(TRIM(?))', [$noTurno])
            ->value('id_tbl_correspondencia'); // Recuperamos el valor de id_tbl_correspondencia

        // Retornamos el valor, si no se encuentra, será null
        return $turno;
    }

    public function validateNoTurnoArea($noTurno)
    {
        $turno = DB::table('correspondencia.tbl_correspondencia')
            ->where('num_turno_sistema', $noTurno)
            ->value('correspondencia.tbl_correspondencia.id_cat_area');

        // Si no se encuentra información, retornamos null
        return $turno ?: null;
    }

    //La funcion retorna  los datos de encabezado de la vista cloud
    public function dataCloud($id)
    {
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.num_turno_sistema',
                'correspondencia.tbl_correspondencia.num_documento',
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_inicio, 'DD/MM/YYYY') as fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin, 'DD/MM/YYYY') as fecha_fin"),
                'correspondencia.cat_anio.descripcion as anio'
            )
            ->join('correspondencia.cat_anio', 'correspondencia.tbl_correspondencia.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', $id)
            ->first();

        return $query;
    }

    //La funcion valida que el no de documento sea unico
    public function uniqueNoDocument($id, $value, $attribute)
    {
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->select('correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->whereRaw('UPPER(TRIM(correspondencia.tbl_correspondencia. ' . $attribute . ')) = UPPER(TRIM(?))', [trim($value)]);

        // Si el ID está presente, agregar la condición para excluir el ID
        if (isset($id)) {
            $query->whereRaw('correspondencia.tbl_correspondencia.id_tbl_correspondencia <> ?', [$id]);
        }

        // Ejecutar la consulta y verificar si hay resultados
        $result = $query->first();

        // Retornar true si se encuentra algún resultado, de lo contrario false
        return $result;//$result !== null;
    }

    // La funcion busca el usuario y enlace a apartir del no de turno de correspondencia que se ingrese, esto se usa en el encabezado
    // de oficio, interno, expedientes y circulares
    public function getUserEnlace($value)
    {
        // Usamos Query Builder de Laravel para construir la consulta
        return DB::table('correspondencia.tbl_correspondencia')
            ->select(
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia',
                'correspondencia.tbl_correspondencia.id_cat_area AS id_cat_area',
                'correspondencia.tbl_correspondencia.id_usuario_area AS id_usuario_area',
                'correspondencia.tbl_correspondencia.id_usuario_enlace AS id_usuario_enlace',
                DB::raw('UPPER(user_area.name) as usuario_area'), // Usamos el alias correcto 'user_area'
                DB::raw('UPPER(correspondencia.cat_area.descripcion) as area'), // Usamos el alias correcto 'user_area'
                DB::raw('UPPER(user_enlace.name) as usuario_enlace') // Usamos el alias correcto 'user_enlace'
            )
            ->join('administration.users AS user_area', 'correspondencia.tbl_correspondencia.id_usuario_area', '=', 'user_area.id')
            ->join('administration.users AS user_enlace', 'correspondencia.tbl_correspondencia.id_usuario_enlace', '=', 'user_enlace.id')
            ->join('correspondencia.cat_area', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->where(function ($query) use ($value) {
                $query->whereRaw('UPPER(TRIM(correspondencia.tbl_correspondencia.num_turno_sistema)) = UPPER(TRIM(?))', [$value])
                    ->orWhereRaw('UPPER(TRIM(correspondencia.tbl_correspondencia.folio_gestion)) = UPPER(TRIM(?))', [$value]);
            })
            ->get();
    }

    // La funcion retorna la informacion que ira en el cuerpo de email que se compartira
    public function mailLetter($id)
    {
        $result = DB::table('correspondencia.tbl_correspondencia')
            ->join('correspondencia.cat_area', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->join('administration.users AS users_user', 'correspondencia.tbl_correspondencia.id_usuario_area', '=', 'users_user.id')
            ->join('administration.users AS users_enlace', 'correspondencia.tbl_correspondencia.id_usuario_enlace', '=', 'users_enlace.id')
            ->select(
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia',
                DB::raw('UPPER(correspondencia.tbl_correspondencia.asunto) AS asunto'),
                DB::raw('UPPER(correspondencia.tbl_correspondencia.num_turno_sistema) AS num_turno_sistema'),
                DB::raw('UPPER(correspondencia.tbl_correspondencia.num_documento) AS num_documento'),
                DB::raw('TO_CHAR(correspondencia.tbl_correspondencia.fecha_inicio, \'DD/MM/YYYY\') AS fecha_inicio'),
                DB::raw('TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin, \'DD/MM/YYYY\') AS fecha_fin'),
                DB::raw('UPPER(correspondencia.cat_area.descripcion) AS area_descripcion'),
                DB::raw('UPPER(users_user.name) AS usuario_area'),
                DB::raw('UPPER(users_enlace.name) AS usuario_enlace')
            )
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', $id)
            ->first();

        return $result;
    }

    // La función retorna el valor mayor de los autoincrementables
    public function getMaxNuSistem()
    {
        // Realizar la consulta usando DB::table
        $maxNumTurno = DB::table('correspondencia.tbl_correspondencia')
            ->selectRaw("MAX(CAST((REGEXP_MATCH(num_turno_sistema, '/([0-9]{4,5})/'))[1] AS INTEGER)) AS max_num_turno")
            ->whereRaw("num_turno_sistema ~ '/[0-9]{4,5}/'")
            ->value('max_num_turno'); // Obtener solo el valor de la columna max_num_turno

        /// FUNCIONES PARA DASHBOARD
        // La función cuenta el todal de no de correspondencia
        return $maxNumTurno;
    }


    // LA función retorna la tabla de copy, con copia a de turnos
    public function tableCopy($id)
    {
        $result = DB::table('correspondencia.ctrl_transcribir_correspondencia')
            ->select(
                'correspondencia.ctrl_transcribir_correspondencia.id_ctrl_transcribir_correspondencia AS id',
                'correspondencia.cat_area.descripcion AS area',
                'usuario_x.name AS usuario',
                'enlace_y.name AS enlace',
                'correspondencia.cat_tramite.descripcion AS tramite',
                'correspondencia.cat_clave.descripcion AS clave'
            )
            ->join('correspondencia.cat_area', 'correspondencia.ctrl_transcribir_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->join('administration.users AS usuario_x', 'correspondencia.ctrl_transcribir_correspondencia.id_usuario_area', '=', 'usuario_x.id')
            ->join('administration.users AS enlace_y', 'correspondencia.ctrl_transcribir_correspondencia.id_usuario_enlace', '=', 'enlace_y.id')
            ->join('correspondencia.cat_tramite', 'correspondencia.ctrl_transcribir_correspondencia.id_cat_tramite', '=', 'correspondencia.cat_tramite.id_cat_tramite')
            ->join('correspondencia.cat_clave', 'correspondencia.ctrl_transcribir_correspondencia.id_cat_clave', '=', 'correspondencia.cat_clave.id_cat_clave')
            ->where('correspondencia.ctrl_transcribir_correspondencia.id_tbl_correspondencia', '=', $id)
            ->limit(20)
            ->get();

        return $result;
    }

    // La Funcion valida que el area y el no de correspondencia no esten asociados
    // La retorna verdadero si la consulta esta vacia o falso si lleva información
    public function getValue($id_letter, $id_area)
    {
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->select('correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->leftJoin('correspondencia.ctrl_transcribir_correspondencia', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', 'correspondencia.ctrl_transcribir_correspondencia.id_tbl_correspondencia')
            ->where('correspondencia.tbl_correspondencia.id_tbl_correspondencia', '=', $id_letter)
            ->where(function ($query) use ($id_area) {
                $query->where('correspondencia.tbl_correspondencia.id_cat_area', '=', $id_area)
                    ->orWhere('correspondencia.ctrl_transcribir_correspondencia.id_cat_area', '=', $id_area);
            })
            ->exists();  // Devuelve true si existen resultados, false si no existen

        return !$query;  // Si hay resultados, retorna false; si no, retorna true
    }
}
