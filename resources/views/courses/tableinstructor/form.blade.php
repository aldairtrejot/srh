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
                            tittle="{{ isset($item->id_tbl_instructores) ? 'Modificar' : 'Agregar ' }} Instructor"
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
                autocomplete="" value="{{ optional($item)->curp ?? '' }}" class="form-control"
                style="font-size: 1rem;" />
        </div>
        <!-- Botón CONSULTAR -->
        <button class="btn ml-2" onclick="validarcurp();" type="button"
            style="font-size: 1rem; padding: 10px 20px; background-color:rgb(235, 235, 235); color:#646464; border: none;display: inline-flex; justify-content: center; align-items: center;"data-bs-toggle="tooltip" data-bs-placement="top" title="Consultar CURP"><i class="fa fa-search"></i>
        </button>
    </div>
</div>

                            <!-- Campo Estatus -->
                            <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                <label for="estatus">Estatus</label>
                                <input type="checkbox" id="estatus" name="estatus" class="toggle-switch" 
                                    {{ optional($item)->estatus ? 'checked' : '' }}>
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
