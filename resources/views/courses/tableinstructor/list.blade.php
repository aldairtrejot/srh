<!-- TEMPLATE APP -->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Gestión de Instructores</h3>
                            <h5 class="font-weight-normal mb-0">Lista de Instructores</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Instructores</h4>
                                <p class="card-description">
                                    ¿Deseas agregar un registro? <a href="{{ route('tableinstructor.create') }}"
                                        class="text-danger" style="margin-left: 10px;">
                                        <i class="fa fa-plus"></i> Agregar Instructor
                                    </a>
                                </p>
                            </div>
                            <div class="input-group" style="max-width: 300px;">
                                <!-- TEMPLATE SEARCH-->
                                <x-template-table.template-search />
                            </div>
                        </div>

                        <!-- TEMPLATE TABLE -->
                        <x-template-table.template-table>
                            <thead>
                                <tr>
                                    <th>MENÚ</th>
                                    <th>CURP</th>
                                    <th>NOMBRE</th>
                                    <th>TOTAL CONSTANCIAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($instructores as $instructor)
                                    <tr>
                                        <td>MENÚ</td>
                                        <td>{{ $instructor->curp }}</td>
                                        <td>{{ $instructor->nombre_completo }}</td>
                                        <td>{{ $instructor->total_constancias }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-template-table.template-table>

                       <!-- TEMPLATE PAGINATOR-->
                       <x-template-table.template-paginator />
                       

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- CODE SCRIPT -->
    <script src="{{ asset('assets/js/app/courses/tableinstructor/table.js') }}"></script>
</x-template-app.app-layout>
