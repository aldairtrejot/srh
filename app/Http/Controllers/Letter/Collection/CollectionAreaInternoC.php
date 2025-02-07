<?php

namespace App\Http\Controllers\Letter\Collection;

use App\Http\Controllers\Controller;
use App\Models\Letter\Collection\CollectionAreaInternoM;
use Illuminate\Http\Request;


class CollectionAreaInternoC extends Controller
{
    // La función lista la clave del area, para que se muestre en la pantalla principal de catalogo de area interno
    public function list(Request $request)
    {
        $collectionAreaInternoM = new CollectionAreaInternoM();
        $result = $collectionAreaInternoM->getClave($request->id);

        return response()->json([
            'result' => $result,
            'status' => true,
        ]);
    }
}
