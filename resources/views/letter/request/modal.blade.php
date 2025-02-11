<!-- MODAL-->
<x-template-modal.modal-small tittle="Actualizar número de nota." idModal="modalBackdrop" idCancel="cancelBtn"
    idConfirm="confirmBtn" functionConfirm="confirmRefreshOficio();" valueInput="" idInput="" />

<!-- MODAL ADD SOLICTANTE-->
<x-template-modal.modal-template tittle="Agregar Solicitante" idModal="modalSolicitante"
    idCancel="cancelBtn_solicitante" idConfirm="confir_sol" functionConfirm="confirmSolicitante();" width="600px"
    height="495px">
    <x-template-form.template-form-input-above label="Nombre" type="text" id="nombreSolicitante" placeholder="NOMBRE" />

    <x-template-form.template-form-input-above label="Primer apellido" type="text" id="primerApellidoSolicitante"
        placeholder="PRIMER APELLIDO" />

    <x-template-form.template-form-input-above label="Segundo apellido" type="text" id="segundoApellidoSolicitante"
        placeholder="SEGUNDO APELLIDO" />
</x-template-modal.modal-template>
