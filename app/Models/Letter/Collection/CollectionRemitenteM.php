<?php

namespace App\Models\Letter\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CollectionRemitenteM extends Model
{
    protected $table = 'correspondencia.cat_remitente';
    public $timestamps = false;
    protected $primaryKey = 'id_cat_remitente';
    protected $fillable = [
        'nombre',
        'primer_apellido',
        'segundo_apellido',
        'rfc',
        'estatus',
        'fecha_usuario',
        'id_usuario_sistema',
    ];

    // La funcion retorna el id de remitente, buscandolo por rfc
    public function getRfc($rfc, $primer_apellido, $segundo_apellido)
    {
        // Usamos Query Builder para realizar la consulta
        $remitente = DB::table('correspondencia.cat_remitente')
            ->where('nombre', $rfc)
            ->where('primer_apellido', $primer_apellido)
            ->where('segundo_apellido', $segundo_apellido)
            ->first();

        return $remitente->id_cat_remitente;
    }

    public function uniqueRemitente($value, $attribute)
    {
        $query = DB::table('correspondencia.cat_remitente')
            ->select('correspondencia.cat_remitente.id_cat_remitente')
            ->whereRaw('UPPER(TRIM(correspondencia.cat_remitente. ' . $attribute . ')) = UPPER(TRIM(?))', [trim($value)]);

        // Ejecutar la consulta y verificar si hay resultados
        $result = $query->first();

        // Retornar true si se encuentra algún resultado, de lo contrario false
        return $result;//$result !== null;
    }


    // La función valida que el remitente por nombre, primer apellido y segundo apellido no exista en el catalogo
    public function uniqueRemitenteName($name, $lastName, $lastNameSecon)
    {
        // Realizar la consulta correctamente
        $query = DB::table('correspondencia.cat_remitente')
            ->select('correspondencia.cat_remitente.id_cat_remitente')
            ->whereRaw('UPPER(TRIM(correspondencia.cat_remitente.nombre)) = UPPER(TRIM(?))', [trim($name)])
            ->whereRaw('UPPER(TRIM(correspondencia.cat_remitente.primer_apellido)) = UPPER(TRIM(?))', [trim($lastName)])
            ->whereRaw('UPPER(TRIM(correspondencia.cat_remitente.segundo_apellido)) = UPPER(TRIM(?))', [trim($lastNameSecon)]);

        // Ejecutar la consulta y verificar si hay resultados
        $result = $query->first();

        // Retornar true si se encuentra algún resultado, de lo contrario false
        return $result;
    }
    //LA funcion obtienen el catalogo de remitente
    public function list()
    {
        // Usamos el Query Builder para realizar la consulta.
        $query = DB::table('correspondencia.cat_remitente')
            ->select(
                'correspondencia.cat_remitente.id_cat_remitente AS id',
                DB::raw("UPPER(correspondencia.cat_remitente.nombre) || ' ' || 
                                UPPER(correspondencia.cat_remitente.primer_apellido) || ' ' || 
                                UPPER(correspondencia.cat_remitente.segundo_apellido) ||
                                CASE 
                                    WHEN correspondencia.cat_remitente.rfc IS NOT NULL THEN 
                                        ' - ' || UPPER(correspondencia.cat_remitente.rfc)
                                    ELSE ''
                                END AS descripcion")
            )
            ->where('correspondencia.cat_remitente.estatus', true)
            ->orderBy('correspondencia.cat_remitente.nombre', 'ASC')
            ->get(); // Esto ejecuta la consulta y obtiene todos los resultados.

        return $query;
    }

    public function edit($id)
    {
        $query = DB::table('correspondencia.cat_remitente')
            ->select([
                'correspondencia.cat_remitente.id_cat_remitente AS id',
                DB::raw("UPPER(correspondencia.cat_remitente.nombre) || ' ' || 
                UPPER(correspondencia.cat_remitente.primer_apellido) || ' ' || 
                UPPER(correspondencia.cat_remitente.segundo_apellido) || ' - ' || 
                UPPER(correspondencia.cat_remitente.rfc) AS descripcion")
            ])
            ->where('correspondencia.cat_remitente.id_cat_remitente', '=', $id);

        $result = $query->first();
        return $result;
    }
}
