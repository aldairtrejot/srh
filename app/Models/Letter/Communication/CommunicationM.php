<?php

namespace App\Models\Letter\Communication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CommunicationM extends Model
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

    // La función lista la tabla para el inciio
    public function list($iterator, $searchValue)
    {
        // Preparar la consulta base
        $query = DB::table('correspondencia.tbl_correspondencia_interno')
            ->select([
                'correspondencia.tbl_correspondencia_interno.id_tbl_correspondencia_interno AS id',
                DB::raw("TO_CHAR(correspondencia.tbl_correspondencia_interno.fecha_asignacion, 'DD/MM/YYYY') AS fecha"),
                DB::raw('correspondencia.tbl_correspondencia_interno.consecutivo AS num_oficio'),
                DB::raw('correspondencia.cat_area_interno.descripcion AS area'),
                DB::raw('correspondencia.cat_tema.descripcion AS tema'),
                DB::raw('correspondencia.tbl_correspondencia_interno.uuid_oficio AS uuid_oficio'),
                DB::raw('correspondencia.tbl_correspondencia_interno.uuid_acuse AS uuid_acuse')
            ])
            ->join('correspondencia.cat_area_interno', 'correspondencia.tbl_correspondencia_interno.id_cat_area_interno', '=', 'correspondencia.cat_area_interno.id_cat_area_interno')
            ->join('correspondencia.cat_tema', 'correspondencia.tbl_correspondencia_interno.id_cat_tema', '=', 'correspondencia.cat_tema.id_cat_tema');

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("UPPER(TRIM(TO_CHAR(correspondencia.tbl_correspondencia_interno.fecha_asignacion, 'DD/MM/YYYY'))) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_correspondencia_interno.consecutivo)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.cat_area_interno.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.cat_tema.descripcion)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('correspondencia.tbl_correspondencia_interno.id_tbl_correspondencia_interno', 'DESC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }

    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('correspondencia.tbl_correspondencia_interno')
            ->where('id_tbl_correspondencia_interno', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }

    // La función retorna el id de la tabla por el año de la tabla interna
    public function getIdAnio($id)
    {
        $query = DB::table('correspondencia.cat_anio')
            ->join(
                'correspondencia.tbl_correspondencia_interno',
                DB::raw('CAST(correspondencia.cat_anio.descripcion AS INTEGER)'),
                '=',
                DB::raw('EXTRACT(YEAR FROM correspondencia.tbl_correspondencia_interno.fecha_asignacion)')
            )
            ->where('correspondencia.tbl_correspondencia_interno.id_tbl_correspondencia_interno', $id)
            ->select('correspondencia.cat_anio.id_cat_anio as id')
            ->first();

        return $query->id;
    }
}
