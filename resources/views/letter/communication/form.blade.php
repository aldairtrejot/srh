<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <?php include(resource_path('views/config.php')); ?>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Gestión de control" caption="Comunicados" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_tbl_correspondencia_interno) ? 'Modificar' : 'Agregar ' }} Comunicado"
                            route="{{ route('communication.list') }}" />
                        <div>
                            <form id="myForm" action="{{ route('file.save') }}" method="POST" class="form-sample">
                                @csrf

                                <!-- item -> hidden -->
                                <x-template-form.template-form-input-hidden name="id_tbl_correspondencia_interno"
                                    value="{{ optional($item)->id_tbl_correspondencia_interno ?? '' }}" />


                                <x-template-tittle.tittle-caption-secon tittle="Información de oficio" />
                                <div class="contenedor">
                                    <div class="item">
                                        <label class="etiqueta">No. Oficio:</label>
                                        <label id="_labNoOficio" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Fecha de captura:</label>
                                        <label id="_labFechaCaptura" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Nom. Área:</label>
                                        <label id="_labNomArea" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Usuario:</label>
                                        <label id="_labUsuario" class="valor"></label>
                                    </div>
                                </div>

                                <br>
                                <x-template-tittle.tittle-caption-secon tittle="Información general" />

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

                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectEntidad"
                                        :selectEdit="$selectEntidadEdit" name="id_cat_remitente" tittle="Tema"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" />

                                    <x-template-form.template-form-select-required :selectValue="$selectEntidad"
                                        :selectEdit="$selectEntidadEdit" name="id_cat_remitente" tittle="Lugar"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" />
                                </div>

                                <x-template-tittle.tittle-caption-secon
                                    tittle="Información de destinatario y solicitante" />
                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectEntidad"
                                        :selectEdit="$selectEntidadEdit" name="id_cat_remitente" tittle="Solicitante"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" />

                                    <x-template-form.template-form-select-required :selectValue="$selectEntidad"
                                        :selectEdit="$selectEntidadEdit" name="id_cat_remitente" tittle="Área / Zona"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" />
                                </div>

                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectEntidad"
                                        :selectEdit="$selectEntidadEdit" name="id_cat_remitente" tittle="Destinatario"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" />

                                    <x-template-form.template-form-input-required label="Cargo destinatario" type="text"
                                        name="num_documento" placeholder="Cargo del destinatario"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" autocomplete=""
                                        value="{{optional($item)->num_documento ?? '' }}" />
                                </div>

                                <x-template-button.button-form-footer routeBack="{{ route('communication.list') }}" />

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