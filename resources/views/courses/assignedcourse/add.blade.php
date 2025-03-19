<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Gestión de Cursos" caption="Cursos" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption tittle="Agregar Curso" route="#" />
                        <br>
                        <form id="myForm" action="#" method="POST" class="form-sample">
                            @csrf

                            <x-template-form.template-form-input-hidden name="id_tbl_cursos" value="" />

                            <x-template-tittle.tittle-caption-secon tittle="Información general del curso" />
                            <div class="row">
                                <x-template-form.template-form-input-required 
                                    label="Nombre Curso" type="text"
                                    name="programa_proyecto" placeholder="Ingrese el nombre del curso"  
                                    autocomplete="on" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4"
                                    value="" />

                                <x-template-form.template-form-input-required 
                                    label="Fecha de inicio" type="date"
                                    name="fecha_inicio" placeholder="Seleccione una fecha"  
                                    autocomplete="on" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4"
                                    value="" />

                                <x-template-form.template-form-input-required 
                                    label="Horas" type="text"
                                    name="horas" placeholder="Ingrese la cantidad de horas"  
                                    autocomplete="on" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4"
                                    value="" />

                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                    <label for="estatus">Estatus</label>
                                    <input type="checkbox" id="estatus" name="estatus" class="toggle-switch">
                                </div>
                            </div>

                            <x-template-tittle.tittle-caption-secon tittle="Costos" />
                            <div class="row">
                                <x-template-form.template-form-input-required 
                                    label="Costo" type="text"
                                    name="costo" id="costo"
                                    grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4"
                                    value="" />

                                <x-template-form.template-form-input-required 
                                    label="IVA" type="text"
                                    name="iva" id="iva"
                                    grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4"
                                    value="" />

                                <x-template-form.template-form-input-required 
                                    label="Costo Total" type="text"
                                    name="costo_total" id="costo_total"
                                    grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4"
                                    value="" readonly />
                            </div>

                            <x-template-tittle.tittle-caption-secon tittle="Instructor de Curso" />
                            <div class="row">
                                <x-template-form.template-form-select-required 
                                    :selectValue="[]" 
                                    :selectEdit="[]" 
                                    name="id_tbl_instructores"
                                    tittle="Instructor"
                                    grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                            </div>

                            <x-template-tittle.tittle-caption-secon tittle="Cursos" />
                            <div class="row">
                                <x-template-form.template-form-select-required 
                                    :selectValue="[]" 
                                    :selectEdit="[]" 
                                    name="id_cat_tipo_cursos"
                                    tittle="Tipo Curso"
                                    grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                <x-template-form.template-form-select-required 
                                    :selectValue="[]" 
                                    :selectEdit="[]" 
                                    name="id_cat_coordinacion"
                                    tittle="Coordinación"
                                    grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                <x-template-form.template-form-select-required 
                                    :selectValue="[]" 
                                    :selectEdit="[]" 
                                    name="id_cat_beneficio"
                                    tittle="Beneficio"
                                    grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                            </div>

                            <x-template-button.button-form-footer routeBack="#" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bootstrap-select .dropdown-menu {
            max-height: 200px;
            overflow-y: auto;
            max-width: 300px;
            overflow-x: auto;
        }
    </style>

</x-template-app.app-layout>
