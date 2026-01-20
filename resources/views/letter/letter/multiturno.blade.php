<!-- MODAL MULTI TURNO (solo vista, sin backend) -->

<style>
  /* Estilo similar al Turnar A / Reply */
  .modal-multiturno{padding:10px 20px;font-family:'Segoe UI',Arial,sans-serif;color:#10312b}
  .modal-multiturno .title{font-weight:700;color:#10312b;margin-bottom:12px;font-size:24px;text-align:center;border-bottom:2px solid #e6ecec;padding-bottom:8px}
  .modal-multiturno .note{text-align:center;margin-bottom:12px;font-size:15px;color:#5a6a6a}
  .modal-multiturno .subtitle{font-weight:600;color:#0f2b26;margin:16px 0 8px;text-align:left;font-size:18px;border-left:4px solid #10312b;padding-left:8px}
  .modal-multiturno label{color:#5a6a6a;font-weight:600;font-size:14px}
  .modal-multiturno .divider{height:1px;background:#e6ecec;margin:18px 0}

  /* Inputs */
  .modal-multiturno .mt-field{
    width:100%;
    border:1px solid #ddd;
    border-radius:8px;
    padding:10px 12px;
    background:#fff;
  }
  .modal-multiturno .mt-field:focus{
    outline:none;
    border-color:#10312b;
    box-shadow:0 0 0 2px rgba(16,49,43,.15);
  }

  /* Para que el footer sticky del layout no se meta */
  body.modal-open-multiturno .form-actions,
  body.modal-open-multiturno .app-sticky-footer,
  body.modal-open-multiturno .sticky-actions{display:none!important}
</style>

<x-template-modal.modal-template
  tittle="Turnar a varios"
  idModal="modalMultiTurno"
  idCancel="mt_cancel"
  idConfirm="mt_confirm"
  functionConfirm="confirmarMultiTurno();"
  width="1100px"
  height="700px">

  <div class="modal-multiturno">

    <div class="title">Turnar a varios</div>

    <p class="note" style="font-size:16px;">
      Turnar el folio de gestión:
      <b><span id="mt_name_folio_gestion">—</span></b>
      a una o varias áreas destino.
    </p>

    <div class="divider"></div>

    <div class="subtitle">Destino</div>

    <div class="row">
      <div class="col-12 col-md-5">
        <div class="form-group">
          <label for="mt_areas_destino">Áreas destino</label>

          <select id="mt_areas_destino" class="selectpicker form-control"
                  multiple data-live-search="true" data-actions-box="true"
                  title="Selecciona una o varias áreas">
            {{-- SOLO VISTA: opciones dummy --}}
            <option value="1">AREA DEMO 1</option>
            <option value="2">AREA DEMO 2</option>
            <option value="3">AREA DEMO 3</option>
          </select>

          <small class="text-muted">Después conectamos este select al catálogo real.</small>
        </div>
      </div>

      <div class="col-12 col-md-7">
        <div class="form-group">
          <label for="mt_observaciones">Observaciones (opcional)</label>
          <textarea id="mt_observaciones" class="mt-field" rows="4"
                    placeholder="ESCRIBE UNA NOTA PARA LOS DESTINATARIOS (OPCIONAL)"></textarea>
        </div>
      </div>
    </div>

    <input type="hidden" id="mt_id_correspondencia" value="" />

  </div>
</x-template-modal.modal-template>





