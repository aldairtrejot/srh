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
use App\Models\Courses\Courses\Instructores\Instructores\InstructorM;
use App\Models\Courses\Courses\Cursoinstructor\CourseinstructorM;
use App\Models\Courses\Courses\Relcurso\RelcoursesM;
use App\Http\Controllers\Admin\MessagesC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TblCoursesC extends Controller
{
    public function __invoke(Request $request)
    {
        $instructorId = $request->query('instructor_id'); // Capturar el ID del instructor desde la URL
        $coursesModel = new TblcoursesM();
    
        if ($instructorId) {
            $courses = $coursesModel->getCoursesByInstructor($instructorId); // ✅ Ahora trae todos los cursos sin paginación
        } else {
            $courses = TblcoursesM::all(); // ✅ Obtener todos los cursos si no hay instructor seleccionado
        }
    
        return view('courses.tablecourses.list', compact('courses'));
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
        $instructorM = new InstructorM();
       

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

        $selectInstructor = $instructorM->listinstructor();
        $selectInstructorEdit = [];

        $item -> costo = 0;
        $item -> iva = 16;
        $costot = 0;


        return view('courses.tablecourses.form', compact('item','costot', 'selectBeneficio','selectBeneficioEdit','selectTipocurso','selectTipoCursoEdit','selectTipoaccion','selectTipoAccionEdit',
    'selectCoordinacion', 'selectCoordinacionEdit','selectNomaccion','selectNomaccionEdit','selectPrograma','selectProgramaEdit','selectEstatuto','selectEstatutoEdit','selectOrganizacion','selecOrganizacionEdit',
'selectModalidad','selectModalidadEdit','selectCategoria','selectCategoriaEdit','selectInstructor','selectInstructorEdit'));
    }
    public function save(Request $request)
    {
        $courseinstructorM = new CourseinstructorM();
        $now = Carbon::now(); // Fecha actual
        $messagesC = new MessagesC();
    
        if (!$request->id_tbl_cursos) {
            // Crear nuevo curso
            $nuevoCurso = TblcoursesM::create([
                'id_cat_tipo_cursos' => $request->id_cat_tipo_cursos,
                'id_cat_coordinacion' => $request->id_cat_coordinacion,
                'id_cat_nombre_accion' => $request->id_cat_nombre_accion,
                'id_cat_programa_institucional' => $request->id_cat_programa_institucional,
                'id_cat_estatuto_organico' => $request->id_cat_estatuto_organico,
                'programa_proyecto' => strtoupper($request->programa_proyecto),
                'id_cat_beneficio' => $request->id_cat_beneficio,
                'id_cat_organizacion' => $request->id_cat_organizacion,
                'id_cat_tipo_accion' => $request->id_cat_tipo_accion,
                'id_cat_modalidad' => $request->id_cat_modalidad,
                'id_cat_categoria' => $request->id_cat_categoria,
                'costo' => $request->costo,
                'iva' => $request->iva,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'horas' => $request->horas,
                'estatus' => $request->estatus ?? false,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ]);
    
            // Obtener ID del curso creado
            $idcursos = $courseinstructorM->cursoinstructor($request->programa_proyecto);
    
            // Relacionar curso con instructor
            RelcoursesM::create([
                'id_usuario_sistema' => Auth::user()->id,
                'id_tbl_cursos' => $idcursos->id_tbl_cursos,
                'id_tbl_instructores' => $request->id_tbl_instructores,
                'fecha_usuario' => $now
            ]);
        } else {
            // Modificar curso existente
            $data = [
                'id_cat_tipo_cursos' => $request->id_cat_tipo_cursos,
                'id_cat_coordinacion' => $request->id_cat_coordinacion,
                'id_cat_nombre_accion' => $request->id_cat_nombre_accion,
                'id_cat_programa_institucional' => $request->id_cat_programa_institucional,
                'id_cat_estatuto_organico' => $request->id_cat_estatuto_organico,
                'programa_proyecto' => strtoupper($request->programa_proyecto),
                'id_cat_beneficio' => $request->id_cat_beneficio,
                'id_cat_organizacion' => $request->id_cat_organizacion,
                'id_cat_tipo_accion' => $request->id_cat_tipo_accion,
                'id_cat_modalidad' => $request->id_cat_modalidad,
                'id_cat_categoria' => $request->id_cat_categoria,
                'costo' => $request->costo,
                'iva' => $request->iva,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'horas' => $request->horas,
                'estatus' => $request->estatus ?? false,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];
    
            TblcoursesM::where('id_tbl_cursos', $request->id_tbl_cursos)->update($data);
    
            // Actualizar observaciones si existen
            if ($request->observaciones) {
                $observacionesData = [
                    'estatus' => $request->estatus ?? false,
                    'observaciones' => strtoupper($request->observaciones),
                    'id_usuario_sistema' => Auth::user()->id,
                    'fecha_usuario' => $now,
                ];
    
                TblcoursesM::where('id_tbl_cursos', $request->id_tbl_cursos)->update($observacionesData);
            }
        }
    
        return $messagesC->messageSuccessRedirect('tablecourses.list', 'Curso guardado exitosamente.');
    }
    public function edit(string $id)
{
    $tblcoursesM = new TblcoursesM();
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
    $instructorM = new InstructorM();
    $relcoursesM = new RelcoursesM();

    $item = $tblcoursesM->edit($id); // Obtener el curso que se está editando

    // Calcular el costo total
    $costot = $item->costo + ($item->costo * ($item->iva / 100)); // Ejemplo de cálculo, depende de la lógica del negocio

    $selectBeneficio = $coursesM->listbeneficio(); // Catalogo de beneficio
    $selectBeneficioEdit = isset($item->id_cat_beneficio) ? $coursesM->edittblcourses($item->id_cat_beneficio) : []; 

    $selectTipocurso = $coursestipocurM->listtipocurso(); // Catalogo de Tipo Curso
    $selectTipoCursoEdit = isset($item->id_cat_tipo_cursos) ? $coursestipocurM->edittblcourses($item->id_cat_tipo_cursos) :[]; 

    $selectTipoaccion = $coursestipoacM->listtipoaccion(); // Catalogo de Tipo Accion
    $selectTipoAccionEdit = isset($item->id_cat_tipo_accion) ? $coursestipoacM->edittblcourses($item->id_cat_tipo_accion) : []; 

    $selectCoordinacion = $coursescoordinacionM->listcoordinacion(); // Catalogo Coordinacion
    $selectCoordinacionEdit = isset($item->id_cat_coordinacion) ? $coursescoordinacionM->edittblcourses($item->id_cat_coordinacion) : []; 

    $selectNomaccion = $coursesnombreaccM->listnomaccion(); // Catalogo Nombre Accion
    $selectNomaccionEdit = isset($item->id_cat_nombre_accion) ? $coursesnombreaccM->edittblcourses($item->id_cat_nombre_accion) : []; 

    $selectPrograma = $coursesprogramaM->listprograma(); // Catalogo Programa Institucional
    $selectProgramaEdit = isset($item->id_cat_programa_institucional) ? $coursesprogramaM->edittblcourses($item->id_cat_programa_institucional) : [];

    $selectEstatuto = $coursesestatutoM->listestatuto();
    $selectEstatutoEdit = isset($item->id_cat_estatuto_organico) ? $coursesestatutoM->edittblcourses($item->id_cat_estatuto_organico) : [];

    $selectOrganizacion = $coursesorganizacionM->listorganizacion();
    $selecOrganizacionEdit = isset($item->id_cat_organizacion) ? $coursesorganizacionM->edittblcourses($item->id_cat_organizacion) : [];

    $selectModalidad = $coursesmodalidadM->listmodalidad();
    $selectModalidadEdit = isset($item->id_cat_modalidad) ? $coursesmodalidadM->edittblcourses($item->id_cat_modalidad) : [];

    $selectCategoria = $coursescategoriaM->listcategoria();
    $selectCategoriaEdit = isset($item->id_cat_categoria) ? $coursescategoriaM->edittblcourses($item->id_cat_categoria) : [];

    $idinstructor = $relcoursesM->relinstructor($item->id_tbl_cursos);
    $selectInstructor = $instructorM->listinstructor();
    $selectInstructorEdit = isset($idinstructor) ? $instructorM->edittblinstructores($idinstructor) : [];

    // Devolver la vista con el costo total calculado
    return view('courses.tablecourses.form', compact('item', 'costot', 'selectBeneficio', 'selectBeneficioEdit', 'selectTipocurso', 'selectTipoCursoEdit', 'selectTipoaccion', 'selectTipoAccionEdit',
    'selectCoordinacion', 'selectCoordinacionEdit', 'selectNomaccion', 'selectNomaccionEdit', 'selectPrograma', 'selectProgramaEdit', 'selectEstatuto', 'selectEstatutoEdit', 'selectOrganizacion', 'selecOrganizacionEdit',
    'selectModalidad', 'selectModalidadEdit', 'selectCategoria', 'selectCategoriaEdit', 'selectInstructor', 'selectInstructorEdit'));
}

      
}


