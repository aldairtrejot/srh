<!-- TEMPLATE APP-->
<?php include(resource_path('views/config.php')); ?>

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

                        <x-template-form.template-form-input-hidden name="id_tbl_cv" value="{{ $idInstructor ?? '' }}" />

                        <!-- Contenedor principal con flexbox -->
                        <div class="main-container">
                            <!-- Lado izquierdo (Documentos de Entrada) -->
                            <div class="left-side">
                                <br>
                                <p class="card-description" style="font-size: 1rem; font-weight: bold; color: #BC955C; font-style: italic;">
                                    Documentos del Instructor
                                </p>

                                <!-- Cargar CV -->
                                <div>
                                <x-template-tittle.tittle-caption-secon tittle="CV (Máx 1)" />
                                <label for="file_cv_entrada" id="label_cv_entrada" class="upload-label"
                                style="color: red !important; font-weight: normal; font-size: 1rem; 
                                padding: 5px 15px; cursor: pointer; display: flex; align-items: center; text-decoration: none;">
        
                                <i class="fa fa-arrow-up" id="icon_cv_entrada"></i> 
                                <span style="color: red !important;">Cargar</span>
                                </label>

                                <input type="file" id="file_cv_entrada" style="display: none;" accept=".pdf,.docx,.jpg,.png">
                                <div id="container_cv_entrada_vacio" class="rectangulo">Sin contenido</div>
                                <div id="container_cv_entrada"></div>
                                </div>


                                <!-- Cargar Constancias -->
                                <div>
                                <x-template-tittle.tittle-caption-secon tittle="Constancias (Máx 1)" />
                                <label for="file_constancia_entrada" id="label_constancia_entrada" 
                                style="background-color: white; color: red !important; font-weight: normal; font-size: 1rem; 
                                padding: 5px 15px; cursor: pointer; display: flex; align-items: center; text-decoration: none;">
    
                                <i class="fa fa-arrow-up" id="icon_constancia_entrada"></i> 
                                <span style="color: red !important;">Cargar</span>
                                </label>

                                <input type="file" id="file_constancia_entrada" style="display: none;" accept=".pdf,.docx,.jpg,.png">
                                <div id="container_constancia_entrada_vacio" class="rectangulo">Sin contenido</div>
                                <div id="container_constancia_entrada"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Código JavaScript -->
    <script defer src="{{ asset('assets/js/app/courses/tableinstructor/cloud.js') }}"></script>
</x-template-app.app-layout>