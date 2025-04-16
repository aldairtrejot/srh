<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        const routeCursosActivos = "{{ route('assignedcourse.cursos.activos') }}";
    </script>

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
                        tittle="{{ isset($item->id_tbl_cursos) ? 'Modificar' : '' }}"
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
                                <p class="card-description">Selecciona un curso para continuar</p>
                            </div>
                            <div class="input-group" style="max-width: 300px;">
                                <x-template-table.template-search />
                            </div>
                        </div>

                        <form method="POST" action="{{ route('assignedcourse.enroll') }}">
                            @csrf
                            <input type="hidden" name="id_empleado_cursos" value="{{ $idEmpleadoCursos }}">

                            <x-template-table.template-table>
                                <thead>
                                    <tr>
                                        <th>SELECCIONAR</th>
                                        <th>NOMBRE DEL CURSO</th>
                                        <th>FECHA INICIO</th>
                                        <th>FECHA FIN</th>
                                    </tr>
                                </thead>
                                <tbody id="cursosBody">
                                    <!-- Se llena con JS -->
                                </tbody>
                            </x-template-table.template-table>

                            <div class="text-center mt-4">
                                <button type="submit" id="btnInscripcionCurso" style="background-color: #10312B; border: none; color: white; padding: 12px 30px; font-size: 16px; border-radius: 6px; cursor: pointer;">
                                    <i class="fa fa-graduation-cap" style="margin-right: 8px;"></i> Inscribirme
                                </button>
                            </div>
                        </form>

                        <x-template-table.template-paginator />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/app/courses/assignedcourse/selectcourses.js') }}"></script>
</x-template-app.app-layout>
