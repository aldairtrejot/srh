<!-- resources/views/letter/returnedmodal.blade.php -->
<!-- MODAL RETURNADO (clon visual del de Copia) -->

<style>
  .table th, .table td { font-size: 15px; padding: 5px 10px; height: 45px; }
  .table thead th { font-size: 16px; height: 35px; }
</style>

<x-template-modal.modal-template
  tittle="Returnado"
  idModal="modalReturnado"
  idCancel="cancel_returnado"
  idConfirm="confir_returnado"
  functionConfirm="confirmarReturnado();"
  width="1400px"
  height="700px">

  <p style="font-size: 16px;">
    Preparar returnado para el folio de gestión:
    <label id="name_folio_gestion_returnado" style="font-weight: bold;"></label>.
    Por ahora es una vista previa sin persistencia.
  </p>

  @if($letterAdminMatch)
    <button onclick="addReturnado();"
      style="background-color: white; color: red; border: none; padding: 10px 20px; font-size: 16px; display: flex; align-items: center; justify-content: center; text-align: left; position: absolute; left: 0;">
      <i class="fa fa-arrow-up" style="margin-right: 8px;"></i> Agregar Registro
    </button>
    <br><br>
  @endif

  <div id="mostrar_ocultar_returnado">
    <div class="row">
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_area_ret" tittle="Área"
        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_usuario_area_ret" tittle="Usuario"
        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_usuario_enlace_ret" tittle="Enlace"
        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
    </div>

    <div class="row">
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_tramite_ret" tittle="Trámite"
        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_clave_ret" tittle="Clave"
        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
    </div>

    <div class="modal-buttons custom-modal-buttons">
      <button style="font-weight: bold;" onclick="hiddenReturnado();">Cancelar</button>
      <button style="font-weight: bold;color: #10312b" onclick="saveReturnado();">Confirmar</button>
    </div>
  </div>

  <div class="table-responsive pt-3">
    <table id="template-table-returnado" class="table table-bordered">
      <thead>
      <tr>
        <th>Menú</th>
        <th>Área / Zona</th>
        <th>Trámite</th>
        <th>Clave</th>
      </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <!-- VALUE OF INPUT -->
  <input type="hidden" id="id_correspondencia_ret" />

</x-template-modal.modal-template>

{{-- (Opcional) Modal de borrar para Returnado, igual al de Copia pero con IDs distintos
<x-template-modal.modal-delete
  tittleModal="id_modal_delete_returnado"
  idInput="id_uuid_returnado"
  valueInput=""
  cancelModal="id_modal_cancel_returnado"
  confirmButton=""
  functionConfirm="confirmModalDeleteReturnado();" />
<input type="text" id="id_delete_returnado" style="display:none;" />
--}}
