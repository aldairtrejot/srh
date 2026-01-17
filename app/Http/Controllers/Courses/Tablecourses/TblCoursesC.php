<?php

namespace App\Http\Controllers\Courses\Tablecourses;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\TblcoursesM;
use App\Models\Courses\Courses\CoursesM;
use App\Models\Courses\Courses\CoursestipocurM;
use App\Models\Courses\Courses\CoursestipoacM;
use App\Models\Courses\Courses\CoursescoordinacionM;
use App\Models\Courses\Courses\CoursesnombreaccM;
use App\Models\Courses\Courses\CoursesprogramaM;
use App\Models\Courses\Courses\CoursesestatutoM;
use App\Models\Courses\Courses\CoursesorganizacionM;
use App\Models\Courses\Courses\CoursesmodalidadM;
use App\Models\Courses\Courses\CoursescategoriaM;
use Illuminate\Http\Request;

class TblCoursesC extends Controller
{
    public function __invoke()
    {
        return view('courses/tablecourses/list');
    }

    public function searchTable(Request $request)
    {
        try {
            $iterator = $request->input('iterator', 1); // Página actual
            $searchValue = $request->input('searchValue', ''); // Valor de búsqueda

            // Validar entrada
            $request->validate([
                'iterator' => 'required|integer|min:1',
                'searchValue' => 'nullable|string|max:255',
            ]);

            // Obtener resultados
            $courses = (new TblcoursesM())->list($iterator, $searchValue);

            return response()->json([
                'success' => true,
                'message' => 'Resultados obtenidos correctamente',
                'data' => $courses->items(),
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function create()
    {
        $item = new TblcoursesM();
        $item->nombre_curso = '';  
        $item->costo = ''; 
        $item->iva = ''; 
        $item->fecha_inicio = ''; 
        $item->fecha_fin = ''; 
        $item->horas = '';// Valor por defecto
        $coursesM = new CoursesM();
        $coursestipocurM = new CoursestipocurM();
        $coursestipoacM = new CoursestipoacM();
        $coursescoordinacionM = new CoursescoordinacionM();
        $coursesnombreaccM = new CoursesnombreaccM();
        $coursesprogramaM = new CoursesprogramaM();
        $coursesestatutoM = new CoursesestatutoM();
        $coursesorganizacionM = new CoursesorganizacionM();
        $coursesmodalidadM = new CoursesmodalidadM();
        $coursescategoriaM = new CoursescategoriaM();

        $selectBeneficio = $coursesM->listbeneficio(); //Catalogo de beneficio
        $selectBeneficioEdit = []; //catalogo de beneficio null

        $selectTipocurso = $coursestipocurM->listtipocurso(); //Catalogo de Tipo Curso
        $selectTipoCursoEdit = []; //catalogo de Tipo Curso

        $selectTipoaccion = $coursestipoacM->listtipoaccion(); //Catalogo de Tipo Accion
        $selectTipoAccionEdit = []; //catalogo de Tipo Accion

        $selectCoordinacion = $coursescoordinacionM->listcoordinacion(); //Catalogo Coordinacion
        $selectCoordinacionEdit = []; //catalogo de Coordinacion

        $selectNomaccion = $coursesnombreaccM->listnomaccion(); //Catalogo Nombre Accion
        $selectNomaccionEdit = []; //catalogo Nombre Accion

        $selectPrograma = $coursesprogramaM->listprograma(); //Catalogo Programa Institucional
        $selectProgramaEdit = [];

        $selectEstatuto = $coursesestatutoM->listestatuto();
        $selectEstatutoEdit = [];

        $selectOrganizacion = $coursesorganizacionM->listorganizacion();
        $selecOrganizacionEdit = [];

        $selectModalidad = $coursesmodalidadM->listmodalidad();
        $selectModalidadEdit = [];

        $selectCategoria = $coursescategoriaM->listcategoria();
        $selectCategoriaEdit = [];

        return view('courses.tablecourses.form', compact('item','selectBeneficio','selectBeneficioEdit','selectTipocurso','selectTipoCursoEdit','selectTipoaccion','selectTipoAccionEdit',
    'selectCoordinacion', 'selectCoordinacionEdit','selectNomaccion','selectNomaccionEdit','selectPrograma','selectProgramaEdit','selectEstatuto','selectEstatutoEdit','selectOrganizacion','selecOrganizacionEdit',
'selectModalidad','selectModalidadEdit','selectCategoria','selectCategoriaEdit'));
    }
}

