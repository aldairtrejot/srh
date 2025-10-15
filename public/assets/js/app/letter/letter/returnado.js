/* assets/js/app/letter/letter/returnado.js */
var token = $('meta[name="csrf-token"]').attr('content');

(function () {
  const $m  = $('#modalReturnado');

  // Selectores: por id o name (según Blade)
  const $a1  = $('#id_cat_area_1_ret,[name="id_cat_area_1_ret"]');
  const $a2  = $('#id_cat_area_2_ret,[name="id_cat_area_2_ret"]');
  const $a3  = $('#id_cat_area_ret,[name="id_cat_area_ret"]');

  const $usr = $('#id_usuario_area_ret,[name="id_usuario_area_ret"]');
  const $enl = $('#id_usuario_enlace_ret,[name="id_usuario_enlace_ret"]');
  const $uni = $('#id_cat_unidad_ret,[name="id_cat_unidad_ret"]');
  const $coor= $('#id_cat_coordinacion_ret,[name="id_cat_coordinacion_ret"]');
  const $tra = $('#id_cat_tramite_ret,[name="id_cat_tramite_ret"]');
  const $cla = $('#id_cat_clave_ret,[name="id_cat_clave_ret"]');

  // Endpoints
  const COLLECTION_AREA_URL =
    (window.LETTER && window.LETTER.collectionAreaUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionArea')
      : '/letter/collection/collectionArea');

  const TURNAR_SAVE_URL =
    (window.LETTER && window.LETTER.returnadoTurnarUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/returnado/turnar')
      : '/letter/returnado/turnar');

  const SEED_URL_BASE =
    (window.LETTER && window.LETTER.returnadoSeedBase) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/returnado/seed/')
      : '/letter/returnado/seed/');

  const COLLECTION_COOR_URL =
    (window.LETTER && window.LETTER.collectionCoorUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionUnidad')
      : '/letter/collection/collectionUnidad');

  const COLLECTION_CLAVE_URL =
    (window.LETTER && window.LETTER.collectionClaveUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionClave')
      : '/letter/collection/collectionClave');

  /* ========= selectpicker helpers ========= */
  function getJQ () {
    return (window.jQuery && window.jQuery.fn && window.jQuery.fn.selectpicker)
      ? window.jQuery
      : (window.$ || window.jQuery);
  }
  function spRefresh(target) {
    const $jq = getJQ();
    if (!$jq) return;
    const $el = (target && target.jquery) ? target : $jq(target);
    try {
      if ($jq.fn && typeof $jq.fn.selectpicker === 'function') {
        $jq($el).selectpicker('refresh');
      }
    } catch (_) {}
  }
  function spInitIn(container) {
    const $jq = getJQ();
    if (!$jq) return;
    try {
      if ($jq.fn && typeof $jq.fn.selectpicker === 'function') {
        $jq(container || document).find('.selectpicker').selectpicker();
      }
    } catch (_) {}
  }
  function enablePicker(sel){
    const $jq = getJQ();
    const $s = (sel && sel.jquery) ? sel : $jq(sel);
    if (!$s || !$s.length) return;
    $s.prop('disabled', false).removeAttr('disabled').removeClass('disabled');
    spRefresh($s);
  }

  /* ====== helpers de selects ====== */
  const PLACEHOLDER = '<option value="">SELECCIONE</option>';

  function setPickerEmpty(sel) {
    const $jq = getJQ();
    const $s = (sel && sel.jquery) ? sel : $jq(sel);
    if (!$s || !$s.length) return;
    $s.html(PLACEHOLDER);
    $s.prop('disabled', false).removeAttr('disabled');
    spRefresh($s);
  }
  function setPickerLoading(sel) {
    const $jq = getJQ();
    const $s = (sel && sel.jquery) ? sel : $jq(sel);
    if (!$s || !$s.length) return;
    $s.html('<option value="">CARGANDO…</option>');
    $s.prop('disabled', false).removeAttr('disabled');
    spRefresh($s);
  }
  function hasRealOptions($sel) {
    return $sel.find('option').not('[value=""]').length > 0;
  }
  const getVal = ($sel) => ($sel.val() ? String($sel.val()) : '');
  function firstOptionOrEmpty($sel) {
    const $opts = $sel.find('option').not('[value=""]');
    return $opts.length ? String($opts.first().val()) : '';
  }
  function fillPicker($sel, rows, selectedId) {
    const sid = String(selectedId || '');
    $sel.html(PLACEHOLDER);
    (rows || []).forEach(r => {
      const v = String(r.id ?? r.value ?? '');
      const t = String(r.label ?? r.descripcion ?? r.text ?? '');
      const opt = new Option(t, v, false, v === sid);
      $sel.append(opt);
    });
    $sel.prop('disabled', false).removeAttr('disabled');
    spRefresh($sel);
  }

  /* ====== logging ====== */
  function isAbortError(e) {
    return e && (e.name === 'AbortError' || String(e.message || '').toLowerCase().includes('abort'));
  }
  function logIfNotAbort(tag, e) {
    if (!isAbortError(e)) console.error(tag + ':', e);
    else if (window.LETTER_DEBUG) console.debug(tag + ' (abortado)');
  }

  // POST x-www-form-urlencoded con CSRF
  async function postForm(url, body, signal) {
    const form = new URLSearchParams();
    let csrf = token || $('input[name="_token"]').val() || '';
    form.append('_token', csrf);
    Object.keys(body || {}).forEach(k => {
      const v = body[k];
      if (v === undefined || v === null) return;
      form.append(k, typeof v === 'boolean' ? (v ? '1' : '0') : String(v));
    });
    const resp = await fetch(url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
      },
      body: form,
      signal
    });
    if (!resp.ok) {
      const text = await resp.text().catch(()=> '');
      console.error('FETCH_ERROR', resp.status, text);
      throw new Error('HTTP ' + resp.status);
    }
    const ct = resp.headers.get('content-type') || '';
    if (!ct.includes('application/json')) return { ok:false, status:false, message:'Respuesta no JSON', raw:true };
    return resp.json();
  }

  /* ====== control de carrera ====== */
  const reqCtl = { a2: null, a3: null, deps: null, coor: null, clave: null };
  function abortAndNew(key) {
    try { reqCtl[key]?.abort(); } catch(_) {}
    reqCtl[key] = new AbortController();
    return reqCtl[key];
  }

  /* ====== resets en cascada ====== */
  function resetFrom(selectName) {
    const order = ['a1','a2','a3','usr','enl','uni','coor','tra','cla'];
    const map = { a1:$a1, a2:$a2, a3:$a3, usr:$usr, enl:$enl, uni:$uni, coor:$coor, tra:$tra, cla:$cla };
    const idx = order.indexOf(selectName);
    if (idx === -1) return;
    for (let i = idx + 1; i < order.length; i++) {
      const $s = map[order[i]];
      setPickerEmpty($s);
    }
  }
  function resetArea2(){ setPickerEmpty($a2); }
  function resetArea3(){ setPickerEmpty($a3); }

  /* =========================================================
     Cargar A2 por A1  (SIN auto-seleccion)
     ========================================================= */
  async function cargarArea2PorArea1(area1Id, preselectA2 = null) {
    resetFrom('a1');
    if (!area1Id) { resetArea2(); resetArea3(); return; }
    setPickerLoading($a2); setPickerEmpty($a3);
    const ctl = abortAndNew('a2');
    try {
      const json = await postForm(COLLECTION_AREA_URL, { by:'area2_by_area1', id_cat_area_1:Number(area1Id) }, ctl.signal);
      const rows = (json.ok && Array.isArray(json.value)) ? json.value : [];
      fillPicker($a2, rows, preselectA2 || null);
      enablePicker($a2);
      // 🔕 SIN autoselección ni trigger change aquí
    } catch (e) {
      logIfNotAbort('cargarArea2PorArea1 error', e);
      resetArea2(); resetArea3();
    }
  }

  /* =========================================================
     Cargar A3 por A2  (SIN auto-seleccion)
     ========================================================= */
  async function cargarArea3PorArea2(area2Id, preselectA3 = null) {
    resetFrom('a2');
    if (!area2Id) { resetArea3(); return; }
    setPickerLoading($a3);
    const ctl = abortAndNew('a3');
    try {
      const json = await postForm(COLLECTION_AREA_URL, {
        by:'area3_by_area2',
        id_cat_area_2:Number(area2Id),
        include_inactive: !!(window.LETTER && window.LETTER.includeInactiveArea3),
      }, ctl.signal);
      const rows = (json.ok && Array.isArray(json.value)) ? json.value : [];
      fillPicker($a3, rows, preselectA3 || null);
      enablePicker($a3);
      // 🔕 SIN autoselección ni trigger change aquí
    } catch (e) {
      logIfNotAbort('cargarArea3PorArea2 error', e);
      resetArea3();
    }
  }

  /* =========================================================
     Dependientes por cualquier nivel de área
     ========================================================= */
  async function actualizarCamposDerivadosPorAreaId(areaId) {
    if (!areaId) {
      [$usr,$enl,$uni,$tra,$coor,$cla].forEach(setPickerEmpty);
      return;
    }
    [$usr,$enl,$uni,$tra].forEach(setPickerLoading);
    setPickerEmpty($coor); setPickerEmpty($cla);

    const ctl = abortAndNew('deps');
    try {
      const json = await postForm(COLLECTION_AREA_URL, { id: Number(areaId) }, ctl.signal);
      if (!json) throw new Error('Respuesta vacía');

      const ensureFirst = ($s) => { const v = firstOptionOrEmpty($s); if (v) { $s.val(v); spRefresh($s); } };

      fillPicker($usr,  json.selectUsuario || json.usuarios || [], null);
      fillPicker($enl,  json.selectEnlace  || json.enlaces  || [], null);
      fillPicker($uni,  json.selectUnidad  || json.unidades || [], null);
      fillPicker($coor, json.selectCoor    || json.coordinaciones || [], null);
      fillPicker($tra,  json.selectTramite || json.tramites || [], null);

      [$usr,$enl,$uni,$coor,$tra].forEach(enablePicker);

      if (!getVal($usr))  ensureFirst($usr);
      if (!getVal($enl))  ensureFirst($enl);
      if (!getVal($uni))  ensureFirst($uni);
      if (!getVal($coor)) ensureFirst($coor);
      if (!getVal($tra))  ensureFirst($tra);

      if (getVal($uni)) await cargarCoordinacionesPorUnidad(getVal($uni), false);
      if (getVal($tra)) await cargarClavesPorTramite(getVal($tra), false);

    } catch (e) {
      logIfNotAbort('actualizarCamposDerivadosPorAreaId error', e);
      [$usr,$enl,$uni,$tra,$coor,$cla].forEach(setPickerEmpty);
    }
  }

  /* =========================================================
     Coordinaciones por Unidad
     ========================================================= */
  async function cargarCoordinacionesPorUnidad(unidadId, resetChain = true) {
    if (resetChain) setPickerEmpty($coor);
    if (!unidadId) return;
    setPickerLoading($coor);
    const ctl = abortAndNew('coor');
    try {
      const json = await postForm(COLLECTION_COOR_URL, { id: Number(unidadId) }, ctl.signal);
      const rows = (json && (json.value || json.selectCoordinacion || json.selectCoor))
        ? (json.value || json.selectCoordinacion || json.selectCoor) : [];
      fillPicker($coor, rows, null);
      enablePicker($coor);
      if (!getVal($coor)) {
        const v = firstOptionOrEmpty($coor);
        if (v) { $coor.val(v); spRefresh($coor); }
      }
    } catch (e) {
      logIfNotAbort('cargarCoordinacionesPorUnidad error', e);
      setPickerEmpty($coor);
    }
  }

  /* =========================================================
     Claves por Trámite
     ========================================================= */
  async function cargarClavesPorTramite(tramiteId, resetChain = true) {
    if (resetChain) setPickerEmpty($cla);
    if (!tramiteId) return;
    setPickerLoading($cla);
    const ctl = abortAndNew('clave');
    try {
      let json = await postForm(COLLECTION_CLAVE_URL, { id: Number(tramiteId) }, ctl.signal);
      if (!json || (!json.value && !json.selectClave)) {
        json = await postForm(COLLECTION_AREA_URL, { by:'clave_by_tramite', id_cat_tramite:Number(tramiteId) }, ctl.signal);
      }
      const rows = (json && (json.value || json.selectClave)) ? (json.value || json.selectClave) : [];
      fillPicker($cla, rows, null);
      enablePicker($cla);
      if (!getVal($cla)) {
        const v = firstOptionOrEmpty($cla);
        if (v) { $cla.val(v); spRefresh($cla); }
      }
    } catch (e) {
      logIfNotAbort('cargarClavesPorTramite error', e);
      setPickerEmpty($cla);
    }
  }

  /* ====== Encadenamientos ====== */
  let __seeding = false;

  // A1 -> carga A2, limpia A3, y carga dependientes por A1
  $(document).on('change', '#id_cat_area_1_ret,[name="id_cat_area_1_ret"]', async function () {
    if (__seeding) return;
    const area1Id = this.value || '';
    await cargarArea2PorArea1(area1Id, null); // no autoselecciona
    resetArea3();
    actualizarCamposDerivadosPorAreaId(area1Id);
  });

  // A2 -> carga A3 y dependientes por A2 (sin autoselección)
  $(document).on('change', '#id_cat_area_2_ret,[name="id_cat_area_2_ret"]', async function () {
    if (__seeding) return;
    const area2Id = this.value || '';
    await cargarArea3PorArea2(area2Id, null); // no autoselecciona
    actualizarCamposDerivadosPorAreaId(area2Id);
  });

  // A3 -> dependientes por Área
  $(document).on('change', '#id_cat_area_ret,[name="id_cat_area_ret"]', function () {
    if (__seeding) return;
    actualizarCamposDerivadosPorAreaId($(this).val());
  });

  // Unidad / Trámite
  $(document).on('change', '#id_cat_unidad_ret,[name="id_cat_unidad_ret"]', function(){
    if (__seeding) return;
    cargarCoordinacionesPorUnidad($(this).val(), true);
  });
  $(document).on('change', '#id_cat_tramite_ret,[name="id_cat_tramite_ret"]', function(){
    if (__seeding) return;
    cargarClavesPorTramite($(this).val(), true);
  });

  /* ====== Seed desde servidor ====== */
  async function seedFromServer(id) {
    const url = SEED_URL_BASE + encodeURIComponent(String(id));
    const resp = await fetch(url, { headers: { 'Accept':'application/json' }});
    if (!resp.ok) throw new Error('HTTP '+resp.status);
    const data = await resp.json();
    if (!data.ok) throw new Error(data.message || 'Seed inválido');

    const L = data.letter || {};
    const S = data.selects || {};

    $('#name_folio_gestion_returnado').text(data.folio || '');

    fillPicker($a1, S.area1, L.id_cat_area_1);
    fillPicker($a2, S.area2, L.id_cat_area_2);
    fillPicker($a3, S.area3, L.id_cat_area);

    fillPicker($usr,  S.usuarios,       L.id_usuario_area);
    fillPicker($enl,  S.enlaces,        L.id_usuario_enlace);
    fillPicker($uni,  S.unidades,       L.id_cat_unidad);
    fillPicker($coor, S.coordinaciones, L.id_cat_coordinacion);
    fillPicker($tra,  S.tramites,       L.id_cat_tramite);
    fillPicker($cla,  S.claves,         L.id_cat_clave);

    [$a1,$a2,$a3,$usr,$enl,$uni,$coor,$tra,$cla].forEach(enablePicker);

    // Si faltan dependientes de A3, consolida
    if (getVal($a3) && (!hasRealOptions($usr) || !hasRealOptions($tra) || !hasRealOptions($uni))) {
      await actualizarCamposDerivadosPorAreaId(getVal($a3));
    }
    if (getVal($uni) && !hasRealOptions($coor)) {
      await cargarCoordinacionesPorUnidad(getVal($uni), false);
    }
    if (getVal($tra) && !hasRealOptions($cla)) {
      await cargarClavesPorTramite(getVal($tra), false);
    }
  }

  /* ====== Seed desde el form principal ====== */
  function cloneSelect(fromSel, toSel) {
    const $from = $(fromSel);
    const $to   = $(toSel);
    if (!$from.length || !$to.length) return;
    $to.html($from.html());
    $to.val($from.val() || '');
    $to.prop('disabled', false).removeAttr('disabled');
    spRefresh($to);
  }
  async function seedFromMainForm() {
    cloneSelect('#id_cat_area_1', '#id_cat_area_1_ret');
    cloneSelect('#id_cat_area_2', '#id_cat_area_2_ret');
    cloneSelect('#id_cat_area',   '#id_cat_area_ret');

    cloneSelect('#id_usuario_area',    '#id_usuario_area_ret');
    cloneSelect('#id_usuario_enlace',  '#id_usuario_enlace_ret');
    cloneSelect('#id_cat_unidad',      '#id_cat_unidad_ret');
    cloneSelect('#id_cat_coordinacion','#id_cat_coordinacion_ret');
    cloneSelect('#id_cat_tramite',     '#id_cat_tramite_ret');
    cloneSelect('#id_cat_clave',       '#id_cat_clave_ret');

    if (getVal($a3)) await actualizarCamposDerivadosPorAreaId(getVal($a3));
    if (getVal($uni)) await cargarCoordinacionesPorUnidad(getVal($uni), false);
    if (getVal($tra)) await cargarClavesPorTramite(getVal($tra), false);
  }

  /* ====== Precarga inicial opcional ====== */
  async function precargaInicialPorInitials() {
    const area1Inicial = window.LETTER?.initials?.area1 || null;
    const area2Inicial = window.LETTER?.initials?.area2 || null;
    const area3Inicial = window.LETTER?.initials?.area3 || null;

    if (area1Inicial) {
      await cargarArea2PorArea1(area1Inicial, area2Inicial); // sin auto
      const a2 = $a2.val() || area2Inicial;
      if (a2) { await cargarArea3PorArea2(a2, area3Inicial); } // sin auto
      await actualizarCamposDerivadosPorAreaId(area1Inicial);
    } else {
      resetArea2();
      resetArea3();
      [$usr,$enl,$uni,$coor,$tra,$cla].forEach(setPickerEmpty);
    }
  }

  /* ====== API modal ====== */
  window.openReturnado = function (id, folGestion) {
    $('#id_correspondencia_ret').val(id || '');
    $('#name_folio_gestion_returnado').text(folGestion || '');
    $('body').addClass('modal-open-returnado');
    $m.fadeIn();

    spInitIn('#modalReturnado');
    [$a1,$a2,$a3,$usr,$enl,$uni,$coor,$tra,$cla].forEach(enablePicker);
    [$a2,$a3,$usr,$enl,$uni,$coor,$tra,$cla].forEach(setPickerEmpty);

    __seeding = true;
    const finish = () => { __seeding = false; };

    if ($('#id_cat_area').length) {
      Promise.resolve()
        .then(seedFromMainForm)
        .then(precargaInicialPorInitials)
        .catch(e => logIfNotAbort('seedFromMainForm/precarga error', e))
        .finally(finish);
    } else {
      seedFromServer(id)
        .then(precargaInicialPorInitials)
        .catch((e) => {
          logIfNotAbort('seedFromServer error', e);
          if (window.notyfEM) notyfEM.error('No se pudo cargar la información del turnado.');
        })
        .finally(finish);
    }
  };

  window.hiddenReturnado = function () {
    $m.fadeOut();
    $('body').removeClass('modal-open-returnado');
    Object.keys(reqCtl).forEach(k => { try { reqCtl[k]?.abort(); } catch(_){} });
  };

  /* ====== Guardado ====== */
  window.saveReturnado = async function () {
    const id    = $('#id_correspondencia_ret').val() || '';
    const area1 = getVal($a1);
    const area2 = getVal($a2);
    const area3 = getVal($a3);
    const destino = area3 || area2 || area1;

    if (!destino) { if (window.notyfEM) notyfEM.error('Selecciona CRH, CRHTOD o Área.'); return; }
    if (!getVal($usr))  { if (window.notyfEM) notyfEM.error('Selecciona un Usuario.'); return; }
    if (!getVal($tra))  { if (window.notyfEM) notyfEM.error('Selecciona un Trámite.'); return; }
    if (!getVal($cla))  { if (window.notyfEM) notyfEM.error('Selecciona una Clave.');  return; }

    const payload = {
      id_tbl_correspondencia: Number(id),
      id_cat_area_1: area1 ? Number(area1) : null,
      id_cat_area_2: area2 ? Number(area2) : null,
      id_cat_area:   Number(destino),

      id_usuario_area:    Number(getVal($usr)),
      id_usuario_enlace:  getVal($enl)  ? Number(getVal($enl))  : null,
      id_cat_unidad:      getVal($uni)  ? Number(getVal($uni))  : null,
      id_cat_coordinacion:getVal($coor) ? Number(getVal($coor)) : null,
      id_cat_tramite:     Number(getVal($tra)),
      id_cat_clave:       Number(getVal($cla)),

      force_turnado: true
    };

    try {
      const res = await postForm(TURNAR_SAVE_URL, payload);
      if (res && res.ok) {
        if ($('#id_cat_estatus').length) { $('#id_cat_estatus').val(String(res.idTurnado || 6)); spRefresh('#id_cat_estatus'); }
        if (window.notyfEM) notyfEM.success('Turnado actualizado.');
        hiddenReturnado();
      } else {
        if (window.notyfEM) notyfEM.error(res?.message || 'No se pudo guardar.');
      }
    } catch (e) {
      logIfNotAbort('TURNAR_SAVE_ERROR', e);
      if (window.notyfEM) notyfEM.error('Error al guardar.');
    }
  };

  // Cerrar por click en overlay
  $(window).on('click', function (ev) {
    if ($(ev.target).is('#modalReturnado')) hiddenReturnado();
  });

  // Refresh inicial de pickers
  [
    '#id_cat_area_1_ret,#id_cat_area_2_ret,#id_cat_area_ret',
    '#id_usuario_area_ret,#id_usuario_enlace_ret',
    '#id_cat_unidad_ret,#id_cat_coordinacion_ret',
    '#id_cat_tramite_ret,#id_cat_clave_ret'
  ].forEach(spRefresh);

  // Exponer helpers si los necesitas en otro script
  window.cargarArea2PorArea1 = cargarArea2PorArea1;
  window.cargarArea3PorArea2 = cargarArea3PorArea2;
  window.actualizarCamposDerivadosPorAreaId = actualizarCamposDerivadosPorAreaId;
})();
