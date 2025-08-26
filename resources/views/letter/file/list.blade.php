<!-- TEMPLATE APP-->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-template-form.template-form-input-hidden name="bool_user_role" value="{{  $letterAdminMatch }}" />

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Control de gestión</h3>
                            <h5 class="font-weight-normal mb-0">Lineamientos</h5>
                        </div>
                        @if($letterAdminMatch)
                            <div class="col-12 col-xl-4 text-xl-right">
                                <button class="btn btn-link" onclick="openModal()" style="color: #10312b;">
                                    <span class="font-weight-bold">Informe</span>
                                    <i class="ti-layout" style="color: #10312b;"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Lineamientos</h4>
                                @if($letterAdminMatch)
                                    <p class="card-description">
                                        ¿Deseas agregar un registro?
                                        <a href="{{ route('file.create') }}" class="text-danger" style="margin-left: 10px;">
                                            <i class="fa fa-arrow-up"></i> Agregar Registro
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
                                    <th>Menú</th>
                                    <th>Año</th>
                                    <th>No. Turno</th>
                                    <th>No. Turno Asoc.</th>
                                    <th>Asunto</th>
                                </tr>
                            </thead>
                        </x-template-table.template-table>

                        <x-template-table.template-paginator />
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('letter.file.modal')

    <script>
        const catalogosURL = "{{ route('expedientoffice.catalogos') }}";
        const reporteURL = "{{ route('expedientoffice.generate') }}";
    </script>
    <!-- CODE SCRIPT -->
    <script src="{{ asset('assets/js/app/template/template-dropdown.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/file/table.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/file/report.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-template-app.app-layout>