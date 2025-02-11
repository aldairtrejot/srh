<?php

namespace App\Models\Letter\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class RequestM extends Model
{
    protected $table = 'correspondencia.tbl_correspondencia_interno';
    public $timestamps = false;
    protected $primaryKey = 'id_tbl_correspondencia_interno';
    protected $fillable = [
        'consecutivo',
        'fecha_asignacion',
        'cargo_destinatario',
        'asunto',
        'observaciones',
        'uuid_oficio',
        'uuid_acuse',
        'nombre_oficio',
        'nombre_acuse',
        'id_usuario',
        'id_cat_area_interno',
        'id_cat_solicitante',
        'id_cat_destinatario',
        'id_cat_tema',
        'id_cat_entidad',
        'estatus',
        'id_usuario_captura',
        'fecha_usuario_captura',
        'id_usuario_sistema',
        'fecha_usuario',
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
