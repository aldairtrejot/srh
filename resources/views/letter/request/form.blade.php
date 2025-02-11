<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <?php include(resource_path('views/config.php')); ?>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Coordinación de Recursos Humanos"
                            caption="Notas de Requerimiento" />
                    </div>
                </div>
            </div>

            <!-- View->modal -->
            @include('letter.request.modal')

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <h2 class="card-title" style="margin-bottom: 0;">
                                    {{ isset($item->id_tbl_requerimiento_interno) ? 'Modificar' : 'Agregar ' }}
                                    Nota
                                </h2>
                            </div>
                            <div class="d-flex align-items-center">
                                <!-- Botón 1 -->
                                @if (!isset($item->id_tbl_requerimiento_interno))
                                    <button class="btn btn-hover-enlarge" onclick="refresNota();"
                                        style="font-size: 1.1rem; padding: 10px; background-color: white; color: #10312B; border-radius: 50%; border: none; margin-right: 10px;"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh No. Nota">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                @endif


                                <!--
                                <button class="btn btn-hover-enlarge" onclick="addSolicitante();"
                                    style="font-size: 1.1rem; padding: 10px; background-color: white; color: #10312B; border-radius: 50%; border: none; margin-right: 10px;"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar Solicitante">
                                    <i class="fa fa-user-plus"></i>
                                </button>
                                 -->

                                <a href="{{ route('request.list') }}" class="btn btn-hover-enlarge"
                                    style="font-size: 1.1rem; padding: 10px; background-color: #10312B; color: white; border-radius: 50%; border: none; display: inline-flex; justify-content: center; align-items: center;"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Regresar">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                        </div>

                        <div>
                            <form id="myForm" action="{{ route('request.save') }}" method="POST"
                                class="form-sample">
                                @csrf

                                <!-- item -> hidden -->
                                <x-template-form.template-form-input-hidden name="id_tbl_requerimiento_interno"
                                    value="{{ optional($item)->id_tbl_requerimiento_interno ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="fecha_asignacion"
                                    value="{{ optional($item)->fecha_asignacion ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="consecutivo"
                                    value="{{ optional($item)->consecutivo ?? '' }}" />

                                <x-template-tittle.tittle-caption-secon tittle="Información de nota" />
                                <div class="contenedor">
                                    <div class="item">
                                        <label class="etiqueta">No. Nota:</label>
                                        <label id="_labNoOficio" class="valor"></label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Fecha de asignación:</label>
                                        <label id="_labFechaCaptura" class="valor"></label>
                                    </div>
                                </div>

                                <br>
                                <x-template-tittle.tittle-caption-secon tittle="Información general" />

                                <div class="row">
                                    <x-template-form.template-form-input-required label="Fecha de documento" type="date"
                                        name="fecha_documento" placeholder=""
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete=""
                                        value="{{optional($item)->fecha_documento ?? '' }}" />

                                    <x-template-form.template-form-input-required label="Fecha de Termino" type="date"
                                        name="fecha_termino" placeholder=""
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete=""
                                        value="{{optional($item)->fecha_termino ?? '' }}" />
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


                                <x-template-tittle.tittle-caption-secon tittle="Información de solicitante" />
                                <div class="row">

                                    <x-template-form.template-form-select-required :selectValue="$selectSolicitante"
                                        :selectEdit="$selectSolicitanteEdit" name="id_cat_solicitante"
                                        tittle="Solicitante" grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" />
                                </div>

                                <x-template-button.button-form-footer routeBack="{{ route('request.list') }}" />

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-template-app.app-layout>

<!-- CODE SCRIPT-->
<script src="{{ asset('assets/js/app/letter/request/form.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/request/modal.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/request/consecutivo.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/function/solicitante.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/function/function.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/request/validate.js') }}"></script>