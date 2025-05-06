<?php

namespace App\Models\Letter\Inside;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class InsideM extends Model
{
    protected $table = 'correspondencia.tbl_interno';
    public $timestamps = false;
    protected $primaryKey = 'id_tbl_interno';
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
        $query = DB::table('correspondencia.tbl_interno')
            ->where('id_tbl_interno', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }

    //La funcion crea la tabla tanto para busquedas como para modelado
    public function list($iterator, $searchValue, $idUser)
    {
        // Preparar la consulta base
        $query = DB::table('correspondencia.tbl_interno')
            ->select([
                'correspondencia.tbl_interno.id_tbl_interno AS id',
                DB::raw('correspondencia.tbl_interno.num_documento_area AS num_turno_sistema'),
                DB::raw("CASE 
                            WHEN TRIM(correspondencia.tbl_correspondencia.folio_gestion) <> '' 
                                AND correspondencia.tbl_correspondencia.folio_gestion IS NOT NULL
                            THEN correspondencia.tbl_correspondencia.folio_gestion
                            ELSE ''
                        END AS foio_gestion"),
                DB::raw("UPPER(correspondencia.tbl_interno.asunto) AS asunto"),
                DB::raw("TO_CHAR(correspondencia.tbl_interno.fecha_inicio::date, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_interno.fecha_fin::date, 'DD/MM/YYYY') AS fecha_fin"),
                DB::raw("correspondencia.cat_anio.descripcion AS anio"),
            ])
            ->leftJoin('correspondencia.tbl_correspondencia', 'correspondencia.tbl_interno.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->join('correspondencia.cat_anio', 'correspondencia.tbl_interno.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio');

        // Filtrar por usuario si se proporciona el id
        if (!empty($idUser)) {

            $query->where(function ($query) use ($idUser) {
                $query->whereIn('correspondencia.tbl_interno.id_cat_area', $idUser);
            });
        }
        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.tbl_interno.num_documento_area)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_correspondencia.folio_gestion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.cat_anio.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_interno.num_documento_area)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_interno.asunto)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('correspondencia.tbl_interno.id_tbl_interno', 'DESC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }

    //La funcion retorna  los datos de encabezado de la vista cloud
    public function dataCloud($id)
    {
        $query = DB::table('correspondencia.tbl_interno')
            ->leftjoin('correspondencia.tbl_correspondencia', 'correspondencia.tbl_interno.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->join('correspondencia.cat_anio', 'correspondencia.tbl_interno.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->select(
                'correspondencia.tbl_interno.num_turno_sistema AS num_turno_sistema',
                DB::raw('CASE WHEN correspondencia.tbl_interno.es_por_area THEN 
                                    correspondencia.tbl_interno.num_documento_area ELSE 
                                    correspondencia.tbl_correspondencia.num_turno_sistema 
                                END AS num_turno_sistema_correspondencia'),
                DB::raw("TO_CHAR(correspondencia.tbl_interno.fecha_inicio::date, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_interno.fecha_fin::date, 'DD/MM/YYYY') AS fecha_fin"),
                'correspondencia.cat_anio.descripcion AS anio'
            )
            ->where('correspondencia.tbl_interno.id_tbl_interno', $id)
            ->first(); // Usamos `first` para obtener solo un resultado

        return $query;
    }

    // La función retorna el valor mayor de los autoincrementables
    public function getMaxNuSistem()
    {
        // Realizar la consulta usando DB::table
        $maxNumTurno = DB::table('correspondencia.tbl_interno')
            ->selectRaw("
                        MAX(CAST(REGEXP_REPLACE(num_turno_sistema, '^[^/]+/([0-9]{5})/.*$', '\\1') AS INTEGER)) AS max_num_turno
                    ")
            ->whereRaw("num_turno_sistema ~ '/[0-9]{5}/'")
            ->value('max_num_turno'); // Obtener solo el valor de la columna max_num_turno

        return $maxNumTurno;
    }

    public function getOnly($idCatArea, $idAnio)
    {
        return DB::table('correspondencia.tbl_interno')
            ->selectRaw('MAX(CAST(REGEXP_REPLACE(num_documento_area, \'^\\D*(\\d+).*\', \'\\1\') AS INT)) AS max_num')
            ->whereRaw("num_documento_area ~ '/\\d{4}$'")
            ->where('id_cat_area_documento', $idCatArea)
            ->where('id_cat_anio', $idAnio)
            ->first();  // Devuelve el primer (y único) resultado
    }

    // La función retorna el reporte generado para la papeleta de reporte
    public function getReport($id)
    {
        $result = DB::table('correspondencia.tbl_interno')
            ->select(
                'correspondencia.tbl_interno.id_tbl_interno as id',
                DB::raw("TO_CHAR(correspondencia.tbl_interno.fecha_captura, 'DD/MM/YYYY') as fecha_captura"),
                DB::raw("TO_CHAR(correspondencia.tbl_interno.fecha_inicio, 'DD/MM/YYYY') as fecha_emision"),
                DB::raw("TO_CHAR(correspondencia.tbl_interno.fecha_fin, 'DD/MM/YYYY') as fecha_aplicacion"),
                DB::raw("UPPER(correspondencia.tbl_interno.num_turno_sistema) as num_turno_sistema"),
                DB::raw("UPPER(correspondencia.tbl_interno.num_documento_area) as num_documento_area"),
                'correspondencia.cat_anio.descripcion as anio',
                DB::raw("UPPER(correspondencia.cat_area.descripcion) as area"),
                DB::raw("UPPER(correspondencia.tbl_interno.asunto) as asunto"),
                DB::raw("UPPER(correspondencia.tbl_interno.observaciones) as observaciones"),
                DB::raw("UPPER(correspondencia.tbl_interno.destinatario) as destinatario")
            )
            ->join('correspondencia.cat_anio', 'correspondencia.tbl_interno.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->join('correspondencia.cat_area', 'correspondencia.tbl_interno.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->where('correspondencia.tbl_interno.id_tbl_interno', '=', $id)
            ->first(); // Usamos `first` porque esperamos un solo resultado

        return $result;
    }

    //La función valida que el fol de gestión sea unico
    public function uniqueFolGestion($id, $folGestion)
    {
        // Start the query using the Query Builder
        $query = DB::table('correspondencia.tbl_interno')
            ->join('correspondencia.tbl_correspondencia', 'correspondencia.tbl_interno.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->whereRaw('TRIM(UPPER(correspondencia.tbl_correspondencia.folio_gestion)) = ?', [trim(strtoupper($folGestion))]);

        // If the ID is set, add the condition to exclude the specific ID
        if (isset($id)) {
            $query->where('correspondencia.tbl_interno.id_tbl_interno', '<>', $id);
        }

        // Execute the query and check if any result is returned
        $result = $query->exists(); // Returns true if the query finds any results, false if not

        return $result;
    }
}
