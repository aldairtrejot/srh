<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <?php include(resource_path('views/config.php')); ?>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Control de correspondencia"
                            caption="Correspondencia" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_tbl_correspondencia) ? 'Modificar' : 'Agregar ' }} Correspondencia"
                            route="{{ route('letter.list') }}" />
                        <div>
                            <form id="myForm" action="{{ route('letter.save') }}" method="POST" class="form-sample">
                                @csrf

                                <x-template-form.template-form-input-hidden name="bool_user_role"
                                    value="{{  $letterAdminMatch }}" />

                                <x-template-form.template-form-input-hidden name="id_tbl_correspondencia"
                                    value="{{ optional($item)->id_tbl_correspondencia ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="fecha_captura"
                                    value="{{ optional($item)->fecha_captura ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="id_cat_anio"
                                    value="{{ optional($item)->id_cat_anio ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="num_turno_sistema"
                                    value="{{ optional($item)->num_turno_sistema ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="id_cat_clave_aux"
                                    value="{{ optional($item)->id_cat_clave ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="rfc_remitente_bool"
                                    value="{{ optional($item)->rfc_remitente_bool ?? '' }}" />


                                <x-template-tittle.tittle-caption-secon tittle="Información de correspondencia" />
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
                                    <div class="item">
                                        <label class="etiqueta">Clave:</label>
                                        <label id="_labClave" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Clave / código:</label>
                                        <label id="_labClaveCodigo" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Clave / redacción:</label>
                                        <label id="_labClaveRedaccion" class="valor"></label>
                                    </div>
                                </div>

                                <br>
                                <x-template-tittle.tittle-caption-secon tittle="Información general" />

                                <div class="row">
                                    <x-template-form.template-form-input-required label="No. Documento" type="text"
                                        name="num_documento" placeholder="NO. DOCUMENTO"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                                        value="{{optional($item)->num_documento ?? '' }}" />

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


                                    <x-template-form.template-form-input-required label="No. hojas" type="integer"
                                        name="num_flojas" placeholder="NO. HOJAS"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                                        value="{{optional($item)->num_flojas ?? '' }}" />

                                    <x-template-form.template-form-input-required label="No. tomos" type="integer"
                                        name="num_tomos" placeholder="NO. TOMOS"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                                        value="{{optional($item)->num_tomos ?? '' }}" />

                                    <x-template-form.template-form-input-required label="Horas respuesta" type="integer"
                                        name="horas_respuesta" placeholder="HORAS DE RESPUESTA"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                                        value="{{optional($item)->horas_respuesta ?? '' }}" />

                                </div>

                                <div class="row">

                                    <x-template-form.template-form-input-required label="Folio de gestión"
                                        type="integer" name="folio_gestion" placeholder="FOLIO DE GESTIÓN"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                                        value="{{optional($item)->folio_gestion ?? '' }}" />

                                    <x-template-form.template-form-input-required label="Lugar" type="text" name="lugar"
                                        placeholder="LUGAR" grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-8"
                                        autocomplete="" value="{{optional($item)->lugar ?? '' }}" />
                                </div>

                                <div class="row">
                                    <x-template-form.template-form-input-required label="Asunto" type="text"
                                        name="asunto" placeholder="ASUNTO"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" autocomplete=""
                                        value="{{optional($item)->asunto ?? '' }}" />
                                </div>

                                <div class="row">
                                    <x-template-form.template-form-input-required label="Observaciones" type="text"
                                        name="observaciones" placeholder="OBSERVACIONES"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" autocomplete=""
                                        value="{{optional($item)->observaciones ?? '' }}" />
                                </div>

                                <x-template-tittle.tittle-caption-secon tittle="Área de atención" />
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

                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectUnidad"
                                        :selectEdit="$selectUnidadEdit" name="id_cat_unidad" tittle="Unidad"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                    <x-template-form.template-form-select-required :selectValue="$selectCoordinacion"
                                        :selectEdit="$selectCoordinacionEdit" name="id_cat_coordinacion"
                                        tittle="Coordinación" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                </div>


                                <x-template-tittle.tittle-caption-secon tittle="Documento de entrada" />
                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectStatus"
                                        :selectEdit="$selectStatusEdit" name="id_cat_estatus" tittle="Estatus"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                    <x-template-form.template-form-select-required :selectValue="$selectTramite"
                                        :selectEdit="$selectTramiteEdit" name="id_cat_tramite" tittle="Tramite"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                    <x-template-form.template-form-select-required :selectValue="$selectClave"
                                        :selectEdit="$selectClaveEdit" name="id_cat_clave" tittle="Clave"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                </div>

                                <x-template-tittle.tittle-caption-secon tittle="Información de remitente" />
                                <div class="row">

                                    <x-template-form.template-form-select-required :selectValue="$selectRemitente"
                                        :selectEdit="$selectRemitenteEdit" name="id_cat_remitente" tittle="Remitente"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8" />

                                    <div class="form-check form-check-flat form-check-primary">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" id="idcheckboxTemplate"
                                                title="Marca este checkbox si no tienes un número de correspondencia.">
                                            Agregar remitente
                                        </label>
                                    </div>

                                </div>

                                <div class="row">
                                    <x-template-form.template-form-input-required label="Puesto remitente" type="text"
                                        name="puesto_remitente" placeholder="PUESTO DE REMITENTE"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8" autocomplete=""
                                        value="{{optional($item)->puesto_remitente ?? '' }}" />
                                </div>


                                <style>
                                    .form-check-primary.form-check label input[type="checkbox"]+.input-helper:before,
                                    .form-check-primary.form-check label input[type="radio"]+.input-helper:before {
                                        border-color: #000000;
                                    }
                                </style>
                                <div id="mostrar_ocultar_template">
                                    <div class="row">
                                        <x-template-form.template-form-input-required label="Nombre" type="text"
                                            name="remitente_nombre" placeholder="NOMBRE"
                                            grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete=""
                                            value="" />

                                        <x-template-form.template-form-input-required label="Apellido paterno"
                                            type="text" name="remitente_apellido_paterno" placeholder="APELLIDO PATERNO"
                                            grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete=""
                                            value="" />
                                    </div>

                                    <div class="row">
                                        <x-template-form.template-form-input-required label="Apellido materno"
                                            type="text" name="remitente_apellido_materno" placeholder="APELLIDO MATERNO"
                                            grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete=""
                                            value="" />

                                        <x-template-form.template-form-input-required label="RFC" type="text"
                                            name="remitente_rfc" placeholder="RFC"
                                            grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete=""
                                            value="" />
                                    </div>
                                </div>

                                <x-template-button.button-form-footer routeBack="{{ route('letter.list') }}" />

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-template-app.app-layout>

<!-- CODE SCRIPT-->
<script src="{{ asset('assets/js/app/other/rfc.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/function/function.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/letter/validate.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/letter/form.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/letter/select.js') }}"></script>