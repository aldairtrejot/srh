<?php

use App\Http\Controllers\Administration\ResultC;
use App\Http\Controllers\Email\EmailC;
use App\Http\Controllers\Letter\Certification\CertificationC;
use App\Http\Controllers\Letter\Collection\CollectionAreaInternoC;
use App\Http\Controllers\Letter\Collection\CollectionIteradorInternoC;
use App\Http\Controllers\Letter\Collection\CollectionSolicitanteC;
use App\Http\Controllers\Letter\Communication\CommunicationC;
use App\Http\Controllers\Letter\Dashboard\DashboardLetterC;
use App\Http\Controllers\Letter\External\CloudExternalC;
use App\Http\Controllers\Letter\External\ExternalC;
use App\Http\Controllers\Letter\File\CloudFileC;
use App\Http\Controllers\Letter\Informative\InformativeC;
use App\Http\Controllers\Letter\Inside\ReportInsideC;
use App\Http\Controllers\Letter\Request\RequestC;
use App\Http\Controllers\Letter\Round\CloudRoundC;
use App\Http\Controllers\Cloud\AlfrescoC;
use App\Http\Controllers\Letter\Collection\CollectionYearC;
use App\Http\Controllers\Letter\File\FileC;
use App\Http\Controllers\Letter\Inside\CloudInsideC;
use App\Http\Controllers\Letter\Letter\CloudLetterC;
use App\Http\Controllers\Letter\Office\CloudC;
use App\Http\Controllers\Letter\Inside\InsideC;
use App\Http\Controllers\Letter\Office\OfficeC;
use App\Http\Controllers\Letter\Collection\CollectionClaveC;
use App\Http\Controllers\Letter\Collection\CollectionTramiteC;
use App\Http\Controllers\Letter\Collection\CollectionUnidadC;
use App\Http\Controllers\Administration\LoginC;
use App\Http\Controllers\Administration\RecoverC;
use App\Http\Controllers\Administration\RegisterC;
use App\Http\Controllers\Administration\UserC;
use App\Http\Controllers\Home\AboutC;
use App\Http\Controllers\Home\DashboardC;
use App\Http\Controllers\Letter\Collection\CollectionAreaC;
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
use App\Http\Controllers\Letter\Letter\LetterC;
use App\Http\Controllers\Letter\Report\ReporteCorrespondenciaC;
use App\Http\Controllers\Letter\Report\ReporteTemplateC;
use App\Http\Controllers\Letter\Round\RoundC;
use App\Http\Controllers\Administration\AdministrationC\AdministrationC;
use App\Http\Controllers\Letter\Area\AreaC;
use App\Http\Controllers\Letter\Dependencia\DependenciaC;
use App\Http\Controllers\Letter\Dependencia\DependenciareaC;
use App\Http\Controllers\Letter\Dependencia\ReldependenciaC;
use Mews\Captcha\Facades\Captcha;

use Illuminate\Support\Facades\Route;

Route::get('captcha/{config?}', function ($config = 'default') {
    return Captcha::create($config);
})->name('captcha');

Route::get('/login', LoginC::class)->name('login'); ///ROUTE_LOGIN
Route::get('/register', RegisterC::class)->name('register'); ///ROUTE_REGISTER
Route::get('/recover', RecoverC::class)->name('recover');//ROUTE_RECOVER
Route::post('/login', [LoginC::class, 'authenticate']);///ROUTE_AUTHENTICATE
Route::get('/result', [ResultC::class, 'result'])->name('result');

// Recover password
Route::post('/password/result', [RecoverC::class, 'updatePassword'])->name('recover.password');


///IS_PROTECT
Route::get('/dashboard', [DashboardC::class, 'dashboard'])->name('dashboard')->middleware('auth'); //ROUTE_DASH BOARD
Route::get('/about', AboutC::class)->name('about')->middleware('auth'); //ROUTE_ABOUT
Route::post('/logout', [LoginC::class, 'logout'])->name('logout')->middleware('auth');//ROUTE_LOGOUT

//ROUTE_USER
Route::get('/user', UserC::class)->name('user.list')->middleware('auth'); //ROUTE_USER
Route::get('/user/list', [UserC::class, 'list'])->middleware('auth'); //ROUTE_LIST_OF_USER
Route::get('/user/create', [UserC::class, 'create'])->name('user.create')->middleware('auth'); //ROUTE_CREATE
Route::post('/user/save', [UserC::class, 'save'])->name('user.save')->middleware('auth');
Route::get('/user/edit/{id}', [UserC::class, 'edit'])->name('user.edit')->middleware('auth');
Route::post('/user/validatePassword', [UserC::class, 'validatePassword'])->name('user.validatePassword')->middleware('auth');
Route::post('/user/changePassword', [UserC::class, 'changePassword'])->name('user.changePassword')->middleware('auth');

//ROUTE_LETTER
Route::get('/letter/dashboard', [LetterC::class, 'dashboard'])->name('letter.dashboard')->middleware('auth');
Route::get('/letter/list', LetterC::class)->name('letter.list')->middleware('auth');
Route::get('/letter/delete', [LetterC::class . 'delete'])->name('letter.delete')->middleware('auth');
Route::get('/letter/table', [LetterC::class, 'table'])->name('letter.table')->middleware('auth');
Route::post('/letter/tableCopy', [LetterC::class, 'tableCopy'])->name('letter.tableCopy')->middleware('auth');
Route::get('/letter/create', [LetterC::class, 'create'])->name('letter.create')->middleware('auth');
Route::get('/letter/edit/{id}', [LetterC::class, 'edit'])->name('letter.edit')->middleware('auth');
Route::post('/letter/save', [LetterC::class, 'save'])->name('letter.save')->middleware('auth');
Route::post('/letter/collection/collectionArea', [CollectionAreaC::class, 'collection'])->name('letter.collection.area')->middleware('auth');
Route::post('/letter/collection/collectionUnidad', [CollectionUnidadC::class, 'collection'])->name('letter.collection.unidad')->middleware('auth');
Route::post('/letter/collection/collectionTramite', [CollectionTramiteC::class, 'collection'])->name('letter.collection.tramite')->middleware('auth');
Route::post('/letter/collection/collectionClave', [CollectionClaveC::class, 'collection'])->name('letter.collection.clabe')->middleware('auth');
Route::post('/letter/collection/dataClave', [CollectionClaveC::class, 'dataClave'])->name('letter.collection.dataClave')->middleware('auth');
Route::get('/letter/generate-pdf/correspondencia/{id}', [ReporteCorrespondenciaC::class, 'generatePdf'])->middleware('auth');
Route::post('/letter/collection/validateUnique', [LetterC::class, 'validateUnique'])->name('letter.validateUnique')->middleware('auth');
Route::post('/letter/collection/uniqueRemitente', [LetterC::class, 'uniqueRemitente'])->name('letter.collection.uniqueRemitente')->middleware('auth');
Route::post('/letter/collection/uniqueNameValidate', [LetterC::class, 'uniqueRemitenteName'])->name('letter.collection.uniqueRemitenteName')->middleware('auth');
Route::post('/letter/delete/copy', [LetterC::class, 'deleteCopy'])->name('letter.deleteCopy')->middleware('auth');
Route::post('/letter/collection/area', [LetterC::class, 'collectionArea'])->name('letter.collectionArea')->middleware('auth');
Route::post('/letter/saveCopy', [LetterC::class, 'saveCopy'])->name('letter.saveCopy')->middleware('auth');
Route::post('/letter/validateCopy', [LetterC::class, 'validateCopy'])->name('letter.validateCopy')->middleware('auth');


// Letter Dashboard
Route::post('/letter/dashboard/getCollection', [DashboardLetterC::class, 'getCollection'])->name('letter.dashboard.getCollection')->middleware('auth');
Route::post('/letter/dashboard/generate', [DashboardLetterC::class, 'generate'])->name('letter.dashboard.generate')->middleware('auth');

////Cloud
Route::get('/letter/cloud/{id}', [LetterC::class, 'cloud'])->name('letter.cloud')->middleware('auth');
Route::post('/letter/cloud/data', [CloudLetterC::class, 'cloudData'])->name('letter.cloud.data')->middleware('auth');
Route::post('/letter/cloud/anexos', [CloudLetterC::class, 'cloudAnexos'])->name('letter.cloud.anexos')->middleware('auth');
Route::post('/letter/cloud/upload', [CloudLetterC::class, 'upload'])->name('letter.cloud.upload')->middleware('auth');
Route::post('/letter/cloud/delete', [CloudLetterC::class, 'delete'])->name('letter.cloud.delete')->middleware('auth');
// --- -- --- - -- - -- --


//ROUTE INSIDE
Route::get('/inside/list', [InsideC::class, 'list'])->name('inside.list')->middleware('auth');
Route::post('/inside/table', [InsideC::class, 'table'])->name('inside.table')->middleware('auth');
Route::get('/inside/create', [InsideC::class, 'create'])->name('inside.create')->middleware('auth');
Route::get('/inside/edit/{id}', [InsideC::class, 'edit'])->name('inside.edit')->middleware('auth');
Route::post('/inside/save', [InsideC::class, 'save'])->name('inside.save')->middleware('auth');
Route::get('/inside/cloud/{id}', [InsideC::class, 'cloud'])->name('inside.cloud')->middleware('auth');
Route::post('/inside/cloud/data', [CloudInsideC::class, 'cloudData'])->name('inside.cloud.data')->middleware('auth');
Route::post('/inside/cloud/anexos', [CloudInsideC::class, 'cloudAnexos'])->name('inside.cloud.anexos')->middleware('auth');
Route::post('/inside/cloud/oficios', [CloudInsideC::class, 'cloudOficios'])->name('inside.cloud.oficios')->middleware('auth');
Route::post('/inside/cloud/upload', [CloudInsideC::class, 'upload'])->name('inside.cloud.upload')->middleware('auth');
Route::post('/inside/cloud/delete', [CloudInsideC::class, 'delete'])->name('inside.cloud.delete')->middleware('auth');
Route::get('/inside/generate-pdf/{id}', [ReportInsideC::class, 'report'])->middleware('auth');
Route::post('/inside/validate/folGestion', [InsideC::class, 'validateFol'])->name('inside.validate.folGestion')->middleware('auth');

//ROUTE ROUND / CIRCULARES
Route::get('/round/list', [RoundC::class, 'list'])->name('round.list')->middleware('auth');
Route::post('/round/table', [RoundC::class, 'table'])->name('round.table')->middleware('auth');
Route::get('/round/create', [RoundC::class, 'create'])->name('round.create')->middleware('auth');
Route::get('/round/edit/{id}', [RoundC::class, 'edit'])->name('round.edit')->middleware('auth');
Route::post('/round/save', [RoundC::class, 'save'])->name('round.save')->middleware('auth');
Route::get('/round/cloud/{id}', [RoundC::class, 'cloud'])->name('round.cloud')->middleware('auth');
Route::post('/round/cloud/data', [CloudRoundC::class, 'cloudData'])->name('round.cloud.data')->middleware('auth');
Route::post('/round/cloud/anexos', [CloudRoundC::class, 'cloudAnexos'])->name('round.cloud.anexos')->middleware('auth');
Route::post('/round/cloud/oficios', [CloudRoundC::class, 'cloudOficios'])->name('round.cloud.oficios')->middleware('auth');
Route::post('/round/cloud/upload', [CloudRoundC::class, 'upload'])->name('round.cloud.upload')->middleware('auth');
Route::post('/round/cloud/delete', [CloudRoundC::class, 'delete'])->name('round.cloud.delete')->middleware('auth');
Route::get('/round/generate-pdf/{id}', [ReporteTemplateC::class, 'round'])->middleware('auth');

//ROUTE EXTERNAK / CIRCULARES EXTERNAS
Route::get('/external/list', [ExternalC::class, 'list'])->name('external.list')->middleware('auth');
Route::post('/external/table', [ExternalC::class, 'table'])->name('external.table')->middleware('auth');
Route::get('/external/create', [ExternalC::class, 'create'])->name('external.create')->middleware('auth');
Route::post('/external/save', [ExternalC::class, 'save'])->name('external.save')->middleware('auth');
Route::post('/external/collection/area', [ExternalC::class, 'area'])->name('external.collection.area')->middleware('auth');
Route::post('/external/unique', [ExternalC::class, 'unique'])->name('external.unique')->middleware('auth');
Route::get('/external/edit/{id}', [ExternalC::class, 'edit'])->name('external.edit')->middleware('auth');
Route::get('/external/cloud/{id}', [CloudExternalC::class, 'cloud'])->name('external.cloud')->middleware('auth');
Route::post('/external/cloud/anexos', [CloudExternalC::class, 'list'])->name('external.cloud.anexos')->middleware('auth');
Route::post('/external/cloud/upload', [CloudExternalC::class, 'upload'])->name('external.cloud.upload')->middleware('auth');
Route::post('/external/cloud/delete', [CloudExternalC::class, 'delete'])->name('external.cloud.delete')->middleware('auth');

//ROUTE file / EXPEDIENTES
Route::get('/file/list', [FileC::class, 'list'])->name('file.list')->middleware('auth');
Route::post('/file/table', [FileC::class, 'table'])->name('file.table')->middleware('auth');
Route::get('/file/create', [FileC::class, 'create'])->name('file.create')->middleware('auth');
Route::get('/file/edit/{id}', [FileC::class, 'edit'])->name('file.edit')->middleware('auth');
Route::post('/file/save', [FileC::class, 'save'])->name('file.save')->middleware('auth');
Route::get('/file/cloud/{id}', [FileC::class, 'cloud'])->name('file.cloud')->middleware('auth');
Route::post('/file/cloud/data', [CloudFileC::class, 'cloudData'])->name('file.cloud.data')->middleware('auth');
Route::post('/file/cloud/anexos', [CloudFileC::class, 'cloudAnexos'])->name('file.cloud.anexos')->middleware('auth');
Route::post('/file/cloud/oficios', [CloudFileC::class, 'cloudOficios'])->name('file.cloud.oficios')->middleware('auth');
Route::post('/file/cloud/upload', [CloudFileC::class, 'upload'])->name('file.cloud.upload')->middleware('auth');
Route::post('/file/cloud/delete', [CloudFileC::class, 'delete'])->name('file.cloud.delete')->middleware('auth');
Route::get('/file/generate-pdf/{id}', [ReporteTemplateC::class, 'file'])->middleware('auth');

//Communication
Route::get('/communication/list', [CommunicationC::class, 'list'])->name('communication.list')->middleware('auth');
Route::post('/communication/table', [CommunicationC::class, 'table'])->name('communication.table')->middleware('auth');
Route::get('/communication/create', [CommunicationC::class, 'create'])->name('communication.create')->middleware('auth');
Route::post('/communication/area', [CollectionAreaInternoC::class, 'list'])->name('communication.area')->middleware('auth');
Route::post('/communication/noOficio', [CollectionIteradorInternoC::class, 'refreshNoOficio'])->name('communication.noOficio')->middleware('auth');
Route::post('/communication/save', [CommunicationC::class, 'save'])->name('communication.save')->middleware('auth');
Route::get('/communication/edit/{id}', [CommunicationC::class, 'edit'])->name('communication.edit')->middleware('auth');
Route::post('/communication/updateOficio', [CommunicationC::class, 'updateOficio'])->name('communication.updateOficio')->middleware('auth');
Route::post('/communication/updateAcuse', [CommunicationC::class, 'updateAcuse'])->name('communication.updateAcuse')->middleware('auth');
Route::post('/communication/addOficio', [CommunicationC::class, 'addOficio'])->name('communication.addOficio')->middleware('auth');
Route::post('/communication/addAcuse', [CommunicationC::class, 'addAcuse'])->name('communication.addAcuse')->middleware('auth');

// REQUEST
Route::get('/request/list', [RequestC::class, 'list'])->name('request.list')->middleware('auth');
Route::post('/request/table', [RequestC::class, 'table'])->name('request.table')->middleware('auth');
Route::get('/request/create', [RequestC::class, 'create'])->name('request.create')->middleware('auth');
Route::post('/request/noOficio', [CollectionIteradorInternoC::class, 'refreshNoRequerimiento'])->name('request.noOficio')->middleware('auth');
Route::post('/request/save', [RequestC::class, 'save'])->name('request.save')->middleware('auth');
Route::get('/request/edit/{id}', [RequestC::class, 'edit'])->name('request.edit')->middleware('auth');
Route::post('/request/saveFile', [RequestC::class, 'saveFile'])->name('request.saveFile')->middleware('auth');
Route::post('/request/deleteFile', [RequestC::class, 'deleteFile'])->name('request.deleteFile')->middleware('auth');

// INFORMATIVE
Route::get('/informative/list', [InformativeC::class, 'list'])->name('informative.list')->middleware('auth');
Route::post('/informative/table', [InformativeC::class, 'table'])->name('informative.table')->middleware('auth');
Route::get('/informative/create', [InformativeC::class, 'create'])->name('informative.create')->middleware('auth');
Route::post('/informative/noOficio', [CollectionIteradorInternoC::class, 'refreshNoInformativo'])->name('informative.noOficio')->middleware('auth');
Route::post('/informative/save', [InformativeC::class, 'save'])->name('informative.save')->middleware('auth');
Route::get('/informative/edit/{id}', [InformativeC::class, 'edit'])->name('informative.edit')->middleware('auth');
Route::post('/informative/saveFile', [InformativeC::class, 'saveFile'])->name('informative.saveFile')->middleware('auth');
Route::post('/informative/deleteFile', [InformativeC::class, 'deleteFile'])->name('informative.deleteFile')->middleware('auth');

// INFORMATIVE
Route::get('/certification/list', [CertificationC::class, 'list'])->name('certification.list')->middleware('auth');
Route::post('/certification/table', [CertificationC::class, 'table'])->name('certification.table')->middleware('auth');
Route::post('/certification/saveFile', [CertificationC::class, 'saveFile'])->name('certification.saveFile')->middleware('auth');
Route::post('/certification/deleteFile', [CertificationC::class, 'deleteFile'])->name('certification.deleteFile')->middleware('auth');


/// GLOBAL DE CORRESPONDENCIA
// SOLICTANTES -> AGREGAR UNO NUEVO
Route::post('/solicitante/add', [CollectionSolicitanteC::class, 'addSolcitante'])->name('solicitante.add')->middleware('auth');

//ALFRESCO -> Descargar archivo
Route::post('/cloud/download', [AlfrescoC::class, 'download'])->name('cloud.download')->middleware('auth');
//ALFRESCO -> ver archivo
Route::post('/cloud/see', [AlfrescoC::class, 'see'])->name('cloud.see')->middleware('auth');
// ALFRESCO DELETE
Route::post('/cloud/delete', [AlfrescoC::class, 'delete'])->name('cloud.delete')->middleware('auth');
//Collection
Route::post('/year/getYear', [CollectionYearC::class, 'getYear'])->name('year.getYear')->middleware('auth');
// CONSECUTIVO DE AREA ->
ROUTE::post('/collection/area/consecutivo', [CollectionAreaC::class, 'areaAutoincrement'])->middleware('auth');
//GENERACION DE REPORTE
Route::get('/other/generate-pdf/office/{id}', [ReporteCorrespondenciaC::class, 'generatePdf'])->middleware('auth');
// VALIDACION DE NO DE CORRESPONDENCIA
Route::post('/collection/validate/letter', [CollectionAreaC::class, 'getletter'])->middleware('auth');
// TRAE INFORMACION COMO EL NO DE CORRESPONDENCIA QUE EXISTA ASI COMO USUAIRO
Route::post('/valitade/letter', [LetterC::class, 'getletter'])->middleware('auth');

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



//ROUTE OFICIOS
Route::get('/office/list', [OfficeC::class, 'list'])->name('office.list')->middleware('auth');
Route::post('/office/table', [OfficeC::class, 'table'])->name('office.table')->middleware('auth');
Route::get('/office/create', [OfficeC::class, 'create'])->name('office.create')->middleware('auth');
Route::get('/office/edit/{id}', [OfficeC::class, 'edit'])->name('office.edit')->middleware('auth');
Route::post('/office/save', [OfficeC::class, 'save'])->name('office.save')->middleware('auth');
Route::get('/office/cloud/{id}', [OfficeC::class, 'cloud'])->name('office.cloud')->middleware('auth');
Route::post('/office/cloud/data', [CloudC::class, 'cloudData'])->name('office.cloud.data')->middleware('auth');
Route::post('/office/cloud/anexos', [CloudC::class, 'cloudAnexos'])->name('office.cloud.anexos')->middleware('auth');
Route::post('/office/cloud/oficios', [CloudC::class, 'cloudOficios'])->name('office.cloud.oficios')->middleware('auth');
Route::post('/office/cloud/upload', [CloudC::class, 'upload'])->name('office.cloud.upload')->middleware('auth');
Route::post('/office/cloud/delete', [CloudC::class, 'delete'])->name('office.cloud.delete')->middleware('auth');
Route::get('/office/generate-pdf/{id}', [ReporteTemplateC::class, 'office'])->middleware('auth');
Route::post('/office/validate/folGestion', [OfficeC::class, 'validateFol'])->name('office.validate.folGestion')->middleware('auth');

//ROUTE_COUSER ---- >Alfresco
Route::get('/alfresco/upload', [AlfrescoC::class, 'showUploadForm'])->name('alfresco.upload.form');// Ruta para mostrar el formulario de carga de archivo
Route::post('/upload-file', [AlfrescoC::class, 'uploadFile'])->name('alfresco.upload.file');// Ruta para manejar la carga de archivo

//ROUTE_COUSER ---- >Tabla Cursos
Route::get('/tablecourses/list', TblCoursesC::class)->name('tablecourses.list')->middleware('auth');
Route::post('/tablecourses/table', [TblCoursesC::class, 'searchTable']);
Route::get('/tablecourses/create', [TblCoursesC::class, 'create'])->name('tablecourses.create')->middleware('auth');

//ROUTE ADMINISTRACION
Route::get('/administration', AdministrationC::class)->name('administration.dashboard')->middleware('auth');

//ROUTE CATALOGO AREA
Route::get('/area/list', AreaC::class)->name('administration.list')->middleware('auth');
Route::get('/area/create', [AreaC::class, 'create'])->name('administration.create')->middleware('auth');
Route::post('/area/save', [AreaC::class, 'save'])->name('administration.save')->middleware('auth');
Route::post('/area/table', [AreaC::class, 'searchTable'])->middleware('auth');
Route::get('/area/edit/{id}', [AreaC::class, 'edit'])->name('administration.edit')->middleware('auth');

//ROUTE CATALOGO DEPENDENCIA
Route::get('/dependencia/list', DependenciaC::class)->name('dependencia.list')->middleware('auth');
Route::get('/dependencia/create', [DependenciaC::class, 'create'])->name('dependencia.create')->middleware('auth');
Route::post('/dependencia/save', [DependenciaC::class, 'save'])->name('dependencia.save')->middleware('auth');
Route::post('/dependencia/table', [DependenciaC::class, 'searchTable'])->middleware('auth');
Route::get('/dependencia/edit/{id}', [DependenciaC::class, 'edit'])->name('dependencia.edit')->middleware('auth');

//ROUTE CATALOGO DEPENDENCIA_AREA
Route::get('/dependenciarea/list', DependenciareaC::class)->name('dependenciarea.list')->middleware('auth');
Route::get('/dependenciarea/create', [DependenciareaC::class, 'create'])->name('dependenciarea.create')->middleware('auth');
Route::post('/dependenciarea/save', [DependenciareaC::class, 'save'])->name('dependenciarea.save')->middleware('auth');
Route::post('/dependenciarea/table', [DependenciareaC::class, 'searchTable'])->middleware('auth');
Route::get('/dependenciarea/edit/{id}', [DependenciareaC::class, 'edit'])->name('dependenciarea.edit')->middleware('auth');

//ROUTE CATALOGO REL_DEPENDENCIA_AREA
Route::get('rel/dependenciarea/list', ReldependenciaC::class)->name('reldependenciarea.list')->middleware('auth');
Route::get('rel/dependenciarea/create', [ReldependenciaC::class, 'create'])->name('reldependenciarea.create')->middleware('auth');
Route::post('rel/dependenciarea/save', [ReldependenciaC::class, 'save'])->name('reldependenciarea.save')->middleware('auth');
Route::post('rel/dependenciarea/table', [ReldependenciaC::class, 'searchTable'])->middleware('auth');
Route::get('rel/dependenciarea/edit/{id}', [ReldependenciaC::class, 'edit'])->name('reldependenciarea.edit')->middleware('auth');





// ENVIO DE CORREO ELECTRONICO
Route::post('/letter/email', [EmailC::class, 'emailLetter'])->middleware('auth');
// CONSULTA DE USUARIO, ENLACE Y AREA
Route::post('/collection/areaAndUser', [CollectionAreaC::class, 'getUserArea'])->middleware('auth');
