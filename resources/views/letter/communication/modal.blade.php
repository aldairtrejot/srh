<!-- MODAL-->
<x-template-modal.modal-small tittle="Actualizar número de oficio." idModal="modalBackdrop" idCancel="cancelBtn"
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

<!-- MODAL ADD REMITENTE-->
<x-template-modal.modal-template tittle="Agregar Destinatario" idModal="modalDestinatario"
    idCancel="cancelBtn_destinatario" idConfirm="confir_des" functionConfirm="confirmDestinatario();" width="600px"
    height="495px">
    <x-template-form.template-form-input-above label="Nombre" type="text" id="nombreDestinatario"
        placeholder="NOMBRE" />

    <x-template-form.template-form-input-above label="Primer apellido" type="text" id="primerApellidoDestinatario"
        placeholder="PRIMER APELLIDO" />

    <x-template-form.template-form-input-above label="Segundo apellido" type="text" id="segundoApellidoDestinatario"
        placeholder="SEGUNDO APELLIDO" />
</x-template-modal.modal-template>

<!-- MODAL ADD REMITENTE-->
<x-template-modal.modal-template tittle="Agregar Tema" idModal="modalTema" idCancel="cancelBtn_tema"
    idConfirm="confir_tema" functionConfirm="confirmTema();" width="600px" height="285px">
    <x-template-form.template-form-input-above label="Tema" type="text" id="descripcionTema"
        placeholder="DESCRIPCIÓN" />
</x-template-modal.modal-template>