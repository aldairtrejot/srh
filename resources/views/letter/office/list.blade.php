<!-- TEMPLATE APP-->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Control de correspondencia</h3>
                            <h5 class="font-weight-normal mb-0">Oficios</h5>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .custom-button[disabled] {
                    cursor: not-allowed;
                }
            </style>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Oficios</h4>
                                @if($letterAdminMatch)
                                    <p class="card-description">
                                        ¿Deseas agregar un registro? <a href="{{ route('office.create') }}"
                                            class="text-danger" style="margin-left: 10px;">
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

                        <style>
                            /*
                            .dropdown-menu {
                                max-height: 100px;
                                overflow-y: auto;
                               
                            }*/
                        </style>

                        <!-- TEMPLATE TABLE -->
                        <x-template-table.template-table>
                            <thead>
                                <tr>
                                    <th>
                                        Menú
                                    </th>
                                    <th>
                                        No. Turno
                                    </th>
                                    <th>
                                        No. Correspondencia Asoc.
                                    </th>
                                    <th>
                                        Área
                                    </th>
                                    <th>
                                        Fecha de Inicio
                                    </th>
                                    <th>
                                        Fecha Fin
                                    </th>
                                    <th>
                                        Año
                                    </th>
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
    <script src="{{ asset('assets/js/app/letter/office/table.js') }}"></script>

</x-template-app.app-layout>