<!-- MODAL REPLY -->
<style>
  #replyModal .modal-dialog{ width:640px; max-width:640px; margin:8vh auto; }
  #replyModal .modal-content{ width:100%; background:#fff; border-radius:12px; overflow:visible!important; }
  #replyModal .modal-body{ max-height:70vh; overflow:auto; padding-bottom:8px; }
  .reply-input-container{ margin-bottom:12px; }
  .reply-input-label{ display:block; font-weight:600; margin-bottom:6px; text-align:left; }
  .reply-input-field{ width:100%; border:1px solid #ddd; border-radius:6px; padding:8px 10px; }
  .reply-input-field:focus{ outline:none; border-color:#10312b; box-shadow:0 0 0 2px rgba(16,49,43,.15); }
  .reply-upload-row{ display:flex; gap:16px; flex-wrap:wrap; margin-top:10px; }
  .reply-upload-col{ flex:1 1 320px; background:#fff; border:1px solid #eee; border-radius:10px; padding:12px; }
  .reply-file-pill{
    display:flex; align-items:center; gap:8px;
    border:1px solid #ddd; border-radius:999px; padding:6px 10px; margin-top:8px;
    font-size:.95rem; background:#fafafa;
  }
  .reply-file-pill .reply-remove-btn{ border:none; background:transparent; cursor:pointer; font-weight:700; font-size:1rem; line-height:1; }
  .reply-rectangulo{
    width:100%; min-height:60px; border:1px dashed #cfd3d7; border-radius:8px;
    display:flex; align-items:center; justify-content:center; color:#9aa0a6; padding:8px;
  }
</style>

<x-template-modal.modal-template
  tittle="Responder"
  idModal="replyModal"
  idCancel="reply_cancel"
  idConfirm="reply_confirm"
  functionConfirm="confirmReplyModal();"
  width="640px"
  height="auto">

  <p style="font-size:16px; margin-bottom:12px; text-align:center;">
    Respuesta del folio de gestión:
    <label id="reply_folio_label" style="font-weight:bold;"></label>.
  </p>

  <div class="row">
    <x-template-form.template-form-input-required
      label="Fecha del documento" type="date" name="fecha_inicio" placeholder=""
      grid="col-12" autocomplete="" value="" id="reply_fecha" />
  </div>

  <div class="row">
    <x-template-form.template-form-input-required
      label="Fecha de captura" type="date" name="fecha_fin" placeholder=""
      grid="col-12" autocomplete="" value="" id="reply_fechafin" />
  </div>

  <div class="reply-input-container">
    <label class="reply-input-label" for="reply_asunto">Asunto</label>
    <input type="text" id="reply_asunto" class="reply-input-field" maxlength="250" placeholder="Asunto…">
  </div>

  <div class="reply-input-container">
    <label class="reply-input-label" for="reply_observacion">Observaciones</label>
    <input type="text" id="reply_observacion" class="reply-input-field" maxlength="200" placeholder="Observaciones…">
  </div>

  <!-- ====== SUBIDA DE ARCHIVOS (opcionales) ====== -->
  <div class="reply-upload-row">
    <div class="reply-upload-col">
      <div style="display:flex; align-items:center; gap:10px;">
        <x-template-tittle.tittle-caption-secon tittle="Oficios (Max 1)" />
        <label for="reply_file_oficio" id="reply_label_oficio"
               style="background-color:white; color:red; font-weight:normal; font-size:1rem; padding:5px 15px; cursor:pointer; display:flex; align-items:center; text-decoration:none;">
          <i class="fa fa-arrow-up" id="reply_icon_oficio" style="margin-right:5px;"></i>
          Cargar
        </label>
        <input type="file" id="reply_file_oficio" name="file_oficio_entrada"
               style="display:none;" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
      </div>

      <div id="reply_container_oficio_empty" class="reply-rectangulo" style="margin-top:8px;">Sin contenido</div>
      <div id="reply_container_oficio" style="margin-top:8px;"></div>

      <div id="reply_msg_oficio_req" style="display:none;color:#c0392b;font-weight:600;margin-top:6px;">
        Hace falta cargar un oficio.
      </div>
    </div>

    <div class="reply-upload-col">
      <div style="display:flex; align-items:center; gap:10px;">
        <x-template-tittle.tittle-caption-secon tittle="Anexos (Max 3)" />
        <label for="reply_file_anexos" id="reply_label_anexos"
               style="background-color:white; color:red; font-weight:normal; font-size:1rem; padding:5px 15px; cursor:pointer; display:flex; align-items:center; text-decoration:none;">
          <i class="fa fa-arrow-up" id="reply_icon_anexos" style="margin-right:5px;"></i>
          Cargar
        </label>
        <input type="file" id="reply_file_anexos" name="file_anexo_entrada[]"
               multiple style="display:none;" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
      </div>

      <div id="reply_container_anexos_empty" class="reply-rectangulo" style="margin-top:8px;">Sin contenido</div>
      <div id="reply_container_anexos" style="margin-top:8px;"></div>
    </div>
  </div>

  <input type="hidden" id="reply_correspondencia_id" />
</x-template-modal.modal-template>






