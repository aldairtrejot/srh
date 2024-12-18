<?php

namespace App\Http\Controllers\Letter\Collection;

use App\Models\Letter\Collection\CollectionAreaM;
use App\Models\Letter\Collection\CollectionTramiteM;
use App\Http\Controllers\Controller;
use App\Models\Letter\Collection\CollectionRelEnlaceM;
use App\Models\Letter\Collection\CollectionRelUsuarioM;
use App\Models\Letter\Letter\LetterM;
use Illuminate\Http\Request;

class CollectionAreaC extends Controller
{

    // Function que valida que el no de correspondencia exista
    public function getletter(Request $request)
    {
        $letterM = new LetterM();
        $result = $letterM->validateNoTurno($request->value);
        $status = $result ? false : true;

        return response()->json([
            'status' => $status,
        ]);
    }

    public function areaAutoincrement(Request $request)
    {
        $collectionAreaM = new CollectionAreaM();
        $id_cat_anio = $request->id_cat_anio;
        $id = $request->id;
        $consecutivo = $collectionAreaM->noDocumento($id_cat_anio, $id);

        return response()->json([
            'consecutivo' => $consecutivo,
            'status' => true,
        ]);
    }


    //Lafuncion obtiene los caralogos dependiendo de el area que el usuario seleccione
    public function collection(Request $request)
    {
        $collectionRelEnlaceM = new CollectionRelEnlaceM();
        $collectionRelUsuarioM = new CollectionRelUsuarioM();
        $collectionTramiteM = new CollectionTramiteM();

        $idArea = $request->id; //Obtenemos el id que el usuario selecciono en el combo de area
        $selectEnlace = $collectionRelEnlaceM->idUsuarioByArea($idArea); //Obtenemos el catalogo de enlaces
        $selectUsuario = $collectionRelUsuarioM->idUsuarioByArea($idArea);
        $selectTramite = $collectionTramiteM->list($idArea);

        return response()->json([
            'selectEnlace' => $selectEnlace,
            'selectUsuario' => $selectUsuario,
            'selectTramite' => $selectTramite,
            'status' => true,
        ]);
    }
}
