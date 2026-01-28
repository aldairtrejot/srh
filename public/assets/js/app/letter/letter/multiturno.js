/* assets/js/app/letter/letter/multiturno.js */
var token = $('meta[name="csrf-token"]').attr('content');

(function(){
  'use strict';

  function prefix() {
    if (typeof URL_DEFAULT !== 'undefined' && URL_DEFAULT) return URL_DEFAULT;
    var path = window.location.pathname;
    var i = path.indexOf('/letter/');
    return (i >= 0) ? path.slice(0, i) : '';
  }

  var AREAS_URL  = prefix() + '/letter/multiturno/areas';
  var LIST_URL   = prefix() + '/letter/multiturno/list';     // /{idCorr}
  var SAVE_URL   = prefix() + '/letter/multiturno/save';
  var DELETE_URL = prefix() + '/letter/multiturno/delete';   // /{idTurnado}

  var __areasLoaded = false;
  var __areasCache  = [];   // [{id_cat_area, descripcion}]
  var __areasById   = {};   // map id -> row

  // Estado del modal
  var __dbTurnados   = [];  // [{id_tbl_correspondencia_turnado, id_cat_area_destino, area, folio_turnado, estatus}]
  var __selectedIds  = [];  // ids (string) seleccionados NUEVOS (no guardados aún)

  async function loadAreasDestino(){
    if (__areasLoaded) return __areasCache;

    const res  = await fetch(AREAS_URL, { headers: { 'Accept': 'application/json' } });
    const data = await res.json().catch(function(){ return {}; });

    if (!res.ok || !data.ok) {
      throw new Error((data && data.message) ? data.message : 'No se pudo cargar catálogo de áreas.');
    }

    __areasCache = Array.isArray(data.value) ? data.value : [];
    __areasById = {};
    __areasCache.forEach(function(r){
      var id = String(r.id_cat_area ?? '');
      if (id) __areasById[id] = r;
    });

    __areasLoaded = true;
    return __areasCache;
  }

  function paintAreasSelect(rows){
    var $sel = $('#mt_area_destino');
    if (!$sel.length) return;

    $sel.empty();

    // opciones
    (rows || []).forEach(function(r){
      var id  = String(r.id_cat_area ?? '');
      var txt = String(r.descripcion ?? '');
      $sel.append(new Option(txt, id, false, false));
    });

    try{ if ($.fn.selectpicker) $sel.selectpicker('refresh'); }catch(e){}
  }

  async function loadTurnadosFromDB(idCorr){
    const res  = await fetch(LIST_URL + '/' + encodeURIComponent(idCorr), {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json().catch(function(){ return {}; });

    if (!res.ok || !data.ok) {
      throw new Error((data && data.message) ? data.message : 'No se pudo cargar turnados.');
    }
    return Array.isArray(data.value) ? data.value : [];
  }

  function closePickerAndFocusObs(){
    try{
      var $sel  = $('#mt_area_destino');
      var $wrap = $sel.closest('.bootstrap-select');
      if ($wrap.length && $wrap.hasClass('open')) {
        // ✅ cierra el dropdown
        $sel.selectpicker('toggle');
      }
    }catch(e){}
    setTimeout(function(){ $('#mt_observaciones').trigger('focus'); }, 80);
  }

  function addSelectedArea(id){
    id = String(id || '').trim();
    if (!id) return;

    // ya está en BD?
    var existsInDB = (__dbTurnados || []).some(function(r){
      return String(r.id_cat_area_destino) === id;
    });
    if (existsInDB) return;

    // ya está en NUEVOS?
    if (__selectedIds.indexOf(id) >= 0) return;

    __selectedIds.push(id);
  }

  function removeSelectedArea(id){
    id = String(id || '');
    __selectedIds = (__selectedIds || []).filter(function(x){ return x !== id; });
  }

  async function deleteTurnadoFromDB(idTurnado){
    const res = await fetch(DELETE_URL + '/' + encodeURIComponent(idTurnado), {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({})
    });

    const data = await res.json().catch(function(){ return {}; });

    if (!res.ok || !data.ok) {
      throw new Error((data && data.message) ? data.message : 'No se pudo eliminar el turnado.');
    }
    return data;
  }

  function renderTable(){
    var $tbody = $('#mt_tbody');
    var $table = $('#mt_table');
    var $empty = $('#mt_empty');
    if (!$tbody.length) return;

    // Mapa de BD por área destino (para evitar duplicados)
    var dbByArea = {};
    (__dbTurnados || []).forEach(function(r){
      dbByArea[String(r.id_cat_area_destino)] = r;
    });

    // Lista final (BD primero + NUEVOS)
    var finalRows = [];

    (__dbTurnados || []).forEach(function(r){
      finalRows.push({
        kind: 'db',
        id_turnado: r.id_tbl_correspondencia_turnado,
        id_area: String(r.id_cat_area_destino),
        area: r.area || '',
        folio_turnado: r.folio_turnado || '',
        estatus: r.estatus || 'PENDIENTE'
      });
    });

    (__selectedIds || []).forEach(function(id){
      if (dbByArea[id]) return;
      var row = __areasById[id] || {};
      finalRows.push({
        kind: 'new',
        id_turnado: null,
        id_area: id,
        area: String(row.descripcion ?? id),
        folio_turnado: '—',
        estatus: 'NUEVO'
      });
    });

    $tbody.empty();

    if (!finalRows.length){
      $table.hide();
      $empty.show();
      return;
    }

    $empty.hide();
    $table.show();

    finalRows.forEach(function(r){
      var tr = document.createElement('tr');

      // Menú (solo basura)
      var tdMenu = document.createElement('td');
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'mt-btn-del';
      btn.innerHTML = '<i class="fa fa-trash"></i>';

      btn.addEventListener('click', async function(){
        try{
          // Si es NUEVO: solo lo quitamos
          if (r.kind === 'new'){
            removeSelectedArea(r.id_area);
            renderTable();
            closePickerAndFocusObs();
            return;
          }

          // Si es BD: lo borramos en BD y recargamos
          if (window.Swal){
            var ask = await Swal.fire({
              title: '¿Eliminar?',
              text: 'Se quitará este destino del documento.',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Sí, eliminar',
              cancelButtonText: 'Cancelar'
            });
            if (!ask.isConfirmed) return;
          }

          await deleteTurnadoFromDB(r.id_turnado);

          // refrescar
          await refreshFromDB();

          if (window.Swal) Swal.fire('Listo', 'Destino eliminado correctamente.', 'success');

        }catch(err){
          console.error(err);
          if (window.Swal) Swal.fire('Error', err.message || 'No se pudo eliminar.', 'error');
          else alert('Error: ' + (err.message || 'No se pudo eliminar.'));
        }
      });

      tdMenu.appendChild(btn);

      // Área
      var tdArea = document.createElement('td');
      tdArea.textContent = r.area;

      // Folio
      var tdFolio = document.createElement('td');
      tdFolio.textContent = r.folio_turnado;

      // Estatus
      var tdEst = document.createElement('td');
      var badge = document.createElement('span');
      badge.className = 'mt-badge' + (r.kind === 'new' ? ' new' : '');
      badge.textContent = r.estatus;
      tdEst.appendChild(badge);

      tr.appendChild(tdMenu);
      tr.appendChild(tdArea);
      tr.appendChild(tdFolio);

      $tbody[0].appendChild(tr);
    });
  }

  async function refreshFromDB(){
    var idCorr = $('#mt_id_correspondencia').val();
    if (!idCorr) return;
    __dbTurnados = await loadTurnadosFromDB(idCorr);
    renderTable();
  }

  function bindSelectionBehavior(){
    // al seleccionar un área (1x1)
    $(document)
      .off('changed.bs.select.mt')
      .on('changed.bs.select.mt', '#mt_area_destino', function(){
        var val = $('#mt_area_destino').val();
        if (!val) return;

        // agrega a NUEVOS
        addSelectedArea(val);

        // limpia el select para poder escoger otra (1x1)
        $('#mt_area_destino').val('');
        try{ if ($.fn.selectpicker) $('#mt_area_destino').selectpicker('refresh'); }catch(e){}

        // render
        renderTable();

        // ✅ cierra catálogo y manda foco a observaciones
        closePickerAndFocusObs();
      });
  }

  // ===== ABRIR =====
  window.openTurnarMultiple = async function(idCorr, folio){
    $('#mt_id_correspondencia').val(idCorr || '');
    $('#mt_name_folio_gestion').text(folio || '—');
    $('#mt_observaciones').val('');

    __selectedIds = [];
    __dbTurnados = [];

    // init selectpicker
    try{
      if ($.fn.selectpicker){
        $('#modalMultiTurno .selectpicker').selectpicker();
        $('#mt_area_destino').selectpicker('refresh');
      }
    }catch(e){}

    $('body').addClass('modal-open-multiturno');
    $('#modalMultiTurno').fadeIn();

    try{
      var rows = await loadAreasDestino();
      paintAreasSelect(rows);
      bindSelectionBehavior();

      await refreshFromDB(); // ✅ carga los ya turnados
      renderTable();

    }catch(err){
      console.error(err);
      if (window.Swal) Swal.fire('Error', err.message || 'No se pudo cargar.', 'error');
      else alert(err.message || 'No se pudo cargar.');
    }
  };

  // ===== CERRAR =====
  window.hiddenMultiTurno = function(){
    $('#modalMultiTurno').fadeOut(150, function(){ $(this).hide(); });
    $('body').removeClass('modal-open-multiturno');
  };

  // ===== CONFIRMAR (guardar) =====
  window.confirmarMultiTurno = async function(){
    var idCorr = $('#mt_id_correspondencia').val();
    var obs    = $('#mt_observaciones').val() || '';

    if (!idCorr) {
      if (window.Swal) Swal.fire('Error', 'No se detectó el ID de correspondencia.', 'error');
      else alert('No se detectó el ID de correspondencia.');
      return;
    }

    if (!__selectedIds.length) {
      if (window.Swal) Swal.fire('Faltan datos', 'Selecciona al menos un área destino.', 'warning');
      else alert('Selecciona al menos un área destino.');
      return;
    }

    // solo manda los nuevos
    var areas = __selectedIds.map(function(x){ return Number(x); });

    try{
      var res = await fetch(SAVE_URL, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
          id_tbl_correspondencia: Number(idCorr),
          areas_destino: areas,
          observaciones: obs
        })
      });

      var data = await res.json().catch(function(){ return {}; });
      if (!res.ok || !data.ok) throw new Error(data.message || 'No fue posible guardar.');

      // limpia NUEVOS
      __selectedIds = [];
      renderTable();

      // recarga BD para que ya se vean con folio_turnado real
      await refreshFromDB();

      if (window.Swal) Swal.fire('Éxito', data.message || 'Guardado.', 'success');

      if (typeof searchInit === 'function') searchInit();
      if (typeof getDataDocument === 'function') getDataDocument();

    }catch(err){
      console.error(err);
      if (window.Swal) Swal.fire('Error', err.message || 'No fue posible guardar.', 'error');
      else alert('Error: ' + (err.message || 'No fue posible guardar.'));
    }
  };

  // Cancelar
  $(document).off('click.mtCancel').on('click.mtCancel', '#mt_cancel', function(e){
    e.preventDefault(); hiddenMultiTurno();
  });

  // ESC
  $(document).off('keydown.mtEsc').on('keydown.mtEsc', function(e){
    if (e.key === 'Escape' && $('#modalMultiTurno').is(':visible')) hiddenMultiTurno();
  });

})();




