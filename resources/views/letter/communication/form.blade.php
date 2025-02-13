<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <?php include(resource_path('views/config.php')); ?>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Coordinación de Recursos Humanos" caption="Oficios" />
                    </div>
                </div>
            </div>

            <!-- View->modal -->
            @include('letter.communication.modal')

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <h2 class="card-title" style="margin-bottom: 0;">
                                    {{ isset($item->id_tbl_correspondencia_interno) ? 'Modificar' : 'Agregar ' }}
                                    Oficio
                                </h2>
                            </div>
                            <div class="d-flex align-items-center">
                                <!-- Botón 1 -->
                                 @if (!isset($item->id_tbl_correspondencia_interno))
                                    <button class="btn btn-hover-enlarge" onclick="refreshOficio();"
                                        style="font-size: 1.1rem; padding: 10px; background-color: white; color: #10312B; border-radius: 50%; border: none; margin-right: 10px;"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh No. Oficio">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                 @endif
            

                                <!--
                                <button class="btn btn-hover-enlarge" onclick="addSolicitante();"
                                    style="font-size: 1.1rem; padding: 10px; background-color: white; color: #10312B; border-radius: 50%; border: none; margin-right: 10px;"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar Solicitante">
                                    <i class="fa fa-user-plus"></i>
                                </button>


                                <button class="btn btn-hover-enlarge" onclick="addDestinatario();"
                                    style="font-size: 1.1rem; padding: 10px; background-color: white; color: #10312B; border-radius: 50%; border: none; margin-right: 10px;"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar Destinatario">
                                    <i class="fa fa-user-plus"></i>
                                </button>

                                <button class="btn btn-hover-enlarge" onclick="addTema();"
                                    style="font-size: 1.1rem; padding: 10px; background-color: white; color: #10312B; border-radius: 50%; border: none; margin-right: 10px;"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar Tema">
                                    <i class="fa fa-file-text"></i>
                                </button>
                                 -->

                                <a href="{{ route('communication.list') }}" class="btn btn-hover-enlarge"
                                    style="font-size: 1.1rem; padding: 10px; background-color: #10312B; color: white; border-radius: 50%; border: none; display: inline-flex; justify-content: center; align-items: center;"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Regresar">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                        </div>

                        <div>
                            <form id="myForm" action="{{ route('communication.save') }}" method="POST" class="form-sample">
                                @csrf

                                <!-- item -> hidden -->
                                <x-template-form.template-form-input-hidden name="id_tbl_correspondencia_interno"
                                    value="{{ optional($item)->id_tbl_correspondencia_interno ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="fecha_asignacion"
                                    value="{{ optional($item)->fecha_asignacion ?? '' }}" />

                                <x-template-form.template-form-input-hidden name="nameUser" value="{{ $nameUser }}" />

                                <x-template-form.template-form-input-hidden name="nomArea" value="{{ $nomArea }}" />

                                <x-template-form.template-form-input-hidden name="consecutivo"
                                    value="{{ optional($item)->consecutivo ?? '' }}" />

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
                                    <x-template-form.template-form-select-required :selectValue="$selectTema"
                                        :selectEdit="$selectTemaEdit" name="id_cat_tema" tittle="Tema"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" />

                                    <x-template-form.template-form-select-required :selectValue="$selectEntidad"
                                        :selectEdit="$selectEntidadEdit" name="id_cat_entidad" tittle="Lugar"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" />
                                </div>

                                <x-template-tittle.tittle-caption-secon
                                    tittle="Información de destinatario y solicitante" />
                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectArea"
                                        :selectEdit="$selectAreaEdit" name="id_cat_area_interno" tittle="Área / Zona"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" />

                                    <x-template-form.template-form-select-required :selectValue="$selectSolicitante"
                                        :selectEdit="$selectSolicitanteEdit" name="id_cat_solicitante"
                                        tittle="Solicitante" grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" />
                                </div>

                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectDestinatario"
                                        :selectEdit="$selectDestinatarioEdit" name="id_cat_destinatario"
                                        tittle="Destinatario" grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" />

                                    <x-template-form.template-form-input-required label="Cargo destinatario" type="text"
                                        name="cargo_destinatario" placeholder="Cargo del destinatario"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" autocomplete=""
                                        value="{{optional($item)->cargo_destinatario ?? '' }}" />
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
<script src="{{ asset('assets/js/app/letter/communication/form.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/communication/modal.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/communication/select.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/communication/consecutivo.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/function/solicitante.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/function/function.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/communication/validate.js') }}"></script>
