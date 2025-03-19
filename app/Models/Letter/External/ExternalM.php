<?php

namespace App\Models\Letter\External;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ExternalM extends Model
{
    protected $table = 'correspondencia.tbl_circular_externa';
    public $timestamps = false;
    protected $primaryKey = 'id_tbl_circular_externa';
    protected $fillable = [
        'num_turno_sistema',
        'no_documento',
        'fecha_documento',
        'fecha_captura',
        'asunto',
        'observaciones',
        'id_cat_dependencia',
        'id_cat_dependencia_area',
        'fecha_usuario',
        'fecha_usuario_captura',
        'id_usuario_sistema',
        'id_usuario_captura',
    ];


    public function list($iterator, $searchValue)
    {
        // Preparar la consulta base
        $query = DB::table('correspondencia.tbl_circular_externa')
            ->select([
                'correspondencia.tbl_circular_externa.id_tbl_circular_externa AS id',
                DB::raw('EXTRACT(YEAR FROM correspondencia.tbl_circular_externa.fecha_documento::DATE) AS anio'),
                DB::raw('correspondencia.tbl_circular_externa.no_documento AS no_documento'),
                DB::raw('correspondencia.cat_dependencia.descripcion AS dependencia'),
                DB::raw("correspondencia.tbl_circular_externa.asunto AS asunto"),
            ])
            ->join('correspondencia.cat_dependencia', 'correspondencia.tbl_circular_externa.id_cat_dependencia', '=', 'correspondencia.cat_dependencia.id_cat_dependencia');

        // Si se proporciona un valor de búsqueda, agregar condiciones de búsqueda
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));  // Limpiar y convertir a mayúsculas

            // Condiciones de búsqueda centralizadas en una sola cláusula
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw("EXTRACT(YEAR FROM correspondencia.tbl_circular_externa.fecha_documento::DATE)::TEXT LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_circular_externa.no_documento)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.cat_dependencia.descripcion)) LIKE ?", ['%' . $searchValue . '%'])
                    ->orWhereRaw("UPPER(TRIM(correspondencia.tbl_circular_externa.asunto)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar la paginación (OFFSET y LIMIT)
        $query->orderBy('correspondencia.tbl_circular_externa.id_tbl_circular_externa', 'DESC')
            ->offset($iterator) // OFFSET
            ->limit(5); // LIMIT

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }

    // La función sirve para modificar
    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $query = DB::table('correspondencia.tbl_circular_externa')
            ->where('id_tbl_circular_externa', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }

    //La funcion valida que el no de documento sea unico
    public function unique($id, $value)
    {
        // Realizar la consulta a la base de datos, buscando si existe un registro con el valor de documento
        $query = DB::table('correspondencia.tbl_circular_externa')
            ->select('correspondencia.tbl_circular_externa.id_tbl_circular_externa')
            ->whereRaw('UPPER(TRIM(correspondencia.tbl_circular_externa.no_documento)) = UPPER(TRIM(?))', [trim($value)]);

        // Si el ID está presente, agregar la condición para excluir el ID
        if (isset($id)) {
            $query->whereRaw('correspondencia.tbl_circular_externa.id_tbl_circular_externa <> ?', [$id]);
        }

        // Ejecutar la consulta y verificar si hay resultados
        $result = $query->first();

        // Retornar true si se encuentra algún resultado, de lo contrario false
        return $result;//$result !== null;
    }

    // La función retorna encabezados de cloud de circulares externas
    public function getDataCloud($id)
    {
        $query = DB::table('correspondencia.tbl_circular_externa')
            ->select(
                'correspondencia.tbl_circular_externa.id_tbl_circular_externa AS id',
                'correspondencia.tbl_circular_externa.num_turno_sistema AS num_turno_sistema',
                'correspondencia.tbl_circular_externa.no_documento AS no_documento',
                DB::raw("TO_CHAR(correspondencia.tbl_circular_externa.fecha_captura, 'DD/MM/YYYY') AS fecha_captura")
            )
            ->where('correspondencia.tbl_circular_externa.id_tbl_circular_externa', '=', $id)
            ->first();  // Usamos 'first' para obtener un solo resultado

        return $query;
    }
}
