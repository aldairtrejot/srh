<!-- TEMPLATE APP -->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row mb-4">
                <div class="col-md-12">
                    <h3 class="font-weight-bold text-primary">Carga Masiva de Alumnos</h3>
                    <p class="text-muted">Sube un archivo Excel o CSV para registrar múltiples alumnos en el sistema.</p>
                </div>
            </div>

            <x-template-tittle.tittle-caption
                tittle="{{ isset($item->id_tbl_cursos) ? 'Modificar' : '' }} "
                route="{{ route('assignedcourse.list') }}"
            />

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-body">
                            <h4 class="card-title mb-4"><i class="fa fa-upload text-primary"></i> Subir archivo</h4>

                            <form id="massUploadForm" method="POST" enctype="multipart/form-data" action="{{ route('assignedcourse.list') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="file" class="form-label">Selecciona un archivo Excel o CSV</label>
                                    <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" id="btnSubirArchivo" style="background-color: #10312B; border: none; color: white; padding: 12px 30px; font-size: 16px; border-radius: 6px; cursor: pointer;">
                                        <i class="fa fa-upload" style="margin-right: 8px;"></i> Subir archivo
                                    </button>
                                </div>
                            </form>

                            <div id="loadingIndicator" class="mt-4 text-center" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                <p class="mt-2">Procesando archivo...</p>
                            </div>

                            <hr class="mt-5">

                            <div id="massUploadResults" style="display: none;">
                                <h5 class="mb-3 text-secondary"><i class="fa fa-list-alt"></i> Resultado de la carga</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>CURP</th>
                                                <th>RFC</th>
                                                <th>Nombre</th>
                                                <th>Observación</th>
                                            </tr>
                                        </thead>
                                        <tbody id="resultBody"></tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
 <script src="{{ asset('assets/js/app/courses/assignedcourse/modal.js') }}"></script>
</x-template-app.app-layout>
