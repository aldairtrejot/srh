/* upload_form_cloud.js
   - UI para sección de archivos (mostrar/ocultar, límites, vista previa simple)
   - El envío real se hace en LetterC::save via multipart/form-data (POST normal)
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

    // Límite y render de anexos (máx 3 en UI)
    $('#file_anexo_entrada').on('change', function(){
      var files = this.files;
      if (files && files.length > 3) {
        this.value = '';
        if (window.notyfEM?.error) { window.notyfEM.error('Puedes seleccionar como máximo 3 anexos.'); }
        else { alert('Puedes seleccionar como máximo 3 anexos.'); }
        return;
      }
      if (files && files.length > 0) {
        $('#container_anexo_entrada_vacio').text('');
        $('#container_anexo_entrada').html('<ul style="margin:0;padding-left:18px;">'+
          Array.from(files).map(function(f){ return '<li>'+ f.name +'</li>'; }).join('')+
        '</ul>');
      } else {
        $('#container_anexo_entrada_vacio').text('Sin contenido');
        $('#container_anexo_entrada').empty();
      }
    });

    // Render simple para oficio
    $('#file_oficio_entrada').on('change', function(){
      var f = this.files && this.files[0];
      if (f){
        $('#container_oficio_entrada_vacio').text('');
        $('#container_oficio_entrada').html('<div>'+ f.name +'</div>');
      } else {
        $('#container_oficio_entrada_vacio').text('Sin contenido');
        $('#container_oficio_entrada').empty();
      }
    });

    // Muestra spinner al enviar si hay archivos o la sección está habilitada
    $('#myForm').on('submit', function(){
      var hasOficio = ($('#file_oficio_entrada')[0] && $('#file_oficio_entrada')[0].files.length > 0);
      var hasAnexos = ($('#file_anexo_entrada')[0] && $('#file_anexo_entrada')[0].files.length > 0);
      var enabled   = !!$('#habilitar_carga').val();
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
      }
    }
  });

})(jQuery, window, document);
