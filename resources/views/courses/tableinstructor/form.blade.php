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
                                <div class="contenedor">
                                    <div class="item">
                                        <label class="etiqueta">Nombre:</label>
                                        <label id="_labNoCorrespondencia" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Primer Apellido:</label>
                                        <label id="_labFechaCaptura" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Segundo Apellido:</label>
                                        <label id="_labAño" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">RFC:</label>
                                        <label id="_labClave" class="valor"></label>
                                    </div>
                                </div>

                        <br>
                        <form action="{{ route('tableinstructor.save') }}" method="POST" class="form-sample">
                            @csrf
            
    
                            <div class="row align-items-center">

                        <!-- Campo CURP -->
                        <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8">
                        <div class="form-group">
                        <label for="curp" style="font-size: 1rem; color: #333;">CURP</label>
                        <input type="text" name="curp" id="curp" placeholder="Ingrese CURP"
                        autocomplete="" value="{{ optional($item)->curp ?? '' }}" class="form-control"
                        style="font-size: 1rem;" />
                        </div>
                        </div>
                        <!-- Botón CONSULTAR -->
                        <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 text-right">
                        <button class="btn" onclick="validarcurp();" type="button"
                        style="font-size: 1rem; padding: 10px 20px; background-color: #10312B; color: white; border: none;">
                        <i class=" "></i> CONSULTAR
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
