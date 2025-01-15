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
                        
                        <br>

                        <form action="{{ route('tableinstructor.save') }}" method="POST" class="form-sample">
                            @csrf
                            <!-- Campo CURP -->
                            <x-template-form.template-form-input-required 
                                label="CURP" 
                                type="text"
                                name="curp" 
                                placeholder="Ingrese CURP"
                                grid="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8" 
                                autocomplete=""
                                value="{{ optional($item)->curp ?? '' }}" />

                            <!-- Campo Nombre -->
                            <x-template-form.template-form-input-required 
                                label="Nombre completo" 
                                type="text"
                                name="nombre" 
                                placeholder="Ingrese el nombre completo"
                                grid="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8" 
                                autocomplete=""
                                value="{{ optional($item)->nombre ?? '' }}" />

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
