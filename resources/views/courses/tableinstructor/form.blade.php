<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Instructor" caption="Instructor" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_tbl_instructores) ? 'Modificar' : 'Agregar' }} Instructor"
                            route="{{ route('tableinstructor.list') }}" />

                     

                        <x-template-tittle.tittle-caption-secon tittle="Información de Usuario" />
                        
                        <!-- Contenedor de Resultados -->
                        <div class="contenedor">
                            <div class="item">
                                <label class="etiqueta">Nombre:</label>
                                <label id="remitente_nombre" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">Primer Apellido:</label>
                                <label id="remitente_primer_apellido" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">Segundo Apellido:</label>
                                <label id="remitente_segundo_apellido" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">RFC:</label>
                                <label id="remitente_rfc" class="valor"></label>
                            </div>
                        </div>
                        <br>

                        <form action="{{ route('tableinstructor.save') }}" method="POST" class="form-sample">
                            @csrf

                            <div class="row align-items-center">
                                <!-- Campo CURP -->
                                <div class="col-md-10 d-flex align-items-center">
                                    <div class="form-group flex-grow-1">
                                        <label for="curp" style="font-size: 1rem; color: #333;">CURP</label>
                                        <input type="text" name="curp" id="curp" placeholder="Ingrese CURP"
                                            autocomplete="off" value="{{ optional($item)->curp ?? '' }}" class="form-control"
                                            style="font-size: 1rem;" />
                                    </div>
                                    <!-- Botón CONSULTAR -->
                                    <button class="btn ml-2" onclick="validarcurp();" type="button"
                                        style="font-size: 1rem; padding: 10px 20px; background-color:rgb(235, 235, 235); color:#646464; border: none;display: inline-flex; justify-content: center; align-items: center;" data-bs-toggle="tooltip" data-bs-placement="top" title="Consultar CURP">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Campo Estatus -->
                            <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                <label for="estatus">Estatus</label>
                                <input type="checkbox" id="estatus" name="estatus" class="toggle-switch" 
                                    {{ optional($item)->estatus ? 'checked' : '' }}>
                            </div>

                           <!-- Campo oculto para id_cat_tipo_schema -->
                            <x-template-form.template-form-input-hidden name="id_cat_tipo_schema"
                                value="{{ optional($item)->id_cat_tipo_schema ?? '' }}" />

                            <!-- Campo oculto para id_tbl_empleados_hraes -->
                            <x-template-form.template-form-input-hidden name="id_tbl_empleados_hraes"
                            value="{{ optional($item)->id_tbl_empleados_hraes ?? '' }}" />

                            <br>
                             
                            <!-- Espacio adicional -->

                            <x-template-tittle.tittle-caption-secon tittle="Cloud" />
                            <!-- Contenedor principal con flexbox -->
                            <div class="main-container">
                                <!-- Lado izquierdo -->
                                <div class="left-side">
                                    <br>
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

                                        <div id="container_cv_entrada_vacio" class="rectangulo">
                                            Sin contenido
                                        </div>
                                        <div id="container_cv_entrada"></div>
                                    </div>
                                </div>

                                <!-- Lado derecho -->
                                <div class="right-side">
                                    <br>
                                    <div>
                                        <div style="display: flex; align-items: center;">
                                            <x-template-tittle.tittle-caption-secon tittle="Constancia (Max 1)" />
                                            <label for="file_cons_entrada" id="label_cons_entrada"
                                                style="background-color: white; color: red; font-weight: normal; font-size: 1rem; padding: 5px 15px; cursor: pointer; display: flex; align-items: center; text-decoration: none;">
                                                <i class="fa fa-arrow-up" id="icon_cons_entrada"
                                                    style="margin-right: 5px;"></i>
                                                Cargar
                                            </label>
                                            <input type="file" id="file_cons_entrada" style="display: none;">
                                        </div>
                                        <div id="container_cons_entrada_vacio" class="rectangulo">
                                            Sin contenido
                                        </div>
                                        <div id="container_cons_entrada"></div>
                                    </div>
                                </div>
                            </div>

                            <x-template-button.button-form-footer routeBack="{{ route('tableinstructor.list') }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-template-app.app-layout>

<!-- CODE SCRIPT-->

<script src="{{ asset('assets/js/app/courses/tableinstructor/form.js') }}"></script>