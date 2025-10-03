// Token CSRF
var token = $('meta[name="csrf-token"]').attr('content') || (window.CSRF_TOKEN || '');

(function () {
  'use strict';

  var HAS_BOOTSTRAP = !!$.fn.modal;
  var BACKDROP_ID   = 'modal-backdrop-reply-custom';

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

  function showModal(selector) {
    var $el = $(selector);
    if (!$el.length) return console.warn('Modal no encontrado:', selector);

    if (HAS_BOOTSTRAP) {
      $el.modal('show');
    } else {
      ensureBackdrop();
      // Centrado con flex y tamaño fijo como pediste
      $el.css({
        display:'flex', position:'fixed', inset:0, zIndex:1050,
        alignItems:'center', justifyContent:'center'
      }).fadeIn(120);

      $el.find('.modal-content').css({
        width:'430px', height:'380px', background:'#fff', borderRadius:'12px', overflow:'hidden'
      });
    }
  }

  function hideModal(selector) {
    var $el = $(selector);
    if (!$el.length) return;
    if (HAS_BOOTSTRAP) { $el.modal('hide'); }
    else { $el.fadeOut(120, removeBackdrop); }
  }

  window.openReply = function (id, folio) {
    try {
      $('#name_folio_gestion').text(folio || '');
      $('#id_correspondencia_x').val(id || '');
      showModal('#modalReply');
    } catch (e) { console.error('openReply error:', e); }
  };

  window.hiddenReply = function () { hideModal('#modalReply'); };

  window.confirmarReply = function () {
    console.log('Confirmar reply (solo visualización). ID:', $('#id_correspondencia_x').val(), 'CSRF:', !!token);
    hideModal('#modalReply');
  };
})();


