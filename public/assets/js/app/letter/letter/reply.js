// reply.js (IDs y funciones con prefijo reply*)
// CSRF + BASE
var token = $('meta[name="csrf-token"]').attr('content') || (window.CSRF_TOKEN || '');
var BASE  = (typeof URL_DEFAULT !== 'undefined' && URL_DEFAULT) ? URL_DEFAULT : '';

(function () {
  'use strict';

  var HAS_BOOTSTRAP = !!($.fn && $.fn.modal);
  var BACKDROP_ID   = 'modal-backdrop-reply-custom';
  var MODAL_SEL     = '#replyModal';

  // Estado local
  var replyOficioFile   = null;   // 1 archivo (REQUERIDO)
  var replyAnexosFiles  = [];     // hasta 3 archivos (OPCIONALES)

  // ===== URL para POST (evita 404 en subcarpetas) =====
  function getReplyUrl(){
    if (typeof window.REPLY_SAVE_URL === 'string' && window.REPLY_SAVE_URL.length > 0) {
      return window.REPLY_SAVE_URL;
    }
    // Fallback: calcula prefijo desde la ruta actual
    var path = window.location.pathname;
    var i = path.indexOf('/letter/');
    var prefix = (i >= 0) ? path.slice(0, i) : '';
    return prefix + '/letter/reply/save';
  }

  // ===== Backdrop (fallback) =====
  function ensureBackdrop() {
    if (document.getElementById(BACKDROP_ID)) return;
    var $bd = $('<div>', { id: BACKDROP_ID, class: 'modal-backdrop fade show' })
      .css({ position:'fixed', inset:0, background:'rgba(0,0,0,.45)', zIndex:1040 });
    $('body').append($bd).addClass('modal-open').css('overflow','hidden');
  }
  function removeBackdrop() {
    $('#'+BACKDROP_ID).remove();
    $('body').removeClass('modal-open').css('overflow','');
  }

  // ===== Mostrar/Ocultar modal =====
  function showReplyModal(selector) {
    var $el = $(selector);
    if (!$el.length) return console.warn('Modal no encontrado:', selector);
    if (HAS_BOOTSTRAP) { $el.modal('show'); return; }
    ensureBackdrop();
    $el.css({ display:'flex', position:'fixed', inset:0, zIndex:1050, alignItems:'center', justifyContent:'center' }).fadeIn(120);
  }
  function hideReplyModal(selector) {
    var $el = $(selector);
    if (!$el.length) return;
    if (HAS_BOOTSTRAP) { $el.modal('hide'); return; }
    $el.fadeOut(120, removeBackdrop);
  }

  // ===== Helpers =====
  function replyValById(id){ return (document.getElementById(id) || {}).value || ''; }
  function replyValByName(name){
    var el = document.querySelector('[name="'+name+'"]');
    return (el && el.value) ? el.value : '';
  }
  function fmtSizeKb(bytes){ return Math.ceil(bytes/1024) + ' KB'; }

  // ===== Render Oficio (1) =====
  function renderReplyOficioPreview(){
    var $vacio = $('#reply_container_oficio_empty');
    var $cont  = $('#reply_container_oficio');
    $cont.empty();

    if (!replyOficioFile) { $vacio.show(); return; }
    $vacio.hide();

    var $pill = $('<div class="reply-file-pill"></div>');
    var $icon = $('<i class="fa fa-file"></i>');
    var $name = $('<span></span>').text(replyOficioFile.name + ' (' + fmtSizeKb(replyOficioFile.size) + ')');
    var $rm   = $('<button type="button" class="reply-remove-btn" aria-label="Quitar">&times;</button>')
      .on('click', function(){
        replyOficioFile = null;
        $('#reply_file_oficio').val('');
        renderReplyOficioPreview();
      });

    $pill.append($icon, $name, $rm);
    $cont.append($pill);
  }

  // ===== Render Anexos (hasta 3) =====
  function renderReplyAnexosPreview(){
    var $vacio = $('#reply_container_anexos_empty');
    var $cont  = $('#reply_container_anexos');
    $cont.empty();

    if (!replyAnexosFiles.length) { $vacio.show(); return; }
    $vacio.hide();

    replyAnexosFiles.forEach(function(file, idx){
      var $pill = $('<div class="reply-file-pill"></div>');
      var $icon = $('<i class="fa fa-file"></i>');
      var $name = $('<span></span>').text(file.name + ' (' + fmtSizeKb(file.size) + ')');
      var $rm   = $('<button type="button" class="reply-remove-btn" aria-label="Quitar">&times;</button>')
        .on('click', function(){
          replyAnexosFiles.splice(idx, 1);
          renderReplyAnexosPreview();
          $('#reply_file_anexos').val('');
        });

      $pill.append($icon, $name, $rm);
      $cont.append($pill);
    });
  }

  // ===== Change: Oficio =====
  function bindReplyOficioInput(){
    var $input = $('#reply_file_oficio');
    if (!$input.length) return;
    $input.off('change.reply').on('change.reply', function(e){
      var files = e.target.files || [];
      replyOficioFile = null;

      if (!files.length){ renderReplyOficioPreview(); return; }

      var file = files[0];
      // SOLO: pdf, jpg, jpeg, png
      var allowed = /\.(pdf|jpg|jpeg|png)$/i.test(file.name);
      if (!allowed){
        Swal.fire('Archivo no permitido','warning');
        $(this).val(''); return;
      }
      var maxBytes = 10 * 1024 * 1024; // 10MB
      if (file.size > maxBytes){
        Swal.fire('Archivo muy grande','Máximo permitido: 10 MB.','warning');
        $(this).val(''); return;
      }

      replyOficioFile = file;
      renderReplyOficioPreview();
      $('#reply_msg_oficio_req').hide();
    });
  }

  // ===== Change: Anexos (hasta 3) =====
  function bindReplyAnexosInput(){
    var $input = $('#reply_file_anexos');
    if (!$input.length) return;
    $input.off('change.reply').on('change.reply', function(e){
      var files = Array.from(e.target.files || []);
      if (!files.length){ replyAnexosFiles = []; renderReplyAnexosPreview(); return; }

      var maxFiles = 3;
      var maxBytes = 10 * 1024 * 1024; // 10MB c/u
      var next = replyAnexosFiles.slice();

      for (var i=0; i<files.length; i++){
        if (next.length >= maxFiles) break;

        var f = files[i];
        // SOLO: pdf, jpg, jpeg, png
        var allowed = /\.(pdf|jpg|jpeg|png)$/i.test(f.name);
        if (!allowed){ Swal.fire('Archivo no permitido','warning'); continue; }
        if (f.size > maxBytes){ Swal.fire('Archivo muy grande', f.name + ': máximo 10 MB.', 'warning'); continue; }

        var dup = next.some(function(x){ return x.name === f.name && x.size === f.size; });
        if (dup) continue;

        next.push(f);
      }

      if (next.length > maxFiles){
        next = next.slice(0, maxFiles);
        Swal.fire('Límite alcanzado','Solo se permiten hasta 3 anexos.','info');
      }

      replyAnexosFiles = next;
      renderReplyAnexosPreview();
      $(this).val('');
    });
  }

  // ===== POST con FormData =====
  async function postReplyForm(formPayload){
    const url = getReplyUrl();
    const fd  = new FormData();

    Object.entries(formPayload).forEach(function(entry){
      var k = entry[0], v = entry[1];
      fd.append(k, v);
    });

    // Oficio (REQUERIDO) + Anexos (opcionales)
    if (replyOficioFile) fd.append('file_oficio_entrada', replyOficioFile);
    if (replyAnexosFiles.length){
      replyAnexosFiles.forEach(function(f){ fd.append('file_anexo_entrada[]', f); });
    }

    const res = await fetch(url, {
      method:'POST',
      headers: { 'X-CSRF-TOKEN': token }, // NO fijar Content-Type manualmente
      body: fd
    });
    if (!res.ok) {
      const txt = await res.text().catch(function(){ return ''; });
      throw new Error('HTTP '+res.status+' → '+txt);
    }
    return res.json().catch(function(){ return {}; });
  }

  // ===== API global =====
  window.openReplyModal = function (id, folio) {
    try {
      $('#reply_folio_label').text(folio || '');
      $('#reply_correspondencia_id').val(id || '');

      // Reset campos
      $('[name="fecha_inicio"]').val('');
      $('[name="fecha_fin"]').val('');
      $('#reply_observacion').val('');
      $('#reply_asunto').val('');
      $('#reply_msg_oficio_req').hide();

      // Reset archivos/preview
      replyOficioFile  = null;
      replyAnexosFiles = [];
      $('#reply_file_oficio').val('');
      $('#reply_file_anexos').val('');
      renderReplyOficioPreview();
      renderReplyAnexosPreview();

      // Vincular change de inputs file
      bindReplyOficioInput();
      bindReplyAnexosInput();

      showReplyModal(MODAL_SEL);
    } catch (e) { console.error('openReplyModal error:', e); }
  };

  window.hideReplyModal = function () { hideReplyModal(MODAL_SEL); };

  window.confirmReplyModal = async function () {
    const id           = replyValById('reply_correspondencia_id');
    const fechaInicio  = replyValByName('fecha_inicio'); // Fecha del documento (REQ)
    const fechaFin     = replyValByName('fecha_fin');    // Fecha de captura (REQ)
    const observacion  = replyValById('reply_observacion');
    const asunto       = replyValById('reply_asunto');

    if(!id){
      Swal.fire('Falta información','No se encontró el ID de correspondencia.','warning'); 
      return;
    }
    if(!fechaInicio){
      Swal.fire('Campo requerido','Selecciona la fecha del documento.','warning'); 
      return;
    }
    if(!fechaFin){
      Swal.fire('Campo requerido','Selecciona la fecha de captura.','warning'); 
      return;
    }
    if(!asunto.trim()){
      Swal.fire('Campo requerido','Escribe el asunto.','warning'); 
      return;
    }
    if(!observacion.trim()){
      Swal.fire('Campo requerido','Escribe las observaciones.','warning'); 
      return;
    }
    if(!replyOficioFile){
      $('#reply_msg_oficio_req').show();
      Swal.fire('Archivo requerido','Debes cargar el Oficio (1).','warning');
      return;
    }

    if (typeof mostrarBarra === 'function') mostrarBarra();

    try{
      const payload = {
        id_tbl_correspondencia: id,
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin,
        observaciones: observacion,  // requerido
        asunto: asunto
      };

      const data = await postReplyForm(payload);

      hideReplyModal(MODAL_SEL);
      Swal.fire('Éxito', (data && data.message) || 'Respuesta guardada correctamente.', 'success');

      // Si tienes funciones para refrescar listado/tablas, llámalas aquí
      if (typeof searchInit === 'function') searchInit();
      if (typeof getDataDocument === 'function') getDataDocument();

    }catch(err){
      console.error(err);
      Swal.fire('Error', 'No fue posible guardar la respuesta.', 'error');
    }finally{
      if (typeof ocultarBarra === 'function') ocultarBarra();
    }
  };

  // Alias para compat con table.js
  window.openReply = function(id, folio){ return window.openReplyModal(id, folio); };

  // Botón "Cancelar"
  $(document)
    .off('click.replyCancel')
    .on('click.replyCancel', '#reply_cancel', function (e) {
      e.preventDefault(); window.hideReplyModal();
    });

  // ESC
  $(document).off('keydown.replyEsc').on('keydown.replyEsc', function(e){
    if (e.key === 'Escape') window.hideReplyModal();
  });

  // Click en backdrop (fallback)
  $(document).off('click.replyBackdrop').on('click.replyBackdrop', '#modal-backdrop-reply-custom', function(){
    window.hideReplyModal();
  });

})();








