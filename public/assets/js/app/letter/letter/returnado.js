/* assets/js/app/letter/letter/returnado.js */
var token = $('meta[name="csrf-token"]').attr('content');

(function () {
  const $m  = $('#modalReturnado');

  // Selects Turnar A (modal)
  const $a1  = $('#id_cat_area_1_ret');      // CRH
  const $a2  = $('#id_cat_area_2_ret');      // CRHTOD
  const $a3  = $('#id_cat_area_ret');        // Área

  const $usr  = $('#id_usuario_area_ret');
  const $enl  = $('#id_usuario_enlace_ret');
  const $uni  = $('#id_cat_unidad_ret');
  const $coor = $('#id_cat_coordinacion_ret');
  const $tra  = $('#id_cat_tramite_ret');
  const $cla  = $('#id_cat_clave_ret');

  // Endpoints (con fallbacks)
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
  function setPickerEmpty(sel) {
    const $jq = getJQ();
    const $s = (sel && sel.jquery) ? sel : $jq(sel);
    if (!$s || !$s.length) return;
    $s.html('<option value="">SELECCIONE</option>');
    spRefresh($s);
  }
  function hasRealOptions($sel) {
    return $sel.find('option').not('[value=""]').length > 0;
  }
  function refreshPicker(sel) { spRefresh(sel); }
  const getVal = ($sel) => ($sel.val() ? String($sel.val()) : '');
  function firstOptionOrEmpty($sel) {
    const $opts = $sel.find('option').not('[value=""]');
    return $opts.length ? String($opts.first().val()) : '';
  }
  function fillPicker($sel, rows, selectedId) {
    const sid = String(selectedId || '');
    $sel.html('<option value="">SELECCIONE</option>');
    (rows || []).forEach(r => {
      const v = String(r.id ?? r.value ?? '');
      const t = String(r.label ?? r.descripcion ?? r.text ?? '');
      const opt = new Option(t, v, false, v === sid);
      $sel.append(opt);
    });
    spRefresh($sel);
  }

  async function postJSON(url, body) {
    const resp = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
      },
      body: JSON.stringify(body),
    });
    if (!resp.ok) throw new Error('HTTP ' + resp.status);
    return resp.json();
  }

  /* ================================================
     Helper: reset dependientes (+ opcional deshabilitar)
     ================================================ */
  function resetDependents(disable = true) {
    [$usr,$enl,$uni,$coor,$tra,$cla].forEach(($s)=>{
      setPickerEmpty($s);
      if (disable) spDisable($s);
    });
  }

  /* =========================================================
     Repoblar dependientes (Usuario/Enlace/Unidad/Coord/Trámite)
     usando /letter/collection/collectionArea
     ========================================================= */
  function actualizarDerivadosPorAreaIdRet(areaId) {
    if (!areaId) {
      resetDependents(true);
      return;
    }
    $.ajax({
      url: COLLECTION_AREA_URL,
      type: 'POST',
      data: { id: areaId, _token: token },
      success: function (response) {
        // Poblado con tus helpers
        if (typeof foreachSelectNull === 'function') {
          foreachSelectNull(response.selectEnlace,  '#id_usuario_enlace_ret');
          foreachSelectNull(response.selectUsuario, '#id_usuario_area_ret');
          foreachSelectNull(response.selectUnidad,  '#id_cat_unidad_ret');
          foreachSelectNull(response.selectCoor,    '#id_cat_coordinacion_ret');
        }
        if (typeof foreachSelect === 'function') {
          foreachSelect(response.selectTramite, '#id_cat_tramite_ret');
        }

        // Habilitar/deshabilitar según haya opciones
        [$usr,$enl,$uni,$coor,$tra,$cla].forEach(($s)=>{
          if (hasRealOptions($s)) spEnable($s);
          else spDisable($s);
        });

        // Auto primer Trámite y disparar change para Claves
        setTimeout(function () {
          const t = firstOptionOrEmpty($tra);
          if (t) { $tra.val(t); spEnable($tra); spRefresh($tra); $tra.trigger('change'); }
          else { setPickerEmpty($tra); spDisable($tra); setPickerEmpty($cla); spDisable($cla); }

          // Evitar NOT NULL en guardado
          if (!getVal($usr)) { const u = firstOptionOrEmpty($usr); if (u) { $usr.val(u); spEnable($usr); spRefresh($usr); } }
          if (!getVal($enl)) { const e = firstOptionOrEmpty($enl); if (e) { $enl.val(e); spEnable($enl); spRefresh($enl); } }
        }, 0);
      },
      error: function () {
        resetDependents(true);
      }
    });
  }

  /* =========================================================
     Cadenas de áreas (Área2 por Área1, Área3 por Área2)
     ========================================================= */
  async function cargarArea2PorArea1Ret(area1Id) {
    setPickerEmpty($a2); setPickerEmpty($a3);
    spDisable($a2); spDisable($a3);
    if (!area1Id) return;

    try {
      const json = await postJSON(COLLECTION_AREA_URL, { by:'area2_by_area1', id_cat_area_1:Number(area1Id) });
      if (json.ok && Array.isArray(json.value)) {
        json.value.forEach(opt => $a2.append(new Option(String(opt.label ?? ''), String(opt.id ?? ''))));
      }
      if (hasRealOptions($a2)) {
        spEnable($a2);
        spRefresh($a2);
      } else {
        spDisable($a2);
      }

      // Autoselección si solo hay una opción real
      if ($a2[0].options.length === 2 && $a2[0].options[1]) {
        $a2.val($a2[0].options[1].value); spEnable($a2); spRefresh($a2); $a2.trigger('change');
      }

      // Igual que en el form: al cambiar A1, repoblar dependientes
      actualizarDerivadosPorAreaIdRet(area1Id);
    } catch (_) {
      setPickerEmpty($a2); setPickerEmpty($a3);
      spDisable($a2); spDisable($a3);
    }
  }

  async function cargarArea3PorArea2Ret(area2Id) {
    setPickerEmpty($a3);
    spDisable($a3);
    if (!area2Id) return;

    try {
      const json = await postJSON(COLLECTION_AREA_URL, {
        by:'area3_by_area2',
        id_cat_area_2:Number(area2Id),
        include_inactive: !!(window.LETTER && window.LETTER.includeInactiveArea3),
      });
      if (json.ok && Array.isArray(json.value)) {
        json.value.forEach(opt => $a3.append(new Option(String(opt.label ?? ''), String(opt.id ?? ''))));
      }
      if (hasRealOptions($a3)) {
        spEnable($a3);
        spRefresh($a3);
      } else {
        spDisable($a3);
      }

      // Autoselección si solo hay una opción real
      if ($a3[0].options.length === 2 && $a3[0].options[1]) {
        $a3.val($a3[0].options[1].value); spEnable($a3); spRefresh($a3); $a3.trigger('change');
      }

      // Igual que en el form: al cambiar A2, repoblar dependientes
      actualizarDerivadosPorAreaIdRet(area2Id);
    } catch (_) {
      setPickerEmpty($a3);
      spDisable($a3);
    }
  }

  // Encadenamientos en el modal
  $a1.on('change', () => {
    const v = getVal($a1);
    if (!v) {
      setPickerEmpty($a2); setPickerEmpty($a3);
      spDisable($a2); spDisable($a3);
      resetDependents(true);
      return;
    }
    cargarArea2PorArea1Ret(v);
  });

  $a2.on('change', () => {
    const v = getVal($a2);
    if (!v) {
      setPickerEmpty($a3);
      spDisable($a3);
      resetDependents(true);
      return;
    }
    cargarArea3PorArea2Ret(v);
  });

  $a3.on('change', () => {
    const v = getVal($a3);
    if (!v) { resetDependents(true); return; }
    actualizarDerivadosPorAreaIdRet(v);
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

    // Estado de habilitación inicial (como en el form)
    spEnable($a1);
    if (hasRealOptions($a2)) spEnable($a2); else spDisable($a2);
    if (hasRealOptions($a3)) spEnable($a3); else spDisable($a3);

    // Dependientes
    fillPicker($usr,  S.usuarios,       L.id_usuario_area);
    fillPicker($enl,  S.enlaces,        L.id_usuario_enlace);
    fillPicker($uni,  S.unidades,       L.id_cat_unidad);
    fillPicker($coor, S.coordinaciones, L.id_cat_coordinacion);
    fillPicker($tra,  S.tramites,       L.id_cat_tramite);
    fillPicker($cla,  S.claves,         L.id_cat_clave);

    // Habilitar dependientes si hay opciones
    [$usr,$enl,$uni,$coor,$tra,$cla].forEach(($s)=>{
      if (hasRealOptions($s)) spEnable($s); else spDisable($s);
    });
  }

  /* =========================================================
     Semilla rápida cuando abrimos desde el FORM principal
     ========================================================= */
  function cloneSelect(fromSel, toSel) {
    const $from = $(fromSel);
    const $to   = $(toSel);
    if (!$from.length || !$to.length) return;
    $to.html($from.html());
    $to.val($from.val() || '');
    spRefresh($to);
  }

  function seedFromMainForm() {
    // Cadenas de áreas
    cloneSelect('#id_cat_area_1', '#id_cat_area_1_ret');
    cloneSelect('#id_cat_area_2', '#id_cat_area_2_ret');
    cloneSelect('#id_cat_area',   '#id_cat_area_ret');

    // Habilitación inicial como en el form
    spEnable($a1);
    if (hasRealOptions($a2)) spEnable($a2); else spDisable($a2);
    if (hasRealOptions($a3)) spEnable($a3); else spDisable($a3);

    // Dependientes
    cloneSelect('#id_usuario_area',    '#id_usuario_area_ret');
    cloneSelect('#id_usuario_enlace',  '#id_usuario_enlace_ret');
    cloneSelect('#id_cat_unidad',      '#id_cat_unidad_ret');
    cloneSelect('#id_cat_coordinacion','#id_cat_coordinacion_ret');
    cloneSelect('#id_cat_tramite',     '#id_cat_tramite_ret');
    cloneSelect('#id_cat_clave',       '#id_cat_clave_ret');

    [$usr,$enl,$uni,$coor,$tra,$cla].forEach(($s)=>{
      if (hasRealOptions($s)) spEnable($s); else spDisable($s);
    });
  }

  /* =========================================================
     API pública del modal
     ========================================================= */
  window.openReturnado = function (id, folGestion) {
    $('#id_correspondencia_ret').val(id || '');
    $('#name_folio_gestion_returnado').text(folGestion || '');
    $('body').addClass('modal-open-returnado');
    $m.fadeIn();

    // Asegura que los .selectpicker queden inicializados
    spInitIn('#modalReturnado');

    // Si estamos en el FORM principal, clonamos de inmediato
    if ($('#id_cat_area').length) {
      seedFromMainForm();
      return;
    }
    // Si venimos desde la LISTA, pedir semilla al servidor
    seedFromServer(id).catch(() => {
      if (window.notyfEM) notyfEM.error('No se pudo cargar la información del turnado.');
    });
  };

  window.hiddenReturnado = function () {
    $m.fadeOut();
    $('body').removeClass('modal-open-returnado');
  };

  /* =========================================================
     Guardado (Turnado) — usa el nivel más profundo disponible como destino
     ========================================================= */
  window.saveReturnado = async function () {
    const id    = $('#id_correspondencia_ret').val() || '';
    const area1 = getVal($a1); // CRH
    const area2 = getVal($a2); // CRHTOD
    const area3 = getVal($a3); // Área (nivel 3)

    // Si hay Área, se usa; si no, CRHTOD; si no, CRH
    const destino = area3 || area2 || area1;

    if (!destino) {
      if (window.notyfEM) notyfEM.error('Selecciona CRH, CRHTOD o Área como destino.');
      return;
    }

    // Evitar NOT NULL en Usuario
    if (!getVal($usr))  { const u = firstOptionOrEmpty($usr); if (u) { $usr.val(u); spEnable($usr); spRefresh($usr); } }
    if (!getVal($usr))  { if (window.notyfEM) notyfEM.error('Selecciona un Usuario.'); return; }

    if (!getVal($tra))  { if (window.notyfEM) notyfEM.error('Selecciona un Trámite.'); return; }
    if (!getVal($cla))  { if (window.notyfEM) notyfEM.error('Selecciona una Clave.');  return; }

    const payload = {
      id_tbl_correspondencia: Number(id),

      // Guardamos toda la cadena seleccionada
      id_cat_area_1: area1 ? Number(area1) : null,
      id_cat_area_2: area2 ? Number(area2) : null,

      // Destino real
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
      const res = await postJSON(TURNAR_SAVE_URL, payload);
      if (res && res.ok) {
        if ($('#id_cat_estatus').length) {
          $('#id_cat_estatus').val(String(res.idTurnado || 6));
          spRefresh('#id_cat_estatus');
        }
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
  ].forEach(refreshPicker);
})();
