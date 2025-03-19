<!-- TEMPLATE APP -->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Token CSRF -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Gestión de control</h3>
                            <h5 class="font-weight-normal mb-0">Cursos del Alumno</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Información de Cursos</h4>
                            </div>
                            <div class="input-group" style="max-width: 300px;">
                                <!-- TEMPLATE SEARCH -->
                                <x-template-table.template-search />
                            </div>
                        </div>

                        <!-- 🔹 Guardar ID del alumno en un campo oculto para usarlo en JavaScript -->
                        <input type="hidden" id="id_empleado_cursos" value="{{ $id_empleado_cursos ?? '' }}">

                    <!-- TEMPLATE TABLE -->
                            <x-template-table.template-table>
                                <thead>
                                <tr>
                                    <th>MENÚ</th>
                                    <th>NOMBRE DE CURSO</th>
                                    <th>TIPO DE CURSO</th>
                                    <th>HORAS</th>
                                    <th>ESTATUS</th>
                                    <th>FECHA INICIO</th>
                                    <th>FECHA FIN</th>
                                </tr>
                                </thead>
                                </x-template-table.template-table>

                        <!-- TEMPLATE PAGINATOR -->
                        <x-template-table.template-paginator />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CODE SCRIPT -->
    <script src="{{ asset('assets/js/app/courses/assignedcourse/courses.js') }}"></script>
</x-template-app.app-layout>
