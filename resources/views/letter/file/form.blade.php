<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <?php include(resource_path('views/config.php')); ?>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Control de correspondencia" caption="Expediente" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_tbl_expediente) ? 'Modificar' : 'Agregar ' }} Expediente"
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
                                    <x-template-form.template-form-input-required label="No. Correspondencia Asoc."
                                        type="text" name="num_correspondencia"
                                        placeholder="NO. CORRESPONDENCIA ASOCIADO"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                                        value="{{$noLetter ?? '' }}" />

                                    <x-template-form.template-form-input-required label="Fecha de inicio" type="date"
                                        name="fecha_inicio" placeholder=""
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                                        value="{{optional($item)->fecha_inicio ?? '' }}" />

                                    <x-template-form.template-form-input-required label="Fecha fin" type="date"
                                        name="fecha_fin" placeholder=""
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                                        value="{{optional($item)->fecha_fin ?? '' }}" />
                                </div>

                                <div class="row">

                                    <x-template-form.template-form-input-required label="Asunto" type="text"
                                        name="asunto" placeholder="ASUNTO"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                                        value="{{optional($item)->asunto ?? '' }}" />

                                    <x-template-form.template-form-input-required label="Observaciones" type="text"
                                        name="observaciones" placeholder="OBSERVACIONES"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-8" autocomplete=""
                                        value="{{optional($item)->observaciones ?? '' }}" />
                                </div>

                                <x-template-tittle.tittle-caption-secon tittle="Información institucional" />
                                <div class="row">

                                    <x-template-form.template-form-select-required :selectValue="$selectArea"
                                        :selectEdit="$selectAreaEdit" name="id_cat_area" tittle="Área"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                    <x-template-form.template-form-select-required :selectValue="$selectUser"
                                        :selectEdit="$selectUserEdit" name="id_usuario_area" tittle="Usuario"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                    <x-template-form.template-form-select-required :selectValue="$selectEnlace"
                                        :selectEdit="$selectEnlaceEdit" name="id_usuario_enlace" tittle="Enlace"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                </div>

                                <x-template-tittle.tittle-caption-secon tittle="Información de remitente" />
                                <div class="row">

                                    <x-template-form.template-form-select-required :selectValue="$selectRemitente"
                                        :selectEdit="$selectRemitenteEdit" name="id_cat_remitente" tittle="Remitente"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8" />

                                </div>

                                <x-template-tittle.tittle-caption-secon tittle="Otros" />
                                <div class="container">
                                    <div class="row">
                                        <div class="form-check form-check-flat form-check-primary">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" id="idcheckboxTemplate"
                                                    title="Marca este checkbox si no tienes un número de correspondencia.">
                                                ¿No tengo un No. de Correspondencia?
                                            </label>
                                        </div>
                                    </div>
                                </div>



                                <div id="mostrar_ocultar_no_area">
                                    <!--
                                    <p class="texto-centro">
                                        Si no cuentas con un número de correspondencia, selecciona el área
                                        correspondiente para asignar uno en su lugar.
                                    </p>
-->
                                    <div class="row">
                                        <x-template-form.template-form-select-required :selectValue="$selectAreaAux"
                                            :selectEdit="$selectAreaEditAux" name="id_cat_area_documento" tittle="Área"
                                            grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                        <x-template-form.template-form-input-required label="No. Doc" type="text"
                                            name="num_documento_area" placeholder="NO. DOCUMENTO"
                                            grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                                            value="{{optional($item)->num_documento_area ?? '' }}" />
                                    </div>
                                </div>

                                <x-template-button.button-form-footer routeBack="{{ route('file.list') }}" />

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