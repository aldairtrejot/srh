<!-- MODAL ADD COPIA A-->

<style>
    /* Estilos para hacer las filas más pequeñas */
    .table th,
    .table td {
        font-size: 15px;
        /* Reduce el tamaño de la fuente */
        padding: 5px 10px;
        /* Reduce el padding */
        height: 45px;
        /* Hace que las filas sean más pequeñas */
    }

    .table thead th {
        font-size: 16px;
        /* Aumenta el tamaño de la fuente en el encabezado */
        height: 35px;
        /* Ajusta la altura de las celdas del encabezado */
    }
</style>

<x-template-modal.modal-template tittle="Turnar con copia" idModal="modalCopy" idCancel="cancel_copy"
    idConfirm="confir_copy" functionConfirm="confirmarCopy();" width="1400px" height="700px">

    <p style="font-size: 16px;">
        Asignarles la información del siguiente folio de gestión: <label id="name_folio_gestion"
            style="font-weight: bold;"></label>. Las áreas / zonas podrán tener el conocimiento, sin embargo,
        estas no
        podrán
        modificar la información, ya que es únicamente con fines de conocimiento.
    </p>

    @if($letterAdminMatch)
        <button onclick="addCopy();"
            style="background-color: white; color: red; border: none; padding: 10px 20px; font-size: 16px; display: flex; align-items: center; justify-content: center; text-align: left; position: absolute; left: 0;">
            <i class="fa fa-arrow-up" style="margin-right: 8px;"></i> Agregar Registro
        </button>
        <br>
        <br>
    @endif

    <div id="mostrar_ocultar_copy">

        <div class="row">

            <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]" name="id_cat_area_copy"
                tittle="Área" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

            <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
                name="id_usuario_area_copy" tittle="Usuario" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

            <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
                name="id_usuario_enlace_copy" tittle="Enlace" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
        </div>

        <div class="row">

            <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]" name="id_cat_tramite_copy"
                tittle="Tramite" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

            <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]" name="id_cat_clave_copy"
                tittle="Clave" grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

        </div>



        <div class="modal-buttons custom-modal-buttons">
            <button style="font-weight: bold;" onclick="hiddenCopy();">Cancelar</button>
            <button style="font-weight: bold;color: #10312b" onclick="saveCopy();">Confirmar</button>
        </div>
    </div>

    <div class="table-responsive pt-3">
        <table id="template-table-copy" class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        Menú
                    </th>
                    <th>
                        Área / Zona
                    </th>
                    <th>
                        Tramite
                    </th>
                    <th>
                        Clave
                    </th>
                    <!--
                    <th>
                        Usuario
                    </th>
                    <th>
                        Enlace
                    </th>
-->
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <!-- VALUE OF INPUT -->
    <input type="hidden" id="id_correspondencia_x" />

</x-template-modal.modal-template>

<!-- MODAL DELETE -->
<x-template-modal.modal-delete tittleModal="id_modal_delete_acuse" idInput="id_uuid_acuse" valueInput=""
    cancelModal="id_modal_calcel_acuse" confirmButton="" functionConfirm="confirmModalDelete();" />

<!-- item with add -->
<input type="text" id="id_delete" style="display: none;" />