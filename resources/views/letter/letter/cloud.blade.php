<!-- TEMPLATE APP-->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- token html-->

    <style>
      /* ====== Layout dos columnas (50/50) ====== */
      .main-container{
        display:flex;
        gap:24px;
        align-items:flex-start;
        flex-wrap:nowrap;           /* columnas lado a lado */
        width:100%;
      }
      .left-side, .right-side{
        flex:0 0 50%;
        max-width:50%;
        min-width:0;
      }
      @media (max-width: 1200px){
        .main-container{ flex-wrap:wrap; }
        .left-side, .right-side{ flex:1 1 100%; max-width:100%; }
      }

      /* Tarjeta simple del lado derecho */
      .reply-card{
        border:1px solid #eee;
        border-radius:10px;
        padding:14px;
        background:#fff;
        margin-bottom:18px;
      }
      /* Grid para resumen */
      .reply-grid{
        display:grid;
        grid-template-columns: 160px 1fr;
        gap:10px 12px;
      }
      .reply-label{ font-weight:600; color:#4e4e4e; }
      .reply-value{ color:#222; word-break:break-word; }

      /* Tu rectángulo existente por si acaso */
      .rectangulo{
        width:120px; height:140px; border:1px dashed #cfd3d7; border-radius:8px;
        display:flex; align-items:center; justify-content:center; color:#9aa0a6; padding:8px; background:#fff;
      }
    </style>

    <div class="main-panel">
      <div class="content-wrapper">
        <div class="row">
          <div class="col-md-12 grid-margin">
            <div class="row">
              <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">Control de gestión</h3>
                <h5 class="font-weight-normal mb-0">Correspondencia</h5>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-12 grid-margin stretch-card">
          <div class="card custom-card">
            <div class="card-body">

              <x-template-tittle.tittle-caption tittle="Cloud" route="{{ route('letter.list') }}" />

              <!-- Hidden inputs -->
              <x-template-form.template-form-input-hidden name="bool_user_role" value="{{ $letterAdminMatch }}" />
              <x-template-form.template-form-input-hidden name="id" value="{{ $item->id_tbl_correspondencia }}" />
              <x-template-form.template-form-input-hidden name="id_cat_area" value="{{ $item->id_cat_area }}" />
              <x-template-form.template-form-input-hidden name="id_cat_entrada" value="{{ config('custom_config.CONFIG_CLOUD_ENTRADA') }}" />
              <x-template-form.template-form-input-hidden name="id_cat_tipo_oficio" value="{{ config('custom_config.CLOUD_ALFRESCO_CORRESPONDENCIA') }}" />

              <!-- Encabezado (arriba de las 2 columnas) -->
              <x-template-tittle.tittle-caption-secon tittle="Doc. seleccionado" />
              <div class="contenedor">
                <div class="item">
                  <label class="etiqueta">No. Turno:</label>
                  <label id="_noOficio" class="valor"></label>
                </div>
                <div class="item">
                  <label class="etiqueta">No. Documento:</label>
                  <label id="_noCorrespondencia" class="valor"></label>
                </div>
                <div class="item">
                  <label class="etiqueta">Año:</label>
                  <label id="_noAnio" class="valor"></label>
                </div>
                <div class="item">
                  <label class="etiqueta">Fecha de inicio:</label>
                  <label id="_fechaInicio" class="valor"></label>
                </div>
                <div class="item">
                  <label class="etiqueta">Fecha fin:</label>
                  <label id="_fechaFin" class="valor"></label>
                </div>
              </div>

              <!-- Modal delete -->
              <x-template-form.template-form-delete tittleModal="modalBackdrop" cancelModal="cancelBtn" confirmButton="confirmBtn" />

              <!-- ====== Contenedor principal 50/50 (lado a lado) ====== -->
              <div class="main-container">

                <!-- Lado izquierdo: Documentos de Entrada -->
                <div class="left-side">
                  <br>
                  <p class="card-description"
                     style="font-size: 1rem; font-weight: bold; color: #BC955C; font-style: italic;">
                    Documentos de Entrada
                  </p>

                  <!-- OFICIOS ENTRADA -->
                  <div>
                    <div style="display:flex; align-items:center;">
                      <x-template-tittle.tittle-caption-secon tittle="Oficios (Max 1)" />
                      <label for="file_oficio_entrada" id="label_oficio_entrada"
                             style="background-color:white; color:red; font-weight:normal; font-size:1rem; padding:5px 15px; cursor:pointer; display:flex; align-items:center; text-decoration:none;">
                        <i class="fa fa-arrow-up" id="icon_oficio_entrada" style="margin-right:5px;"></i>
                        Cargar
                      </label>
                      <input type="file" id="file_oficio_entrada" style="display:none;">
                    </div>

                    <div id="container_oficio_entrada_vacio" class="rectangulo">Sin contenido</div>
                    <div id="container_oficio_entrada"></div>
                  </div>

                  <!-- ANEXOS ENTRADA -->
                  <div>
                    <div style="display:flex; align-items:center;">
                      <x-template-tittle.tittle-caption-secon tittle="Anexos (Max 3)" />
                      <label for="file_anexo_entrada" id="label_anexo_entrada"
                             style="background-color:white; color:red; font-weight:normal; font-size:1rem; padding:5px 15px; cursor:pointer; display:flex; align-items:center; text-decoration:none;">
                        <i class="fa fa-arrow-up" id="icon_anexo_entrada" style="margin-right:5px;"></i>
                        Cargar
                      </label>
                      <input type="file" id="file_anexo_entrada" style="display:none;">
                    </div>

                    <div id="container_anexo_entrada_vacio" class="rectangulo">Sin contenido</div>
                    <div id="container_anexo_entrada"></div>
                  </div>
                </div>

                <!-- Lado derecho: Documento de Respuesta -->
                <div class="right-side">
                  <br>
                  <p class="card-description"
                     style="font-size:1rem; font-weight:bold; color:#10312b; font-style:italic;">
                    Documento de Respuesta
                  </p>

                  <!-- Resumen (asunto/observaciones) -->
                  <div class="reply-card">
                    <x-template-tittle.tittle-caption-secon tittle="Resumen del oficio" />
                    <div class="reply-grid" style="margin-top:10px;">
                      <div class="reply-label">Asunto:</div>
                      <div id="resp_asunto" class="reply-value">—</div>

                      <div class="reply-label">Observaciones:</div>
                      <div id="resp_observaciones" class="reply-value">—</div>
                    </div>
                  </div>

                  <!-- Oficio enviado (salida) -->
                  <div class="reply-card">
                    <x-template-tittle.tittle-caption-secon tittle="Oficio enviado" />
                    <div id="container_oficio_salida_vacio" class="rectangulo" style="margin-top:8px;">Sin contenido</div>
                    <div id="container_oficio_salida" style="margin-top:8px;"></div>
                  </div>

                 <!-- Anexos enviados (SALIDA) -->
<div class="reply-card">
  <div style="display:flex; align-items:center;">
    <x-template-tittle.tittle-caption-secon tittle="Anexos enviados (Max 3)" />
    <label for="file_anexo_salida" id="label_anexo_salida"
           style="background-color:white; color:red; font-weight:normal; font-size:1rem; padding:5px 15px; cursor:pointer; display:flex; align-items:center; text-decoration:none; margin-left:8px;">
      <i class="fa fa-arrow-up" id="icon_anexo_salida" style="margin-right:5px;"></i>
      Cargar
    </label>
    <input type="file" id="file_anexo_salida" style="display:none;">
  </div>

  <div id="container_anexo_salida_vacio" class="rectangulo" style="margin-top:8px;">Sin contenido</div>
  <div id="container_anexo_salida" style="margin-top:8px;"></div>
</div>



              </div>
              <!-- /Contenedor principal -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- CODE SCRIPT-->
    <script src="{{ asset('assets/js/app/letter/cloud/cloud.js') }}"></script>
    <script src="{{ asset('assets/js/app/letter/letter/cloud.js') }}"></script>
</x-template-app.app-layout>

