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
                                        ¿Deseas agregar un registro? 
                                        <a href="{{ route('assignedcourse.create') }}" class="text-danger" style="margin-left: 10px;">
                                            <i class="fa fa-arrow-up"></i> Agregar Alumno
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
                                    <th>MENU</th>
                                    <th>CURP</th>
                                    <th>RFC</th>
                                    <th>PATERNO</th>
                                    <th>MATERNO</th>
                                    <th>NOMBRE</th>
                                </tr>
                            </thead>
                        </x-template-table.template-table>
<!-- modal delete -->
<x-template-form.template-form-delete tittleModal="modalBackdrop" cancelModal="cancelBtn"
                            confirmButton="confirmBtn" />

<!-- Modal de Confirmación -->
<div id="modalBackdrop" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>¿Estás seguro de que deseas eliminar este instructor?</p>
        <button id="confirmBtn">Confirmar</button>
        <button id="cancelBtn">Cancelar</button>
    </div>
</div>


                        <!-- TEMPLATE PAGINATOR-->
                        <x-template-table.template-paginator />

                    </div>
                </div>
            </div>

        </div>
    </div>
 
 <!-- CODE SCRIPT-->
 <script src="{{ asset('assets/js/app/courses/assignedcourse/table.js') }}"></script>
</x-template-app.app-layout>

   