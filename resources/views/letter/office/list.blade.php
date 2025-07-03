<!-- TEMPLATE APP -->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Control de gestión</h3>
                    <h5 class="font-weight-normal mb-0">Oficios</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-link" onclick="openModal()" style="color: #10312b;">
            <span class="font-weight-bold">Informe</span>
            <i class="ti-layout"></i>
        </button>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">Oficios</h4>
                        <p class="card-description">
                            ¿Deseas agregar un registro?
                            <a href="{{ route('office.create') }}" class="text-danger" style="margin-left: 10px;">
                                <i class="fa fa-arrow-up"></i> Agregar Registro
                            </a>
                        </p>
                    </div>
                    <div class="input-group" style="max-width: 300px;">
                        <x-template-table.template-search />
                    </div>
                </div>

                <x-template-table.template-table>
                    <thead>
                        <tr>
                            <th>Menú</th>
                            <th>Estatus</th>
                            <th>Año</th>
                            <th>No. Turno Asoc. / Fol. Gestión Asoc.</th>
                            <th>Asunto</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                </x-template-table.template-table>

                <x-template-table.template-paginator />
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const reporteURL = "{{ route('dashboardoffice.generate') }}";
    const catalogosURL = "{{ route('dashboardoffice.catalogos') }}";
</script>

<script src="{{ asset('assets/js/app/letter/office/table.js') }}"></script>
<script src="{{ asset('assets/js/app/template/template-dropdown.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/office/report.js') }}"></script>

@include('letter.office.modal')
</x-template-app.app-layout>
