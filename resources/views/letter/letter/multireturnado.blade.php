<!-- MODAL MULTI RETURNADO (estilo COPIAS) -->

<style>
  #mr_table_style th, #mr_table_style td{
    font-size:15px;
    padding:5px 10px;
    height:45px;
  }
  #mr_table_style thead th{
    font-size:16px;
    height:35px;
  }

  /* Solo ocultamos los campos “extras”, NO el área */
  #mr_rest_wrap{ display:none; }

  .mr-btn-insertar{
    font-weight:bold;
    color:#10312b;
    background:transparent;
    border:none;
    cursor:pointer;
  }
</style>

<x-template-modal.modal-template
  tittle="Returnar a varios"
  idModal="modalMultiReturnado"
  idCancel="mr_cancel"
  idConfirm="mr_confirm"
  functionConfirm="confirmarMultiReturnado();"
  width="1400px"
  height="700px">

  <p style="font-size:16px;">
    Returnar el folio de gestión:
    <label id="mr_name_folio_gestion" style="font-weight:bold;"></label>.
  </p>

  <!-- ÁREA SIEMPRE VISIBLE -->
  <div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
      <div class="form-group">
        <label>Área</label>
        <select id="mr_area_destino" class="selectpicker" data-live-search="true" title="SELECCIONE" data-size="8"></select>
      </div>
    </div>
  </div>

  <!-- RESTO COMO COPIAS (se muestra al elegir Área) -->
  <div id="mr_rest_wrap">
    <div class="row">
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="mr_id_usuario_area" tittle="Usuario" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="mr_id_usuario_enlace" tittle="Enlace" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="mr_id_cat_tramite" tittle="Trámite" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
    </div>

    <div class="row">
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="mr_id_cat_clave" tittle="Clasif. Archivística" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="mr_id_cat_unidad" tittle="Unidad" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="mr_id_cat_coordinacion" tittle="Coordinación" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
    </div>

    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label>Observaciones (opcional)</label>
          <textarea id="mr_observaciones" class="form-control" rows="3"
            placeholder="ESCRIBE UNA NOTA PARA LOS DESTINATARIOS (OPCIONAL)"></textarea>
        </div>
      </div>
    </div>

    <div class="modal-buttons custom-modal-buttons">
      <button class="mr-btn-insertar" type="button" onclick="mrInsertarDestino();">Insertar</button>
    </div>
  </div>

  <!-- TABLA (debajo) -->
  <div class="table-responsive pt-3">
    <table id="mr_table_style" class="table table-bordered">
      <thead>
        <tr>
          <th>Menú</th>
          <th>Área / Zona</th>
          <th>Usuario</th>
          <th>Enlace</th>
          <th>Trámite</th>
          <th>Clasif. Archivística</th>
          <th>Unidad</th>
          <th>Coordinación</th>
        </tr>
      </thead>
      <tbody id="mr_tbody"></tbody>
    </table>
  </div>

  <input type="hidden" id="mr_id_correspondencia" value="" />

</x-template-modal.modal-template>




