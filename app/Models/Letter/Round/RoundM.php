<?php

namespace App\Models\Letter\Round;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class RoundM extends Model
{
    protected $table = 'correspondencia.tbl_circular';
    public $timestamps = false;
    protected $primaryKey = 'id_tbl_circular';
    protected $fillable = [
        'num_turno_sistema',
        'fecha_inicio',
        'fecha_fin',
        'fecha_captura',
        'asunto',
        'observaciones',
        'fecha_usuario',
        'id_usuario_sistema',
        'id_cat_anio',
        'id_tbl_correspondencia',
        'es_por_area',
        'num_documento_area',
        'id_cat_area_documento',
        'id_usuario_captura',
        'id_usuario_area',
        'id_usuario_enlace',
        'id_cat_area',
        'destinatario',
    ];

    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('correspondencia.tbl_circular')
            ->where('id_tbl_circular', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }

    //La funcion crea la tabla tanto para busquedas como para modelado
    public function list($iterator, $searchValue, $idUser)
    {
        // Preparar la consulta base
        $query = DB::table('correspondencia.tbl_circular')
            ->select([
                'correspondencia.tbl_circular.id_tbl_circular AS id',
                DB::raw('correspondencia.tbl_circular.num_turno_sistema AS num_turno_sistema'),
                DB::raw('
                CASE 
                    WHEN correspondencia.tbl_circular.es_por_area THEN 
                        correspondencia.tbl_circular.num_documento_area 
                    ELSE 
                        correspondencia.tbl_correspondencia.num_turno_sistema 
                END AS num_documento
            '),
                DB::raw("UPPER(correspondencia.tbl_circular.asunto) AS asunto"),
                DB::raw("TO_CHAR(correspondencia.tbl_circular.fecha_inicio::date, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_circular.fecha_fin::date, 'DD/MM/YYYY') AS fecha_fin"),
                DB::raw("correspondencia.cat_anio.descripcion AS anio"),
            ])
            ->leftJoin('correspondencia.tbl_correspondencia', 'correspondencia.tbl_circular.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->join('correspondencia.cat_anio', 'correspondencia.tbl_circular.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio');

        // Filtrar por usuario si se proporciona el id
        /*
        if (!empty($idUser)) {
            $query->where('correspondencia.tbl_circular.id_usuario_area', $idUser)
                ->orWhere('correspondencia.tbl_circular.id_usuario_enlace', $idUser);
        }
        */
        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.tbl_circular.num_turno_sistema)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_correspondencia.num_turno_sistema)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_circular.num_documento_area)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.cat_anio.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_circular.asunto)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('correspondencia.tbl_circular.id_tbl_circular', 'DESC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }

    //La funcion retorna  los datos de encabezado de la vista cloud
    public function dataCloud($id)
    {
        $query = DB::table('correspondencia.tbl_circular')
            ->leftjoin('correspondencia.tbl_correspondencia', 'correspondencia.tbl_circular.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->join('correspondencia.cat_anio', 'correspondencia.tbl_circular.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->select(
                'correspondencia.tbl_circular.num_turno_sistema AS num_turno_sistema',
                DB::raw('CASE WHEN correspondencia.tbl_circular.es_por_area THEN 
                                    correspondencia.tbl_circular.num_documento_area ELSE 
                                    correspondencia.tbl_correspondencia.num_turno_sistema 
                                END AS num_turno_sistema_correspondencia'),
                DB::raw("TO_CHAR(correspondencia.tbl_circular.fecha_inicio::date, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_circular.fecha_fin::date, 'DD/MM/YYYY') AS fecha_fin"),
                'correspondencia.cat_anio.descripcion AS anio'
            )
            ->where('correspondencia.tbl_circular.id_tbl_circular', $id)
            ->first(); // Usamos `first` para obtener solo un resultado

        return $query;
    }

    // La función retorna el valor mayor de los autoincrementables
    public function getMaxNuSistem()
    {
        // Realizar la consulta usando DB::table
        $maxNumTurno = DB::table('correspondencia.tbl_circular')
            ->selectRaw("
                           MAX(CAST(REGEXP_REPLACE(num_turno_sistema, '^[^/]+/([0-9]{5})/.*$', '\\1') AS INTEGER)) AS max_num_turno
                       ")
            ->whereRaw("num_turno_sistema ~ '/[0-9]{5}/'")
            ->value('max_num_turno'); // Obtener solo el valor de la columna max_num_turno

        return $maxNumTurno;
    }

    public function getOnly($idCatArea, $idAnio)
    {
        return DB::table('correspondencia.tbl_circular')
            ->selectRaw('MAX(CAST(REGEXP_REPLACE(num_documento_area, \'^\\D*(\\d+).*\', \'\\1\') AS INT)) AS max_num')
            ->whereRaw("num_documento_area ~ '/\\d{4}$'")
            ->where('id_cat_area_documento', $idCatArea)
            ->where('id_cat_anio', $idAnio)
            ->first();  // Devuelve el primer (y único) resultado
    }


    public function getReporteFiltrado($area, $year)
{
    $query = DB::table('correspondencia.tbl_circular as o')
        ->select([
            'o.num_turno_sistema as No_Turno',
            'o.fecha_captura as Fecha_captura',
            'anio.descripcion as Anio',
            'a.descripcion as Area',
            'o.num_documento_area as No_Doc',
            'ua.name as Usuario',
            'ue.name as Enlace',
            'o.fecha_inicio as Fecha_emision',
            'o.fecha_fin as Fecha_aplicacion',
            'o.asunto as Asunto',
            'o.destinatario as Destinatario',
            'o.observaciones as Observaciones'
        ])
        ->leftJoin('correspondencia.tbl_correspondencia as c', 'o.id_tbl_correspondencia', '=', 'c.id_tbl_correspondencia')
        ->leftJoin('correspondencia.cat_anio as anio', 'o.id_cat_anio', '=', 'anio.id_cat_anio')
        ->leftJoin('correspondencia.cat_area as a', 'o.id_cat_area', '=', 'a.id_cat_area')
        ->leftJoin('administration.users as ua', 'o.id_usuario_area', '=', 'ua.id')
        ->leftJoin('administration.users as ue', 'o.id_usuario_enlace', '=', 'ue.id');

    // Filtros dinámicos
    if (!empty($area)) {
        $query->where('o.id_cat_area', $area);
    }
    if (!empty($year)) {
        $query->where('o.id_cat_anio', $year);
    }

    $query->orderBy('o.id_tbl_circular', 'ASC');

    return $query->get();
}

}