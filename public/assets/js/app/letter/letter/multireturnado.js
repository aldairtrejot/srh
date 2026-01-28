/* assets/js/app/letter/letter/multireturnado.js */
var token = $('meta[name="csrf-token"]').attr('content');

(function(){
  'use strict';

  /* ======================== helpers debug ======================== */
  const DEBUG = !!window.LETTER_DEBUG;
  const L = (...a) => { if (DEBUG) console.log('[MR]', ...a); };

  function prefix() {
    if (typeof URL_DEFAULT !== 'undefined' && URL_DEFAULT) return URL_DEFAULT;
    var path = window.location.pathname;
    var i = path.indexOf('/letter/');
    return (i >= 0) ? path.slice(0, i) : '';
  }

  /* ======================== ENDPOINTS ======================== */
  var AREAS_URL_BASE = prefix() + '/letter/multireturnado/areas'; // + /{idCorr}
  var LIST_URL       = prefix() + '/letter/multireturnado/list';  // /{idCorr}
  var SAVE_URL       = prefix() + '/letter/multireturnado/save';
  var DELETE_URL     = prefix() + '/letter/multireturnado/delete'; // /{idTurnado}

  // mismos endpoints que Returnado.js (para reglas)
  var COLLECTION_AREA_URL =
    (window.LETTER && window.LETTER.collectionAreaUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionArea')
      : '/letter/collection/collectionArea');

  var COLLECTION_COOR_URL =
    (window.LETTER && window.LETTER.collectionCoorUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionUnidad')
      : '/letter/collection/collectionUnidad');

  /* ======================== SELECTS (MR) ======================== */
  // Área destino (A3)
  var $area = $('#mr_area_destino');

  // Dependientes
  var $usr  = $('#mr_id_usuario_destino,[name="mr_id_usuario_destino"]');
  var $enl  = $('#mr_id_usuario_enlace,[name="mr_id_usuario_enlace"]');
  var $uni  = $('#mr_id_cat_unidad,[name="mr_id_cat_unidad"]');
  var $coor = $('#mr_id_cat_coordinacion,[name="mr_id_cat_coordinacion"]');
  var $tra  = $('#mr_id_cat_tramite,[name="mr_id_cat_tramite"]');
  var $cla  = $('#mr_id_cat_clave,[name="mr_id_cat_clave"]');

  /* ==================== selectpicker helpers ==================== */
  function getJQ () {
    return (window.jQuery && window.jQuery.fn && window.jQuery.fn.selectpicker)
      ? window.jQuery
      : (window.$ || window.jQuery);
  }
  function spRefresh(sel) {
    const $jq = getJQ(); if (!$jq) return;
    const $s = (sel && sel.jquery) ? sel : $jq(sel);
    try { if ($jq.fn && typeof $jq.fn.selectpicker === 'function') $jq($s).selectpicker('refresh'); } catch(_) {}
  }
  function spInitIn(container) {
    const $jq = getJQ(); if (!$jq) return;
    try { if ($jq.fn && typeof $jq.fn.selectpicker === 'function') $jq(container || document).find('.selectpicker').selectpicker(); } catch(_) {}
  }
  function enablePicker($s){
    if (!$s || !$s.length) return;
    $s.prop('disabled', false).removeAttr('disabled').removeClass('disabled');
    spRefresh($s);
  }

  const PLACEH = '<option value="">SELECCIONE</option>';

  function setEmpty($s) {
    if (!$s || !$s.length) return;
    $s.html(PLACEH).prop('disabled', false).val('');
    spRefresh($s);
  }
  function setLoading($s) {
    if (!$s || !$s.length) return;
    $s.html('<option value="">CARGANDO…</option>').prop('disabled', false).val('');
    spRefresh($s);
  }
  function fillPicker($s, rows, selectedId) {
    if (!$s || !$s.length) return;
    $s.html(PLACEH);
    (rows || []).forEach(r => {
      const v = String(r.id ?? r.value ?? r.id_cat_area ?? '');
      const t = String(r.label ?? r.descripcion ?? r.text ?? '');
      if (!v) return;
      $s.append(new Option(t, v, false, selectedId != null && String(selectedId) === v));
    });
    $s.prop('disabled', false);
    if (selectedId == null) $s.val('');
    spRefresh($s);
  }
  const getVal = ($s) => ($s && $s.length && $s.val()) ? String($s.val()) : '';
  const firstRealVal = ($s) => {
    const $o = $s.find('option').not('[value=""]');
    return $o.length ? String($o.first().val()) : '';
  };
  function optionText($s){
    try{
      var v = getVal($s);
      if (!v) return '';
      return String($s.find('option:selected').text() || '').trim();
    }catch(e){ return ''; }
  }

  /* ========================= fetch helpers ========================= */
  function isAbortError(e) { return e && (e.name === 'AbortError' || String(e.message||'').toLowerCase().includes('abort')); }

  async function postForm(url, body, signal) {
    const form = new URLSearchParams();
    let csrf = token || $('input[name="_token"]').val() || '';
    form.append('_token', csrf);
    Object.keys(body || {}).forEach(k => {
      const v = body[k];
      if (v === undefined || v === null) return;
      form.append(k, typeof v === 'boolean' ? (v ? '1':'0') : String(v));
    });

    const resp = await fetch(url, {
      method:'POST',
      headers: {
        'Accept':'application/json',
        'X-CSRF-TOKEN':csrf,
        'Content-Type':'application/x-www-form-urlencoded;charset=UTF-8'
      },
      body: form,
      signal
    });

    const ct = (resp.headers.get('content-type') || '').toLowerCase();
    const isJson = ct.includes('application/json');
    const data = isJson ? await resp.json() : { ok:false };

    if (!resp.ok) {
      const err = new Error('HTTP ' + resp.status);
      err.data = data;
      throw err;
    }
    return data;
  }

  /* ====================== state ====================== */
  var __areasLoaded = false;
  var __areasCache  = [];
  var __areasById   = {};

  // filas ya guardadas (desde DB)
  var __dbRows = [];

  // filas nuevas (a guardar al confirmar)
  // cada item trae detalle completo (opción A con tabla detalle)
  var __items = [];

  // control de carrera (abort)
  var reqCtl = { deps:null, coor:null, clave:null };

  function abortAndNew(key){
    try{ reqCtl[key]?.abort(); }catch(_){}
    reqCtl[key] = new AbortController();
    return reqCtl[key];
  }

  /* ====================== load áreas ====================== */
  async function loadAreasDestino(idCorr){
    if (__areasLoaded) return __areasCache;

    const url = AREAS_URL_BASE + '/' + encodeURIComponent(String(idCorr || ''));
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const data = await res.json().catch(function(){ return {}; });

    if (!res.ok || !data.ok) {
      throw new Error((data && data.message) ? data.message : 'No se pudo cargar catálogo de áreas.');
    }

    __areasCache = Array.isArray(data.value) ? data.value : [];
    __areasById = {};
    __areasCache.forEach(function(r){
      var id = String(r.id_cat_area ?? r.id ?? '');
      if (id) __areasById[id] = r;
    });

    __areasLoaded = true;
    return __areasCache;
  }

  function paintAreasSelect(rows){
    if (!$area.length) return;

    $area.empty();
    (rows || []).forEach(function(r){
      var id  = String(r.id_cat_area ?? r.id ?? '');
      var txt = String(r.descripcion ?? r.label ?? '');
      if (!id) return;
      $area.append(new Option(txt, id, false, false));
    });

    spRefresh($area);
  }

  /* ====================== DB list/delete ====================== */
  async function loadFromDB(idCorr){
    const res = await fetch(LIST_URL + '/' + encodeURIComponent(idCorr), {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json().catch(function(){ return {}; });

    if (!res.ok || !data.ok) {
      throw new Error((data && data.message) ? data.message : 'No se pudo cargar returnados.');
    }
    return Array.isArray(data.value) ? data.value : [];
  }

  async function deleteFromDB(idTurnado){
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
      throw new Error((data && data.message) ? data.message : 'No se pudo eliminar.');
    }
    return data;
  }

  async function refreshFromDB(){
    var idCorr = $('#mr_id_correspondencia').val();
    if (!idCorr) return;
    __dbRows = await loadFromDB(idCorr);
    renderTable();
  }

  /* ====================== REGLAS (igual a Returnado) ====================== */
  async function actualizarCamposDerivadosPorAreaId(areaId) {
    if (!areaId) {
      [$usr,$enl,$uni,$coor,$tra,$cla].forEach(setEmpty);
      return;
    }

    // carga usuarios/enlace/unidad/tramite desde collectionArea (id)
    setLoading($usr); setLoading($enl); setLoading($uni);
    setLoading($tra); setEmpty($coor); setEmpty($cla);

    const ctl = abortAndNew('deps');

    try{
      const json = await postForm(COLLECTION_AREA_URL, { id: Number(areaId) }, ctl.signal);

      fillPicker($usr, json.selectUsuario || json.usuarios || [], null);
      fillPicker($enl, json.selectEnlace  || json.enlaces  || [], null);
      fillPicker($uni, json.selectUnidad  || json.unidades || [], null);

      // trámite (para que “se vea” como Turnar A)
      fillPicker($tra, json.selectTramite || json.tramites || [], null);

      [$usr,$enl,$uni,$tra].forEach(enablePicker);

      // autoselección si solo hay 1 opción
      function ensureFirst($s){
        if (!getVal($s)) {
          var v = firstRealVal($s);
          if (v) { $s.val(v); spRefresh($s); }
        }
      }
      ensureFirst($usr);
      ensureFirst($enl);
      ensureFirst($uni);
      ensureFirst($tra);

      // coordinación por unidad (collectionUnidad)
      var unidadId = getVal($uni);
      if (unidadId) await cargarCoordinacionesPorUnidad(unidadId);

      // clave por trámite
      var tramiteId = getVal($tra);
      if (tramiteId) await cargarClavesPorTramite(tramiteId);

    }catch(e){
      if (!isAbortError(e)) console.error('MR deps error:', e);
      [$usr,$enl,$uni,$coor,$tra,$cla].forEach(setEmpty);
    }
  }

  async function cargarCoordinacionesPorUnidad(unidadId){
    setLoading($coor);
    const ctl = abortAndNew('coor');

    try{
      const json = await postForm(COLLECTION_COOR_URL, { id: Number(unidadId) }, ctl.signal);
      const rows = (json && (json.selectCoordinacion || json.value || json.selectCoor))
        ? (json.selectCoordinacion || json.value || json.selectCoor)
        : [];

      fillPicker($coor, rows, null);
      enablePicker($coor);

      // autoselección si solo hay 1
      if (!getVal($coor)) {
        var v = firstRealVal($coor);
        if (v) { $coor.val(v); spRefresh($coor); }
      }
    }catch(e){
      if (!isAbortError(e)) console.error('MR coor error:', e);
      setEmpty($coor);
    }
  }

  async function cargarClavesPorTramite(tramiteId){
    setLoading($cla);
    const ctl = abortAndNew('clave');

    try{
      const json = await postForm(COLLECTION_AREA_URL, {
        by:'clave_by_tramite',
        id_cat_tramite:Number(tramiteId)
      }, ctl.signal);

      const rows = (json && (json.value || json.selectClave))
        ? (json.value || json.selectClave)
        : [];

      fillPicker($cla, rows, null);
      enablePicker($cla);

      if (!getVal($cla)) {
        var v = firstRealVal($cla);
        if (v) { $cla.val(v); spRefresh($cla); }
      }
    }catch(e){
      if (!isAbortError(e)) console.error('MR clave error:', e);
      setEmpty($cla);
    }
  }

  /* ====================== INSERTAR / TABLA ====================== */
  function existsInNew(areaId){
    return (__items || []).some(it => String(it.id_cat_area_destino) === String(areaId));
  }
  function existsInDB(areaId){
    return (__dbRows || []).some(r => String(r.id_cat_area_destino) === String(areaId));
  }

  window.mrInsertarDestino = function(){
    var areaId = getVal($area);
    if (!areaId) {
      if (window.Swal) Swal.fire('Faltan datos','Selecciona un área.','warning');
      else alert('Selecciona un área.');
      return;
    }

    if (existsInDB(areaId)) {
      if (window.Swal) Swal.fire('Aviso','Esa área ya está registrada en BD.','info');
      else alert('Esa área ya está registrada en BD.');
      return;
    }
    if (existsInNew(areaId)) {
      if (window.Swal) Swal.fire('Aviso','Esa área ya fue agregada a la lista.','info');
      else alert('Esa área ya fue agregada a la lista.');
      return;
    }

    // valida mínimos como Turnar A (usuario, trámite, clave)
    if (!getVal($usr)) {
      if (window.Swal) Swal.fire('Faltan datos','Selecciona Usuario.','warning');
      else alert('Selecciona Usuario.');
      return;
    }
    if (!getVal($tra)) {
      if (window.Swal) Swal.fire('Faltan datos','Selecciona Trámite.','warning');
      else alert('Selecciona Trámite.');
      return;
    }
    if (!getVal($cla)) {
      if (window.Swal) Swal.fire('Faltan datos','Selecciona Clasif. Archivística.','warning');
      else alert('Selecciona Clasif. Archivística.');
      return;
    }

    // item completo (para tbl_turnado_detalle)
    __items.push({
      id_cat_area_destino: Number(areaId),

      id_usuario_area: getVal($usr) ? Number(getVal($usr)) : null,
      id_usuario_enlace: getVal($enl) ? Number(getVal($enl)) : null,
      id_cat_unidad: getVal($uni) ? Number(getVal($uni)) : null,
      id_cat_coordinacion: getVal($coor) ? Number(getVal($coor)) : null,
      id_cat_tramite: getVal($tra) ? Number(getVal($tra)) : null,
      id_cat_clave: getVal($cla) ? Number(getVal($cla)) : null,

      // textos para render
      __txt_area: optionText($area) || (String((__areasById[String(areaId)]||{}).descripcion||'') || areaId),
      __txt_tramite: optionText($tra) || '—',
      __txt_clave: optionText($cla) || '—'
    });

    renderTable();
  };

  function removeNewByArea(areaId){
    __items = (__items || []).filter(it => String(it.id_cat_area_destino) !== String(areaId));
  }

  function renderTable(){
    var $tbody = $('#mr_tbody');
    if (!$tbody.length) return;
    $tbody.empty();

    // 1) filas BD (ya guardadas)
    (__dbRows || []).forEach(function(r){
      var tr = document.createElement('tr');

      var tdMenu = document.createElement('td');
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'mr-btn-del';
      btn.innerHTML = '<i class="fa fa-trash"></i>';

      btn.addEventListener('click', async function(){
        try{
          if (window.Swal){
            var ask = await Swal.fire({
              title:'¿Eliminar?',
              text:'Se quitará este destino returnado.',
              icon:'warning',
              showCancelButton:true,
              confirmButtonText:'Sí, eliminar',
              cancelButtonText:'Cancelar'
            });
            if (!ask.isConfirmed) return;
          }

          await deleteFromDB(r.id_tbl_correspondencia_turnado);
          await refreshFromDB();

          if (window.Swal) Swal.fire('Listo','Destino eliminado.','success');
        }catch(err){
          console.error(err);
          if (window.Swal) Swal.fire('Error', err.message || 'No se pudo eliminar.','error');
          else alert(err.message || 'No se pudo eliminar.');
        }
      });

      tdMenu.appendChild(btn);

      var tdArea = document.createElement('td');
      tdArea.textContent = r.area || '—';

      var tdTra = document.createElement('td');
      tdTra.textContent = r.tramite || '—';

      var tdCla = document.createElement('td');
      tdCla.textContent = r.clasif || r.clave || '—';

      tr.appendChild(tdMenu);
      tr.appendChild(tdArea);
      tr.appendChild(tdTra);
      tr.appendChild(tdCla);

      $tbody[0].appendChild(tr);
    });

    // 2) filas nuevas (aún no guardadas)
    (__items || []).forEach(function(it){
      var tr = document.createElement('tr');

      var tdMenu = document.createElement('td');
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'mr-btn-del';
      btn.innerHTML = '<i class="fa fa-trash"></i>';
      btn.addEventListener('click', function(){
        removeNewByArea(it.id_cat_area_destino);
        renderTable();
      });
      tdMenu.appendChild(btn);

      var tdArea = document.createElement('td');
      tdArea.textContent = it.__txt_area || '—';

      var tdTra = document.createElement('td');
      tdTra.textContent = it.__txt_tramite || '—';

      var tdCla = document.createElement('td');
      tdCla.textContent = it.__txt_clave || '—';

      tr.appendChild(tdMenu);
      tr.appendChild(tdArea);
      tr.appendChild(tdTra);
      tr.appendChild(tdCla);

      $tbody[0].appendChild(tr);
    });
  }

  /* ====================== eventos ====================== */
  function bindRules(){
    // al seleccionar área => aplicar reglas (como returnado)
    $(document).off('changed.bs.select.mrArea change.mrArea')
      .on('changed.bs.select.mrArea change.mrArea', '#mr_area_destino', function(){
        var v = $(this).val();
        if (!v) return;
        actualizarCamposDerivadosPorAreaId(v);
      });

    // unidad => recalcular coordinación
    $(document).off('changed.bs.select.mrUni change.mrUni')
      .on('changed.bs.select.mrUni change.mrUni', '#mr_id_cat_unidad,[name="mr_id_cat_unidad"]', function(){
        var u = $(this).val();
        if (!u) return;
        cargarCoordinacionesPorUnidad(u);
      });

    // trámite => recalcular claves
    $(document).off('changed.bs.select.mrTra change.mrTra')
      .on('changed.bs.select.mrTra change.mrTra', '#mr_id_cat_tramite,[name="mr_id_cat_tramite"]', function(){
        var t = $(this).val();
        if (!t) return;
        cargarClavesPorTramite(t);
      });
  }

  /* ====================== ABRIR / CERRAR ====================== */
  window.openMultiReturnado = async function(idCorr, folio){
    $('#mr_id_correspondencia').val(idCorr || '');
    $('#mr_name_folio_gestion').text(folio || '—');
    $('#mr_observaciones').val('');

    __items = [];
    __dbRows = [];

    spInitIn('#modalMultiReturnado');

    // limpia dependientes al abrir
    [$usr,$enl,$uni,$coor,$tra,$cla].forEach(setEmpty);

    $('body').addClass('modal-open-multireturnado');
    $('#modalMultiReturnado').fadeIn();

    try{
      var rows = await loadAreasDestino(idCorr);
      paintAreasSelect(rows);
      bindRules();
      await refreshFromDB();
    }catch(err){
      console.error(err);
      if (window.Swal) Swal.fire('Error', err.message || 'No se pudo cargar.','error');
      else alert(err.message || 'No se pudo cargar.');
    }
  };

  window.hiddenMultiReturnado = function(){
    $('#modalMultiReturnado').fadeOut(150, function(){ $(this).hide(); });
    $('body').removeClass('modal-open-multireturnado');
    Object.keys(reqCtl).forEach(k => { try { reqCtl[k]?.abort(); } catch(_){} });
  };

  // Cancelar
  $(document).off('click.mrCancel').on('click.mrCancel', '#mr_cancel', function(e){
    e.preventDefault(); hiddenMultiReturnado();
  });

  // ESC
  $(document).off('keydown.mrEsc').on('keydown.mrEsc', function(e){
    if (e.key === 'Escape' && $('#modalMultiReturnado').is(':visible')) hiddenMultiReturnado();
  });

  /* ====================== CONFIRMAR (guardar) ====================== */
  window.confirmarMultiReturnado = async function(){
    var idCorr = $('#mr_id_correspondencia').val();
    var obs    = $('#mr_observaciones').val() || '';

    if (!idCorr) {
      if (window.Swal) Swal.fire('Error','No se detectó el ID de correspondencia.','error');
      else alert('No se detectó el ID de correspondencia.');
      return;
    }

    if (!__items.length) {
      if (window.Swal) Swal.fire('Faltan datos','Agrega al menos un destino (botón Insertar).','warning');
      else alert('Agrega al menos un destino (botón Insertar).');
      return;
    }

    try{
      const res = await fetch(SAVE_URL, {
        method: 'POST',
        headers: {
          'Accept':'application/json',
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
          id_tbl_correspondencia: Number(idCorr),
          observaciones: obs,
          items: __items // <- requiere backend (MultiReturnadoC@save) para insertar en turnado + detalle
        })
      });

      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data.ok) throw new Error(data.message || 'No fue posible guardar.');

      __items = [];
      await refreshFromDB();

      if (window.Swal) Swal.fire('Éxito', data.message || 'Guardado.','success');

      if (typeof searchInit === 'function') searchInit();
      if (typeof getDataDocument === 'function') getDataDocument();

    }catch(err){
      console.error(err);
      if (window.Swal) Swal.fire('Error', err.message || 'No fue posible guardar.','error');
      else alert(err.message || 'No fue posible guardar.');
    }
  };

})();




