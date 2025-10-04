<!-- MODAL REPLY -->
<style>
  /* Centrado y ancho del modal */
  #modalReply .modal-dialog{
    width: 640px;         /* cámbialo si quieres otro ancho */
    max-width: 640px;
    margin: 8vh auto;     /* centrado vertical/horizontal */
  }

  /* Deja que la altura se adapte y NO ocultes contenido */
  #modalReply .modal-content{
    width: 100%;
    background:#fff;
    border-radius:12px;
    /* importante: no ocultar nada para que se vea el footer */
    overflow: visible !important;
  }

  /* El body puede scrollear si el contenido crece,
     pero no recortamos header/footer */
  #modalReply .modal-body{
    max-height: 70vh;              /* altura visual cómoda */
    overflow: auto;                /* scroll interno si hace falta */
    padding-bottom: 8px;           /* espacio para que no “toque” el footer */
  }

  /* Inputs */
  .custom-input-container { margin-bottom: 12px; }
  .custom-input-label { display:block; font-weight:600; margin-bottom:6px; }
  .custom-input-field {
    width:100%; border:1px solid #ddd; border-radius:6px; padding:8px 10px;
  }
  .custom-input-field:focus {
    outline:none; border-color:#10312b; box-shadow:0 0 0 2px rgba(16,49,43,.15);
  }
</style>

<x-template-modal.modal-template
  tittle="Responder"
  idModal="modalReply"
  idCancel="cancel_reply"
  idConfirm="confir_reply"
  functionConfirm="confirmarReply();"
  width="640px"     {{-- alineado con el CSS arriba --}}
  height="auto">    {{-- deja que el contenido crezca --}}

  <p style="font-size:16px; margin-bottom:12px; text-align:center;">
    Respuesta del folio de gestión:
    <label id="name_folio_gestion" style="font-weight:bold;"></label>.
  </p>

  {{-- Fecha a ancho completo para que no se quiebre el label --}}
  <div class="row">
    <x-template-form.template-form-input-required
      label="Fecha" type="date" name="fecha_inicio" placeholder=""
      grid="col-12" autocomplete=""
      value="" />
  </div>

  <div class="custom-input-container">
    <label class="custom-input-label" for="observacion">Observaciones</label>
    <input type="text" id="observacion" class="custom-input-field" maxlength="200" placeholder="Observaciones…">
  </div>

  <div class="custom-input-container">
    <label class="custom-input-label" for="asunto">Asunto</label>
    <input type="text" id="asunto" class="custom-input-field" maxlength="250" placeholder="Asunto…">
  </div>

  <input type="hidden" id="id_correspondencia_x" />
</x-template-modal.modal-template>

