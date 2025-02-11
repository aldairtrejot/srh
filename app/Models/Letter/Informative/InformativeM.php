<?php

namespace App\Models\Letter\Informative;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class InformativeM extends Model
{
    protected $table = 'correspondencia.tbl_notas_interno';
    public $timestamps = false;
    protected $primaryKey = 'id_tbl_notas_interno';
    protected $fillable = [
        'consecutivo',
        'fecha_asignacion',
        'fecha_documento',
        'asunto',
        'uuid_pdf',
        'nombre_pdf',
        'id_cat_solicitante_2',
        'id_cat_solicitante',
        'id_cat_destinatario',
        'fecha_usuario',
        'id_usuario_sistema',
        'estatus',
        'id_usuario_captura',
        'fecha_usuario_captura',
    ];

    // Lsita la función para que se muestre la tabla
    public function list($iterator, $searchValue)
    {
        // Preparar la consulta base
        $query = DB::table('correspondencia.tbl_notas_interno')
            ->select([
                'correspondencia.tbl_notas_interno.id_tbl_notas_interno AS id',
                DB::raw("correspondencia.tbl_notas_interno.consecutivo AS consecutivo"),
                DB::raw("TO_CHAR(correspondencia.tbl_notas_interno.fecha_asignacion, 'DD/MM/YYYY') AS fecha_asignacion"),
                DB::raw("TO_CHAR(correspondencia.tbl_notas_interno.fecha_documento, 'DD/MM/YYYY') AS fecha_documento"),
                DB::raw("LEFT(correspondencia.tbl_notas_interno.asunto, 90) || ' ...' AS asunto"),
                DB::raw("correspondencia.tbl_notas_interno.uuid_pdf AS uuid"),
            ]);
        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.tbl_notas_interno.consecutivo)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("TO_CHAR(correspondencia.tbl_notas_interno.fecha_asignacion, 'DD/MM/YYYY')::TEXT LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("TO_CHAR(correspondencia.tbl_notas_interno.fecha_documento, 'DD/MM/YYYY')::TEXT LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_notas_interno.asunto)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('correspondencia.tbl_notas_interno.id_tbl_notas_interno', 'DESC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }

    // La función modifica el elemento por su id 
    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('correspondencia.tbl_notas_interno')
            ->where('id_tbl_notas_interno', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }

    public function getIdAnio($id)
    {
        $query = DB::table('correspondencia.cat_anio')
            ->join(
                'correspondencia.tbl_notas_interno',
                DB::raw('CAST(correspondencia.cat_anio.descripcion AS INTEGER)'),
                '=',
                DB::raw('EXTRACT(YEAR FROM correspondencia.tbl_notas_interno.fecha_asignacion)')
            )
            ->where('correspondencia.tbl_notas_interno.id_tbl_notas_interno', $id)
            ->select('correspondencia.cat_anio.id_cat_anio as id')
            ->first();

        return $query->id;
    }
}

/*
<?php

namespace App\Models\Letter\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class RequestM extends Model
{
    protected $table = 'correspondencia.tbl_requerimiento_interno';
    public $timestamps = false;
    protected $primaryKey = 'id_tbl_requerimiento_interno';
    protected $fillable = [
        'consecutivo',
        'fecha_asignacion',
        'fecha_documento',
        'fecha_termino',
        'observaciones',
        'uuid_pdf',
        'nombre_pdf',
        'asunto',
        'id_cat_solicitante',
        'fecha_usuario',
        'id_usuario_sistema',
        'estatus',
        'id_usuario_captura',
        'fecha_usuario_captura',
    ];

    // Lsita la función para que se muestre la tabla
    public function list($iterator, $searchValue)
    {
        // Preparar la consulta base
        $query = DB::table('correspondencia.tbl_requerimiento_interno')
            ->select([
                'correspondencia.tbl_requerimiento_interno.id_tbl_requerimiento_interno AS id',
                DB::raw("correspondencia.tbl_requerimiento_interno.consecutivo AS consecutivo"),
                DB::raw("TO_CHAR(correspondencia.tbl_requerimiento_interno.fecha_asignacion, 'DD/MM/YYYY') AS fecha_asignacion"),
                DB::raw("LEFT(correspondencia.tbl_requerimiento_interno.asunto, 90) || ' ...' AS asunto"),
                DB::raw("TO_CHAR(correspondencia.tbl_requerimiento_interno.fecha_documento, 'DD/MM/YYYY') AS fecha_documento"),
                DB::raw("TO_CHAR(correspondencia.tbl_requerimiento_interno.fecha_termino, 'DD/MM/YYYY') AS fecha_termino"),
                DB::raw('correspondencia.tbl_requerimiento_interno.uuid_pdf AS uuid')
            ]);
        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(correspondencia.tbl_requerimiento_interno.consecutivo)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("TO_CHAR(correspondencia.tbl_requerimiento_interno.fecha_asignacion, 'DD/MM/YYYY')::TEXT LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("TO_CHAR(correspondencia.tbl_requerimiento_interno.fecha_documento, 'DD/MM/YYYY')::TEXT LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_requerimiento_interno.asunto)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('correspondencia.tbl_requerimiento_interno.id_tbl_requerimiento_interno', 'DESC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }




}

*/