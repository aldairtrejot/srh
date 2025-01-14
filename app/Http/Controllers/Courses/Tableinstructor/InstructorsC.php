<?php
namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Courses\Courses\Instructores\Instructores\InstructorM; // Modelo correcto

class InstructorsC extends Controller
{
    // Mostrar la lista principal de instructores
    public function list()
    {
        $instructorModel = new InstructorM();
        $instructores = $instructorModel->obtenerInstructoresConDetalles();
        return view('courses.tableinstructor.list', compact('instructores'));
    }

    // Método para obtener la tabla (por ejemplo, para Datatables)
    public function table(Request $request)
    {
        try {
            $searchValue = $request->input('searchValue', ''); // Valor de búsqueda, por defecto vacío
    
            // Consulta básica con filtros de búsqueda
            $query = InstructorM::query();
            if ($searchValue) {
                $query->where('uuid_cv', 'like', '%' . $searchValue . '%')
                      ->orWhere('uuid_constancia', 'like', '%' . $searchValue . '%');
            }
    
            // Obtener resultados con paginación
            $instructores = $query->paginate(10); // Cambia el número 10 por el número de registros por página que desees
    
            return response()->json([
                'data' => $instructores,
                'status' => true,
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al cargar la tabla: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Mostrar formulario de creación
    public function create()
    {
        $selectValue = [
            ['id' => 1, 'value' => 'Apto'],
            ['id' => 0, 'value' => 'No Apto'],
        ];
    
        $instructor = null; // Pasar la variable $instructor como null
    
        return view('courses.tableinstructor.form', compact('selectValue', 'instructor'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $instructor = InstructorM::find($id);
        if (!$instructor) {
            return redirect()->route('tableinstructor.list')->with('error', 'Instructor no encontrado');
        }

        $selectValue = [
            ['id' => 1, 'value' => 'Apto'],
            ['id' => 0, 'value' => 'No Apto'],
        ];

        return view('courses.tableinstructor.form', compact('instructor', 'selectValue'));
    }

    // Guardar un nuevo instructor o actualizar uno existente
    public function save(Request $request)
    {
        $validated = $request->validate([
            'id_empleados' => 'required|integer',
            'uuid_constancia' => 'nullable|string',
            'uuid_cv' => 'nullable|string',
            'estatus_apto' => 'nullable|integer',
            'estatus_instructor' => 'nullable|integer',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'constancia' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        try {
            // Datos generales del instructor
            $data = array_merge($validated, [
                'id_usuario_sistema' => Auth::id(),
                'fecha_usuario' => Carbon::now(),
            ]);

            // Manejo de archivos (CV y Constancia)
            if ($request->hasFile('cv')) {
                $cvFile = $request->file('cv');
                $cvResponse = $this->uploadToAlfresco($cvFile, 'CV');
                if ($cvResponse['success']) {
                    $data['uuid_cv'] = $cvResponse['data']['uuid']; // Guardar el UUID devuelto por Alfresco
                } else {
                    return redirect()->route('tableinstructor.list')->with('error', 'Error al subir el CV: ' . $cvResponse['message']);
                }
            }

            if ($request->hasFile('constancia')) {
                $constanciaFile = $request->file('constancia');
                $constanciaResponse = $this->uploadToAlfresco($constanciaFile, 'Constancia');
                if ($constanciaResponse['success']) {
                    $data['uuid_constancia'] = $constanciaResponse['data']['uuid']; // Guardar el UUID devuelto por Alfresco
                } else {
                    return redirect()->route('tableinstructor.list')->with('error', 'Error al subir la Constancia: ' . $constanciaResponse['message']);
                }
            }

            // Guardar o actualizar el instructor
            if ($request->has('id_instructor')) {
                // Actualización
                $instructor = InstructorM::find($request->id_instructor);
                if (!$instructor) {
                    return redirect()->route('tableinstructor.list')->with('error', 'Instructor no encontrado para actualizar.');
                }

                $instructor->update($data);
                return redirect()->route('tableinstructor.list')->with('success', 'Instructor actualizado correctamente.');
            } else {
                // Creación
                InstructorM::create($data);
                return redirect()->route('tableinstructor.list')->with('success', 'Instructor creado correctamente.');
            }
        } catch (\Exception $e) {
            return redirect()->route('tableinstructor.list')->with('error', 'Error al guardar el instructor: ' . $e->getMessage());
        }
    }

    // Eliminar un instructor
    public function destroy($id)
    {
        try {
            $instructor = InstructorM::find($id);
            if (!$instructor) {
                return response()->json(['success' => false, 'message' => 'Instructor no encontrado'], 404);
            }

            $instructor->delete();
            return response()->json(['success' => true, 'message' => 'Instructor eliminado']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el instructor: ' . $e->getMessage()], 500);
        }
    }

    // Método adicional para gestionar datos en la nube
    public function cloud($id)
    {
        $instructor = InstructorM::find($id);
        if (!$instructor) {
            return redirect()->route('tableinstructor.list')->with('error', 'Instructor no encontrado');
        }

        return view('courses.tableinstructor.cloud', compact('instructor'));
    }

    private function uploadToAlfresco($file, $type)
    {
        $alfrescoBaseUrl = 'http://<alfresco-server-url>/alfresco/api/-default-/public/alfresco/versions/1/nodes';
        $alfrescoToken = '<alfresco-auth-token>';
    
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($alfrescoBaseUrl, [
                'headers' => [
                    'Authorization' => "Bearer $alfrescoToken",
                ],
                'multipart' => [
                    [
                        'name' => 'filedata',
                        'contents' => fopen($file->getRealPath(), 'r'),
                        'filename' => $file->getClientOriginalName(),
                    ],
                    [
                        'name' => 'name',
                        'contents' => $type . '_' . $file->getClientOriginalName(),
                    ],
                ],
            ]);
    
            if ($response->getStatusCode() === 201) {
                $data = json_decode($response->getBody(), true);
                return ['success' => true, 'data' => $data];
            }
    
            return ['success' => false, 'message' => 'Error al subir el archivo'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Método para obtener instructores con detalles adicionales
    public function obtenerInstructoresConDetalles()
{
    try {
        // Crear una instancia del modelo
        $instructorModel = new InstructorM();

        // Llamar al método usando la instancia
        $instructores = $instructorModel->obtenerInstructoresConDetalles();

        // Pasar los datos a la vista
        return view('courses.tableinstructor.detalles', compact('instructores'));
    } catch (\Exception $e) {
        // Manejo de errores
        return redirect()->route('tableinstructor.list')->with('error', 'Error al obtener los detalles de los instructores: ' . $e->getMessage());
    }
}
    
}   