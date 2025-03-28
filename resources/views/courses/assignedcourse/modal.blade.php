<!-- MODAL-->
<x-template-modal.modal-small tittle="¿Desea continuar con la Auditoria?" idModal="modalBackdrop" idCancel="cancelBtn"
    idConfirm="confirmBtn" functionConfirm="confirmRefreshOficio();" valueInput="" idInput="" />


<!-- MODAL AUDITORIA-->
<x-template-modal.modal-template tittle="Auditoria curso" idModal="modalSolicitante"
idCancel="cancelBtn_solicitante" idConfirm="confir_sol" functionConfirm="confirmSolicitante();" width="1000px"
height="700px">

<input type="hidden" id="idtbl_cursos_audit">
<input type="hidden" id="id_tbl_auditoria_cursos">

<div class="table-responsive pt-3">
    <table id="template-tableaudit" class="table table-bordered custom-table">
        <thead>
            <tr>
                <th>Requisito</th>
                <th style="width: 2%;">Aplica</th>
                <th style="width: 2%;">Cargar Constancia</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
 <!-- item with add -->
 <input type="file" class="file-input-oficio" style="display: none;" />
 <input type="text" id="id_oficio" style="display: none;" />

</x-template-modal.modal-template>

<div id="massUploadModal" class="modal-template">
    <!-- Contenido del modal, e.g. formulario de carga masiva -->
    <h5>Carga Masiva de Datos</h5>
    <!-- ... campos del formulario ... -->
    <button class="close-modal">Cerrar</button>
</div>

<x-template-form.template-form-delete tittleModal="modalDelete" cancelModal="cancelBtn"
                            confirmButton="confirmBtn" />

<!-- Modal de Confirmación -->
<div id="modalDelete" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>¿Estás seguro de que deseas eliminar este Archivo? Esta acción no se puede deshacer.</p>
        <button id="confirmBtn">Eliminar</button>
        <button id="cancelBtn">Cancelar</button>
    </div>
</div>



