<?php

namespace App\Http\Controllers\Letter\Collection;

use App\Models\Administration\UserM;
use App\Models\Letter\Collection\CollectionAreaM;
use App\Models\Letter\Collection\CollectionCoordinacionM;
use App\Models\Letter\Collection\CollectionTramiteM;
use App\Http\Controllers\Controller;
use App\Models\Letter\Collection\CollectionRelEnlaceM;
use App\Models\Letter\Collection\CollectionRelUsuarioM;
use App\Models\Letter\Collection\CollectionUnidadM;
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
        $consecutivo = $collectionAreaM->noDocumentoAux($id_cat_anio, $id, $request->name);

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
        $collectionAreaM = new CollectionAreaM();
        $collectionUnidadM = new CollectionUnidadM();
        $collectionCoordinacionM = new CollectionCoordinacionM();

        $idArea = $request->id; //Obtenemos el id que el usuario selecciono en el combo de area
        $selectEnlace = $collectionRelEnlaceM->idUsuarioByArea($idArea); //Obtenemos el catalogo de enlaces
        $selectUsuario = $collectionRelUsuarioM->idUsuarioByArea($idArea);
        $selectTramite = $collectionTramiteM->list($idArea);
        $selectUnidad = $collectionUnidadM->listOfUnidad($idArea);
        $selectCoor = $collectionCoordinacionM->listOfArea($idArea);
        $clave = $collectionAreaM->getClave($idArea);


        return response()->json([
            'clave' => $clave,
            'selectEnlace' => $selectEnlace,
            'selectUsuario' => $selectUsuario,
            'selectTramite' => $selectTramite,
            'selectUnidad' => $selectUnidad,
            'selectCoor' => $selectCoor,
            'status' => true,
        ]);
    }

    // La función obtiene el nombre de usuario, area y enlace
    public function getUserArea(Request $request)
    {
        $collectionAreaM = new CollectionAreaM();
        $userM = new UserM();

        $nameArea = $collectionAreaM->getName($request->id_area);
        $nameUser = $userM->getName($request->id_usuario);
        $nameEnlace = $userM->getName($request->id_enlace);

        return response()->json([
            'nameArea' => $nameArea,
            'nameUser' => $nameUser,
            'nameEnlace' => $nameEnlace,
            'status' => true,
        ]);
    }
}
