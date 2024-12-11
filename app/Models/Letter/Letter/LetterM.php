<?php

namespace App\Models\Letter\Letter;

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
        'num_copias',
        'lugar',
        'asunto',
        'observaciones',
        'rfc_remitente_aux',
        'rfc_remitente_bool',
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
    ];

    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->where('id_tbl_correspondencia', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }
    public function list($iterator, $searchValue, $idArea, $idEnlace)
    {
        // Preparar la consulta base
        $query = DB::table('correspondencia.tbl_correspondencia')
            ->select([
                'correspondencia.tbl_correspondencia.id_tbl_correspondencia AS id',
                DB::raw('UPPER(correspondencia.tbl_correspondencia.num_turno_sistema) AS num_turno_sistema'),
                DB::raw('UPPER(correspondencia.tbl_correspondencia.num_documento) AS num_documento'),
                DB::raw('UPPER(correspondencia.cat_estatus.descripcion) AS estatus'),
                DB::raw('UPPER(correspondencia.cat_tramite.descripcion) AS tramite'),
                DB::raw('UPPER(correspondencia.cat_area.descripcion) AS area'),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_inicio::date, 'DD/MM/YYYY') AS fecha_inicio"),
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin::date, 'DD/MM/YYYY') AS fecha_fin")
            ])
            ->join('correspondencia.cat_estatus', 'correspondencia.tbl_correspondencia.id_cat_estatus', '=', 'correspondencia.cat_estatus.id_cat_estatus')
            ->join('correspondencia.cat_area', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->join('correspondencia.cat_tramite', 'correspondencia.tbl_correspondencia.id_cat_tramite', '=', 'correspondencia.cat_tramite.id_cat_tramite');

        // Filtrar por área si se proporciona el id
        if (!empty($idArea)) {
            $query->where('correspondencia.tbl_correspondencia.id_cat_area', $idArea);
        }

        // Filtrar por enlace si se proporciona el id
        if (!empty($idEnlace)) {
            $query->where('correspondencia.tbl_correspondencia.id_usuario_enlace', $idEnlace);
        }

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.tbl_correspondencia.num_turno_sistema)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_correspondencia.num_documento)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.cat_estatus.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.cat_tramite.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.cat_area.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(TO_CHAR(correspondencia.tbl_correspondencia.fecha_inicio, 'DD/MM/YYYY'))) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(TO_CHAR(correspondencia.tbl_correspondencia.fecha_fin, 'DD/MM/YYYY'))) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('correspondencia.tbl_correspondencia.id_tbl_correspondencia', 'DESC')
            ->offset($iterator) // OFFSET
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
            ->whereRaw('TRIM(correspondencia.tbl_correspondencia.num_documento) = ?', [trim($value)]);

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
                'correspondencia.tbl_correspondencia.num_flojas AS num_flojas',
                'correspondencia.tbl_correspondencia.num_tomos AS num_tomos',
                'correspondencia.tbl_correspondencia.num_copias AS num_copias',
                'correspondencia.tbl_correspondencia.lugar AS lugar',
                'correspondencia.tbl_correspondencia.asunto AS asunto',
                'correspondencia.tbl_correspondencia.observaciones AS observaciones',
                DB::raw("COALESCE(correspondencia.cat_remitente.nombre, '') || ' ' || 
                            COALESCE(correspondencia.cat_remitente.primer_apellido, '') || ' ' ||
                            COALESCE(correspondencia.cat_remitente.segundo_apellido, '') || ' ' ||
                            ' - ' || COALESCE(correspondencia.cat_remitente.rfc, '') AS remitente"),
                'correspondencia.cat_anio.descripcion AS anio',
                'correspondencia.cat_tramite.descripcion AS tramite',
                'correspondencia.cat_area.descripcion AS area',
                'correspondencia.cat_clave.descripcion AS codigo',
                DB::raw("COALESCE(correspondencia.cat_clave.descripcion, '') || '/' ||
                            COALESCE(correspondencia.cat_clave.redaccion, '') AS clave"),
                'correspondencia.cat_unidad.descripcion AS unidad',
                'correspondencia.cat_coordinacion.descripcion AS coordinacion'
            )
            ->leftJoin('correspondencia.cat_area', 'correspondencia.tbl_correspondencia.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->leftJoin('correspondencia.cat_remitente', 'correspondencia.tbl_correspondencia.id_cat_remitente', '=', 'correspondencia.cat_remitente.id_cat_remitente')
            ->leftJoin('correspondencia.cat_anio', 'correspondencia.tbl_correspondencia.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->leftJoin('correspondencia.cat_tramite', 'correspondencia.tbl_correspondencia.id_cat_tramite', '=', 'correspondencia.cat_tramite.id_cat_tramite')
            ->leftJoin('correspondencia.cat_clave', 'correspondencia.tbl_correspondencia.id_cat_clave', '=', 'correspondencia.cat_clave.id_cat_clave')
            ->leftJoin('correspondencia.cat_unidad', 'correspondencia.tbl_correspondencia.id_cat_unidad', '=', 'correspondencia.cat_unidad.id_cat_unidad')
            ->leftJoin('correspondencia.cat_coordinacion', 'correspondencia.tbl_correspondencia.id_cat_coordinacion', '=', 'correspondencia.cat_coordinacion.id_cat_coordinacion')
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
            ->value('num_turno_sistema');

        // Si no se encuentra información, retornamos null
        return $turno ?: null;
    }

    public function validateNoTurno($noTurno)
    {
        $turno = DB::table('correspondencia.tbl_correspondencia')
            ->where('num_turno_sistema', $noTurno)
            ->value('correspondencia.tbl_correspondencia.id_tbl_correspondencia');

        // Si no se encuentra información, retornamos null
        return $turno ?: null;
    }
}
