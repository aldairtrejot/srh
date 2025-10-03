<!-- MODAL REPLY -->
<style>
  /* Centrado y tamaño fijo del modal */
  #modalReply .modal-dialog{
    width: 430px;               /* ancho fijo */
    max-width: 430px;
    margin: calc((100vh - 380px)/2) auto; /* centra vertical y horizontal */
  }
  #modalReply .modal-content{
    width: 430px;               /* ancho fijo */
    height: 380px;              /* alto fijo */
    background:#fff;
    border-radius:12px;
    overflow:hidden;            /* evita desbordes feos */
  }
  /* Si el template usa .modal-body, controla el scroll interno */
  #modalReply .modal-body{
    max-height: calc(380px - 120px); /* aprox. descuenta header/footer */
    overflow:auto;
  }

  /* Responsive de emergencia en pantallas chicas */
  @media (max-width: 480px){
    #modalReply .modal-dialog, #modalReply .modal-content{
      width: 92vw; max-width: 92vw; height: auto;
    }
    #modalReply .modal-body{ max-height: 60vh; }
  }

  /* Estilos de tus inputs */
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
  tittle="Turnar con copia"
  idModal="modalReply"
  idCancel="cancel_reply"
  idConfirm="confir_reply"
  functionConfirm="confirmarReply();"
  width="430px"   {{-- opcional, por si tu componente lo usa --}}
  height="380px"> {{-- opcional, por si tu componente lo usa --}}

  <p style="font-size:16px; margin-bottom:10px;">
    Respuesta del folio de gestión:
    <label id="name_folio_gestion" style="font-weight:bold;"></label>.
  </p>

  <div class="custom-input-container">
    <label class="custom-input-label" for="observacion">Nombre</label>
    <input type="text" id="observacion" class="custom-input-field" maxlength="200" placeholder="Nombre…">
  </div>

  <div class="custom-input-container">
    <label class="custom-input-label" for="asunto">Asunto</label>
    <input type="text" id="asunto" class="custom-input-field" maxlength="250" placeholder="Asunto…">
  </div>

  <input type="hidden" id="id_correspondencia_x" />
</x-template-modal.modal-template>


