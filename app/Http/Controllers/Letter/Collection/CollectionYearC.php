<?php

namespace App\Http\Controllers\Letter\Collection;
use App\Models\Letter\Collection\CollectionDateM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CollectionYearC extends Controller
{
    //LA funcion obtiene el año apartir del id generado
    public function getYear(Request $request)
    {
        $collectionDateM = new CollectionDateM();

        $id_cat_anio = $request->id_cat_anio; //Se obtienen los valores

        $nameYear = $collectionDateM->getYearName($id_cat_anio);

        return response()->json([
            'nameYear' => $nameYear,
            'status' => true,
        ]);
    }
}
