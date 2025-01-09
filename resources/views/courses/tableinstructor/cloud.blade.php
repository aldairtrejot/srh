<!-- TEMPLATE APP-->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- token html-->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Gestión de Instructores</h3>
                            <h5 class="font-weight-normal mb-0">Archivos en la Nube</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <div>
                            <x-template-tittle.tittle-caption tittle="Cloud" route="{{ route('tableinstructor.list') }}" />

                            <x-template-form.template-form-input-hidden name="id_instructor"
                                value="{{  $id_instructor }}" />

                            <x-template-tittle.tittle-caption-secon tittle="Instructor Seleccionado" />
                            <div class="contenedor">
                                <div class="item">
                                    <label class="etiqueta">Nombre:</label>
                                    <label id="_nombreInstructor" class="valor"></label>
                                </div>
                                <div class="item">
                                    <label class="etiqueta">Estatus:</label>
                                    <label id="_estatusInstructor" class="valor"></label>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de confirmación para eliminar -->
                        <x-template-form.template-form-delete tittleModal="modalBackdrop" cancelModal="cancelBtn"
                            confirmButton="confirmBtn" />

                        <!-- Contenedor principal con flexbox -->
                        <div class="main-container">
                            <!-- Lado izquierdo -->
                            <div class="left-side">
                                <br>
                                <p class="card-description"
                                    style="font-size: 1rem; font-weight: bold; color: #BC955C; font-style: italic;">
                                    CVs
                                </p>

                                <div>
                                    <div style="display: flex; align-items: center;">
                                        <x-template-tittle.tittle-caption-secon tittle="Subir CV" />
                                        <label for="file_cv" id="label_cv"
                                            style="background-color: white; color: red; font-weight: normal; font-size: 1rem; padding: 5px 15px; cursor: pointer; display: flex; align-items: center; text-decoration: none;">
                                            <i class="fa fa-arrow-up" id="icon_cv"
                                                style="margin-right: 5px;"></i>
                                            Cargar
                                        </label>
                                        <input type="file" id="file_cv" style="display: none;">
                                    </div>

                                    <div id="container_cv_vacio" class="rectangulo">
                                        Sin contenido
                                    </div>
                                    <div id="container_cv"></div>
                                </div>
                            </div>

                            <!-- Lado derecho -->
                            <div class="right-side">
                                <br>
                                <p class="card-description"
                                    style="font-size: 1rem; font-weight: bold; color: #BC955C; font-style: italic;">
                                    Constancias
                                </p>

                                <div>
                                    <div style="display: flex; align-items: center;">
                                        <x-template-tittle.tittle-caption-secon tittle="Subir Constancia" />
                                        <label for="file_constancia" id="label_constancia"
                                            style="background-color: white; color: red; font-weight: normal; font-size: 1rem; padding: 5px 15px; cursor: pointer; display: flex; align-items: center; text-decoration: none;">
                                            <i class="fa fa-arrow-up" id="icon_constancia"
                                                style="margin-right: 5px;"></i>
                                            Cargar
                                        </label>
                                        <input type="file" id="file_constancia" style="display: none;">
                                    </div>
                                    <div id="container_constancia_vacio" class="rectangulo">
                                        Sin contenido
                                    </div>
                                    <div id="container_constancia"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CODE SCRIPT-->
        <script src="{{ asset('assets/js/app/instructors/cloud.js') }}"></script>
    </div>
</x-template-app.app-layout>
