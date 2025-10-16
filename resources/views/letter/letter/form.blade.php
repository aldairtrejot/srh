<!-- TEMPLATE APP -->
<x-template-app.app-layout>
  <?php include(resource_path('views/config.php')); ?>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    .rectangulo {
      width: 120px;
      height: 150px;
      border: 1px solid #ddd;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #9aa0a6;
      background: #fff;
      box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
      user-select: none;
      padding: 6px;
    }

    .icon-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      height: 100%;
    }

    .doc-icon {
      font-size: 42px;
      line-height: 1;
      color: #9aa0a6;
    }

    .file-pills-row {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 8px;
    }

    .file-pill {
      display: inline-block;
      padding: 6px 10px;
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      background: #f9fafb;
      font-size: .875rem;
      color: #374151;
      max-width: 280px;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
    }

    .upload-row {
      display: flex;
      gap: 16px;
      align-items: flex-start;
      flex-wrap: wrap;
    }

    .upload-col {
      flex: 1 1 420px;
      min-width: 300px;
    }

    .btn-upload {
      background: #fff;
      color: #f00;
      font-weight: normal;
      font-size: 1rem;
      padding: 5px 15px;
      cursor: pointer;
      display: flex;
      align-items: center;
      text-decoration: none;
    }

    .btn-upload i {
      margin-right: 5px;
    }

    .section-note {
      font-size: 1rem;
      font-weight: bold;
      color: #BC955C;
      font-style: italic;
      margin: 0;
    }

    .warn-msg {
      display: none;
      color: #c0392b;
      font-weight: 600;
      margin-top: 6px;
    }
  </style>

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
              <form id="myForm" action="{{ route('letter.save') }}" method="POST" class="form-sample"
                enctype="multipart/form-data">
                @csrf

                {{-- ===== Config accesible para JS ===== --}}
                <script>
                  window.LETTER = {
                    collectionAreaUrl: "{{ route('letter.collectionArea') }}",
                    includeInactiveArea3: {{ isset($isEdit) && $isEdit ? 'true' : 'false' }},
                    isEdit: {{ isset($isEdit) && $isEdit ? 'true' : 'false' }},
                    initials: {
                      area1: "{{ old('id_cat_area_1', optional($item)->id_cat_area_1) }}",
                      area2: "{{ old('id_cat_area_2', optional($item)->id_cat_area_2) }}",
                      area3: "{{ old('id_cat_area', optional($item)->id_cat_area) }}"
                    }
                  };
                </script>

                {{-- ===== HIDDEN FIELDS ===== --}}
                <x-template-form.template-form-input-hidden id="bool_user_role" name="bool_user_role"
                  value="{{ $letterAdminMatch ?? '' }}" />
                <x-template-form.template-form-input-hidden id="id_tbl_correspondencia" name="id_tbl_correspondencia"
                  value="{{ optional($item)->id_tbl_correspondencia ?? '' }}" />

                @php
                  $fc = $item->fecha_captura ?? null;
                  try {
                    $fc_fmt = \Carbon\Carbon::parse($fc)->format('d/m/Y');
                  } catch (\Exception $e) {
                    $fc_fmt = is_string($fc) ? $fc : now()->format('d/m/Y');
                  }
                @endphp
                <x-template-form.template-form-input-hidden id="fecha_captura" name="fecha_captura"
                  value="{{ $fc_fmt }}" />

                <x-template-form.template-form-input-hidden id="id_cat_anio" name="id_cat_anio"
                  value="{{ optional($item)->id_cat_anio ?? '' }}" />
                <x-template-form.template-form-input-hidden id="num_turno_sistema" name="num_turno_sistema"
                  value="{{ optional($item)->num_turno_sistema ?? '' }}" />
                <x-template-form.template-form-input-hidden id="id_cat_clave_aux" name="id_cat_clave_aux"
                  value="{{ optional($item)->id_cat_clave ?? '' }}" />
                <x-template-form.template-form-input-hidden id="rfc_remitente_bool" name="rfc_remitente_bool"
                  value="{{ optional($item)->rfc_remitente_bool ?? '' }}" />
                <x-template-form.template-form-input-hidden id="es_doc_fisico" name="es_doc_fisico"
                  value="{{ optional($item)->es_doc_fisico ?? '' }}" />
                <x-template-form.template-form-input-hidden id="son_mas_remitentes" name="son_mas_remitentes"
                  value="{{ optional($item)->son_mas_remitentes ?? '' }}" />

                {{-- Destino Alfresco --}}
                <x-template-form.template-form-input-hidden name="id_cat_entrada"
                  value="{{ config('custom_config.CONFIG_CLOUD_ENTRADA') }}" />
                <x-template-form.template-form-input-hidden name="id_cat_tipo_oficio"
                  value="{{ config('custom_config.CLOUD_ALFRESCO_CORRESPONDENCIA') }}" />

                {{-- Estado del bloque de archivos (checkbox UI) --}}
                <x-template-form.template-form-input-hidden id="habilitar_carga" name="habilitar_carga"
                  value="{{ old('habilitar_carga', '') }}" />

                {{-- ===== Encabezado de resumen ===== --}}
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

                {{-- ===== Información general ===== --}}
                <x-template-tittle.tittle-caption-secon tittle="Información general" />
                <div class="row">
                  <x-template-form.template-form-input-required label="No. Documento" type="text" name="num_documento"
                    placeholder="NO. DOCUMENTO" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                    value="{{ optional($item)->num_documento ?? '' }}" />

                  <x-template-form.template-form-input-required label="Folio de gestión" type="text"
                    name="folio_gestion" placeholder="FOLIO DE GESTIÓN"
                    grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                    value="{{ optional($item)->folio_gestion ?? '' }}" />

                  <x-template-form.template-form-input-required label="Fecha de doc." type="date" name="fecha_documento"
                    placeholder="" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                    value="{{ optional($item)->fecha_documento ?? '' }}" />
                </div>

                <div class="row">
                  <x-template-form.template-form-input-required label="Fecha de inicio" type="date" name="fecha_inicio"
                    placeholder="" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                    value="{{ optional($item)->fecha_inicio ?? '' }}" />

                  <x-template-form.template-form-input-required label="Fecha fin" type="date" name="fecha_fin"
                    placeholder="" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                    value="{{ optional($item)->fecha_fin ?? '' }}" />

                  <x-template-form.template-form-input-check idDiv="id_checkbox_Template_tooltip_fisico"
                    name="es_doc_fisico_box" label="¿El documento es físico?" />
                </div>

                <div class="row">
                  <x-template-form.template-form-select-required :selectValue="$selectEntidad"
                    :selectEdit="$selectEntidadEdit" name="id_cat_entidad" tittle="Entidad"
                    grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-8" />

                  <x-template-form.template-form-input-required label="Horas respuesta" type="integer"
                    name="horas_respuesta" placeholder="HORAS DE RESPUESTA"
                    grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" autocomplete=""
                    value="{{ optional($item)->horas_respuesta ?? '' }}" />
                </div>

                <div class="row">
                  <x-template-form.template-form-input-text-area grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"
                    label="Asunto" name="asunto" placeholder="ASUNTO" value="{{ optional($item)->asunto ?: '' }}" />
                </div>

                <div class="row">
                  <x-template-form.template-form-input-text-area grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"
                    label="Observaciones" name="observaciones" placeholder="OBSERVACIONES"
                    value="{{ optional($item)->observaciones ?: '' }}" />
                </div>

                {{-- ===== Turnar A ===== --}}
                <x-template-tittle.tittle-caption-secon tittle="Turnar A" />
                <div class="row">
                  <x-template-form.template-form-select-required :selectValue="$selectArea1"
                    :selectEdit="$selectArea1Edit" :valueSelected="optional($item)->id_cat_area_1" name="id_cat_area_1"
                    tittle="CRH" grid="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4" />

                  <x-template-form.template-form-select-required :selectValue="$selectArea2"
                    :selectEdit="$selectArea2Edit" :valueSelected="optional($item)->id_cat_area_2" name="id_cat_area_2"
                    tittle="CRHTOD" grid="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4" />

                  <x-template-form.template-form-select-required :selectValue="$selectArea"
                    :selectEdit="$selectAreaEdit" name="id_cat_area" tittle="Área"
                    grid="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4" />
                </div>

                <div class="row">
                  <x-template-form.template-form-select-required :selectValue="$selectUser"
                    :selectEdit="$selectUserEdit" name="id_usuario_area" tittle="Usuario"
                    grid="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-5" />


                  <x-template-form.template-form-select-required :selectValue="$selectEnlace"
                    :selectEdit="$selectEnlaceEdit" name="id_usuario_enlace" tittle="Enlace"
                    grid="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-5" />
                </div>

                <div class="row">
                  <x-template-form.template-form-select-required :selectValue="$selectUnidad"
                    :selectEdit="$selectUnidadEdit" name="id_cat_unidad" tittle="Unidad"
                    grid="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-5" />

                  <x-template-form.template-form-select-required :selectValue="$selectCoordinacion"
                    :selectEdit="$selectCoordinacionEdit" name="id_cat_coordinacion" tittle="Coordinación"
                    grid="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-5" />
                </div>

                {{-- ===== Documento de entrada ===== --}}
                <x-template-tittle.tittle-caption-secon tittle="Documento de entrada" />
                <div class="row">
                  {{-- *** ESTATUS vuelve a TU COMPONENTE (mantiene diseño) *** --}}
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

                {{-- ===== Información de remitente ===== --}}
                <p class="card-description"
                  style="font-size:1rem; font-weight:bold; color:#000; display:inline-block; margin-right:30px;">
                  Información de remitente
                </p>
                <x-template-form.template-form-input-check idDiv="mas_remitentes" name="son_mas_remitentes_box"
                  label="¿Cuenta con varios remitentes?" />

                <div id="_hidden_select">
                  <div class="row">
                    <x-template-form.template-form-select-required :selectValue="$selectRemitente"
                      :selectEdit="$selectRemitenteEdit" name="id_cat_remitente" tittle="Remitente"
                      grid="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8" />

                    <x-template-form.template-form-input-check idDiv="id_checkbox_Template_tooltip"
                      name="idcheckboxTemplate" label="Agregar remitente" />
                  </div>
                </div>

                <div id="mostrar_ocultar_template" style="display:none;">
                  <div class="row">
                    <x-template-form.template-form-input-required label="Nombre" type="text" name="remitente_nombre"
                      placeholder="NOMBRE" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete=""
                      value="" />

                    <x-template-form.template-form-input-required label="Apellido paterno" type="text"
                      name="remitente_apellido_paterno" placeholder="APELLIDO PATERNO"
                      grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete="" value="" />
                  </div>

                  <div class="row">
                    <x-template-form.template-form-input-required label="Apellido materno" type="text"
                      name="remitente_apellido_materno" placeholder="APELLIDO MATERNO"
                      grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete="" value="" />

                    <x-template-form.template-form-input-required label="RFC" type="text" name="remitente_rfc"
                      placeholder="RFC" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6" autocomplete="" value="" />
                  </div>
                </div>

                <div id="mostrar_ocultar_mas_remitentes" style="display:none;">
                  <div class="row">
                    <x-template-form.template-form-input-text-area grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"
                      label="Remitentes" name="remitente" placeholder="REMITENTES"
                      value="{{ optional($item)->remitente ?: '' }}" />
                  </div>
                </div>

                <div class="row">
                  <x-template-form.template-form-input-required label="Puesto remitente" type="text"
                    name="puesto_remitente" placeholder="PUESTO DE REMITENTE"
                    grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" autocomplete=""
                    value="{{ optional($item)->puesto_remitente ?? '' }}" />
                </div>

                {{-- ===== Carga de archivos ===== --}}
                <x-template-tittle.tittle-caption-secon tittle="Carga de archivos" />
                <div class="row">
                  <x-template-form.template-form-input-check idDiv="habilitar_carga_archivos" name="habilitar_carga_box"
                    label="¿Adjuntar oficio y/o anexos?" />
                </div>

                <div id="contenedor_carga_archivos" style="display:none; margin-top:6px;">
                  <p class="card-description section-note">Documentos de Entrada</p>

                  <div class="upload-row">
                    <div class="upload-col">
                      <div style="display:flex; align-items:center;">
                        <x-template-tittle.tittle-caption-secon tittle="Oficios (Max 1)" />
                        <label for="file_oficio_entrada" id="label_oficio_entrada" class="btn-upload">
                          <i class="fa fa-arrow-up" id="icon_oficio_entrada"></i> Cargar
                        </label>
                        <input type="file" id="file_oficio_entrada" name="file_oficio_entrada" style="display:none;"
                          accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                      </div>
                      <div id="container_oficio_entrada_vacio" class="rectangulo">Sin contenido</div>
                      <div id="container_oficio_entrada"></div>
                      <div id="msg_oficio_req" class="warn-msg">Hace falta cargar un oficio.</div>
                    </div>

                    <div class="upload-col">
                      <div style="display:flex; align-items:center;">
                        <x-template-tittle.tittle-caption-secon tittle="Anexos (Max 3)" />
                        <label for="file_anexo_entrada" id="label_anexo_entrada" class="btn-upload">
                          <i class="fa fa-arrow-up" id="icon_anexo_entrada"></i> Cargar
                        </label>
                        <input type="file" id="file_anexo_entrada" name="file_anexo_entrada[]" multiple
                          style="display:none;" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                      </div>
                      <div id="container_anexo_entrada_vacio" class="rectangulo">Sin contenido</div>
                      <div id="container_anexo_entrada"></div>
                    </div>
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

<!-- === Quitar obligatoriedad de Área 2 sin tocar Área (id_cat_area) === -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var a2 = document.querySelector("select[name='id_cat_area_2']");
    if (a2) {
      a2.required = false;
      a2.removeAttribute('required');
      a2.removeAttribute('aria-required');
      a2.removeAttribute('data-rule-required');
      if (a2.setCustomValidity) a2.setCustomValidity('');
      if (typeof $ !== 'undefined' && $.fn.selectpicker) {
        $(a2).prop('required', false).selectpicker('refresh');
      }
    }
  });
</script>

{{-- JS existentes --}}
<script src="{{ asset('assets/js/app/other/rfc.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/function/function.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/letter/validate.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/letter/form.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/letter/select.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/letter/deps-areas.js') }}"></script>
<script src="{{ asset('assets/js/app/letter/letter/upload_form_cloud.js') }}"></script>