<x-template-app.app-layout>

    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Token CSRF -->

    <!-- Metaetiquetas para rutas de JavaScript -->
    <meta name="route-cloud-data" content="{{ route('tableinstructor.cloud.data') }}">
    <meta name="route-cloud-upload" content="{{ route('tableinstructor.cloud.upload') }}">
    <meta name="route-cloud-delete" content="{{ route('tableinstructor.cloud.delete') }}">
    <meta name="route-cloud-see" content="{{ route('tableinstructor.cloud.see') }}">
    <meta name="route-cloud-download" content="{{ route('tableinstructor.cloud.download', ['uuid' => '__UUID__']) }}">


    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <h3 class="font-weight-bold">Gestión de control</h3>
                    <h5 class="font-weight-normal mb-0">Carga de Documentos</h5>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption tittle="Cloud" route="{{ route('tableinstructor.list') }}" />

                        <input type="hidden" id="id_tbl_cv" value="{{ $idInstructor ?? '' }}">
                        <p>ID Instructor en Blade: <strong>{{ $idInstructor ?? 'No disponible' }}</strong></p>

                        <!-- Cargar CV -->
                        <div>
                            <x-template-tittle.tittle-caption-secon tittle="CV (Máx 1)" />
                            <label for="file_cv_entrada" id="label_cv_entrada" class="upload-label">
                                <i class="fa fa-arrow-up" id="icon_cv_entrada"></i> Cargar
                            </label>
                            <input type="file" id="file_cv_entrada" style="display: none;" accept=".pdf,.docx,.jpg,.png">
                            <div id="container_cv_entrada_vacio" class="rectangulo">Sin contenido</div>
                            <div id="container_cv_entrada"></div>
                        </div>

                        <!-- Cargar Constancias -->
                        <div>
                            <x-template-tittle.tittle-caption-secon tittle="Constancias (Máx 1)" />
                            <label for="file_constancia_entrada" id="label_constancia_entrada" class="upload-label">
                                <i class="fa fa-arrow-up" id="icon_constancia_entrada"></i> Cargar
                            </label>
                            <input type="file" id="file_constancia_entrada" multiple style="display: none;" accept=".pdf,.docx,.jpg,.png">
                            <div id="container_constancia_entrada_vacio" class="rectangulo">Sin contenido</div>
                            <div id="container_constancia_entrada"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Código JavaScript -->
    <script defer src="{{ asset('assets/js/app/courses/tableinstructor/cloud.js') }}"></script>

</x-template-app.app-layout>
