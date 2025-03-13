<?php

namespace App\Models\Letter\Office;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Scalar\Encapsed;
class OfficeM extends Model
{
    protected $table = 'correspondencia.tbl_oficio';
    public $timestamps = false;
    protected $primaryKey = 'id_tbl_oficio';
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
    ];

    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('correspondencia.tbl_oficio')
            ->where('id_tbl_oficio', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }

    //La funcion crea la tabla tanto para busquedas como para modelado
    public function list($iterator, $searchValue, $idUser)
    {
        // Preparar la consulta base
        $query = DB::table('correspondencia.tbl_oficio')
            ->select([
                'correspondencia.tbl_oficio.id_tbl_oficio AS id',
                DB::raw('correspondencia.tbl_oficio.num_turno_sistema AS num_turno_sistema'),
                DB::raw('
                CASE 
                    WHEN correspondencia.tbl_oficio.es_por_area THEN 
                        correspondencia.tbl_oficio.num_documento_area 
                    ELSE 
                        correspondencia.tbl_correspondencia.folio_gestion 
                END AS num_documento
            '),
                DB::raw('
                CASE 
                    WHEN NOT correspondencia.tbl_oficio.es_por_area THEN 
                        CASE 
                            WHEN correspondencia.tbl_correspondencia.id_cat_estatus = 4 THEN 1  -- Si el estatus es 4, retorna 1
                            ELSE 0  -- Si el estatus no es 4, retorna 0
                        END
                    ELSE 0  -- Si es por área, retorna 0
                END AS status
            '),
                DB::raw("UPPER(correspondencia.tbl_oficio.asunto) AS asunto"),
                DB::raw("UPPER(correspondencia.tbl_oficio.observaciones) AS observaciones"),
                DB::raw("TO_CHAR(correspondencia.tbl_oficio.fecha_inicio::date, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_oficio.fecha_fin::date, 'DD/MM/YYYY') AS fecha_fin"),
                DB::raw('correspondencia.cat_anio.descripcion AS anio'),
            ])
            ->leftJoin('correspondencia.tbl_correspondencia', 'correspondencia.tbl_oficio.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->join('correspondencia.cat_anio', 'correspondencia.tbl_oficio.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio');
        // Filtrar por usuario si se proporciona el id

        // Filtrar por área si se proporciona el id
        if (!empty($idUser)) {

            $query->where(function ($query) use ($idUser) {
                $query->whereIn('correspondencia.tbl_oficio.id_cat_area', $idUser);
            });
        }

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.tbl_oficio.num_turno_sistema)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_correspondencia.folio_gestion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_oficio.num_documento_area)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.cat_anio.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_correspondencia.observaciones)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_oficio.asunto)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('correspondencia.tbl_oficio.id_tbl_oficio', 'DESC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }

    //La funcion retorna  los datos de encabezado de la vista cloud
    public function dataCloud($id)
    {
        $query = DB::table('correspondencia.tbl_oficio')
            ->leftjoin('correspondencia.tbl_correspondencia', 'correspondencia.tbl_oficio.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->join('correspondencia.cat_anio', 'correspondencia.tbl_oficio.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->select(
                'correspondencia.tbl_oficio.num_turno_sistema AS num_turno_sistema',
                DB::raw('CASE WHEN correspondencia.tbl_oficio.es_por_area THEN 
                                    correspondencia.tbl_oficio.num_documento_area ELSE 
                                    correspondencia.tbl_correspondencia.folio_gestion 
                                END AS num_turno_sistema_correspondencia'),
                DB::raw("TO_CHAR(correspondencia.tbl_oficio.fecha_inicio::date, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_oficio.fecha_fin::date, 'DD/MM/YYYY') AS fecha_fin"),
                'correspondencia.cat_anio.descripcion AS anio'
            )
            ->where('correspondencia.tbl_oficio.id_tbl_oficio', $id)
            ->first(); // Usamos `first` para obtener solo un resultado

        return $query;
    }

    // La función retorna el valor mayor de los autoincrementables
    public function getMaxNuSistem()
    {
        // Realizar la consulta usando DB::table
        $maxNumTurno = DB::table('correspondencia.tbl_oficio')
            ->selectRaw("
                    MAX(CAST(REGEXP_REPLACE(num_turno_sistema, '^[^/]+/([0-9]{5})/.*$', '\\1') AS INTEGER)) AS max_num_turno
                ")
            ->whereRaw("num_turno_sistema ~ '/[0-9]{5}/'")
            ->value('max_num_turno'); // Obtener solo el valor de la columna max_num_turno

        return $maxNumTurno;
    }

    public function getOnly($idCatArea, $idAnio)
    {
        return DB::table('correspondencia.tbl_oficio')
            ->selectRaw('MAX(CAST(REGEXP_REPLACE(num_documento_area, \'^\\D*(\\d+).*\', \'\\1\') AS INT)) AS max_num')
            ->whereRaw("num_documento_area ~ '/\\d{4}$'")
            ->where('id_cat_area_documento', $idCatArea)
            ->where('id_cat_anio', $idAnio)
            ->first();  // Devuelve el primer (y único) resultado
    }

    // La funcion, retorna el area, uausrio y enlace dependiendo del id que se le pase
    public function getDataFormat($id)
    {
        $query = DB::table('correspondencia.tbl_oficio')
            ->select(
                'correspondencia.tbl_oficio.id_tbl_oficio',
                DB::raw('CASE 
                            WHEN correspondencia.tbl_oficio.es_por_area
                                THEN other_area.descripcion
                            ELSE 
                                is_area.descripcion
                        END AS area'),
                'usuario_area.name AS user_name',
                'usuario_enlace.name AS user_enlace'
            )
            ->join('administration.users AS usuario_area', 'correspondencia.tbl_oficio.id_usuario_area', '=', 'usuario_area.id')
            ->join('administration.users AS usuario_enlace', 'correspondencia.tbl_oficio.id_usuario_enlace', '=', 'usuario_enlace.id')
            ->leftJoin('correspondencia.tbl_correspondencia', 'correspondencia.tbl_oficio.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->leftJoin('correspondencia.cat_area AS is_area', 'correspondencia.tbl_oficio.id_cat_area', '=', 'is_area.id_cat_area')
            ->leftJoin('correspondencia.cat_area AS other_area', 'correspondencia.tbl_oficio.id_cat_area_documento', '=', 'other_area.id_cat_area')
            ->where('correspondencia.tbl_oficio.id_tbl_oficio', '=', $id)
            ->get();

        return $query->first();
    }

    //La función valida que el fol de gestión sea unico
    public function uniqueFolGestion($id, $folGestion)
    {
        // Start the query using the Query Builder
        $query = DB::table('correspondencia.tbl_oficio')
            ->join('correspondencia.tbl_correspondencia', 'correspondencia.tbl_oficio.id_tbl_correspondencia', '=', 'correspondencia.tbl_correspondencia.id_tbl_correspondencia')
            ->whereRaw('TRIM(UPPER(correspondencia.tbl_correspondencia.folio_gestion)) = ?', [trim(strtoupper($folGestion))]);

        // If the ID is set, add the condition to exclude the specific ID
        if (isset($id)) {
            $query->where('correspondencia.tbl_oficio.id_tbl_oficio', '<>', $id);
        }

        // Execute the query and check if any result is returned
        $result = $query->exists(); // Returns true if the query finds any results, false if not

        return $result;
    }
}
