<!-- TEMPLATE APP-->
<?php include resource_path('views/config.php'); ?>
<x-template-app.app-layout>

    <x-template-form.template-form-input-hidden name="bool_user_role" value="{{ $letterAdminMatch }}" />
    <style></style>

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Control de gestión</h3>
                            <h5 class="font-weight-normal mb-0">Corresponencia</h5>
                        </div>
                        <div class="col-12 col-xl-4 text-xl-right">
                            <button onclick="openModal();" type="button" class="btn btn-link" id="reporteBtn">
                                <span class="font-weight-bold" style="color: #10312b;">Informe</span>
                                <i class="ti-layout" style="color: #10312b;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @include('letter.letter.modal')
            @include('letter.dashboard.modal')
            @include('letter.letter.replymodal')

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Correspondencia</h4>
                                @if ($letterAdminMatch)
                                    <p class="card-description">
                                        ¿Deseas agregar un registro?
                                        <a href="{{ route('letter.create') }}" class="text-danger" style="margin-left: 10px;">
                                            <i class="fa fa-arrow-up"></i> Agregar Registro
                                        </a>
                                    </p>
                                @endif
                            </div>

                            <div class="input-group" style="max-width: 300px;">
                                <x-template-table.template-search />
                            </div>
                        </div>

                        <div id="progress-bar" class="dark-progress-hidden">
                            <div class="dark-progress-bar"></div>
                        </div>

                        <!-- TEMPLATE TABLE -->
                        <x-template-table.template-table>
                            <thead>
                                <tr>
                                    <th>Menú</th>
                                    <th>Estatus</th>
                                    <th>Fecha de captura</th>
                                    <th>Fólio de gestión</th>
                                    <th>No. Documento</th>
                                    <th>Área</th>
                                    <th>CRH</th>
                                    <th>CRHTOD</th>
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

    <x-template-other.app-mail />

    <!-- CODE SCRIPT-->
    <script src="{{ asset('assets/js/app/letter/function/email.js') }}"></script>
    <script src="{{ asset('assets/js/app/template/template-dropdown.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/letter/table.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/letter/tableCopy.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/letter/modal.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/letter/reply.js') }}"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('assets/js/app/letter/dashboard/report.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/dashboard/validate.js') }}"></script>

</x-template-app.app-layout>


