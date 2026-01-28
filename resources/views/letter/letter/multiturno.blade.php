<!-- MODAL MULTI TURNO -->

<style>
  .modal-multiturno{padding:10px 20px;font-family:'Segoe UI',Arial,sans-serif;color:#10312b}
  .modal-multiturno .title{font-weight:700;color:#10312b;margin-bottom:12px;font-size:24px;text-align:center;border-bottom:2px solid #e6ecec;padding-bottom:8px}
  .modal-multiturno .note{text-align:center;margin-bottom:12px;font-size:15px;color:#5a6a6a}
  .modal-multiturno .subtitle{font-weight:600;color:#0f2b26;margin:16px 0 8px;text-align:left;font-size:18px;border-left:4px solid #10312b;padding-left:8px}
  .modal-multiturno label{color:#5a6a6a;font-weight:600;font-size:14px}
  .modal-multiturno .divider{height:1px;background:#e6ecec;margin:18px 0}

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

  .mt-table-wrap{margin-top:14px;border:1px solid #e6ecec;border-radius:10px;overflow:hidden;background:#fff}
  .mt-table-head{
    display:flex;justify-content:space-between;align-items:center;
    padding:10px 12px;background:#f7faf9;border-bottom:1px solid #e6ecec
  }
  .mt-table-head .mt-table-title{font-weight:700;color:#10312b}
  .mt-table{width:100%;border-collapse:collapse}
  .mt-table th,.mt-table td{border-top:1px solid #eef2f2;padding:10px 12px;font-size:13px;vertical-align:middle}
  .mt-table th{background:#fff;font-weight:700;color:#203433}
  .mt-empty{padding:16px 12px;color:#7a8a8a;font-size:13px}

  .mt-btn-del{
    border:none;background:#7b1e2b;color:#fff;border-radius:8px;
    width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;
    cursor:pointer;
  }
  .mt-btn-del:hover{filter:brightness(.95)}

  .mt-badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:999px;
    font-weight:700;
    font-size:12px;
    background:#e9f2f0;
    color:#10312b;
  }
  .mt-badge.new{background:#fff3e6;color:#a85b00}

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
          <label for="mt_area_destino">Áreas destino</label>

          <!-- ✅ OJO: ya NO es multiple. Es 1 por 1 -->
          <select
            id="mt_area_destino"
            class="selectpicker"
            data-live-search="true"
            title="Selecciona un área..."
            data-size="8">
          </select>

          <small class="text-muted">Selecciona un área y se agregará a la lista.</small>
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

    <!-- ✅ UNA SOLA TABLA (BD + NUEVOS) -->
    <div class="mt-table-wrap">
      <div class="mt-table-head">
        <div class="mt-table-title">Destinos</div>
        <div class="text-muted" style="font-size:12px;">Se guardarán al confirmar</div>
      </div>

      <div id="mt_empty" class="mt-empty">Aún no hay destinos para este folio.</div>

      <table class="mt-table" id="mt_table" style="display:none;">
        <thead>
          <tr>
            <th style="width:80px;">Menú</th>
            <th>Área</th>
            <th style="width:260px;">Folio turnado</th>
          </tr>
        </thead>
        <tbody id="mt_tbody"></tbody>
      </table>
    </div>

    <input type="hidden" id="mt_id_correspondencia" value="" />

  </div>
</x-template-modal.modal-template>










