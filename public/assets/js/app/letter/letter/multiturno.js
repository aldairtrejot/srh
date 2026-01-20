/* assets/js/app/letter/letter/multiturno.js */
var token = $('meta[name="csrf-token"]').attr('content');

(function(){
  'use strict';

  // ABRIR (solo vista)
  window.openTurnarMultiple = function(idCorr, folio){
    $('#mt_id_correspondencia').val(idCorr || '');
    $('#mt_name_folio_gestion').text(folio || '—');
    $('#mt_observaciones').val('');
    $('#mt_areas_destino').val([]);

    // inicializa selectpicker dentro del modal (como returnado)
    try{
      if ($.fn.selectpicker){
        $('#modalMultiTurno .selectpicker').selectpicker();
        $('#mt_areas_destino').selectpicker('refresh');
      }
    }catch(e){}

    // oculta footer sticky del layout (como returnado)
    $('body').addClass('modal-open-multiturno');

    // abre modal-template (tu sistema lo abre con fadeIn)
    $('#modalMultiTurno').fadeIn();
  };

  // CERRAR
  window.hiddenMultiTurno = function(){
    $('#modalMultiTurno').fadeOut(150, function(){ $(this).hide(); });
    $('body').removeClass('modal-open-multiturno');
  };

  // CONFIRM (por ahora no hace nada)
  window.confirmarMultiTurno = function(){
    // SOLO VISTA, luego conectamos backend
    hiddenMultiTurno();
  };

  // Cancelar
  $(document).off('click.mtCancel').on('click.mtCancel', '#mt_cancel', function(e){
    e.preventDefault(); hiddenMultiTurno();
  });

  // Cerrar por ESC (opcional)
  $(document).off('keydown.mtEsc').on('keydown.mtEsc', function(e){
    if (e.key === 'Escape' && $('#modalMultiTurno').is(':visible')) hiddenMultiTurno();
  });

})();
