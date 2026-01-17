<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <?php include(resource_path('views/config.php')); ?>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Control de gestión" caption="Oficio" />
                    </div>
                </div>
            </div>

            <!-- MODAL ALERT -->
            @include('letter.office.alert')

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_tbl_oficio) ? 'Modificar' : 'Agregar ' }} Oficio"
                            route="{{ route('office.list') }}" />
                        <div>
                            <form id="myForm" action="{{ route('office.save') }}" method="POST" class="form-sample">
                                @csrf

                                <x-template-form.template-form-input-hidden name="bool_user_role"
                                    value="{{  $letterAdminMatch }}" />

                                <x-template-form.template-form-input-hidden name="id_tbl_oficio"
                                    value="{{ optional($item)->id_tbl_oficio ?? '' }}" />

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

                                <!-- itme-->
                                <x-template-form.template-form-input-hidden name="area_format" value="{{ $area }}" />

                                <x-template-form.template-form-input-hidden name="user_name" value="{{ $user_name }}" />

                                <x-template-form.template-form-input-hidden name="user_enlace"
                                    value="{{ $user_enlace }}" />

                                <!-- -->
                                <input type="hidden" id="update_letter" name="update_letter" />


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

                                    <div class="item">
                                        <label class="etiqueta">Área:</label>
                                        <label id="_labArea" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Usuario:</label>
                                        <label id="_labUsuario" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Enlace:</label>
                                        <label id="_labEnlace" class="valor"></label>
                                    </div>

                                </div>

                                <br>
                                <x-template-tittle.tittle-caption-secon tittle="Información general" />

                                <div class="row">
                                    <x-template-form.template-form-input-required label="Fol. Gestión" type="text"
                                        name="num_correspondencia" placeholder="NO. CORRESPONDENCIA ASOCIADO"
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

                                    <x-template-form.template-form-input-text-area
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" label="Asunto"
                                        name="asunto" placeholder="ASUNTO"
                                        value="{{ optional($item)->asunto ?: '' }}" />

                                    <x-template-form.template-form-input-text-area
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" label="Observaciones"
                                        name="observaciones" placeholder="OBSERVACIONES"
                                        value="{{ optional($item)->observaciones ?: '' }}" />

                                </div>

                                <p class="card-description"
                                    style="font-size: 1rem; font-weight: bold; color: #000; display: inline-block; margin-right: 30px;">
                                    Otros
                                </p>

                                <x-template-form.template-form-input-check idDiv="id_checkbox_Template_tooltip"
                                    name="idcheckboxTemplate" label="¿No tengo un folio de gestión?" />

                                <div id="mostrar_ocultar_no_area">
                                    <br>
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
                                </div>

                                <x-template-button.button-form-footer routeBack="{{ route('office.list') }}" />

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-template-app.app-layout>

<!-- CODE SCRIPT-->
<script src="{{ asset('assets/js/app/letter/office/form.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/office/select.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/function/function.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/office/validate.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/office/modal.js') }}"></script>