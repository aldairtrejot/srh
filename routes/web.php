<?php

use App\Http\Controllers\Courses\Courses\CoursesC;
use App\Http\Controllers\Courses\Coursesauditoria\Courses11C;
use App\Http\Controllers\Courses\Coursescategoria\Courses2C;
use App\Http\Controllers\Courses\Coursescoordinacion\Courses3C;
use App\Http\Controllers\Courses\Coursesestatuto\Courses4C;
use App\Http\Controllers\Courses\Coursesmodalidad\Courses5C;
use App\Http\Controllers\Courses\Coursesnombreacc\Courses6C;
use App\Http\Controllers\Courses\Coursesorganizacion\Courses7C;
use App\Http\Controllers\Courses\Coursesprograma\Courses8C;
use App\Http\Controllers\Courses\Coursestipoac\Courses9C;
use App\Http\Controllers\Courses\Coursestipocur\Courses10C;
use App\Http\Controllers\Courses\Tableinstructor\InstructorsC;
use App\Http\Controllers\Courses\Tablecourses\TblCoursesC;

use Illuminate\Support\Facades\Route;

// ALL_ROUTE
foreach (glob(__DIR__ . '/web/web/*/*/*.php') as $filename) {
    require $filename;
}


//ROUTE_COUSER ---- > Beneficio
Route::get('/courses/list', CoursesC::class)->name('courses.list')->middleware('auth');
Route::get('/courses/create', [CoursesC::class, 'create'])->name('courses.create')->middleware('auth');
Route::post('/courses/save', [CoursesC::class, 'save'])->name('courses.save')->middleware('auth');
Route::post('/courses/table', [CoursesC::class, 'searchTable']);
Route::match(['get', 'post'], '/courses/edit/{id}', [CoursesC::class, 'edit'])->name('courses.edit')->middleware('auth');
Route::delete('/courses/delete/{id}', [CoursesC::class, 'destroy']);

//ROUTE_COUSER ---- > Categoria
Route::get('/coursescategoria/list', Courses2C::class)->name('coursescategoria.list')->middleware('auth');
Route::get('/coursescategoria/create', [Courses2C::class, 'create'])->name('coursescategoria.create')->middleware('auth');
Route::post('/coursescategoria/save', [Courses2C::class, 'save'])->name('coursescategoria.save')->middleware('auth');
Route::post('/coursescategoria/table', [Courses2C::class, 'searchTable']);
Route::match(['get', 'post'], '/coursescategoria/edit/{id}', [Courses2C::class, 'edit'])->name('coursescategoria.edit')->middleware('auth');
Route::delete('/coursescategoria/delete/{id}', [Courses2C::class, 'destroy']);

//ROUTE_COUSER ---- > Coordinacion
Route::get('/coursescoordinacion/list', Courses3C::class)->name('coursescoordinacion.list')->middleware('auth');
Route::get('/coursescoordinacion/create', [Courses3C::class, 'create'])->name('coursescoordinacion.create')->middleware('auth');
Route::post('/coursescoordinacion/save', [Courses3C::class, 'save'])->name('coursescoordinacion.save')->middleware('auth');
Route::post('/coursescoordinacion/table', [Courses3C::class, 'searchTable']);
Route::match(['get', 'post'], '/coursescoordinacion/edit/{id}', [Courses3C::class, 'edit'])->name('coursescoordinacion.edit')->middleware('auth');
Route::delete('/coursescoordinacion/delete/{id}', [Courses3C::class, 'destroy']);

//ROUTE_COUSER ---- > Estatuto Orgánico
Route::get('/coursesestatuto/list', Courses4C::class)->name('coursesestatuto.list')->middleware('auth');
Route::get('/coursesestatuto/create', [Courses4C::class, 'create'])->name('coursesestatuto.create')->middleware('auth');
Route::post('/coursesestatuto/save', [Courses4C::class, 'save'])->name('coursesestatuto.save')->middleware('auth');
Route::post('/coursesestatuto/table', [Courses4C::class, 'searchTable']);
Route::match(['get', 'post'], '/coursesestatuto/edit/{id}', [Courses4C::class, 'edit'])->name('coursesestatuto.edit')->middleware('auth');
Route::delete('/coursesestatuto/delete/{id}', [Courses4C::class, 'destroy']);

//ROUTE_COUSER ---- > Modalidad
Route::get('/coursesmodalidad/list', Courses5C::class)->name('coursesmodalidad.list')->middleware('auth');
Route::get('/coursesmodalidad/create', [Courses5C::class, 'create'])->name('coursesmodalidad.create')->middleware('auth');
Route::post('/coursesmodalidad/save', [Courses5C::class, 'save'])->name('coursesmodalidad.save')->middleware('auth');
Route::post('/coursesmodalidad/table', [Courses5C::class, 'searchTable']);
Route::match(['get', 'post'], '/coursesmodalidad/edit/{id}', [Courses5C::class, 'edit'])->name('coursesmodalidad.edit')->middleware('auth');
Route::delete('/coursesmodalidad/delete/{id}', [Courses5C::class, 'destroy']);

//ROUTE_COUSER ---- > Nombre Acción
Route::get('/coursesnombreacc/list', Courses6C::class)->name('coursesnombreacc.list')->middleware('auth');
Route::get('/coursesnombreacc/create', [Courses6C::class, 'create'])->name('coursesnombreacc.create')->middleware('auth');
Route::post('/coursesnombreacc/save', [Courses6C::class, 'save'])->name('coursesnombreacc.save')->middleware('auth');
Route::post('/coursesnombreacc/table', [Courses6C::class, 'searchTable']);
Route::match(['get', 'post'], '/coursesnombreacc/edit/{id}', [Courses6C::class, 'edit'])->name('coursesnombreacc.edit')->middleware('auth');
Route::delete('/coursesnombreacc/delete/{id}', [Courses6C::class, 'destroy']);

//ROUTE_COUSER ---- > Organizacion
Route::get('/coursesorganizacion/list', Courses7C::class)->name('coursesorganizacion.list')->middleware('auth');
Route::get('/coursesorganizacion/create', [Courses7C::class, 'create'])->name('coursesorganizacion.create')->middleware('auth');
Route::post('/coursesorganizacion/save', [Courses7C::class, 'save'])->name('coursesorganizacion.save')->middleware('auth');
Route::post('/coursesorganizacion/table', [Courses7C::class, 'searchTable']);
Route::match(['get', 'post'], '/coursesorganizacion/edit/{id}', [Courses7C::class, 'edit'])->name('coursesorganizacion.edit')->middleware('auth');
Route::delete('/coursesorganizacion/delete/{id}', [Courses7C::class, 'destroy']);

//ROUTE_COUSER ---- > Programa
Route::get('/coursesprograma/list', Courses8C::class)->name('coursesprograma.list')->middleware('auth');
Route::get('/coursesprograma/create', [Courses8C::class, 'create'])->name('coursesprograma.create')->middleware('auth');
Route::post('/coursesprograma/save', [Courses8C::class, 'save'])->name('coursesprograma.save')->middleware('auth');
Route::post('/coursesprograma/table', [Courses8C::class, 'searchTable']);
Route::match(['get', 'post'], '/coursesprograma/edit/{id}', [Courses8C::class, 'edit'])->name('coursesprograma.edit')->middleware('auth');
Route::delete('/coursesprograma/delete/{id}', [Courses8C::class, 'destroy']);

//ROUTE_COUSER ---- > Tipo de acción
Route::get('/coursestipoac/list', Courses9C::class)->name('coursestipoac.list')->middleware('auth');
Route::get('/coursestipoac/create', [Courses9C::class, 'create'])->name('coursestipoac.create')->middleware('auth');
Route::post('/coursestipoac/save', [Courses9C::class, 'save'])->name('coursestipoac.save')->middleware('auth');
Route::post('/coursestipoac/table', [Courses9C::class, 'searchTable']);
Route::match(['get', 'post'], '/coursestipoac/edit/{id}', [Courses9C::class, 'edit'])->name('coursestipoac.edit')->middleware('auth');
Route::delete('/coursestipoac/delete/{id}', [Courses9C::class, 'destroy']);

//ROUTE_COUSER ---- > Tipo Cursos
Route::get('/coursestipocur/list', Courses10C::class)->name('coursestipocur.list')->middleware('auth');
Route::get('/coursestipocur/create', [Courses10C::class, 'create'])->name('coursestipocur.create')->middleware('auth');
Route::post('/coursestipocur/save', [Courses10C::class, 'save'])->name('coursestipocur.save')->middleware('auth');
Route::post('/coursestipocur/table', [Courses10C::class, 'searchTable']);
Route::match(['get', 'post'], '/coursestipocur/edit/{id}', [Courses10C::class, 'edit'])->name('coursestipocur.edit')->middleware('auth');
Route::delete('/coursestipocur/delete/{id}', [Courses10C::class, 'destroy']);

//ROUTE_COUSER ---- > Auditoria
Route::get('/coursesauditoria/list', Courses11C::class)->name('coursesauditoria.list')->middleware('auth');
Route::get('/coursesauditoria/create', [Courses11C::class, 'create'])->name('coursesauditoria.create')->middleware('auth');
Route::post('/coursesauditoria/save', [Courses11C::class, 'save'])->name('coursesauditoria.save')->middleware('auth');
Route::post('/coursesauditoria/table', [Courses11C::class, 'searchTable']);
Route::match(['get', 'post'], '/coursesauditoria/edit/{id}', [Courses11C::class, 'edit'])->name('coursesauditoria.edit')->middleware('auth');
Route::delete('/coursesauditoria/delete/{id}', [Courses11C::class, 'destroy']);


//ROUTE_COUSER ---- >Tabla instructores
Route::get('/tableinstructor/list', InstructorsC::class)->name('tableinstructor.list')->middleware('auth');
Route::get('/tableinstructor/create', [InstructorsC::class, 'create'])->name('tableinstructor.create')->middleware('auth');
Route::post('/tableinstructor/save', [InstructorsC::class, 'save'])->name('tableinstructor.save')->middleware('auth');
Route::post('/tableinstructor/table', [InstructorsC::class, 'searchTable']);
Route::match(['get', 'post'], '/tableinstructor/edit/{id}', [InstructorsC::class, 'edit'])->name('tableinstructor.edit')->middleware('auth');
Route::delete('/tableinstructor/delete/{id}', [InstructorsC::class, 'destroy']);
Route::post('/tableinstructor/table/dataCurp', [InstructorsC::class, 'dataCurp'])->name('tableinstructor.dataCurp')->middleware('auth');

//ROUTE_COUSER ---- >Alfresco
//Route::get('/alfresco/upload', [AlfrescoC::class, 'showUploadForm'])->name('alfresco.upload.form');// Ruta para mostrar el formulario de carga de archivo
//Route::post('/upload-file', [AlfrescoC::class, 'uploadFile'])->name('alfresco.upload.file');// Ruta para manejar la carga de archivo
//Route::post('/cloud/delete', [AlfrescoC::class, 'delete'])->name('cloud.delete')->middleware('auth'); // ALFRESCO DELETE

//ROUTE_COUSER ---- >Tabla Cursos
Route::get('/tablecourses/list', TblCoursesC::class)->name('tablecourses.list')->middleware('auth');
Route::post('/tablecourses/table', [TblCoursesC::class, 'searchTable']);
Route::get('/tablecourses/create', [TblCoursesC::class, 'create'])->name('tablecourses.create')->middleware('auth');










