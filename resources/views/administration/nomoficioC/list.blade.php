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
                            <h3 class="font-weight-bold">Catálogo</h3>
                            <h5 class="font-weight-normal mb-0">Nombre Oficio</h5>
                        </div>
                        <!-- Botón Regresar alineado a la derecha -->
                        <div class="col-12 col-xl-4 d-flex justify-content-end align-items-start">
                            <a href="{{ route('administration.dashboard') }}" class="btn btn-hover-enlarge"
                               style="font-size: 1.1rem; padding: 10px; background-color: #10312B; color: white; border-radius: 50%; border: none;"
                               data-bs-toggle="tooltip" data-bs-placement="top" title="Regresar">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Nombre Oficio</h4>
                                @if($coursesMatch ?? true) {{-- para evitar error si no existe --}}
                                    <p class="card-description">
                                        ¿Deseas agregar un registro? 
                                        <a href="{{ route('nomoficio.create') }}" class="text-danger" style="margin-left: 10px;">
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
                                    <th>Menu</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Estatus</th>
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

<!-- Modal de confirmación -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Confirmar Eliminación</h2>
        <p>¿Estás seguro de que deseas eliminar esta área?</p>
        <button id="confirmDeleteBtn" class="btn btn-danger">Eliminar</button>
        <button id="cancelDeleteBtn" class="btn btn-secondary">Cancelar</button>
    </div>
</div>

<!-- CODE SCRIPT-->
<script src="/srh/public/assets/js/app/letter/nomoficio/table.js"></script>
</x-template-app.app-layout>