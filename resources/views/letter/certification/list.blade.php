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
                            <h3 class="font-weight-bold">Coordinación de Recursos Humanos</h3>
                            <h5 class="font-weight-normal mb-0">Certificaciones</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Certificaciones</h4>
                                <p class="card-description">
                                    ¿Deseas agregar un registro? <a href="{{ route('informative.create') }}"
                                        class="text-danger" style="margin-left: 10px;">
                                        <i class="fa fa-arrow-up"></i> Agregar Registro
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
                                    <th>
                                        Menú
                                    </th>
                                    <th>
                                        No. Certificación
                                    </th>
                                    <th>
                                        Fecha de asignación
                                    </th>
                                    <th>
                                        Asunto
                                    </th>
                                    <th>
                                        Archivo
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

    <!-- CODE DELETE OFICIO-->
    <x-template-modal.modal-delete tittleModal="id_modal_delete_oficio" idInput="id_uuid_oficio" valueInput=""
        cancelModal="id_modal_calcel_oficio" confirmButton="" functionConfirm="confirmModalOficio();" />

    <!-- item with add -->
    <input type="file" class="file-input-oficio" style="display: none;" />
    <input type="text" id="id_oficio" style="display: none;" />

    <!-- CODE SCRIPT-->
    <script src="{{ asset('assets/js/app/template/template-dropdown.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/certification/table.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/cloud/cloud.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/certification/cloud.js') }}"></script>

</x-template-app.app-layout>