<!-- MODAL-->
<x-template-modal.modal-small tittle="¿Desea continuar con la Auditoria?" idModal="modalBackdrop" idCancel="cancelBtn"
    idConfirm="confirmBtn" functionConfirm="confirmRefreshOficio();" valueInput="" idInput="" />

    <!-- MODAL AUDITORIA-->
<x-template-modal.modal-template tittle="Auditoria curso" idModal="modalSolicitante"
idCancel="cancelBtn_solicitante" idConfirm="confir_sol" functionConfirm="confirmSolicitante();" width="600px"
height="495px">

<input type="hidden" id="idtbl_cursos_audit">


<div class="table-responsive pt-3">
    <table id="template-tableaudit" class="table table-bordered custom-table">
        <thead>
            <tr>
                <th>Requisito</th>
                <th>Aplica</th>
                <th>Cargar Constancia</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
</x-template-modal.modal-template>