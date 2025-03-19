<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <?php include(resource_path('views/config.php')); ?>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Gestión de control" caption="Circular Externa" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_tbl_circular_externa) ? 'Modificar' : 'Agregar ' }} Circular Externa"
                            route="{{ route('external.list') }}" />
                        <div>
                            <form id="myForm" action="{{ route('external.save') }}" method="POST" class="form-sample">
                                @csrf


                                <x-template-form.template-form-input-hidden name="id_tbl_circular_externa"
                                    value="{{ optional($item)->id_tbl_circular_externa ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="fecha_captura"
                                    value="{{ optional($item)->fecha_captura ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="anio"
                                    value="{{ optional($item)->anio ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="num_turno_sistema"
                                    value="{{ optional($item)->num_turno_sistema ?? '' }}" />


                                <x-template-tittle.tittle-caption-secon tittle="Información de documento" />
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
                                    <x-template-form.template-form-select-required :selectValue="$selectDependencia"
                                        :selectEdit="$selectDependenciaEdit" name="id_cat_dependencia" tittle="Dependencia"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" />

                                    <x-template-form.template-form-select-required :selectValue="$selectArea"
                                        :selectEdit="$selectAreaEdit" name="id_cat_dependencia_area" tittle="Área"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" />
                                </div>


                                <div class="row">

                                    <x-template-form.template-form-input-required label="Fecha de documento" type="date"
                                        name="fecha_documento" placeholder=""
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6" autocomplete=""
                                        value="{{optional($item)->fecha_documento ?? '' }}" />

                                    <x-template-form.template-form-input-required label="No. Documento" type="text"
                                        name="no_documento" placeholder="NO. DOCUMENTO"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-6" autocomplete=""
                                        value="{{optional($item)->no_documento ?? '' }}" />
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

                                <x-template-button.button-form-footer routeBack="{{ route('external.list') }}" />

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-template-app.app-layout>

<!-- CODE SCRIPT-->
<script src="{{ asset('assets/js/app/letter/external/form.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/external/select.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/function/function.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/external/validate.js') }}"></script>