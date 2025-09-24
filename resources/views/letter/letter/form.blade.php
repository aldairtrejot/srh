<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <?php include(resource_path('views/config.php')); ?>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Control de gestión" caption="Correspondencia" />
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
                            <form id="myForm" action="{{ route('letter.save') }}" method="POST" class="form-sample" enctype="multipart/form-data">
                                @csrf

                                {{-- ====== HIDDEN EXISTENTES ====== --}}
                                <x-template-form.template-form-input-hidden name="bool_user_role" value="{{ $letterAdminMatch }}" />
                                <x-template-form.template-form-input-hidden id="id_tbl_correspondencia" name="id_tbl_correspondencia" value="{{ optional($item)->id_tbl_correspondencia ?? '' }}" />
                                <x-template-form.template-form-input-hidden id="fecha_captura" name="fecha_captura" value="{{ optional($item)->fecha_captura ?? '' }}" />
                                <x-template-form.template-form-input-hidden id="id_cat_anio" name="id_cat_anio" value="{{ optional($item)->id_cat_anio ?? '' }}" />
                                <x-template-form.template-form-input-hidden id="num_turno_sistema" name="num_turno_sistema" value="{{ optional($item)->num_turno_sistema ?? '' }}" />
                                <x-template-form.template-form-input-hidden id="id_cat_clave_aux" name="id_cat_clave_aux" value="{{ optional($item)->id_cat_clave ?? '' }}" />
                                <x-template-form.template-form-input-hidden id="rfc_remitente_bool" name="rfc_remitente_bool" value="{{ optional($item)->rfc_remitente_bool ?? '' }}" />
                                <x-template-form.template-form-input-hidden id="es_doc_fisico" name="es_doc_fisico" value="{{ optional($item)->es_doc_fisico ?? '' }}" />
                                <x-template-form.template-form-input-hidden id="son_mas_remitentes" name="son_mas_remitentes" value="{{ optional($item)->son_mas_remitentes ?? '' }}" />

                                {{-- ====== NUEVO: hidden para recordar estado del bloque de archivos ====== --}}
                                <x-template-form.template-form-input-hidden id="habilitar_carga" name="habilitar_carga" value="{{ old('habilitar_carga','') }}" />

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
                                </div>

                                <br>
                                <x-template-tittle.tittle-caption-secon tittle="Información general" />

                                <div class="row">
                                    <x-template-form.template-form-input-required label="No. Documento" type="text" name="num_documento" placeholder="NO. DOCUMENTO" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete="" value="{{ optional($item)->num_documento ?? '' }}" />
                                    <x-template-form.template-form-input-required label="Folio de gestión" type="text" name="folio_gestion" placeholder="FOLIO DE GESTIÓN" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete="" value="{{ optional($item)->folio_gestion ?? '' }}" />
                                    <x-template-form.template-form-input-required label="Fecha de doc." type="date" name="fecha_documento" placeholder="" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete="" value="{{ optional($item)->fecha_documento ?? '' }}" />
                                </div>

                                <div class="row">
                                    <x-template-form.template-form-input-required label="Fecha de inicio" type="date" name="fecha_inicio" placeholder="" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete="" value="{{ optional($item)->fecha_inicio ?? '' }}" />
                                    <x-template-form.template-form-input-required label="Fecha fin" type="date" name="fecha_fin" placeholder="" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete="" value="{{ optional($item)->fecha_fin ?? '' }}" />
                                    <x-template-form.template-form-input-check idDiv="id_checkbox_Template_tooltip_fisico" name="es_doc_fisico_box" label="¿El documento es físico?" />
                                </div>

                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectEntidad" :selectEdit="$selectEntidadEdit" name="id_cat_entidad" tittle="Entidad" grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-8" />
                                    <x-template-form.template-form-input-required label="Horas respuesta" type="integer" name="horas_respuesta" placeholder="HORAS DE RESPUESTA" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete="" value="{{ optional($item)->horas_respuesta ?? '' }}" />
                                </div>

                                <div class="row">
                                    <x-template-form.template-form-input-text-area grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" label="Asunto" name="asunto" placeholder="ASUNTO" value="{{ optional($item)->asunto ?: '' }}" />
                                </div>

                                <div class="row">
                                    <x-template-form.template-form-input-text-area grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" label="Observaciones" name="observaciones" placeholder="OBSERVACIONES" value="{{ optional($item)->observaciones ?: '' }}" />
                                </div>

                                <x-template-tittle.tittle-caption-secon tittle="Turnar A" />
                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectArea" :selectEdit="$selectAreaEdit" name="id_cat_area" tittle="Área" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                    <x-template-form.template-form-select-required :selectValue="$selectUser" :selectEdit="$selectUserEdit" name="id_usuario_area" tittle="Usuario" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                    <x-template-form.template-form-select-required :selectValue="$selectEnlace" :selectEdit="$selectEnlaceEdit" name="id_usuario_enlace" tittle="Enlace" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                </div>

                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectUnidad" :selectEdit="$selectUnidadEdit" name="id_cat_unidad" tittle="Unidad" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                    <x-template-form.template-form-select-required :selectValue="$selectCoordinacion" :selectEdit="$selectCoordinacionEdit" name="id_cat_coordinacion" tittle="Coordinación" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                </div>

                                <x-template-tittle.tittle-caption-secon tittle="Documento de entrada" />
                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectStatus" :selectEdit="$selectStatusEdit" name="id_cat_estatus" tittle="Estatus" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                    <x-template-form.template-form-select-required :selectValue="$selectTramite" :selectEdit="$selectTramiteEdit" name="id_cat_tramite" tittle="Tramite" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                    <x-template-form.template-form-select-required :selectValue="$selectClave" :selectEdit="$selectClaveEdit" name="id_cat_clave" tittle="Clave" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                </div>

                                {{-- =============== CARGA DE ARCHIVOS =============== --}}
                                <x-template-tittle.tittle-caption-secon tittle="Carga de archivos" />

                                <div class="row">
                                    {{-- ÚNICO CHECKBOX maestro --}}
                                    <x-template-form.template-form-input-check
                                        idDiv="habilitar_carga_archivos"
                                        name="habilitar_carga_box"
                                        label="¿Adjuntar oficio y/o anexos?" />
                                </div>

                                {{-- Contenedor que se muestra/oculta con el checkbox maestro --}}
                                <div id="contenedor_carga_archivos" style="display:none; margin-top: 6px;">

                                    {{-- Oficio (Max 1) --}}
                                    <div style="display: flex; align-items: center; gap: 12px; margin-top: 6px; margin-bottom: 10px;">
                                        <x-template-tittle.tittle-caption-secon tittle="Oficios (Max 1)" />
                                        <label for="file_oficio_entrada" id="label_oficio_entrada"
                                            style="background-color: white; color: red; font-weight: normal; font-size: 1rem; padding: 5px 15px; cursor: pointer; display: flex; align-items: center; text-decoration: none; margin-left: 4px;">
                                            <i class="fa fa-arrow-up" id="icon_oficio_entrada" style="margin-right: 5px;"></i>
                                            Cargar
                                        </label>
                                        <input type="file" id="file_oficio_entrada" name="file_oficio_entrada"
                                               style="display:none;" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    </div>
                                    <div id="container_oficio_entrada_vacio" class="rectangulo">Sin contenido</div>
                                    <div id="container_oficio_entrada"></div>

                                    {{-- Anexos (Max 3 con UN SOLO input) --}}
                                    <div style="display: flex; align-items: center; gap: 12px; margin-top: 18px; margin-bottom: 8px;">
                                        <x-template-tittle.tittle-caption-secon tittle="Anexos (Max 3)" />
                                        <label for="file_anexo_entrada" id="label_anexo_entrada"
                                            style="background-color: white; color: red; font-weight: normal; font-size: 1rem; padding: 5px 15px; cursor: pointer; display: flex; align-items: center; text-decoration: none;">
                                            <i class="fa fa-arrow-up" id="icon_anexo_entrada" style="margin-right: 5px;"></i>
                                            Cargar anexos
                                        </label>
                                        <input
                                            type="file"
                                            id="file_anexo_entrada"
                                            name="file_anexo_entrada[]"
                                            multiple
                                            style="display:none;"
                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    </div>

                                    <div id="container_anexo_entrada_vacio" class="rectangulo">Sin contenido</div>
                                    <div id="container_anexo_entrada"></div>
                                </div>
                                {{-- ============ FIN CARGA DE ARCHIVOS ============ --}}

                                <p class="card-description" style="font-size:1rem; font-weight:bold; color:#000; display:inline-block; margin-right:30px;">
                                    Información de remitente
                                </p>

                                <x-template-form.template-form-input-check idDiv="mas_remitentes" name="son_mas_remitentes_box" label="¿Cuenta con varios remitentes?" />

                                <div id="_hidden_select">
                                    <div class="row">
                                        <x-template-form.template-form-select-required :selectValue="$selectRemitente" :selectEdit="$selectRemitenteEdit" name="id_cat_remitente" tittle="Remitente" grid="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8" />
                                        <x-template-form.template-form-input-check idDiv="id_checkbox_Template_tooltip" name="idcheckboxTemplate" label="Agregar remitente" />
                                    </div>
                                </div>

                                <div class="row">
                                    <x-template-form.template-form-input-required label="Puesto remitente" type="text" name="puesto_remitente" placeholder="PUESTO DE REMITENTE" grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" autocomplete="" value="{{ optional($item)->puesto_remitente ?? '' }}" />
                                </div>

                                <div id="mostrar_ocultar_template">
                                    <div class="row">
                                        <x-template-form.template-form-input-required label="Nombre" type="text" name="remitente_nombre" placeholder="NOMBRE" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete="" value="" />
                                        <x-template-form.template-form-input-required label="Apellido paterno" type="text" name="remitente_apellido_paterno" placeholder="APELLIDO PATERNO" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete="" value="" />
                                    </div>
                                    <div class="row">
                                        <x-template-form.template-form-input-required label="Apellido materno" type="text" name="remitente_apellido_materno" placeholder="APELLIDO MATERNO" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete="" value="" />
                                        <x-template-form.template-form-input-required label="RFC" type="text" name="remitente_rfc" placeholder="RFC" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete="" value="" />
                                    </div>
                                </div>

                                <div id="mostrar_ocultar_mas_remitentes">
                                    <div class="row">
                                        <x-template-form.template-form-input-text-area grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" label="Remitentes" name="remitente" placeholder="REMITENTES" value="{{ optional($item)->remitente ?: '' }}" />
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

{{-- Script para mostrar/ocultar el bloque de archivos con EL ÚNICO CHECKBOX y limitar anexos a 3 --}}
<script>
(function ($) {
  'use strict';

  var $checkBox   = $('#habilitar_carga_box');
  var $hiddenFlag = $('#habilitar_carga');
  var $container  = $('#contenedor_carga_archivos');

  function _show($el){ if(window.showDiv){ return window.showDiv($el.attr('id')); } $el.show(); }
  function _hide($el){ if(window.hideDiv){ return window.hideDiv($el.attr('id')); } $el.hide(); }

  function syncUI() {
    if (!!$hiddenFlag.val()) {
      _show($container);
    } else {
      _hide($container);
      // limpiar selección si se apaga
      $('#file_oficio_entrada, #file_anexo_entrada').val('');
    }
  }

  $(document).ready(function () {
    if (typeof window.tooltip === 'function') {
      window.tooltip('#habilitar_carga_archivos', 'Habilita la sección para subir oficio y anexos');
    }

    syncUI();

    $checkBox.on('change', function () {
      $hiddenFlag.val($(this).is(':checked') ? true : '');
      syncUI();
    });

    // Limitar anexos a máx 3 en un solo input
    $('#file_anexo_entrada').on('change', function(){
      var files = this.files;
      if (files && files.length > 3) {
        this.value = '';
        if (window.notyfEM && window.notyfEM.error) {
          window.notyfEM.error('Puedes seleccionar como máximo 3 anexos.');
        } else {
          alert('Puedes seleccionar como máximo 3 anexos.');
        }
      }
    });
  });

})(jQuery);
</script>
