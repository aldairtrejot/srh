/* =========================================================================
   assets/js/app/letter/letter/table.js
   -------------------------------------------------------------------------
   - Paginación y búsqueda (sin tocar endpoints ni helpers existentes)
   - Columnas togglables: CRH (6), CRHTOD (7), Cloud (9), Respuesta (10)
   - Estado inicial: TODAS desmarcadas → ocultas hasta que el usuario elija
   - Cloud: SOLO botón "ojo" (entrada)
   - Respuesta: SOLO "ojo" si existe documento de respuesta (sin botón Responder)
   ========================================================================= */

var iterator = 1;            // Se comienza el iterador en 1
var emptyContent = false;    // Flag para paginadores
var columnVisibility = {};   // Mapa de visibilidad por índice (base 0)
var LOCAL_KEY = 'letter_table_column_visibility';

/* =========================== INIT =========================== */
$(document).ready(function () {
  // 1) Cargar estado guardado (si existe)
  var saved = null;
  try { saved = JSON.parse(localStorage.getItem(LOCAL_KEY) || 'null'); } catch (_) {}

  // 2) Leer checkboxes del menú de columnas
  columnVisibility = {};
  if ($('.toggle-column').length) {
    $('.toggle-column').each(function () {
      var idx = parseInt($(this).data('column'), 10);
      var visible = $(this).is(':checked'); // por Blade: sin checked → false
      if (saved && typeof saved[idx] !== 'undefined') visible = !!saved[idx]; // estado guardado tiene prioridad
      columnVisibility[idx] = !!visible;
      $(this).prop('checked', visible);
    });
  } else {
    // Fallback si no existieran checkboxes (oculta por defecto las pedidas)
    [6, 7, 9, 10].forEach(function (i) { columnVisibility[i] = false; });
  }

  // 3) Aplicar visibilidad inicial a encabezados (evita parpadeos)
  applySavedColumnVisibility(true);

  // 4) Bind de eventos (toggle de columnas)
  bindToggleMenu();

  // 5) Proceso normal
  searchInit();
  setValue();
});

/* ===================== VISIBILIDAD DE COLUMNAS ===================== */
function applySavedColumnVisibility(headersOnly) {
  for (var idx in columnVisibility) {
    if (!columnVisibility.hasOwnProperty(idx)) continue;
    var visible = !!columnVisibility[idx];
    var colIndex = parseInt(idx, 10) + 1; // nth-child es 1-based

    // THEAD
    $('#template-table thead tr th:nth-child(' + colIndex + ')')
      .css('display', visible ? '' : 'none');

    if (!headersOnly) {
      // TBODY
      $('#template-table tbody tr').each(function () {
        $(this).find('td:nth-child(' + colIndex + ')')
          .css('display', visible ? '' : 'none');
      });
    }

    // Refleja estado en checkbox (si existe)
    $('.toggle-column[data-column="' + idx + '"]').prop('checked', visible);
  }
}

function persistVisibility() {
  try { localStorage.setItem(LOCAL_KEY, JSON.stringify(columnVisibility)); } catch (_) {}
}

function bindToggleMenu() {
  $(document).on('change', '.toggle-column', function () {
    var idx = parseInt($(this).data('column'), 10);
    var visible = $(this).is(':checked');
    columnVisibility[idx] = !!visible;

    var colIndex = idx + 1;

    // THEAD
    $('#template-table thead tr th:nth-child(' + colIndex + ')')
      .css('display', visible ? '' : 'none');

    // TBODY
    $('#template-table tbody tr').each(function () {
      $(this).find('td:nth-child(' + colIndex + ')')
        .css('display', visible ? '' : 'none');
    });

    persistVisibility();
  });
}

/* =========================== UTIL: OJITO =========================== */
function seeDocumentUid(uid) {
  if (!uid) return;
  try {
    var url = URL_DEFAULT.concat('/letter/cloud/view?uid=').concat(encodeURIComponent(uid));
    window.open(url, '_blank');
  } catch (e) {
    if (window.Swal) Swal.fire({ icon: 'error', title: 'No se pudo abrir el visor' });
  }
}

function renderEye(uid, title) {
  if (!uid) return '';
  return (
    '<div style="display:flex; justify-content:center; align-items:center;">' +
      '<button type="button" class="custom-button-x custom-button" ' +
        'style="background-color:#338CD4; padding:10px; border-radius:50%; border:none; cursor:pointer;" ' +
        'title="' + (title || 'Ver documento') + '" onclick="seeDocumentUid(\'' + uid + '\')">' +
        '<i class="fa fa-eye" style="color:#fff; font-size:18px;"></i>' +
      '</button>' +
    '</div>'
  );
}

/* =========================== CLOUD (col 9) =========================== */
function renderCloudCell(uid) {
  return renderEye(uid, 'Ver documento de entrada');
}

/* =========================== RESPUESTA (col 10) =========================== */
/** Slot vacío que luego se llena con el ojito si hay respuesta */
function renderReplyEyeSlot(idCorr) {
  return '<div id="resp-eye-' + idCorr + '" style="display:flex; justify-content:center; align-items:center;"></div>';
}

/** Trae UID de respuesta por fila y pinta el ojo si existe */
function fetchReplyUid(idCorr) {
  var token = $('meta[name="csrf-token"]').attr('content');
  $.post(URL_DEFAULT.concat('/letter/cloud/reply'), { id: idCorr, _token: token })
   .done(function (r) {
     var $slot = $('#resp-eye-' + idCorr);
     if (!$slot.length) return;

     if (r && Array.isArray(r.oficiosSalida) && r.oficiosSalida.length > 0) {
       var uid = r.oficiosSalida[0].uid; // backend ya los ordena
       $slot.html(renderEye(uid, 'Ver documento de respuesta'));
     } else {
       $slot.html(''); // deja vacío si no hay respuesta
     }
   })
   .fail(function () {
     // en caso de error, no mostramos nada
     $('#resp-eye-' + idCorr).html('');
   });
}

/* ===================== BÚSQUEDA Y RENDER FILAS ===================== */
function searchInit() {
  mostrarBarra();
  var startTime = Date.now();

  var searchValue = document.getElementById('searchValue').value;
  var iteradorAux = (iterator * 5) - 5;

  $.get(URL_DEFAULT.concat('/letter/table'), {
    iterator: iteradorAux,
    searchValue: searchValue
  }, function (response) {

    var tbody = $('#template-table tbody');
    tbody.empty();

    if (response && response.value && response.value.length > 0) {
      response.value.forEach(function (object) {
        var finalUrl   = URL_DEFAULT.concat('/letter/edit/').concat(object.id);
        var finalCloud = URL_DEFAULT.concat('/letter/cloud/').concat(object.id);
        var urlReport  = URL_DEFAULT.concat('/letter/generate-pdf/correspondencia/').concat(object.id);

        var estatusColors = {
          'TURNADO': '#FFA82E',
          'CANCELADO': '#660000',
          'EN PROCESO': '#0077B6',
          'CONCLUIDO': '#26874A',
          'VENCIDO': '#FF0000',
          'RECHAZADO': '#b30000',
          'CONOCIMIENTO': '#6fc5f4ff'
        };
        var estatusColor = estatusColors[object.estatus] || '#6c757d';

        // UID de entrada (si tu backend lo provee en la lista).
        var uidEntrada = object.uid_entrada || object.uuid_oficio || object.uuid || object.uuid_documento || object.uid || '';

        // Para dropdown
        var folioSafe = String(object.folio_gestion || '').replace(/'/g, "\\'");

        var rowHTML =
          '<tr>' +
            // 0: Menú
            '<td style="text-align:center;">' +
              '<div class="dropdown">' +
                '<button class="custom-button-x custom-button btn dropdown-toggle-split" type="button" ' +
                        'id="dropdownMenuIconButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ' +
                        'style="background:#10312b" data-toggle="tooltip" data-placement="top" title="Menú">' +
                  '<i style="color:#fff; font-size:15px" class="fa fa-pencil"></i>' +
                '</button>' +
                '<div class="dropdown-menu" aria-labelledby="dropdownMenuIconButton1">' +
                  '<h6 class="dropdown-header">Acciones</h6>' +
                  '<a class="dropdown-item" href="' + finalUrl + '">' +
                    '<span style="background:#1D5B3B" class="icon-container-template">' +
                      '<div style="text-align:center;"><i class="fa fa-pencil item-icon-menu"></i></div>' +
                    '</span>Modificar' +
                  '</a>' +
                  '<a class="dropdown-item" href="' + finalCloud + '">' +
                    '<span style="background:#8a6f19" class="icon-container-template">' +
                      '<div style="text-align:center;"><i class="fa fa-cloud item-icon-menu"></i></div>' +
                    '</span>Cloud' +
                  '</a>' +
                  '<a class="dropdown-item" href="' + urlReport + '">' +
                    '<span style="background:#707070" class="icon-container-template">' +
                      '<div style="text-align:center;"><i class="fa fa-print item-icon-menu"></i></div>' +
                    '</span>Reporte' +
                  '</a>' +
                  // Responder SOLO queda en el dropdown (no en la columna 10)
                  '<button class="dropdown-item" onclick="openReply(' + object.id + ', \'' + folioSafe + '\')">' +
                    '<span style="background:#2986cc" class="icon-container-template">' +
                      '<div style="text-align: center;">' +
                        '<i class="fa fa-retweet item-icon-menu"></i>' +
                      '</div>' +
                    '</span>' +
                    'Responder' +
                  '</button>' +
                  '<button class="dropdown-item" onclick="openReturnado(' + object.id + ', \'' + folioSafe + '\')">' +
                    '<span style="background:#2a848c" class="icon-container-template">' +
                      '<div style="text-align:center;"><i class="fa fa-undo item-icon-menu"></i></div>' +
                    '</span>Returnado' +
                  '</button>' +
                '</div>' +
              '</div>' +
            '</td>' +

            // 1: Estatus
            '<td><label style="background:' + estatusColor + '; color:#fff" class="badge">' + (object.estatus || '') + '</label></td>' +

            // 2: Fecha de captura
            '<td>' + (object.fecha_captura || '') + '</td>' +

            // 3: Folio de gestión
            '<td>' + (object.folio_gestion || '') + '</td>' +

            // 4: No. Documento
            '<td>' + (object.num_documento || '') + '</td>' +

            // 5: Área
            '<td class="col-area" style="font-size:12px; width:300px; word-wrap:break-word; white-space:normal;">' +
              (object.area || '') +
            '</td>' +

            // 6: CRH
            '<td class="col-crh" style="font-size:12px; width:300px; word-wrap:break-word; white-space:normal;">' +
              (object.area_1 || '') +
            '</td>' +

            // 7: CRHTOD
            '<td class="col-crhtod" style="font-size:12px; width:300px; word-wrap:break-word; white-space:normal;">' +
              (object.area_2 || '') +
            '</td>' +

            // 8: Asunto
            '<td style="font-size:12px; width:800px; word-wrap:break-word; white-space:normal;">' +
              (object.asunto || '') +
            '</td>' +

            // 9: Cloud → ojo (entrada)
            '<td>' + renderCloudCell(uidEntrada) + '</td>' +

            // 10: Respuesta → SOLO slot para el ojo (sin botón Responder)
            '<td id="resp-cell-' + object.id + '">' + renderReplyEyeSlot(object.id) + '</td>' +
          '</tr>';

        $('#template-table tbody').append(rowHTML);

        // Traer y pintar el ojito de respuesta (si existe)
        fetchReplyUid(object.id);
      });

      emptyContent = false;
      talldropdown(response.value.length, 2);
    } else {
      $('#template-table tbody').html('<tr><td colspan="11" class="text-center">No se encontraron resultados</td></tr>');
      emptyContent = true;
      setValue();
    }

    // Re-aplicar visibilidad tras render (ya con filas)
    applySavedColumnVisibility(false);

    // Barra de progreso (respetando delay)
    var elapsed = Date.now() - startTime;
    var wait = Math.max(0, 2000 - elapsed);
    setTimeout(ocultarBarra, wait);
  });
}

/* =========================== PAGINADORES =========================== */
function paginatorMax1() { iterator = emptyContent ? iterator : iterator + 1; setValue(); searchInit(); }
function paginatorMax5() { iterator = emptyContent ? iterator : iterator + 5; setValue(); searchInit(); }
function paginatorMin5() { var x = iterator - 5; iterator = (x > 0) ? x : 1; setValue(); searchInit(); }
function paginatorMin1() { var x = iterator - 1; iterator = (x > 0) ? x : 1; setValue(); searchInit(); }

function setValue() {
  var it = iterator;
  document.getElementById('is_iterator').innerHTML = it;
  document.getElementById('is_iteratorMin').innerHTML = it - 1;
  document.getElementById('is_iteratorMax').innerHTML = it + 1;
}

function searchValue() {
  iterator = 1;
  setValue();
  searchInit();
}



