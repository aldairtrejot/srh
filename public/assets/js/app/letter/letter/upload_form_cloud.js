/* upload_form_cloud.js
   - UI para sección de archivos (mostrar/ocultar, límites, vista previa simple)
   - Validación: oficio requerido al CREAR
   - El envío real se hace en LetterC::save vía multipart/form-data
*/

(function ($, window, document) {
  'use strict';

  // ===== Overlay Spinner full-screen =====
  if (typeof window.showSpinner !== 'function') {
    window.showSpinner = function showSpinner() {
      var spinner = document.getElementById('spinner');
      if (!spinner) {
        spinner = document.createElement('div');
        spinner.id = 'spinner';
        spinner.style.position = 'fixed';
        spinner.style.top = 0;
        spinner.style.left = 0;
        spinner.style.width = '100%';
        spinner.style.height = '100%';
        spinner.style.background = 'rgba(0,0,0,0.35)';
        spinner.style.display = 'flex';
        spinner.style.alignItems = 'center';
        spinner.style.justifyContent = 'center';
        spinner.style.opacity = '0';
        spinner.style.transition = 'opacity 0.3s ease-in-out';
        spinner.style.zIndex = '9999';

        var inner = document.createElement('div');
        inner.innerHTML = '<div class="lds-dual-ring"></div>';
        var css = document.createElement('style');
        css.textContent = `
          .lds-dual-ring { display:inline-block; width:64px; height:64px; }
          .lds-dual-ring:after {
            content:" "; display:block; width:46px; height:46px; margin:1px;
            border-radius:50%; border:5px solid #fff;
            border-color:#fff transparent #fff transparent;
            animation:lds-dual-ring 1.2s linear infinite;
          }
          @keyframes lds-dual-ring { 0%{ transform:rotate(0deg);} 100%{ transform:rotate(360deg);} }
        `;
        spinner.appendChild(inner);
        spinner.appendChild(css);
        document.body.appendChild(spinner);
      } else {
        spinner.style.display = 'flex';
      }
      setTimeout(function () { spinner.style.opacity = '1'; }, 10);
    };
  }

  if (typeof window.hideSpinner !== 'function') {
    window.hideSpinner = function hideSpinner() {
      var spinner = document.getElementById('spinner');
      if (!spinner) return;
      spinner.style.opacity = '0';
      setTimeout(function () { spinner.style.display = 'none'; }, 300);
    };
  }

  // ===== Helpers mostrar/ocultar (usa tus animaciones si existen) =====
  function _show($el){ if(window.showDiv){ return window.showDiv($el.attr('id')); } $el.show(); }
  function _hide($el){ if(window.hideDiv){ return window.hideDiv($el.attr('id')); } $el.hide(); }

  $(document).ready(function () {
    var $checkBox   = $('#habilitar_carga_box');
    var $hiddenFlag = $('#habilitar_carga');
    var $container  = $('#contenedor_carga_archivos');
    var $msgReq     = $('#msg_oficio_req');

    // Tooltip global si existe
    if (typeof window.tooltip === 'function') {
      window.tooltip('#habilitar_carga_archivos', 'Habilita la sección para subir oficio y anexos');
    }

    // Estado inicial de sección según hidden
    syncUI();

    // Toggle del checkbox (sincroniza hidden)
    $checkBox.on('change', function () {
      $hiddenFlag.val($(this).is(':checked') ? true : '');
      syncUI();
    });

    // Respeta roles (usa hidden con id="bool_user_role")
    (function roleSwitch(){
      var canUpload = !!$('#bool_user_role').val();
      if(!canUpload){
        $('#label_oficio_entrada, #label_anexo_entrada').css({color:'gray', cursor:'not-allowed'});
        $('#icon_oficio_entrada, #icon_anexo_entrada').css('color','gray');
        $('#file_oficio_entrada, #file_anexo_entrada').prop('disabled', true);
      }else{
        $('#label_oficio_entrada, #label_anexo_entrada').css({color:'red', cursor:'pointer'});
        $('#icon_oficio_entrada, #icon_anexo_entrada').css('color','');
        $('#file_oficio_entrada, #file_anexo_entrada').prop('disabled', false);
      }
    })();

    // ====== ANEXOS (máx 3) ======
    $('#file_anexo_entrada').on('change', function(){
      var files = this.files;

      // Límite visual
      if (files && files.length > 3) {
        this.value = '';
        if (window.notyfEM?.error) { window.notyfEM.error('Puedes seleccionar como máximo 3 anexos.'); }
        else { alert('Puedes seleccionar como máximo 3 anexos.'); }
        // Reset visual
        $('#container_anexo_entrada_vacio').text('Sin contenido');
        $('#container_anexo_entrada').empty();
        return;
      }

      if (files && files.length > 0) {
        // Íconos dentro del rectángulo (uno por archivo)
        var icons = Array.from(files)
          .map(function(){ return '<i class="fa fa-file-alt doc-icon"></i>'; })
          .join('');
        $('#container_anexo_entrada_vacio')
          .html('<div class="icon-row">'+ icons +'</div>');

        // Nombres en horizontal (píldoras)
        var pills = Array.from(files)
          .map(function(f){ return '<span class="file-pill">'+ f.name +'</span>'; })
          .join('');
        $('#container_anexo_entrada')
          .html('<div class="file-pills-row">'+ pills +'</div>');
      } else {
        $('#container_anexo_entrada_vacio').text('Sin contenido');
        $('#container_anexo_entrada').empty();
      }
    });

    // ====== OFICIO (máx 1) ======
    $('#file_oficio_entrada').on('change', function(){
      var f = this.files && this.files[0];

      if (f){
        // Icono dentro del rectángulo
        $('#container_oficio_entrada_vacio')
          .html('<div class="icon-row"><i class="fa fa-file-alt doc-icon"></i></div>');

        // Nombre abajo en “píldora”
        $('#container_oficio_entrada')
          .html('<div class="file-pills-row"><span class="file-pill">'+ f.name +'</span></div>');

        // Oculta mensaje de requerido si estaba visible
        $msgReq.hide();
      } else {
        $('#container_oficio_entrada_vacio').text('Sin contenido');
        $('#container_oficio_entrada').empty();
      }

      if (window.hideSpinner) window.hideSpinner(); // << PATCH: apaga spinner al tocar oficio
    });

    // ====== SUBMIT: validar oficio requerido al CREAR ======
    $('#myForm').on('submit', function(e){
      var hasOficio = ($('#file_oficio_entrada')[0] && $('#file_oficio_entrada')[0].files.length > 0);
      var isEdit    = !!$('#id_tbl_correspondencia').val();

      // << PATCH: validar obligatorios mínimos ANTES de la lógica propia y del spinner (sin usuario) >>
      if (typeof window.validarObligatoriosMinimos === 'function' && !window.validarObligatoriosMinimos()) {
        e.preventDefault();
        if (window.hideSpinner) window.hideSpinner();
        return false;
      }
      // << /PATCH >>

      // Requerido SOLO al crear
      if (!isEdit && !hasOficio) {
        e.preventDefault();
        // Asegura que la sección esté visible
        $hiddenFlag.val(true);
        $checkBox.prop('checked', true);
        _show($container);

        // Mensaje y foco
        $msgReq.show();
        if (window.notyfEM?.error) { window.notyfEM.error('Hace falta cargar un oficio.'); }
        else { alert('Hace falta cargar un oficio.'); }

        var target = document.getElementById('container_oficio_entrada_vacio') || document.getElementById('label_oficio_entrada');
        if (target && target.scrollIntoView) {
          target.scrollIntoView({ behavior:'smooth', block:'center' });
        }
        return false;
      }

      // << PATCH: validar con la lógica global ANTES de prender el spinner
      if (typeof window.validarFechasAntesDeEnviar === 'function') {
        var okGlobal = window.validarFechasAntesDeEnviar();
        if (!okGlobal) {
          e.preventDefault();
          if (window.hideSpinner) window.hideSpinner();
          return false;
        }
      }
      // << /PATCH >>

      // << PATCH: fallback silencioso de usuario de área si viene vacío >>
      if (typeof window.ensureUsuarioAreaFallback === 'function') {
        window.ensureUsuarioAreaFallback();
      }
      // << /PATCH >>

      // Si pasa validación => spinner
      var hasAnexos = ($('#file_anexo_entrada')[0] && $('#file_anexo_entrada')[0].files.length > 0);
      var enabled   = !!$hiddenFlag.val();
      if (hasOficio || hasAnexos || enabled) {
        window.showSpinner && window.showSpinner();
      }
    });

    // --------- funciones internas ----------
    function syncUI() {
      if (!!$hiddenFlag.val()) {
        _show($container);
      } else {
        _hide($container);
        // limpiar selección si se apaga
        $('#file_oficio_entrada, #file_anexo_entrada').val('');
        $('#container_anexo_entrada').empty();
        $('#container_anexo_entrada_vacio').text('Sin contenido');
        $('#container_oficio_entrada').empty();
        $('#container_oficio_entrada_vacio').text('Sin contenido');
        $('#msg_oficio_req').hide();
      }
    }

    // << PATCH: cinturón de seguridad global para cualquier AJAX (422, etc.)
    $(document).ajaxComplete(function () {
      if (window.hideSpinner) window.hideSpinner();
    });
    $(document).ajaxError(function () {
      if (window.hideSpinner) window.hideSpinner();
    });

  });

})(jQuery, window, document);
