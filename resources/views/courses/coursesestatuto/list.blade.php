<!-- TEMPLATE APP-->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Catálogo</h3>
                            <h5 class="font-weight-normal mb-0">Cursos</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Estatuto Orgánico</h4>
                                @if($letterAdminMatch)
                                    <p class="card-description">
                                        ¿Deseas agregar un registro? 
                                        <a href="{{ route('coursesestatuto.create') }}" class="text-danger" style="margin-left: 10px;">
                                            <i class="fa fa-arrow-up"></i> Agregar Registro
                                        </a>
                                    </p>
                                @endif
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
                                    <th>Id_Estatuto_Organico</th>
                                    <th>Descripción</th>
                                    <th>Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coursesestatuto as $course)
                                    <tr>
                                        <td>{{ $course->id_estatuto_organico }}</td>
                                        <td>{{ $course->descripcion }}</td>
                                        <td>{{ $course->estatus ? 'Activo' : 'Inactivo' }}</td>
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


    <!-- CODE SCRIPT-->
    <script src="{{ asset('assets/js/app/courses/coursesestatuto/table.js') }}"></script>

</x-template-app.app-layout>