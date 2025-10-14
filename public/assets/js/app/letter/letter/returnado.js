/* assets/js/app/letter/letter/returnado.js */
var token = $('meta[name="csrf-token"]').attr('content');

(function () {
  const $m  = $('#modalReturnado');

  // Jerarquía:
  // CRH ($a1) → CRHTOD ($a2) → Área ($a3) → (Usuario $usr, Enlace $enl, Unidad $uni) → (Coordinación $coor, Trámite $tra) → Clave $cla
  const $a1  = $('#id_cat_area_1_ret');
  const $a2  = $('#id_cat_area_2_ret');
  const $a3  = $('#id_cat_area_ret');

  const $usr  = $('#id_usuario_area_ret');
  const $enl  = $('#id_usuario_enlace_ret');
  const $uni  = $('#id_cat_unidad_ret');
  const $coor = $('#id_cat_coordinacion_ret');
  const $tra  = $('#id_cat_tramite_ret');
  const $cla  = $('#id_cat_clave_ret');

  // Endpoints (same-origin + fallbacks)
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

  // Endpoints dependientes (fallan a nada si tu app no los usa; si ya tienes handlers globales, seguirán funcionando)
  const COLLECTION_COOR_URL =
    (window.LETTER && window.LETTER.collectionCoorUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionCoordinacion')
      : '/letter/collection/collectionCoordinacion');

  const COLLECTION_CLAVE_URL =
    (window.LETTER && window.LETTER.collectionClaveUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionClave')
      : '/letter/collection/collectionClave');

  /* =========================
     selectpicker-safe helpers
     ========================= */
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
  function spDisable(sel) {
    const $jq = getJQ();
    const $s = (sel && sel.jquery) ? sel : $jq(sel);
    if (!$s || !$s.length) return;
    $s.prop('disabled', true);
    spRefresh($s);
  }
  function spEnable(sel) {
    const $jq = getJQ();
    const $s = (sel && sel.jquery) ? sel : $jq(sel);
    if (!$s || !$s.length) return;
    $s.prop('disabled', false);
    spRefresh($s);
  }

  /* ========== Helpers de selects ========== */
  const PLACEHOLDER = '<option value="">SELECCIONA UNA OPCIÓN</option>';

  function setPickerEmpty(sel) {
    const $jq = getJQ();
    const $s = (sel && sel.jquery) ? sel : $jq(sel);
    if (!$s || !$s.length) return;
    $s.html(PLACEHOLDER);
    spRefresh($s);
  }
  function setPickerLoading(sel) {
    const $jq = getJQ();
    const $s = (sel && sel.jquery) ? sel : $jq(sel);
    if (!$s || !$s.length) return;
    $s.html('<option value="">CARGANDO…</option>');
    spDisable($s);
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
    if (hasRealOptions($sel)) spEnable($sel); else spDisable($sel);
    spRefresh($sel);
  }

  // form-urlencoded (compatible con backend que espera $.post)
  async function postForm(url, body, signal) {
    const form = new URLSearchParams();
    form.append('_token', token || '');
    Object.keys(body || {}).forEach(k => {
      const v = body[k];
      if (v === undefined || v === null) return;
      form.append(k, typeof v === 'boolean' ? (v ? '1' : '0') : String(v));
    });
    const resp = await fetch(url, { method: 'POST', headers: { 'Accept': 'application/json' }, body: form, signal });
    if (!resp.ok) throw new Error('HTTP ' + resp.status);
    return resp.json();
  }

  /* ==========================
     Race control per-select
     ========================== */
  const reqCtl = {
    a2: null,
    a3: null,
    deps: null,
    coor: null,
    clave: null,
  };
  function abortAndNew(key) {
    try { reqCtl[key]?.abort(); } catch(_) {}
    reqCtl[key] = new AbortController();
    return reqCtl[key];
  }

  /* =========================================
     Reset en cascada (ancestro cambia → hijos)
     ========================================= */
  function resetFrom(selectName) {
    // Orden desde la raíz
    const order = ['a1','a2','a3','usr','enl','uni','coor','tra','cla'];
    const map = { a1:$a1, a2:$a2, a3:$a3, usr:$usr, enl:$enl, uni:$uni, coor:$coor, tra:$tra, cla:$cla };
    const idx = order.indexOf(selectName);
    if (idx === -1) return;
    for (let i = idx + 1; i < order.length; i++) {
      const $s = map[order[i]];
      setPickerEmpty($s);
      spDisable($s);
    }
  }

  /* =========================================================
     Cargar A2 por A1
     ========================================================= */
  async function loadA2ByA1(area1Id) {
    resetFrom('a1');
    if (!area1Id) return;
    setPickerLoading($a2);
    setPickerEmpty($a3);
    const ctl = abortAndNew('a2');
    try {
      const json = await postForm(COLLECTION_AREA_URL, { by:'area2_by_area1', id_cat_area_1:Number(area1Id) }, ctl.signal);
      fillPicker($a2, (json.ok && Array.isArray(json.value)) ? json.value : [], null);
      // Si solo hay una opción, autoselecciona y dispara cambio
      if ($a2[0].options.length === 2) {
        $a2.val($a2[0].options[1].value); spRefresh($a2); $a2.trigger('change');
      }
    } catch (e) {
      if (e.name !== 'AbortError') {
        console.error('loadA2ByA1 error:', e);
        setPickerEmpty($a2); spDisable($a2);
      }
    }
  }

  /* =========================================================
     Cargar A3 por A2
     ========================================================= */
  async function loadA3ByA2(area2Id) {
    resetFrom('a2');
    if (!area2Id) return;
    setPickerLoading($a3);
    const ctl = abortAndNew('a3');
    try {
      const json = await postForm(COLLECTION_AREA_URL, {
        by:'area3_by_area2',
        id_cat_area_2:Number(area2Id),
        include_inactive: !!(window.LETTER && window.LETTER.includeInactiveArea3),
      }, ctl.signal);
      fillPicker($a3, (json.ok && Array.isArray(json.value)) ? json.value : [], null);
      if ($a3[0].options.length === 2) {
        $a3.val($a3[0].options[1].value); spRefresh($a3); $a3.trigger('change');
      }
    } catch (e) {
      if (e.name !== 'AbortError') {
        console.error('loadA3ByA2 error:', e);
        setPickerEmpty($a3); spDisable($a3);
      }
    }
  }

  /* =========================================================
     Cargar dependientes por Área (A3)
     ========================================================= */
  async function loadDependentsByArea(area3Id) {
    resetFrom('a3');
    if (!area3Id) return;

    // Poner hijos inmediatos en loading
    [$usr,$enl,$uni,$tra].forEach(setPickerLoading);
    setPickerEmpty($coor); spDisable($coor);
    setPickerEmpty($cla);  spDisable($cla);

    const ctl = abortAndNew('deps');
    try {
      const json = await postForm(COLLECTION_AREA_URL, { id: Number(area3Id) }, ctl.signal);
      if (!json) throw new Error('Respuesta vacía');

      // Estas funciones ya existen en tu app; si no existen, usamos nuestro fillPicker
      if (typeof foreachSelectNull === 'function') {
        foreachSelectNull(json.selectUsuario, '#id_usuario_area_ret');
        foreachSelectNull(json.selectEnlace,  '#id_usuario_enlace_ret');
        foreachSelectNull(json.selectUnidad,  '#id_cat_unidad_ret');
      } else {
        fillPicker($usr, json.selectUsuario || [], null);
        fillPicker($enl, json.selectEnlace  || [], null);
        fillPicker($uni, json.selectUnidad  || [], null);
      }

      if (typeof foreachSelect === 'function') {
        foreachSelect(json.selectTramite, '#id_cat_tramite_ret');
      } else {
        fillPicker($tra, json.selectTramite || [], null);
      }

      // Auto-primeros donde aplique
      const ensureFirst = ($s) => { const v = firstOptionOrEmpty($s); if (v) { $s.val(v); spEnable($s); spRefresh($s); } };
      if (!getVal($usr)) ensureFirst($usr);
      if (!getVal($enl)) ensureFirst($enl);
      if (!getVal($uni)) ensureFirst($uni);
      if (!getVal($tra)) ensureFirst($tra);

      // Si ya hay unidad, cargar coordinaciones
      if (getVal($uni)) loadCoordinacionesByUnidad(getVal($uni), /*resetChain*/false);
      // Si ya hay trámite, cargar claves
      if (getVal($tra)) loadClavesByTramite(getVal($tra), /*resetChain*/false);

    } catch (e) {
      if (e.name !== 'AbortError') {
        console.error('loadDependentsByArea error:', e);
        [$usr,$enl,$uni,$tra,$coor,$cla].forEach(s => { setPickerEmpty(s); spDisable(s); });
      }
    }
  }

  /* =========================================================
     Cargar Coordinaciones por Unidad
     ========================================================= */
  async function loadCoordinacionesByUnidad(unidadId, resetChain = true) {
    if (resetChain) { // cuando cambia unidad manualmente
      setPickerEmpty($coor); spDisable($coor);
    }
    if (!unidadId) return;
    setPickerLoading($coor);
    const ctl = abortAndNew('coor');
    try {
      // Si tu backend usa otra firma, ajusta aquí:
      const json = await postForm(COLLECTION_COOR_URL, { id: Number(unidadId) }, ctl.signal);
      fillPicker($coor, (json && (json.value || json.selectCoor)) ? (json.value || json.selectCoor) : [], null);
    } catch (e) {
      if (e.name !== 'AbortError') {
        console.error('loadCoordinacionesByUnidad error:', e);
        setPickerEmpty($coor); spDisable($coor);
      }
    }
  }

  /* =========================================================
     Cargar Claves por Trámite
     ========================================================= */
  async function loadClavesByTramite(tramiteId, resetChain = true) {
    if (resetChain) { // cuando cambia trámite manualmente
      setPickerEmpty($cla); spDisable($cla);
    }
    if (!tramiteId) return;
    setPickerLoading($cla);
    const ctl = abortAndNew('clave');
    try {
      // Si tu backend usa otra firma, ajusta aquí:
      const json = await postForm(COLLECTION_CLAVE_URL, { id: Number(tramiteId) }, ctl.signal);
      fillPicker($cla, (json && (json.value || json.selectClave)) ? (json.value || json.selectClave) : [], null);
    } catch (e) {
      if (e.name !== 'AbortError') {
        console.error('loadClavesByTramite error:', e);
        setPickerEmpty($cla); spDisable($cla);
      }
    }
  }

  /* =========================================
     Encadenamientos (cascada y reset)
     ========================================= */
  $a1.on('change', () => {
    const v = getVal($a1);
    loadA2ByA1(v);
  });

  $a2.on('change', () => {
    const v = getVal($a2);
    loadA3ByA2(v);
  });

  $a3.on('change', () => {
    const v = getVal($a3);
    loadDependentsByArea(v);
  });

  $uni.on('change', () => {
    const v = getVal($uni);
    loadCoordinacionesByUnidad(v, /*resetChain*/true);
  });

  $tra.on('change', () => {
    const v = getVal($tra);
    loadClavesByTramite(v, /*resetChain*/true);
  });

  /* =========================================================
     Semilla desde el servidor (LISTA)
     ========================================================= */
  async function seedFromServer(id) {
    const url = SEED_URL_BASE + encodeURIComponent(String(id));
    const resp = await fetch(url, { headers: { 'Accept':'application/json' }});
    if (!resp.ok) throw new Error('HTTP '+resp.status);
    const data = await resp.json();
    if (!data.ok) throw new Error(data.message || 'Seed inválido');

    const L = data.letter || {};
    const S = data.selects || {};

    $('#name_folio_gestion_returnado').text(data.folio || '');

    // Áreas
    fillPicker($a1, S.area1, L.id_cat_area_1);
    fillPicker($a2, S.area2, L.id_cat_area_2);
    fillPicker($a3, S.area3, L.id_cat_area);

    // Dependientes existentes (si vienen del seed)
    fillPicker($usr,  S.usuarios,       L.id_usuario_area);
    fillPicker($enl,  S.enlaces,        L.id_usuario_enlace);
    fillPicker($uni,  S.unidades,       L.id_cat_unidad);
    fillPicker($coor, S.coordinaciones, L.id_cat_coordinacion);
    fillPicker($tra,  S.tramites,       L.id_cat_tramite);
    fillPicker($cla,  S.claves,         L.id_cat_clave);

    // Si hay valores preseleccionados, asegúrate de que los descendientes estén habilitados
    [$usr,$enl,$uni,$coor,$tra,$cla].forEach(($s)=>{ if (hasRealOptions($s)) spEnable($s); });

    // Si tenemos A3 válida pero dependientes vacíos, cargarlos (por consistencia)
    if (getVal($a3) && (!hasRealOptions($usr) || !hasRealOptions($tra) || !hasRealOptions($uni))) {
      loadDependentsByArea(getVal($a3));
    }
    if (getVal($uni) && !hasRealOptions($coor)) {
      loadCoordinacionesByUnidad(getVal($uni), /*resetChain*/false);
    }
    if (getVal($tra) && !hasRealOptions($cla)) {
      loadClavesByTramite(getVal($tra), /*resetChain*/false);
    }
  }

  /* =========================================================
     Semilla rápida desde el FORM principal
     ========================================================= */
  function cloneSelect(fromSel, toSel) {
    const $from = $(fromSel);
    const $to   = $(toSel);
    if (!$from.length || !$to.length) return;
    $to.html($from.html());
    $to.val($from.val() || '');
    if (!hasRealOptions($to)) spDisable($to); else spEnable($to);
    spRefresh($to);
  }

  function seedFromMainForm() {
    // Cadenas de áreas
    cloneSelect('#id_cat_area_1', '#id_cat_area_1_ret');
    cloneSelect('#id_cat_area_2', '#id_cat_area_2_ret');
    cloneSelect('#id_cat_area',   '#id_cat_area_ret');

    // Dependientes
    cloneSelect('#id_usuario_area',    '#id_usuario_area_ret');
    cloneSelect('#id_usuario_enlace',  '#id_usuario_enlace_ret');
    cloneSelect('#id_cat_unidad',      '#id_cat_unidad_ret');
    cloneSelect('#id_cat_coordinacion','#id_cat_coordinacion_ret');
    cloneSelect('#id_cat_tramite',     '#id_cat_tramite_ret');
    cloneSelect('#id_cat_clave',       '#id_cat_clave_ret');

    // Si hay A3, consolidar dependientes con backend (para evitar inconsistencias)
    if (getVal($a3)) loadDependentsByArea(getVal($a3));
    if (getVal($uni)) loadCoordinacionesByUnidad(getVal($uni), /*resetChain*/false);
    if (getVal($tra)) loadClavesByTramite(getVal($tra), /*resetChain*/false);
  }

  /* =========================================================
     API pública del modal
     ========================================================= */
  window.openReturnado = function (id, folGestion) {
    $('#id_correspondencia_ret').val(id || '');
    $('#name_folio_gestion_returnado').text(folGestion || '');
    $('body').addClass('modal-open-returnado');
    $m.fadeIn();

    // Inicializa selectpicker dentro del modal
    spInitIn('#modalReturnado');

    // Estado inicial: todo deshabilitado salvo A1 (origen)
    [$a2,$a3,$usr,$enl,$uni,$coor,$tra,$cla].forEach(s => { setPickerEmpty(s); spDisable(s); });

    // Si estamos en el FORM principal, clonamos; si no, pedimos seed al backend
    if ($('#id_cat_area').length) {
      seedFromMainForm();
    } else {
      seedFromServer(id).catch(() => {
        if (window.notyfEM) notyfEM.error('No se pudo cargar la información del turnado.');
      });
    }
  };

  window.hiddenReturnado = function () {
    $m.fadeOut();
    $('body').removeClass('modal-open-returnado');
    // Abortamos cualquier fetch pendiente al cerrar
    Object.keys(reqCtl).forEach(k => { try { reqCtl[k]?.abort(); } catch(_){} });
  };

  /* =========================================================
     Guardado (Turnado)
     ========================================================= */
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
      if (window.notyfEM) notyfEM.error('Error al guardar.');
      console.error('TURNAR_SAVE_ERROR:', e);
    }
  };

  // Botones del footer del modal
  $('#cancel_returnado').on('click', hiddenReturnado);
  $('#confir_returnado').on('click', saveReturnado);

  // Cerrar si clic en overlay
  $(window).on('click', function (ev) {
    if ($(ev.target).is('#modalReturnado')) hiddenReturnado();
  });

  // Refrescar pickers por si Blade ya pintó opciones
  [
    '#id_cat_area_1_ret','#id_cat_area_2_ret','#id_cat_area_ret',
    '#id_usuario_area_ret','#id_usuario_enlace_ret',
    '#id_cat_unidad_ret','#id_cat_coordinacion_ret',
    '#id_cat_tramite_ret','#id_cat_clave_ret'
  ].forEach(spRefresh);
})();
