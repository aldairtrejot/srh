<style>
  .modal-turnarA{padding:10px 20px;font-family:'Segoe UI',Arial,sans-serif;color:#10312b}
  .modal-turnarA .title{font-weight:700;color:#10312b;margin-bottom:12px;font-size:24px;text-align:center;border-bottom:2px solid #e6ecec;padding-bottom:8px}
  .modal-turnarA .subtitle{font-weight:600;color:#0f2b26;margin:16px 0 8px;text-align:left;font-size:18px;border-left:4px solid #10312b;padding-left:8px}
  .modal-turnarA .note{text-align:center;margin-bottom:12px;font-size:15px;color:#5a6a6a}
  .modal-turnarA .divider{height:1px;background:#e6ecec;margin:20px 0}
  .modal-turnarA label{color:#5a6a6a;font-weight:600;font-size:14px}
  .modal-turnarA .row{margin-bottom:12px}
  .modal-footer button{border-radius:6px;padding:8px 16px;font-weight:600;transition:background .3s ease}
  .modal-footer .btn-primary{background-color:#10312b;color:#fff}
  .modal-footer .btn-primary:hover{background-color:#145c4a}
  .modal-footer .btn-secondary{background-color:#e6ecec;color:#10312b}
  .modal-footer .btn-secondary:hover{background-color:#d1d8d8}
  body.modal-open-returnado .form-actions,
  body.modal-open-returnado .app-sticky-footer,
  body.modal-open-returnado .sticky-actions{display:none!important}
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

    <div class="subtitle">Turnar A</div>
    <div class="row">
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_area_1_ret" tittle="CRH" grid="col-12 col-sm-6 col-md-4" data-container="#modalReturnado"/>
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_area_2_ret" tittle="CRHTOD" grid="col-12 col-sm-6 col-md-4" data-container="#modalReturnado"/>
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_area_ret" tittle="Área" grid="col-12 col-sm-6 col-md-4" data-container="#modalReturnado"/>
    </div>

    <div class="divider"></div>

    <div class="subtitle">Detalle</div>
    <div class="row">
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_usuario_area_ret" tittle="Usuario" grid="col-12 col-sm-6 col-md-4" data-container="#modalReturnado"/>
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_usuario_enlace_ret" tittle="Enlace" grid="col-12 col-sm-6 col-md-4" data-container="#modalReturnado"/>
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_unidad_ret" tittle="Unidad" grid="col-12 col-sm-6 col-md-4" data-container="#modalReturnado"/>
    </div>

    <div class="row">
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_coordinacion_ret" tittle="Coordinación" grid="col-12 col-sm-6 col-md-4" data-container="#modalReturnado"/>
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_tramite_ret" tittle="Trámite" grid="col-12 col-sm-6 col-md-4" data-container="#modalReturnado"/>
      <x-template-form.template-form-select-required :selectValue="[]" :selectEdit="[]"
        name="id_cat_clave_ret" tittle="Clave" grid="col-12 col-sm-6 col-md-4" data-container="#modalReturnado"/>
    </div>
  </div>

  <input type="hidden" id="id_correspondencia_ret" />
</x-template-modal.modal-template>

<script>
  (function () {
    const PATH_SEED   = "{{ parse_url(url('/letter/returnado/seed'), PHP_URL_PATH) }}/";
    const PATH_TURNAR = "{{ parse_url(url('/letter/returnado/turnar'), PHP_URL_PATH) }}";
    const PATH_AREA   = "{{ parse_url(url('/letter/collection/collectionArea'), PHP_URL_PATH) }}";
    const PATH_COOR   = "{{ parse_url(url('/letter/collection/collectionUnidad'), PHP_URL_PATH) }}";
    const PATH_CLAVE  = "{{ parse_url(url('/letter/collection/collectionClave'), PHP_URL_PATH) }}";

    window.LETTER = {
      returnadoSeedBase:  PATH_SEED,
      returnadoTurnarUrl: PATH_TURNAR,
      collectionAreaUrl:  PATH_AREA,
      collectionCoorUrl:  PATH_COOR,
      collectionClaveUrl: PATH_CLAVE,

      // ✅ Permitir SOLO este estatus (cambia 7 por el ID real de RETURNADO)
      statusReturnadoId: 8
      // includeInactiveArea3: true,
      // initials: { area1: null, area2: null, area3: null },
    };
  })();
</script>


<script src="{{ asset('assets/js/app/letter/letter/returnado.js') }}?v={{ filemtime(public_path('assets/js/app/letter/letter/returnado.js')) }}"></script>
