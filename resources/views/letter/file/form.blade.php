<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <?php include(resource_path('views/config.php')); ?>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Gestión de control" caption="Lineamientos" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_tbl_expediente) ? 'Modificar' : 'Agregar ' }} Lineamientos"
                            route="{{ route('file.list') }}" />
                        <div>
                            <form id="myForm" action="{{ route('file.save') }}" method="POST" class="form-sample">
                                @csrf

                                <x-template-form.template-form-input-hidden name="bool_user_role"
                                    value="{{  $letterAdminMatch }}" />

                                <x-template-form.template-form-input-hidden name="id_tbl_expediente"
                                    value="{{ optional($item)->id_tbl_expediente ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="fecha_captura"
                                    value="{{ optional($item)->fecha_captura ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="id_cat_anio"
                                    value="{{ optional($item)->id_cat_anio ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="num_turno_sistema"
                                    value="{{ optional($item)->num_turno_sistema ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="es_por_area"
                                    value="{{ optional($item)->es_por_area ?? '' }}" />

                                <!-- change -->
                                <x-template-form.template-form-input-hidden name="id_cat_area"
                                    value="{{ optional($item)->id_cat_area ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="id_tbl_correspondencia"
                                    value="{{ optional($item)->id_tbl_correspondencia ?? '' }}" />

                                <x-template-tittle.tittle-caption-secon tittle="Información de oficio" />
                                <div class="contenedor">
                                    <div class="item">
                                        <label class="etiqueta">No. Turno:</label>
                                        <label id="_labNoCorrespondencia" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Fecha de captura:</label>
                                        <label id="_labFechaCaptura" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Año:</label>
                                        <label id="_labAño" class="valor"></label>
                                    </div>
                                </div>

                                <br>
                                <x-template-tittle.tittle-caption-secon tittle="Información general" />

                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectAreaAux"
                                        :selectEdit="$selectAreaEditAux" name="id_cat_area_documento" tittle="Área"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" />

                                    <x-template-form.template-form-input-required label="No. Doc" type="text"
                                        name="num_documento_area" placeholder="NO. DOCUMENTO"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete=""
                                        value="{{optional($item)->num_documento_area ?? '' }}" />
                                </div>

                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectUser"
                                        :selectEdit="$selectUserEdit" name="id_usuario_area" tittle="Usuario"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" />

                                    <x-template-form.template-form-select-required :selectValue="$selectEnlace"
                                        :selectEdit="$selectEnlaceEdit" name="id_usuario_enlace" tittle="Enlace"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" />
                                </div>

                                <div class="row">

                                    <x-template-form.template-form-input-required label="Fecha de emisión" type="date"
                                        name="fecha_inicio" placeholder=""
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6" autocomplete=""
                                        value="{{optional($item)->fecha_inicio ?? '' }}" />

                                    <x-template-form.template-form-input-required label="Fecha de aplicación"
                                        type="date" name="fecha_fin" placeholder=""
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6" autocomplete=""
                                        value="{{optional($item)->fecha_fin ?? '' }}" />
                                </div>

                                <div class="row">

                                    <x-template-form.template-form-input-text-area
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" label="Asunto"
                                        name="asunto" placeholder="ASUNTO"
                                        value="{{ optional($item)->asunto ?: '' }}" />

                                    <x-template-form.template-form-input-text-area
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" label="Destinatario"
                                        name="destinatario" placeholder="Destinatario"
                                        value="{{ optional($item)->destinatario ?: '' }}" />

                                    <x-template-form.template-form-input-text-area
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" label="Observaciones"
                                        name="observaciones" placeholder="OBSERVACIONES"
                                        value="{{ optional($item)->observaciones ?: '' }}" />
                                </div>

                                <x-template-button.button-form-footer-boolean routeBack="{{ route('file.list') }}"
                                    :status="$letterAdminMatch" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-template-app.app-layout>

<!-- CODE SCRIPT-->
<script src="{{ asset('assets/js/app/letter/file/form.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/file/select.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/function/function.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/file/validate.js') }}"></script>