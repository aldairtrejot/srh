<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionAreaM extends Model
{

    //La funcion retorna el consecutivo de las tablaspublic function noDocumento($idAnio, $idTable)
    public function noDocumento($idAnio, $idTable)
    {
        $query = DB::table('correspondencia.rel_anio_area')
            ->join('correspondencia.cat_area', 'correspondencia.rel_anio_area.id_cat_area', '=', 'correspondencia.cat_area.id_cat_area')
            ->join('correspondencia.cat_anio', 'correspondencia.rel_anio_area.id_cat_anio', '=', 'correspondencia.cat_anio.id_cat_anio')
            ->select(
                DB::raw("
                    UPPER(correspondencia.cat_area.clave) || '/' || 
                    TO_CHAR(correspondencia.rel_anio_area.consecutivo + 1, 'FM0000') || '/' ||
                    correspondencia.cat_anio.descripcion AS documento_id
                ")
            )
            ->where('correspondencia.rel_anio_area.id_cat_area', $idTable)
            ->where('correspondencia.rel_anio_area.id_cat_anio', $idAnio)
            ->first(); // Obtener el primer resultado

        // Verifica si el resultado tiene la propiedad 'documento_id'
        return $query ? $query->documento_id : null;
    }

    //LA funcion actualiza el consecutivo de area y año
    public function iteratorConsecutivo($idYear, $idDoc)
    {
        // Usando Query Builder para hacer el UPDATE
        DB::table('correspondencia.rel_anio_area')
            ->where('id_cat_anio', $idYear)
            ->where('id_cat_area', $idDoc)
            ->increment('consecutivo', 1); // Aumenta el campo 'consecutivo' en 1
    }

    public function list()
    {
        $query = DB::table('correspondencia.cat_area')
            ->select([
                'correspondencia.cat_area.id_cat_area AS id',
                DB::raw('UPPER(correspondencia.cat_area.descripcion) AS descripcion')
            ])
            ->orderBy('correspondencia.cat_area.descripcion', 'ASC');

        // Ejecutar la consulta y obtener los resultados
        $results = $query->get();

        // Retornar los resultados (puedes pasarlo a tu vista o devolverlo como respuesta)
        return $results;
    }

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_area')
            ->select([
                'correspondencia.cat_area.id_cat_area AS id',
                DB::raw('UPPER(correspondencia.cat_area.descripcion) AS descripcion')
            ])
            ->where('correspondencia.cat_area.id_cat_area', '=', $id)
            ->orderBy('correspondencia.cat_area.descripcion', 'ASC');

        // Usar first() para obtener un único resultado
        $result = $query->first();
        return $result;
    }
}
