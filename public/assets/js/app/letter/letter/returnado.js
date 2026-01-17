/* assets/js/app/letter/letter/returnado.js — FINAL (FIX A2 COOR + SHOW TRAMITE/CLAVE LOCKED) */

var token = $('meta[name="csrf-token"]').attr('content');

(function () {
  /* =========================== LOG =========================== */
  const URL_HAS_RETLOG = /(?:\?|&)retlog=1(?:&|$)/i.test(String(window.location.search || ''));
  const DEBUG = !!(window.LETTER_DEBUG || URL_HAS_RETLOG);
  const L = (...a) => { if (DEBUG) console.log('[RET]', ...a); };

  /* ======================== SELECTS ========================= */
  const $m  = $('#modalReturnado');

  // Los 3 niveles de área (buscar por id o name)
  const $a1  = $('#id_cat_area_1_ret,[name="id_cat_area_1_ret"]');
  const $a2  = $('#id_cat_area_2_ret,[name="id_cat_area_2_ret"]');
  const $a3  = $('#id_cat_area_ret,[name="id_cat_area_ret"], #modalReturnado select#id_cat_area');

  // Dependientes
  const $usr = $('#id_usuario_area_ret,[name="id_usuario_area_ret"]');
  const $enl = $('#id_usuario_enlace_ret,[name="id_usuario_enlace_ret"]');
  const $uni = $('#id_cat_unidad_ret,[name="id_cat_unidad_ret"]');
  const $coor= $('#id_cat_coordinacion_ret,[name="id_cat_coordinacion_ret"]');
  const $tra = $('#id_cat_tramite_ret,[name="id_cat_tramite_ret"]');
  const $cla = $('#id_cat_clave_ret,[name="id_cat_clave_ret"]');

  /* ======================== BLOQUEO (COOR/TRAM/CLAVE) ======================== */
  // ✅ Cambia a false si en algún momento quieres permitir editar estos 3 campos
  const LOCK_FIXED_FIELDS = true;
  const __fixed = { coor:'', tra:'', cla:'' };

  function captureFixedFields() {
    __fixed.coor = getVal($coor);
    __fixed.tra  = getVal($tra);
    __fixed.cla  = getVal($cla);
  }
  function applyFixedFields() {
    if (__fixed.coor) { $coor.val(__fixed.coor); spRefresh($coor); }
    if (__fixed.tra)  { $tra.val(__fixed.tra);  spRefresh($tra); }
    if (__fixed.cla)  { $cla.val(__fixed.cla);  spRefresh($cla); }
  }
  function lockFixedFieldsUI() {
    if (!LOCK_FIXED_FIELDS) return;
    [$coor,$tra,$cla].forEach($s => {
      $s.prop('disabled', true).attr('disabled', 'disabled').addClass('disabled');
      spRefresh($s);
    });
  }

  /* ======================== ENDPOINTS ======================== */
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

  // Puedes dejarla definida, pero ya no se usa para listar claves:
  const COLLECTION_CLAVE_URL =
    (window.LETTER && window.LETTER.collectionClaveUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionClave')
      : '/letter/collection/collectionClave');

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
  function enablePicker(sel){
    const $jq = getJQ(); const $s = (sel && sel.jquery) ? sel : $jq(sel);
    if (!$s || !$s.length) return;
    $s.prop('disabled', false).removeAttr('disabled').removeClass('disabled');
    spRefresh($s);
  }

  /* ======================== helpers UI ======================== */
  const PLACEH = '<option value="">SELECCIONE</option>';
  function setEmpty($s) { $s.html(PLACEH).prop('disabled', false).val(''); spRefresh($s); }
  function setLoading($s) { $s.html('<option value="">CARGANDO…</option>').prop('disabled', false).val(''); spRefresh($s); }
  function fillPicker($s, rows, selectedId) {
    $s.html(PLACEH);
    (rows || []).forEach(r => {
      const v = String(r.id ?? r.value ?? '');
      const t = String(r.label ?? r.descripcion ?? r.text ?? '');
      $s.append(new Option(t, v, false, selectedId != null && String(selectedId) === v));
    });
    $s.prop('disabled', false);
    if (selectedId == null) $s.val('');
    spRefresh($s);
  }
  const getVal = ($s) => ($s && $s.length && $s.val()) ? String($s.val()) : '';
  const hasRealOptions = ($s) => $s.find('option').not('[value=""]').length > 0;
  const firstRealVal = ($s) => {
    const $o = $s.find('option').not('[value=""]'); return $o.length ? String($o.first().val()) : '';
  };

  function ensureOptionExists($s, id, textIfMissing) {
    if (!id) return;
    const sid = String(id);
    if ($s.find('option[value="'+sid+'"]').length) return;
    // fallback: agrega opción para que el select pueda mostrar el value (si no vino lista)
    $s.append(new Option(textIfMissing || 'SELECCIONADO', sid, true, true));
  }

  /* ========================= FETCH base ========================= */
  function isAbortError(e) { return e && (e.name === 'AbortError' || String(e.message||'').toLowerCase().includes('abort')); }
  function logIfNotAbort(tag, e) { if (!isAbortError(e)) console.error(tag+':', e); else L(tag, '(abortado)'); }

  async function postForm(url, body, signal) {
    const form = new URLSearchParams();
    let csrf = token || $('input[name="_token"]').val() || '';
    form.append('_token', csrf);
    Object.keys(body || {}).forEach(k => {
      const v = body[k];
      if (v === undefined || v === null) return;
      form.append(k, typeof v === 'boolean' ? (v ? '1':'0') : String(v));
    });
    L('POST', url, Object.fromEntries(form));
    const resp = await fetch(url, {
      method:'POST',
      headers: { 'Accept':'application/json', 'X-CSRF-TOKEN':csrf, 'Content-Type':'application/x-www-form-urlencoded;charset=UTF-8' },
      body: form, signal
    });
    const ct = (resp.headers.get('content-type') || '').toLowerCase();
    const isJson = ct.includes('application/json');
    const data = isJson ? await resp.json() : { ok:false, status:false, raw:true };
    L('RESP', url, resp.status, data);
    if (!resp.ok) throw Object.assign(new Error('HTTP '+resp.status), {data, status:resp.status});
    return data;
  }

  /* ====================== control de carrera ====================== */
  const reqCtl = { a2:null, a3:null, deps:null, coor:null, clave:null };
  function abortAndNew(key){ try{ reqCtl[key]?.abort(); }catch(_){} reqCtl[key] = new AbortController(); return reqCtl[key]; }

  /* ========================= GUARD ESTATUS ======================== */
  function getAllowedStatusSet() {
    if (Array.isArray(window.LETTER?.statusAllowedReturnado) && window.LETTER.statusAllowedReturnado.length) {
      return new Set(window.LETTER.statusAllowedReturnado.map(String));
    }
    if (window.LETTER?.statusReturnadoId != null) return new Set([String(window.LETTER.statusReturnadoId), '1']);
    return new Set(['1','8']); // TURNADO / RE-TURNADO
  }
  function getCurrentStatusId() {
    const domVal = $('#id_cat_estatus').val(); if (domVal != null && domVal !== '') return String(domVal);
    if (window.LETTER && window.LETTER.currentStatusId != null) return String(window.LETTER.currentStatusId);
    return '';
  }
  function isReturnadoAllowedNow() {
    const allowed = getAllowedStatusSet(); const cur = getCurrentStatusId();
    return !allowed.size || allowed.has(cur);
  }
  function guardReturnadoOrWarn() {
    const ok = isReturnadoAllowedNow();
    if (!ok) {
      const msg = (window.LETTER && window.LETTER.allowedStatusMessage)
        ? window.LETTER.allowedStatusMessage
        : 'Solo las correspondencias en estatus TURNADO o RE-TURNADO pueden usar esta función.';
      if (window.notyfEM) notyfEM.error(msg); else alert(msg);
    }
    L('guard estatus:', {allowed:[...getAllowedStatusSet()], current:getCurrentStatusId(), ok});
    return ok;
  }

  /* =================== DETECCIÓN DE NIVEL ACTUAL =================== */
  function detectNivelActual() {
    const modal = { a1: getVal($a1), a2: getVal($a2), a3: getVal($a3) };
    const main = {
      a1: $('#id_cat_area_1').val() ? String($('#id_cat_area_1').val()) : '',
      a2: $('#id_cat_area_2').val() ? String($('#id_cat_area_2').val()) : '',
      a3: $('#id_cat_area').val()   ? String($('#id_cat_area').val())   : '',
    };
    const a3 = modal.a3 || main.a3;
    const a2 = modal.a2 || main.a2;
    const a1 = modal.a1 || main.a1;

    let nivel = null, id = null;
    if (a3) { nivel = 'A3'; id = a3; }
    else if (a2) { nivel = 'A2'; id = a2; }
    else if (a1) { nivel = 'A1'; id = a1; }

    L('detectNivelActual =>', {nivel, id}, {modal, main});
    return { nivel, id };
  }

  function setBadgeNivel(nivelTxt) {
    let $host = $('#name_folio_gestion_returnado');
    if (!$host.length) return;
    let $b = $('#ret_badge_nivel');
    if (!$b.length) {
      $b = $('<span id="ret_badge_nivel" style="margin-left:8px; font-size:12px; padding:2px 6px; border-radius:10px; background:#e6f4ea; color:#10312b;"></span>');
      $host.after($b);
    }
    $b.text(nivelTxt || '');
  }

  function computeAndShowNivelActual() {
    const d = detectNivelActual();
    const label = d.nivel === 'A3' ? 'ÁREA (A3)'
                : d.nivel === 'A2' ? 'CRHTOD (A2)'
                : d.nivel === 'A1' ? 'CRH (A1)' : '';
    if (label) setBadgeNivel(label);
  }

  /* =================== Cascadas: A1 → A2 → A3 =================== */
  async function cargarArea2PorArea1(area1Id, preselectA2 = null) {
    if (!area1Id) { setEmpty($a2); setEmpty($a3); return; }
    setLoading($a2); setEmpty($a3);
    const ctl = abortAndNew('a2');
    try {
      const json = await postForm(COLLECTION_AREA_URL, { by:'area2_by_area1', id_cat_area_1:Number(area1Id) }, ctl.signal);
      const rows = (json.ok && Array.isArray(json.value)) ? json.value : [];
      fillPicker($a2, rows, preselectA2 || null);
      enablePicker($a2);
      const $opts = $a2.find('option').not('[value=""]');
      if (!getVal($a2) && $opts.length === 1) $a2.val(String($opts.first().val())).trigger('change');
    } catch (e) { logIfNotAbort('cargarArea2PorArea1 error', e); setEmpty($a2); setEmpty($a3); }
  }

  async function cargarArea3PorArea2(area2Id, preselectA3 = null) {
    if (!area2Id) { setEmpty($a3); return; }
    setLoading($a3);
    const ctl = abortAndNew('a3');
    try {
      const json = await postForm(COLLECTION_AREA_URL, { by:'area3_by_area2', id_cat_area_2:Number(area2Id), include_inactive:false }, ctl.signal);
      const rows = (json.ok && Array.isArray(json.value)) ? json.value : [];
      fillPicker($a3, rows, preselectA3 || null);
      enablePicker($a3);
      const $opts = $a3.find('option').not('[value=""]');
      if (!getVal($a3) && $opts.length === 1) $a3.val(String($opts.first().val())).trigger('change');
    } catch (e) { logIfNotAbort('cargarArea3PorArea2 error', e); setEmpty($a3); }
  }

  /* =================== Dependientes por Área =================== */
  async function actualizarCamposDerivadosPorAreaId(areaId, preserve = true) {
    if (!areaId) { [$usr,$enl,$uni,$coor,$tra,$cla].forEach(setEmpty); return; }

    const prev = preserve ? {
      usr:  getVal($usr),  enl:  getVal($enl),
      uni:  getVal($uni),  coor: getVal($coor),
      tra:  getVal($tra),  cla:  getVal($cla),
    } : null;

    // Usuarios/Enlace/Unidad siempre se refrescan
    [$usr,$enl,$uni].forEach(setLoading);

    // Si están bloqueados, NO borres coor/tra/cla, pero sí permite cargarlos para MOSTRAR
    if (!LOCK_FIXED_FIELDS) {
      setEmpty($coor); setLoading($tra); setEmpty($cla);
    }

    const ctl = abortAndNew('deps');
    try {
      const json = await postForm(COLLECTION_AREA_URL, { id:Number(areaId) }, ctl.signal);

      fillPicker($usr,  json.selectUsuario || json.usuarios || [], preserve ? prev.usr  : null);
      fillPicker($enl,  json.selectEnlace  || json.enlaces  || [], preserve ? prev.enl  : null);
      fillPicker($uni,  json.selectUnidad  || json.unidades || [], preserve ? prev.uni  : null);

      // ✅ Trámite: aunque esté LOCK, lo cargamos para que “se vea” como en el form
      const traWanted = (preserve ? prev?.tra : null) || __fixed.tra || window.LETTER?.initials?.tramite || null;
      if (json.selectTramite || json.tramites) {
        fillPicker($tra, json.selectTramite || json.tramites || [], traWanted);
      }

      // ✅ Si está LOCK, Clave la cargamos por trámite (ver función)
      // Coordinación se corrige por unidad (ver más abajo)

      [$usr,$enl,$uni,$tra].forEach(enablePicker);

      const ensureFirst = ($s) => { if (!getVal($s)) { const v = firstRealVal($s); if (v) { $s.val(v); spRefresh($s); } } };
      ensureFirst($usr); ensureFirst($enl); ensureFirst($uni);

      // ✅ Coordinación SIEMPRE por unidad, con preselección (A2)
      if (getVal($uni)) {
        const coorWanted = (preserve ? prev?.coor : null) || __fixed.coor || window.LETTER?.initials?.coordinacion || null;
        await cargarCoordinacionesPorUnidad(getVal($uni), false, coorWanted);
      }

      // ✅ Clave SIEMPRE por trámite, aunque esté LOCK (solo para mostrar + fijar)
      const traId = getVal($tra) || traWanted;
      if (traId) {
        const claWanted = (preserve ? prev?.cla : null) || __fixed.cla || window.LETTER?.initials?.clave || null;
        await cargarClavesPorTramite(traId, false, claWanted, /*force*/ true);
      } else {
        if (!LOCK_FIXED_FIELDS) setEmpty($cla);
      }

      if (LOCK_FIXED_FIELDS) {
        // Captura lo que quedó (ya con opciones visibles) y bloquea
        captureFixedFields();
        applyFixedFields();
        lockFixedFieldsUI();
      }

    } catch (e) {
      logIfNotAbort('actualizarCamposDerivadosPorAreaId error', e);
      [$usr,$enl,$uni].forEach(setEmpty);
      if (!LOCK_FIXED_FIELDS) [$coor,$tra,$cla].forEach(setEmpty);
      else { applyFixedFields(); lockFixedFieldsUI(); }
    }
  }

  /* ====== FIX: Coordinaciones por Unidad (SIEMPRE recalcular y preseleccionar) ====== */
  async function cargarCoordinacionesPorUnidad(unidadId, resetChain = true, preselectId = null) {
    if (resetChain) setEmpty($coor);
    if (!unidadId) { if (LOCK_FIXED_FIELDS) { applyFixedFields(); lockFixedFieldsUI(); } return; }

    setLoading($coor);
    const ctl = abortAndNew('coor');

    try {
      const json = await postForm(COLLECTION_COOR_URL, { id: Number(unidadId) }, ctl.signal);
      const rows = (json && (json.selectCoordinacion || json.value || json.selectCoor))
        ? (json.selectCoordinacion || json.value || json.selectCoor)
        : [];

      const want = preselectId ? String(preselectId) : (getVal($coor) || null);

      fillPicker($coor, rows, want);
      enablePicker($coor);

      if (!getVal($coor)) {
        const v = firstRealVal($coor);
        if (v) { $coor.val(v); spRefresh($coor); }
      }

      if (LOCK_FIXED_FIELDS) {
        if (preselectId) { $coor.val(String(preselectId)); spRefresh($coor); }
        __fixed.coor = getVal($coor);
        applyFixedFields();
        lockFixedFieldsUI();
      }
    } catch (e) {
      logIfNotAbort('cargarCoordinacionesPorUnidad error', e);
      setEmpty($coor);
      if (LOCK_FIXED_FIELDS) { applyFixedFields(); lockFixedFieldsUI(); }
    }
  }

  /* ====== FIX: Claves por Trámite (permite force para LOCK) ====== */
  async function cargarClavesPorTramite(tramiteId, resetChain = true, preselectId = null, force = false) {
    if (LOCK_FIXED_FIELDS && !force) { applyFixedFields(); lockFixedFieldsUI(); return; }

    if (resetChain) setEmpty($cla);
    if (!tramiteId) return;

    setLoading($cla);
    const ctl = abortAndNew('clave');

    try {
      const json = await postForm(COLLECTION_AREA_URL, {
        by:'clave_by_tramite',
        id_cat_tramite:Number(tramiteId)
      }, ctl.signal);

      const rows = (json && (json.value || json.selectClave))
        ? (json.value || json.selectClave)
        : [];

      const want = preselectId ? String(preselectId) : null;

      fillPicker($cla, rows, want);
      enablePicker($cla);

      if (!getVal($cla)) {
        const v = firstRealVal($cla);
        if (v) { $cla.val(v); spRefresh($cla); }
      }

      if (LOCK_FIXED_FIELDS) {
        if (preselectId) { $cla.val(String(preselectId)); spRefresh($cla); }
        __fixed.cla = getVal($cla);
        applyFixedFields();
        lockFixedFieldsUI();
      }
    } catch (e) {
      logIfNotAbort('cargarClavesPorTramite error', e);
      setEmpty($cla);
      if (LOCK_FIXED_FIELDS) { applyFixedFields(); lockFixedFieldsUI(); }
    }
  }

  /* ======================= Tabla / lista ======================= */
  function refreshMainTable() {
    try {
      if ($.fn && $.fn.DataTable) {
        const ids = ['#tablaCorrespondencia', '#tabla_returnado', '#tabla_principal', '.dataTable'];
        let hit = false;
        for (const sel of ids) {
          const $t = $(sel);
          if ($t.length && ($t.hasClass('dataTable') || $t.is('.dataTable'))) {
            const api = $t.DataTable();
            if (api && api.ajax) api.ajax.reload(null, false);
            else if (api) api.draw(false);
            hit = true;
          }
        }
        if (hit) return;
      }
      if (window.Livewire && typeof window.Livewire.dispatch === 'function') { window.Livewire.dispatch('refreshTable'); return; }
      if (window.Turbo && typeof window.Turbo.visit === 'function') { window.Turbo.visit(window.location.href, { action:'replace' }); return; }
      window.location.reload();
    } catch (e) { console.error('REFRESH_TABLE_ERROR:', e); window.location.reload(); }
  }

  /* ==================== Seed / precarga ==================== */
  let __seeding = false;

  function cloneSelect(fromSel, toSel) {
    const $from = $(fromSel); const $to = $(toSel);
    if (!$from.length || !$to.length) return;
    $to.html($from.html()); $to.val($from.val() || ''); $to.prop('disabled', false);
    spRefresh($to);
  }

  async function seedFromMainForm() {
    L('seedFromMainForm');

    cloneSelect('#id_cat_area_1', '#id_cat_area_1_ret');
    cloneSelect('#id_cat_area_2', '#id_cat_area_2_ret');
    cloneSelect('#id_cat_area',   '#id_cat_area_ret');

    cloneSelect('#id_usuario_area',    '#id_usuario_area_ret');
    cloneSelect('#id_usuario_enlace',  '#id_usuario_enlace_ret');
    cloneSelect('#id_cat_unidad',      '#id_cat_unidad_ret');
    cloneSelect('#id_cat_coordinacion','#id_cat_coordinacion_ret');
    cloneSelect('#id_cat_tramite',     '#id_cat_tramite_ret');
    cloneSelect('#id_cat_clave',       '#id_cat_clave_ret');

    // captura fijos del form
    if (LOCK_FIXED_FIELDS) captureFixedFields();

    // refresca dependientes por el nivel disponible (A3/A2/A1)
    const areaRef = getVal($a3) || getVal($a2) || getVal($a1);
    if (areaRef) await actualizarCamposDerivadosPorAreaId(areaRef, true);

    // asegura coordinación por unidad
    if (getVal($uni)) {
      const coorWanted = __fixed.coor || window.LETTER?.initials?.coordinacion || null;
      await cargarCoordinacionesPorUnidad(getVal($uni), false, coorWanted);
    }

    // asegura claves por trámite (mostrar)
    const traWanted = __fixed.tra || window.LETTER?.initials?.tramite || getVal($tra) || null;
    const claWanted = __fixed.cla || window.LETTER?.initials?.clave || getVal($cla) || null;
    if (traWanted) await cargarClavesPorTramite(traWanted, false, claWanted, true);

    if (LOCK_FIXED_FIELDS) { captureFixedFields(); applyFixedFields(); lockFixedFieldsUI(); }
  }

  async function seedFromServer(id) {
    const url = SEED_URL_BASE + encodeURIComponent(String(id)) + '?include_inactive=1';
    L('seedFromServer', url);

    const resp = await fetch(url, { headers:{'Accept':'application/json'} });
    if (!resp.ok) throw new Error('HTTP '+resp.status);
    const data = await resp.json();
    if (!data.ok) throw new Error(data.message || 'Seed inválido');

    $('#name_folio_gestion_returnado').text(data.folio || '');

    const LTR = data.letter || {};
    const S   = data.selects || {};

    // pinta lo que venga
    fillPicker($a1,  S.area1,  LTR.id_cat_area_1);
    fillPicker($a2,  S.area2,  LTR.id_cat_area_2);
    fillPicker($a3,  S.area3,  LTR.id_cat_area);

    fillPicker($usr, S.usuarios,       LTR.id_usuario_area);
    fillPicker($enl, S.enlaces,        LTR.id_usuario_enlace);
    fillPicker($uni, S.unidades,       LTR.id_cat_unidad);

    // Trámite y Clave: si el seed trae listas, úsalo; si no, NO borres y luego los cargamos por área/trámite
    if (S.tramites && S.tramites.length) fillPicker($tra, S.tramites, LTR.id_cat_tramite);
    else { $tra.val(LTR.id_cat_tramite ? String(LTR.id_cat_tramite) : ''); spRefresh($tra); }

    if (S.claves && S.claves.length) fillPicker($cla, S.claves, LTR.id_cat_clave);
    else { $cla.val(LTR.id_cat_clave ? String(LTR.id_cat_clave) : ''); spRefresh($cla); }

    // habilita
    [$a1,$a2,$a3,$usr,$enl,$uni,$coor,$tra,$cla].forEach(enablePicker);

    // fija IDs deseados desde seed (para que luego se seleccionen)
    __fixed.tra  = LTR.id_cat_tramite ? String(LTR.id_cat_tramite) : (__fixed.tra || '');
    __fixed.cla  = LTR.id_cat_clave   ? String(LTR.id_cat_clave)   : (__fixed.cla || '');
    __fixed.coor = LTR.id_cat_coordinacion ? String(LTR.id_cat_coordinacion) : (__fixed.coor || '');

    // fuerza cascada si faltaron áreas
    if (!hasRealOptions($a2) && LTR.id_cat_area_1) await cargarArea2PorArea1(LTR.id_cat_area_1, LTR.id_cat_area_2 || null);
    if (!hasRealOptions($a3) && (LTR.id_cat_area_2 || LTR.id_cat_area_1)) {
      const a2 = LTR.id_cat_area_2 || getVal($a2);
      if (a2) await cargarArea3PorArea2(a2, LTR.id_cat_area || null);
    }

    // completa dependientes por el nivel disponible
    const areaRef = getVal($a3) || LTR.id_cat_area || getVal($a2) || LTR.id_cat_area_2 || getVal($a1) || LTR.id_cat_area_1;
    if (areaRef) await actualizarCamposDerivadosPorAreaId(areaRef, true);

    // coordinación por unidad (preselect seed)
    if (getVal($uni)) await cargarCoordinacionesPorUnidad(getVal($uni), false, __fixed.coor || null);

    // claves por trámite (preselect seed) aunque esté LOCK
    const traRef = getVal($tra) || __fixed.tra || null;
    if (traRef) await cargarClavesPorTramite(traRef, false, __fixed.cla || null, true);

    if (LOCK_FIXED_FIELDS) { captureFixedFields(); applyFixedFields(); lockFixedFieldsUI(); }
  }

  /* ===================== ABRIR / CERRAR MODAL ===================== */
  window.openReturnado = function (id, folGestion) {
    if (!guardReturnadoOrWarn()) return;

    $('#id_correspondencia_ret').val(id || '');
    $('#name_folio_gestion_returnado').text(folGestion || '');

    $('body').addClass('modal-open-returnado');
    $m.fadeIn();

    spInitIn('#modalReturnado');
    [$a1,$a2,$a3,$usr,$enl,$uni,$coor,$tra,$cla].forEach(enablePicker);

    __seeding = true;
    const finish = () => {
      __seeding = false;
      computeAndShowNivelActual();
      if (LOCK_FIXED_FIELDS) { applyFixedFields(); lockFixedFieldsUI(); }
    };

    if ($('#id_cat_area').length) {
      Promise.resolve()
        .then(seedFromMainForm)
        .catch(e => logIfNotAbort('seedFromMainForm error', e))
        .finally(finish);
    } else {
      Promise.resolve()
        .then(() => seedFromServer(id))
        .catch(e => { logIfNotAbort('seedFromServer error', e); if (window.notyfEM) notyfEM.error('No se pudo cargar la información del turnado.'); })
        .finally(finish);
    }
  };

  window.hiddenReturnado = function () {
    $m.stop(true, true).fadeOut(150, function(){ $(this).hide(); });
    $('body').removeClass('modal-open-returnado');
    Object.keys(reqCtl).forEach(k => { try { reqCtl[k]?.abort(); } catch(_){} });
  };

  /* =========================== SAVE =========================== */
  window.saveReturnado = async function () {
    if (!guardReturnadoOrWarn()) return;

    const id    = $('#id_correspondencia_ret').val() || '';
    const a1Val = getVal($a1);
    const a2Val = getVal($a2);
    const a3Val = getVal($a3);

    const a3HasOptions = $a3.find('option').not('[value=""]').length > 0;
    if (a3HasOptions && !a3Val) { if (window.notyfEM) notyfEM.warning('Selecciona un Área (A3) para continuar.'); return; }

    const destino = a3Val || (!a3HasOptions ? a2Val : '') || a1Val;

    if (!destino) { if (window.notyfEM) notyfEM.error('Selecciona CRH, CRHTOD o Área.'); return; }
    if (!getVal($usr)) { if (window.notyfEM) notyfEM.error('Selecciona un Usuario.'); return; }
    if (!getVal($tra)) { if (window.notyfEM) notyfEM.error('Selecciona un Trámite.'); return; }
    if (!getVal($cla)) { if (window.notyfEM) notyfEM.error('Selecciona una Clave.'); return; }

    const payload = {
      id_tbl_correspondencia: Number(id),
      id_cat_area_1: a1Val ? Number(a1Val) : null,
      id_cat_area_2: a2Val ? Number(a2Val) : null,
      id_cat_area:   Number(destino),
      id_usuario_area:    Number(getVal($usr)),
      id_usuario_enlace:  getVal($enl)  ? Number(getVal($enl))  : null,
      id_cat_unidad:      getVal($uni)  ? Number(getVal($uni))  : null,
      id_cat_coordinacion:getVal($coor) ? Number(getVal($coor)) : null,
      id_cat_tramite:     Number(getVal($tra)),
      id_cat_clave:       Number(getVal($cla)),
      toggle_status: true
    };

    try {
      const res = await postForm(TURNAR_SAVE_URL, payload);
      if (res && res.ok) {
        const newStatus = (res.newStatusId != null) ? res.newStatusId : (res.idTurnado ?? null);
        if ($('#id_cat_estatus').length && newStatus != null) {
          $('#id_cat_estatus').val(String(newStatus)); spRefresh('#id_cat_estatus');
        }
        window.dispatchEvent(new CustomEvent('returnado:saved', { detail: { id: Number(id), newStatusId: newStatus } }));
        if (window.notyfEM) notyfEM.success('Turnado actualizado.');
        refreshMainTable();
        hiddenReturnado();
      } else {
        if (window.notyfEM) notyfEM.error(res?.message || 'No se pudo guardar.');
      }
    } catch (e) { logIfNotAbort('TURNAR_SAVE_ERROR', e); if (window.notyfEM) notyfEM.error('Error al guardar.'); }
  };

  /* ======================= EVENT LISTENERS ======================= */
  // A1
  $(document).on('changed.bs.select change',
    '#id_cat_area_1_ret,[name="id_cat_area_1_ret"]',
    async function () {
      if (__seeding) return;
      const area1Id = this.value || '';
      L('change A1 =>', area1Id);
      await cargarArea2PorArea1(area1Id, null);
      setEmpty($a3);
      actualizarCamposDerivadosPorAreaId(area1Id, false);
      computeAndShowNivelActual();
  });

  // A2
  $(document).on('changed.bs.select change',
    '#id_cat_area_2_ret,[name="id_cat_area_2_ret"]',
    async function () {
      if (__seeding) return;
      const area2Id = this.value || '';
      L('change A2 =>', area2Id);
      await cargarArea3PorArea2(area2Id, null);
      actualizarCamposDerivadosPorAreaId(area2Id, false);
      computeAndShowNivelActual();
  });

  // A3
  $(document).on('changed.bs.select change',
    '#id_cat_area_ret,[name="id_cat_area_ret"], #modalReturnado select#id_cat_area',
    function () {
      if (__seeding) return;
      L('change A3 =>', $(this).val());
      actualizarCamposDerivadosPorAreaId($(this).val(), false);
      computeAndShowNivelActual();
  });

  // Unidad: SIEMPRE recalcular coordinación
  $(document).on('changed.bs.select change', '#id_cat_unidad_ret,[name="id_cat_unidad_ret"]', function(){
    if (__seeding) return;
    cargarCoordinacionesPorUnidad($(this).val(), true, __fixed.coor || null);
  });

  // Trámite: recargar claves (aunque LOCK, solo para mostrar y fijar)
  $(document).on('changed.bs.select change', '#id_cat_tramite_ret,[name="id_cat_tramite_ret"]', function(){
    if (__seeding) return;
    const t = $(this).val();
    if (!t) return;
    cargarClavesPorTramite(t, true, __fixed.cla || null, true);
  });

  // Overlay, cerrar y ESC
  $(window).on('click', function (ev) { if ($(ev.target).is('#modalReturnado')) hiddenReturnado(); });
  $(document).off('click.returnadoCancel').on('click.returnadoCancel', '#cancel_returnado', function (e) { e.preventDefault(); hiddenReturnado(); });
  $(document).off('click.returnadoClose').on('click.returnadoClose',
    '#modalReturnado .modal-close, #modalReturnado [data-dismiss="modal"], #modalReturnado .btn-cancel',
    function (e) { e.preventDefault(); hiddenReturnado(); }
  );
  $(document).off('keydown.returnadoEsc').on('keydown.returnadoEsc', function (e) {
    if (e.key === 'Escape' && $m.is(':visible')) hiddenReturnado();
  });

  // Refresh inicial
  [
    '#id_cat_area_1_ret,#id_cat_area_2_ret,#id_cat_area_ret,#modalReturnado select#id_cat_area',
    '#id_usuario_area_ret,#id_usuario_enlace_ret',
    '#id_cat_unidad_ret,#id_cat_coordinacion_ret',
    '#id_cat_tramite_ret,#id_cat_clave_ret'
  ].forEach(spRefresh);

  // Exponer helpers (opcional)
  window.cargarArea2PorArea1 = cargarArea2PorArea1;
  window.cargarArea3PorArea2 = cargarArea3PorArea2;
  window.actualizarCamposDerivadosPorAreaId = actualizarCamposDerivadosPorAreaId;

  /* ============ Compatibilidad con código existente ============ */
  window.__ret_open_impl__ = window.openReturnado;
  window.Returnado = window.Returnado || {};
  window.Returnado.open = function (arg) {
    if (arg && typeof arg === 'object') {
      return window.__ret_open_impl__(arg.id, arg.folGestion, arg.status);
    }
    return window.__ret_open_impl__.apply(null, arguments);
  };
  window.Returnado.save = function () {
    return (typeof window.saveReturnado === 'function') ? window.saveReturnado() : null;
  };

})();



