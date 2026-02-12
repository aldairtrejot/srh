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
  var replyOficioFile   = null;   // archivo oficio (FINAL: requerido / otros tipos: no se usa)
  var replyAnexosFiles  = [];     // NUEVOS anexos a subir (máx 3 - guardados)
  var replyAnexosPrev   = [];     // Anexos ya guardados en el servidor (solo vista)

  // ✅ NUEVO: si true, este folio se ve como COPIA para el usuario → solo AVANCE
  var replyCopyOnly = false;

  // ✅ Tipos permitidos para Reply (oficio + anexos)
  // PDF, ZIP, Excel, CSV, Word, Imágenes
  var REPLY_ALLOWED_RE = /\.(pdf|zip|xls|xlsx|xlsm|csv|doc|docx|jpg|jpeg|png)$/i;

  // Tamaño máximo por archivo (en bytes)
  // Si quieres 20MB: cambia a 20 * 1024 * 1024
  var MAX_BYTES_PER_FILE = 10 * 1024 * 1024; // 10MB

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

  // ===== URL para obtener datos previos de respuesta =====
  function getReplyDataUrl(id){
    var path = window.location.pathname;
    var i = path.indexOf('/letter/');
    var prefix = (i >= 0) ? path.slice(0, i) : '';
    return prefix + '/letter/reply/data/' + id;
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
  function fmtSizeKb(bytes){ return Math.ceil((bytes || 0)/1024) + ' KB'; }

  function isAllowedReplyFile(file){
    if (!file || !file.name) return false;
    return REPLY_ALLOWED_RE.test(file.name);
  }

  function allowedHint(){
    return 'Permitidos: PDF, ZIP, Excel (XLS/XLSX/XLSM), CSV, Word (DOC/DOCX), JPG/PNG.';
  }

  // ✅ NUEVO: reconstruye opciones del select según COPIA
  function setTipoRespuestaOptions(copyOnly){
    var $sel = $('#reply_tipo_respuesta');
    if (!$sel.length) return;

    $sel.empty();

    if (copyOnly) {
      // Solo AVANCE (sin "Selecciona...")
      $sel.append('<option value="AVANCE">Avance de Respuesta</option>');
      $sel.val('AVANCE');
      $sel.prop('disabled', true);
    } else {
      $sel.append('<option value="">Selecciona tipo de respuesta…</option>');
      $sel.append('<option value="FINAL">Respuesta Final</option>');
      $sel.append('<option value="AVANCE">Avance de Respuesta</option>');
      $sel.val('');
      $sel.prop('disabled', false);
    }
  }

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

  // ===== Render Anexos (guardados + nuevos, máx 3 en total) =====
  function renderReplyAnexosPreview(){
    var $vacio = $('#reply_container_anexos_empty');
    var $cont  = $('#reply_container_anexos');
    $cont.empty();

    var totalPrev = replyAnexosPrev.length;
    var totalNew  = replyAnexosFiles.length;

    if (!totalPrev && !totalNew) { $vacio.show(); return; }
    $vacio.hide();

    // 1) Anexos ya guardados (solo vista, sin botón quitar)
    replyAnexosPrev.forEach(function(a){
      var nombre = (a && a.nombre) ? a.nombre : ((a && a.name) ? a.name : '(sin nombre)');
      var $pill  = $('<div class="reply-file-pill"></div>');
      var $icon  = $('<i class="fa fa-file"></i>');
      var $name  = $('<span></span>').text(nombre + ' (guardado)');
      $pill.append($icon, $name);
      $cont.append($pill);
    });

    // 2) Nuevos anexos (con botón quitar)
    replyAnexosFiles.forEach(function(file, idx){
      var $pill = $('<div class="reply-file-pill"></div>');
      var $icon = $('<i class="fa fa-file"></i>');
      var labelName = file.name || file.nombre || '(sin nombre)';
      var $name = $('<span></span>').text(labelName + ' (' + fmtSizeKb(file.size) + ')');
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

      // ✅ Permitidos (pdf/zip/excel/csv/word/img)
      if (!isAllowedReplyFile(file)){
        Swal.fire('Archivo no permitido', allowedHint(), 'warning');
        $(this).val(''); return;
      }

      if (file.size > MAX_BYTES_PER_FILE){
        Swal.fire('Archivo muy grande','Máximo permitido: ' + Math.round(MAX_BYTES_PER_FILE/1024/1024) + ' MB.', 'warning');
        $(this).val(''); return;
      }

      replyOficioFile = file;
      renderReplyOficioPreview();
      $('#reply_msg_oficio_req').hide();
    });
  }

  // ===== Change: Anexos (hasta 3 TOTAL: guardados + nuevos) =====
  function bindReplyAnexosInput(){
    var $input = $('#reply_file_anexos');
    if (!$input.length) return;
    $input.off('change.reply').on('change.reply', function(e){
      var files = Array.from(e.target.files || []);
      if (!files.length){ renderReplyAnexosPreview(); return; }

      var maxTotal = 3;
      var totalPrev = replyAnexosPrev.length;
      var totalNew  = replyAnexosFiles.length;
      var total     = totalPrev + totalNew;

      if (total >= maxTotal) {
        Swal.fire('Límite alcanzado','Ya tienes el máximo de 3 anexos (entre guardados y nuevos).','info');
        $(this).val('');
        return;
      }

      var maxNewAllowed = maxTotal - totalPrev; // cupo para nuevos (considerando guardados)
      var next = replyAnexosFiles.slice();

      for (var i=0; i<files.length; i++){
        if (next.length >= maxNewAllowed) break;

        var f = files[i];

        // ✅ Permitidos (pdf/zip/excel/csv/word/img)
        if (!isAllowedReplyFile(f)){
          Swal.fire('Archivo no permitido', f.name + '\n' + allowedHint(), 'warning');
          continue;
        }

        if (f.size > MAX_BYTES_PER_FILE){
          Swal.fire('Archivo muy grande', f.name + ': máximo ' + Math.round(MAX_BYTES_PER_FILE/1024/1024) + ' MB.', 'warning');
          continue;
        }

        // evitar duplicados entre nuevos
        var dup = next.some(function(x){ return x.name === f.name && x.size === f.size; });
        if (dup) continue;

        next.push(f);
      }

      if (next.length > replyAnexosFiles.length && next.length >= maxNewAllowed) {
        Swal.fire('Límite alcanzado','Se guardaron solo algunos anexos hasta completar el máximo de 3.','info');
      }

      replyAnexosFiles = next;
      renderReplyAnexosPreview();
      $(this).val('');
    });
  }

  // ===== Reglas según Tipo de Respuesta =====
  function applyTipoRespuestaRules(){
    var tipo = ($('#reply_tipo_respuesta').val() || '').toUpperCase();

    var $fechaInicio = $('[name="fecha_inicio"]');
    var $fechaFin    = $('[name="fecha_fin"]');
    var $asunto      = $('#reply_asunto');
    var $obs         = $('#reply_observacion');
    var $fileOficio  = $('#reply_file_oficio');
    var $fileAnexos  = $('#reply_file_anexos');

    // Si no hay tipo seleccionado → TODO bloqueado (menos el select)
    if (!tipo) {
      $('#reply_observacion_group').show();
      $('#reply_oficios_group').show();

      $fechaInicio.prop('disabled', true);
      $fechaFin.prop('disabled', true);
      $asunto.prop('disabled', true);
      $obs.prop('disabled', true);
      $fileOficio.prop('disabled', true);
      $fileAnexos.prop('disabled', true);

      $('#reply_label_oficio, #reply_label_anexos')
        .css('opacity', 0.4)
        .css('pointer-events', 'none');

      return;
    }

    // Con tipo seleccionado → habilitar base
    $fechaInicio.prop('disabled', false);
    $fechaFin.prop('disabled', false);
    $asunto.prop('disabled', false);
    $fileAnexos.prop('disabled', false);
    $('#reply_label_anexos')
      .css('opacity', 1)
      .css('pointer-events', 'auto');

    if (tipo === 'FINAL') {
      // FINAL: mostrar y habilitar Observaciones + Oficios
      $('#reply_observacion_group').show();
      $('#reply_oficios_group').show();

      $obs
        .prop('disabled', false)
        .attr('placeholder', 'Observaciones…');

      $fileOficio.prop('disabled', false);
      $('#reply_label_oficio')
        .css('opacity', 1)
        .css('pointer-events', 'auto');

      // Limpiar anexos NUEVOS (los guardados se mantienen en replyAnexosPrev)
      replyAnexosFiles = [];
      $fileAnexos.val('');
      renderReplyAnexosPreview();

    } else {
      // AVANCE (u otro distinto a FINAL):
      // ocultar Observaciones + Oficios, anexos activos
      $('#reply_observacion_group').hide();
      $('#reply_oficios_group').hide();

      $obs.val('').prop('disabled', true);

      replyOficioFile = null;
      $fileOficio.val('').prop('disabled', true);
      renderReplyOficioPreview();
      $('#reply_msg_oficio_req').hide();

      $('#reply_label_oficio')
        .css('opacity', 0.4)
        .css('pointer-events', 'none');
    }
  }

  // ✅ NUEVO: reglas COPIA → solo AVANCE (con select reconstruido)
  function applyCopyOnlyRules(){
    if (!replyCopyOnly) {
      setTipoRespuestaOptions(false);
      return;
    }
    setTipoRespuestaOptions(true);
    applyTipoRespuestaRules();
  }

  // ===== Rellenar modal desde el servidor (última respuesta) =====
  function fillReplyFromServer(data){
    if (!data || !data.ok) return;

    // ✅ leer flag COPIA desde el servidor
    replyCopyOnly = !!data.copy_only;

    // ✅ aplicar restricciones si es COPIA (forzará AVANCE y ocultará placeholder/FINAL)
    applyCopyOnlyRules();

    // Tipo de respuesta según estatus (solo si NO es copia)
    if (!replyCopyOnly && data.tipo_respuesta) {
      $('#reply_tipo_respuesta').val(data.tipo_respuesta);
      applyTipoRespuestaRules();
    }

    // Oficio (fechas, asunto, observaciones)
    if (data.oficio) {
      if (data.oficio.fecha_inicio) {
        var fi = (data.oficio.fecha_inicio || '').toString();
        $('[name="fecha_inicio"]').val(fi.substring(0,10));
      }
      if (data.oficio.fecha_fin) {
        var ff = (data.oficio.fecha_fin || '').toString();
        $('[name="fecha_fin"]').val(ff.substring(0,10));
      }
      if (data.oficio.asunto) {
        $('#reply_asunto').val(data.oficio.asunto);
      }
      if (data.oficio.observaciones) {
        $('#reply_observacion').val(data.oficio.observaciones);
      }
    }

    // Anexos ya guardados (se muestran SIEMPRE, en AVANCE y FINAL)
    replyAnexosPrev = Array.isArray(data.anexos) ? data.anexos.slice() : [];
    renderReplyAnexosPreview();
  }

  // ===== POST con FormData =====
  async function postReplyForm(formPayload){
    const url = getReplyUrl();
    const fd  = new FormData();

    Object.entries(formPayload).forEach(function(entry){
      var k = entry[0], v = entry[1];
      fd.append(k, v);
    });

    // Oficio + Anexos (según reglas)
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
      $('#reply_observacion').val('').prop('disabled', false).attr('placeholder', 'Observaciones…');
      $('#reply_asunto').val('');

      // reset tipo (mientras carga)
      replyCopyOnly = false;
      setTipoRespuestaOptions(false);     // placeholder + FINAL + AVANCE
      $('#reply_tipo_respuesta').prop('disabled', true); // se habilita al cargar si no es copia
      $('#reply_msg_oficio_req').hide();

      // Asegurar que los grupos estén visibles al inicio
      $('#reply_observacion_group').show();
      $('#reply_oficios_group').show();

      // Forzar límites de longitud (Asunto 300 / Observaciones 200)
      $('#reply_asunto').attr('maxlength', 300);
      $('#reply_observacion').attr('maxlength', 200);

      // Aviso en tiempo real al llegar al tope (sin spam)
      var asuntoToastGuard = false, obsToastGuard = false;

      $('#reply_asunto').off('input.replyMax').on('input.replyMax', function () {
        var v = this.value || '';
        if (v.length >= 300 && !asuntoToastGuard) {
          asuntoToastGuard = true;
          Swal.fire('Límite de caracteres', 'El Asunto no puede exceder 300 caracteres.', 'warning');
          setTimeout(function(){ asuntoToastGuard = false; }, 1200);
        }
      });

      $('#reply_observacion').off('input.replyMax').on('input.replyMax', function () {
        var v = this.value || '';
        if (v.length >= 200 && !obsToastGuard) {
          obsToastGuard = true;
          Swal.fire('Límite de caracteres', 'Las Observaciones no pueden exceder 200 caracteres.', 'warning');
          setTimeout(function(){ obsToastGuard = false; }, 1200);
        }
      });

      // Reset archivos/preview
      replyOficioFile   = null;
      replyAnexosFiles  = [];
      replyAnexosPrev   = [];
      $('#reply_file_oficio').val('').prop('disabled', false);
      $('#reply_file_anexos').val('');
      renderReplyOficioPreview();
      renderReplyAnexosPreview();
      $('#reply_label_oficio, #reply_label_anexos')
        .css('opacity', 1)
        .css('pointer-events', 'auto');

      // Vincular change de inputs file
      bindReplyOficioInput();
      bindReplyAnexosInput();

      // Vincular cambio de tipo de respuesta
      $('#reply_tipo_respuesta')
        .off('change.replyTipo')
        .on('change.replyTipo', function () {
          applyTipoRespuestaRules();
        });

      // Aplicar reglas iniciales (si está placeholder, bloqueará todo)
      applyTipoRespuestaRules();

      // Cargar datos de última respuesta (si existen)
      if (id) {
        fetch(getReplyDataUrl(id))
          .then(function(res){ return res.ok ? res.json() : null; })
          .then(function(data){
            if (!data) return;

            // Si NO es copia, habilitar select (si es copia, setTipoRespuestaOptions(true) ya lo dejó disabled)
            if (!data.copy_only) {
              $('#reply_tipo_respuesta').prop('disabled', false);
            }

            fillReplyFromServer(data);
          })
          .catch(function(err){
            console.error('replyData error:', err);
          });
      }

      showReplyModal(MODAL_SEL);
    } catch (e) { console.error('openReplyModal error:', e); }
  };

  window.hideReplyModal = function () { hideReplyModal(MODAL_SEL); };

  window.confirmReplyModal = async function () {
    const id            = replyValById('reply_correspondencia_id');
    const fechaInicio   = replyValByName('fecha_inicio');
    const fechaFin      = replyValByName('fecha_fin');
    const observacion   = replyValById('reply_observacion');
    const asunto        = replyValById('reply_asunto');
    const tipoRespuesta = ($('#reply_tipo_respuesta').val() || '').toUpperCase();

    if (!tipoRespuesta) {
      Swal.fire('Campo requerido','Selecciona el tipo de respuesta.','warning');
      return;
    }

    // ✅ UX: si es COPIA, solo AVANCE
    if (replyCopyOnly && tipoRespuesta !== 'AVANCE') {
      Swal.fire('Sin permiso', 'Este folio te aparece como COPIA; solo puedes registrar AVANCE de respuesta.', 'warning');
      return;
    }

    if (asunto && asunto.length > 300){
      Swal.fire('Límite de caracteres', 'El Asunto no puede exceder 300 caracteres.', 'warning');
      return;
    }
    if (observacion && observacion.length > 200){
      Swal.fire('Límite de caracteres', 'Las Observaciones no pueden exceder 200 caracteres.', 'warning');
      return;
    }

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

    if (tipoRespuesta === 'FINAL') {
      if(!observacion.trim()){
        Swal.fire('Campo requerido','Escribe las observaciones.','warning');
        return;
      }
      if(!replyOficioFile){
        $('#reply_msg_oficio_req').show();
        Swal.fire('Archivo requerido','Debes cargar el Oficio (1).','warning');
        return;
      }
    } else {
      if (!replyAnexosFiles || !replyAnexosFiles.length) {
        Swal.fire('Archivo requerido','Debes cargar al menos un anexo.','warning');
        return;
      }
    }

    if (typeof mostrarBarra === 'function') mostrarBarra();

    try{
      const payload = {
        id_tbl_correspondencia: id,
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin,
        observaciones: observacion,
        asunto: asunto,
        tipo_respuesta: tipoRespuesta
      };

      const data = await postReplyForm(payload);

      hideReplyModal(MODAL_SEL);
      Swal.fire('Éxito', (data && data.message) || 'Respuesta guardada correctamente.', 'success');

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





















