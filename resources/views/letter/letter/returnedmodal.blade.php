{{-- resources/views/letter/returnedmodal.blade.php --}}

<style>
  /* Apariencia consistente con el sistema */
  .modal-turnarA { padding-top: 2px; }
  .modal-turnarA .title { font-weight: 700; color:#10312b; margin-bottom: 10px; font-size: 22px; }
  .modal-turnarA .subtitle { font-weight: 700; color:#0f2b26; margin: 6px 0 4px; text-align:center }
  .modal-turnarA .note { text-align:center; margin-bottom: 8px; font-size: 14px; }
  .modal-turnarA .divider { height:1px; background:#e6ecec; margin:14px 0; }
  .modal-turnarA label { color:#5a6a6a; font-weight:600; }
  /* Evitar acciones del formulario cuando el modal está visible */
  body.modal-open-returnado .form-actions,
  body.modal-open-returnado .app-sticky-footer,
  body.modal-open-returnado .sticky-actions {
    display:none !important;
  }
</style>

<x-template-modal.modal-template
  tittle="Returnado"
  idModal="modalReturnado"
  idCancel="cancel_returnado"
  idConfirm="confir_returnado"
  functionConfirm="saveReturnado();"
  width="1100px"
  height="680px">

  <div class="modal-turnarA">
    <div class="title">Turnar A</div>
    <p class="note">
      Turnar el folio de gestión: <strong id="name_folio_gestion_returnado"></strong>
    </p>

    {{-- Bloque Turnar A (idéntico al form) --}}
    <div class="subtitle">Turnar A</div>
    <div class="row">
      {{-- CRH (Área 1) --}}
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_area_1_ret" tittle="CRH"
        grid="col-12 col-sm-6 col-md-4" />

      {{-- CRHTOD (Área 2) --}}
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_area_2_ret" tittle="CRHTOD"
        grid="col-12 col-sm-6 col-md-4" />

      {{-- Área (Área 3) --}}
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_area_ret" tittle="Área"
        grid="col-12 col-sm-6 col-md-4" />
    </div>

    <div class="divider"></div>

    <div class="subtitle">Detalle</div>
    <div class="row">
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_usuario_area_ret" tittle="Usuario"
        grid="col-12 col-sm-6 col-md-4" />

      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_usuario_enlace_ret" tittle="Enlace"
        grid="col-12 col-sm-6 col-md-4" />

      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_unidad_ret" tittle="Unidad"
        grid="col-12 col-sm-6 col-md-4" />
    </div>

    <div class="row">
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_coordinacion_ret" tittle="Coordinación"
        grid="col-12 col-sm-6 col-md-4" />

      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_tramite_ret" tittle="Trámite"
        grid="col-12 col-sm-6 col-md-4" />

      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_clave_ret" tittle="Clave"
        grid="col-12 col-sm-6 col-md-4" />
    </div>
  </div>

  {{-- ID de correspondencia a turnar --}}
  <input type="hidden" id="id_correspondencia_ret" />

</x-template-modal.modal-template>

{{-- (Opcional) ayudas de URL para JS; el JS también tiene fallback si no están --}}
<script>
  window.LETTER = window.LETTER || {};
  window.LETTER.returnadoTurnarUrl = "{{ route('letter.returnado.turnar') }}";
  // Base para /seed/{id}
  window.LETTER.returnadoSeedBase = "{{ url('/letter/returnado/seed') }}/";
</script>
