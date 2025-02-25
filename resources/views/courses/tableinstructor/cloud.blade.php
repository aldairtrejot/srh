<!-- TEMPLATE APP -->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Token CSRF -->

    <!-- Metaetiquetas para rutas de JavaScript -->
    <meta name="route-cloud-data" content="{{ route('tableinstructor.cloud.data') }}">
    <meta name="route-cloud-cv" content="{{ route('tableinstructor.cloud.cv') }}">
    <meta name="route-cloud-cons" content="{{ route('tableinstructor.cloud.cons') }}">
    <meta name="route-cloud-upload" content="{{ route('tableinstructor.cloud.upload') }}">
    <meta name="route-cloud-delete" content="{{ route('tableinstructor.cloud.delete') }}">

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Gestión de control</h3>
                            <h5 class="font-weight-normal mb-0">Carga de Documentos</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption tittle="Cloud" route="{{ route('tableinstructor.list') }}" />

                        <!-- Contenedor principal -->
                        <div class="main-container d-flex">

                            <!-- Lado izquierdo -->
                            <div class="left-side">
                                <br>
                                <p class="card-description"
                                    style="font-size: 1rem; font-weight: bold; color: #BC955C; font-style: italic;">
                                    Adjuntar Archivos
                                </p>

                                <!-- Cargar CV -->
                                <div>
                                    <div style="display: flex; align-items: center;">
                                        <x-template-tittle.tittle-caption-secon tittle="CV (Max 1)" />
                                        <label for="file_cv_entrada" id="label_cv_entrada"
                                            style="background-color: white; color: red; font-weight: normal; font-size: 1rem; padding: 5px 15px; cursor: pointer; display: flex; align-items: center; text-decoration: none;">
                                            <i class="fa fa-arrow-up" id="icon_cv_entrada"
                                                style="margin-right: 5px;"></i>
                                            Cargar
                                        </label>
                                        <input type="file" id="file_cv_entrada" style="display: none;">
                                    </div>

                                    <div id="container_cv_entrada_vacio" class="rectangulo">Sin contenido</div>
                                    <div id="container_cv_entrada"></div>
                                </div>

                                <!-- Cargar Constancias -->
                                <div>
                                    <div style="display: flex; align-items: center;">
                                        <x-template-tittle.tittle-caption-secon tittle="Constancias (Max 3)" />
                                        <label for="file_constancia_entrada" id="label_constancia_entrada"
                                            style="background-color: white; color: red; font-weight: normal; font-size: 1rem; padding: 5px 15px; cursor: pointer; display: flex; align-items: center; text-decoration: none;">
                                            <i class="fa fa-arrow-up" id="icon_constancia_entrada"
                                                style="margin-right: 5px;"></i>
                                            Cargar
                                        </label>
                                        <input type="file" id="file_constancia_entrada" multiple style="display: none;">
                                    </div>
                                    <div id="container_constancia_entrada_vacio" class="rectangulo">Sin contenido</div>
                                    <div id="container_constancia_entrada"></div>
                                </div>
                            </div> <!-- Fin de left-side -->

                        </div> <!-- Fin de main-container -->

                    </div> <!-- Fin de card-body -->
                </div> <!-- Fin de card -->
            </div> <!-- Fin de grid-margin -->

        </div> <!-- Fin de content-wrapper -->
    </div> <!-- Fin de main-panel -->

    <!-- Código JavaScript -->
    <script src="{{ asset('assets/js/app/courses/tableinstructor/cloud.js') }}"></script>

</x-template-app.app-layout>
