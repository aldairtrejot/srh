// assets/js/app/letter/letter/returnado.js
// Modal Returnado (idéntico al de Copia, sin llamadas a endpoints aún)

var token = $('meta[name="csrf-token"]').attr('content'); // Token CSRF

$(document).ready(function () {
  // Cerrar haciendo click fuera del contenido
  $(window).on('click', function (event) {
    if ($(event.target).is('#modalReturnado')) {
      $('#modalReturnado').fadeOut();
      cleanSelectReturnado();
    }
  });

  // Botones de cancelar/confirmar del modal
  $('#cancel_returnado').on('click', function () {
    $('#modalReturnado').fadeOut();
  });

  $('#confir_returnado').on('click', function () {
    confirmarReturnado();
  });

  cleanSelectReturnado(); // Oculta el bloque de “agregar” por defecto
});

// ACTIVACIÓN DEL MODAL
function openReturnado(id, folGestion) {
  $('#name_folio_gestion_returnado').text(folGestion || '');
  $('#id_correspondencia_ret').val(id || '');
  $('#modalReturnado').fadeIn();      // Mostrar modal
  searchInitToReturnado(id);          // Placeholder tabla (si luego listamos algo)
  cleanSelectReturnado();             // Ocultar zona de “agregar”
}

// PLACEHOLDERS (idénticos a Copia, pero sin lógica de backend todavía)
function addReturnado() {
  // Aquí, cuando toque, puedes cargar catálogos como en “getSelectAreaCopy()”
  showDiv('mostrar_ocultar_returnado');  // Usa tus helpers existentes
}

function hiddenReturnado() {
  cleanSelectReturnado();
}

function saveReturnado() {
  // A futuro: validaciones + POST
  // Por ahora solo cierra y notifica (si usas notyfEM)
  if (window.notyfEM) notyfEM.success('Acción preparada (sin guardar aún).');
  $('#modalReturnado').fadeOut();
}

function confirmarReturnado() {
  // Mismo comportamiento que confirmarCopy() del ejemplo
  $('#modalReturnado').fadeOut();
}

function searchInitToReturnado(id) {
  // A futuro: llenar tabla del modal (como “searchInitToCopy(id)”)
  // Por ahora, limpia la tabla
  const tbody = $('#template-table-returnado tbody');
  tbody.html('<tr><td colspan="4" class="text-center">Sin registros</td></tr>');
}

function cleanSelectReturnado() {
  hideDiv('mostrar_ocultar_returnado'); // Usa tu helper
  cleanSelectMoreSelect('#id_cat_area_ret');
  cleanSelectMoreSelect('#id_usuario_area_ret');
  cleanSelectMoreSelect('#id_usuario_enlace_ret');
  cleanSelectMoreSelect('#id_cat_tramite_ret');
  cleanSelectMoreSelect('#id_cat_clave_ret');
}

// Si luego quieres encadenar selects como en Copia:
// $('#id_cat_area_ret').on('change', function(){ /* ... */ });
// $('#id_cat_tramite_ret').on('change', function(){ /* ... */ });
