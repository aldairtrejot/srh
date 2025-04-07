<?php

namespace App\Http\Controllers\Letter\Dependencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;
use App\Models\Letter\Dependencia\ReldependenciaM;
use App\Models\Letter\Dependencia\DependenciaM;
use App\Models\Letter\Dependencia\DependenciareaM;

class ReldependenciaC extends Controller
{
    public function searchTable(Request $request)
    {
        $iterator = $request->input('iterator', 0);
        $searchValue = $request->input('searchValue', '');

        $model = new ReldependenciaM();
        $data = $model->list($iterator, $searchValue);

        return response()->json([
            'value' => $data
        ]);
    }

    // Si también estás usando esta vista como index
    public function __invoke()
    {
        return view('administration.reldependenciaC.list');
    }

    public function create()
    {
        $item = new ReldependenciaM();
        $dependenciaM = new DependenciaM();
        $dependenciareaM = new DependenciareaM();
       
        $selectDependencia = $dependenciaM->listdependencia(); //Catalogo de beneficio
        $selectDependenciaEdit = []; //catalogo de beneficio null

        $selectDependenciarea = $dependenciareaM->listdependenciarea(); //Catalogo de beneficio
        $selectDependenciareaEdit = []; //catalogo de beneficio null


        return view('administration.reldependenciaC.form', compact('item', 'selectDependencia','selectDependenciaEdit','selectDependenciarea','selectDependenciareaEdit'));
    }
    public function save(Request $request)
{
    $messagesC = new MessagesC();

    // Validar campos requeridos
    $request->validate([
        'id_cat_dependencia' => 'required|integer',
        'id_cat_dependencia_area' => 'required|integer',
    ]);

    $model = new ReldependenciaM();

    // Armar datos
    $data = [
        'id_cat_dependencia' => $request->id_cat_dependencia,
        'id_cat_dependencia_area' => $request->id_cat_dependencia_area,
    ];

    // Nuevo registro o actualización
    if (!$request->id_rel_dependencia_area) {
        $model::create($data);
    } else {
        $model::where('id_rel_dependencia_area', $request->id_rel_dependencia_area)
            ->update($data);
    }

    return $messagesC->messageSuccessRedirect('reldependenciarea.list', 'Registro guardado exitosamente.');
}
public function edit(string $id)
{
    $reldependenciaM = new ReldependenciaM();
        $dependenciaM = new DependenciaM();
        $dependenciareaM = new DependenciareaM();

    $item = $reldependenciaM->edit($id); // Obtener el curso que se está editando

    $selectDependencia = $dependenciaM->listdependencia(); //Catalogo de beneficio
    $selectDependenciaEdit = isset($item->id_cat_dependencia) ? $dependenciaM->editreldepencia($item->id_cat_dependencia) : [];

    $selectDependenciarea = $dependenciareaM->listdependenciarea(); //Catalogo de beneficio
    $selectDependenciareaEdit = isset($item->id_cat_dependencia_area) ? $dependenciareaM->editreldepencia($item->id_cat_dependencia_area) : [];



    // Devolver la vista con el costo total calculado
    return view('administration.reldependenciaC.form', compact('item', 'selectDependencia','selectDependenciaEdit','selectDependenciarea','selectDependenciareaEdit'));
}


}