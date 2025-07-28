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
                            <h3 class="font-weight-bold">Gestion</h3>
                            <h5 class="font-weight-normal mb-0">Documentos</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Expediente</h4>
                                @if($letterAdminMatch)
                                    <p class="card-description">
                                        ¿Deseas agregar un expediente?
                                        <a href="" class="text-danger" style="margin-left: 10px;">
                                            <i class="fa fa-arrow-up"></i> Agregar Expediente
                                        </a>
                                    </p>
                                @endif
                            </div>
                            <div class="input-group" style="max-width: 300px;">
                                <x-template-table.template-search />
                            </div>
                        </div>

                        <x-template-table.template-table>
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Folio Documento</th>
                                    <th>Fecha Registro</th>
                                    <th>Observaciones</th>
                                    <th>Ubicacion</th>
                                    <th>Archivo</th>
                                    <th>Es fisico?</th>
                                    <th>Posicion</th>
                                    <th>Fecha ultima ubicacion</th>
                                    <th>Empleado</th>
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
            <p>¿Estás seguro de que deseas eliminar este documento?</p>
            <button id="confirmDeleteBtn" class="btn btn-danger">Eliminar</button>
            <button id="cancelDeleteBtn" class="btn btn-secondary">Cancelar</button>
        </div>
    </div>

    <!-- Script principal -->
    <script src="/srh/public/assets/js/app/files/filesgestion/table.js"></script>
</x-template-app.app-layout>
