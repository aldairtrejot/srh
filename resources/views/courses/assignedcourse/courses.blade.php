<!-- TEMPLATE APP-->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- token html-->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Gestión de control</h3>
                            <h5 class="font-weight-normal mb-0">Alumnos</h5>
                        </div>
                    </div>
                    <x-template-tittle.tittle-caption
                     tittle="{{ isset($item->id_tbl_cursos) ? 'Modificar' : '' }} "
                     route="{{ route('assignedcourse.list') }}"
                        />                 
                </div>           
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Grupo Alumnos</h4>
                                    @if($coursesMatch)
                                    <p class="card-description">
                                            ¿Deseas agregar un curso? 
                                            <a href="{{ route('tablecourses.list') }}" class="text-danger" style="margin-left: 10px;">
                                                <i class="fa fa-arrow-up"></i> Agregar Curso
                                            </a>
                                            </p>
                                    @endif
                                </div>
                                <div class="input-group" style="max-width: 300px;">
                                <!-- TEMPLATE SEARCH-->
                                <x-template-table.template-search />
                            </div>
                        </div>
                     <!-- Alumno id -->
                     <input type="hidden" id="idEmpleadoCursos" value="{{ $idEmpleadoCursos }}">
                        <!-- TEMPLATE TABLE -->
                        <x-template-table.template-table>
                            <thead>
                                <tr>
                                    <th>MENU</th>
                                    <th>NOMBRE DE CURSO</th>
                                    <th>TIPO DE CURSO</th>
                                    <th>HORAS</th>
                                    <th>ESTATUS</th>
                                    <th>FECHA</th>
                                    <th>FECHA FIN</th>
                                    <th>CALIFICACION</th>
                                </tr>
                            </thead>
                        </x-template-table.template-table>
                        <!-- TEMPLATE PAGINATOR-->
                        <x-template-table.template-paginator />
                    </div>
                </div>
            </div>
        </div>
    </div>
 <!-- CODE SCRIPT-->
  <script src="{{ asset('assets/js/app/courses/assignedcourse/courses.js') }}"></script>
</x-template-app.app-layout>

   