<!-- TEMPLATE APP-->
<?php include resource_path('views/config.php'); ?>
<x-template-app.app-layout>

    <x-template-form.template-form-input-hidden name="bool_user_role" value="{{ $letterAdminMatch }}" />

    <style>
        #dropdownColumnToggle {
            background-color: #10312b;
            color: white;
            border: none;
        }
        #dropdownColumnToggle:hover {
            background-color: #15504e;
            color: white;
        }
        #columnToggleMenu {
            max-height: 300px;
            overflow-y: auto;
        }
        #columnToggleMenu label {
            font-size: 14px;
            cursor: pointer;
        }
        #columnToggleMenu input {
            margin-right: 6px;
        }

        /* Presentación simple para Cloud */
        .cloud-cell {
            white-space: nowrap;
            text-align: center;
        }
        .cloud-cell .icon-row {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            vertical-align: middle;
        }
        .cloud-cell i.fa-file { color: #707070; font-size: 18px; }
        .cloud-cell .btn-eye {
            border: none; border-radius: 6px; background: #10312b; padding: 6px 10px; cursor: pointer;
        }
        .cloud-cell .btn-eye i { color: #fff; font-size: 14px; }
    </style>

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

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <!-- Título arriba -->
                        <div class="mb-3">
                            <h4 class="card-title">Correspondencia</h4>
                        </div>

                        <!-- Fila con "¿Deseas agregar...?" + Buscador + Botón columnas -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <!-- Texto y botón agregar -->
                            <div class="card-description" style="margin-bottom: 0;">
                                @if ($letterAdminMatch)
                                    ¿Deseas agregar un registro?
                                    <a href="{{ route('letter.create') }}" class="text-danger ml-2">
                                        <i class="fa fa-arrow-up"></i> Agregar Registro
                                    </a>
                                @endif
                            </div>

                            <!-- Buscador y botón columnas -->
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <!-- Buscador -->
                                <div class="input-group" style="max-width: 300px;">
                                    <x-template-table.template-search />
                                </div>

                                <!-- Botón columnas -->
<!-- Botón columnas -->
<div class="dropdown">
  <button class="btn btn-sm dropdown-toggle shadow-sm" type="button"
          id="dropdownColumnToggle" data-toggle="dropdown" aria-expanded="false">
    <i class="fa fa-columns mr-1"></i> Mostrar columnas
  </button>
  <div class="dropdown-menu p-2 border shadow" id="columnToggleMenu" style="min-width: 250px;">
    <!-- AHORA TODAS DESMARCADAS POR DEFECTO -->
    <label class="dropdown-item">
      <input type="checkbox" class="toggle-column" data-column="6"> CRH
    </label>
    <label class="dropdown-item">
      <input type="checkbox" class="toggle-column" data-column="7"> CRHTOD
    </label>
    <label class="dropdown-item">
      <input type="checkbox" class="toggle-column" data-column="9"> Cloud
    </label>
    <label class="dropdown-item">
      <input type="checkbox" class="toggle-column" data-column="10"> Respuesta
    </label>
  </div>
</div>

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
                                    <!-- NUEVAS -->
                                    <th>Cloud</th>
                                    <th>Respuesta</th>
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

    <!-- Tu archivo de la tabla -->
    <script src="{{ asset('assets/js/app/letter/letter/table.js') }}"></script>

    <script src="{{ asset('assets/js/app/letter/letter/tableCopy.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/letter/modal.js') }}"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('assets/js/app/letter/dashboard/report.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/dashboard/validate.js') }}"></script>

</x-template-app.app-layout>
