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
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/returnado/seed/')
      : '/letter/returnado/seed/');

  /* ========== Helpers ========== */
  function setPickerEmpty(sel) {
    const $s = (sel.jquery) ? sel : $(sel);
    $s.html('<option value="">SELECCIONE</option>');
    if ($.fn.selectpicker) $s.selectpicker('refresh');
  }
  function refreshPicker(sel) {
    if ($.fn.selectpicker) $(sel).selectpicker('refresh');
  }
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
    if ($.fn.selectpicker) $sel.selectpicker('refresh');
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

  /* ========== Dependientes por Área (como en el form) ========== */
  function actualizarDerivadosPorAreaIdRet(areaId) {
    if (!areaId) {
      [$usr,$enl,$uni,$coor,$tra,$cla].forEach(setPickerEmpty);
      return;
    }
    $.ajax({
      url: COLLECTION_AREA_URL,
      type: 'POST',
      data: { id: areaId, _token: token },
      success: function (response) {
        // Estos helpers ya existen en tu proyecto
        if (typeof foreachSelectNull === 'function') {
          foreachSelectNull(response.selectEnlace,  '#id_usuario_enlace_ret');
          foreachSelectNull(response.selectUsuario, '#id_usuario_area_ret');
          foreachSelectNull(response.selectUnidad,  '#id_cat_unidad_ret');
          foreachSelectNull(response.selectCoor,    '#id_cat_coordinacion_ret');
        }
        if (typeof foreachSelect === 'function') {
          foreachSelect(response.selectTramite, '#id_cat_tramite_ret');
        }

        setTimeout(function () {
          [$enl,$usr,$uni,$coor].forEach(($s)=>{
            if ($s.find('option').length === 0) setPickerEmpty($s);
            else refreshPicker($s);
          });

          // Auto primer trámite y preparar clave
          const t = firstOptionOrEmpty($tra);
          if (t) { $tra.val(t).selectpicker('refresh').trigger('change'); }
          else { setPickerEmpty($tra); setPickerEmpty($cla); }

          // Evitar NULL en guardado
          if (!getVal($usr)) { const u = firstOptionOrEmpty($usr); if (u) $usr.val(u).selectpicker('refresh'); }
          if (!getVal($enl)) { const e = firstOptionOrEmpty($enl); if (e) $enl.val(e).selectpicker('refresh'); }
        }, 0);
      },
      error: function () {
        [$usr,$enl,$uni,$coor,$tra,$cla].forEach(setPickerEmpty);
      }
    });
  }

  /* ========== Cadenas de áreas ========== */
  async function cargarArea2PorArea1Ret(area1Id) {
    setPickerEmpty($a2); setPickerEmpty($a3);
    if (!area1Id) return;
    try {
      const json = await postJSON(COLLECTION_AREA_URL, { by:'area2_by_area1', id_cat_area_1:Number(area1Id) });
      if (json.ok && Array.isArray(json.value)) {
        json.value.forEach(opt => $a2.append(new Option(String(opt.label ?? ''), String(opt.id ?? ''))));
      }
      refreshPicker($a2);

      // Autoselección si solo hay una opción real
      if ($a2[0].options.length === 2 && $a2[0].options[1]) {
        $a2.val($a2[0].options[1].value).selectpicker('refresh').trigger('change');
      }

      // Como en el form: al cambiar A1 también llenamos dependientes
      actualizarDerivadosPorAreaIdRet(area1Id);
    } catch (_) {
      setPickerEmpty($a2); setPickerEmpty($a3);
    }
  }

  async function cargarArea3PorArea2Ret(area2Id) {
    setPickerEmpty($a3);
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
      refreshPicker($a3);

      if ($a3[0].options.length === 2 && $a3[0].options[1]) {
        $a3.val($a3[0].options[1].value).selectpicker('refresh').trigger('change');
      }

      // Como en el form: al cambiar A2 también llenamos dependientes
      actualizarDerivadosPorAreaIdRet(area2Id);
    } catch (_) {
      setPickerEmpty($a3);
    }
  }

  // Encadenamientos
  $a1.on('change', () => { cargarArea2PorArea1Ret(getVal($a1)); setPickerEmpty($a3); });
  $a2.on('change', () => { cargarArea3PorArea2Ret(getVal($a2)); });
  $a3.on('change', () => { actualizarDerivadosPorAreaIdRet(getVal($a3)); });

  /* ========== Semilla desde el servidor (LISTA) ========== */
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

    // Dependientes
    fillPicker($usr,  S.usuarios,       L.id_usuario_area);
    fillPicker($enl,  S.enlaces,        L.id_usuario_enlace);
    fillPicker($uni,  S.unidades,       L.id_cat_unidad);
    fillPicker($coor, S.coordinaciones, L.id_cat_coordinacion);
    fillPicker($tra,  S.tramites,       L.id_cat_tramite);
    fillPicker($cla,  S.claves,         L.id_cat_clave);
  }

  /* ========== Semilla rápida cuando abrimos desde el FORM ========== */
  function cloneSelect(fromSel, toSel) {
    const $from = $(fromSel);
    const $to   = $(toSel);
    if (!$from.length || !$to.length) return;
    $to.html($from.html());
    $to.val($from.val() || '');
    if ($.fn.selectpicker) $to.selectpicker('refresh');
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
  }

  /* ========== API pública ========== */
  window.openReturnado = function (id, folGestion) {
    $('#id_correspondencia_ret').val(id || '');
    $('#name_folio_gestion_returnado').text(folGestion || '');
    $('body').addClass('modal-open-returnado');
    $m.fadeIn();

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

  /* ========== Guardado (Turnado) ========== */
  window.saveReturnado = async function () {
    const id   = $('#id_correspondencia_ret').val() || '';
    const area1 = getVal($a1);
    const area2 = getVal($a2);
    const area3 = getVal($a3);

    if (!area3) { if (window.notyfEM) notyfEM.error('Selecciona un Área destino.'); return; }

    // Evitar NOT NULL en Usuario
    if (!getVal($usr))  { const u = firstOptionOrEmpty($usr); if (u) $usr.val(u).selectpicker('refresh'); }
    if (!getVal($usr))  { if (window.notyfEM) notyfEM.error('Selecciona un Usuario.'); return; }

    if (!getVal($tra))  { if (window.notyfEM) notyfEM.error('Selecciona un Trámite.'); return; }
    if (!getVal($cla))  { if (window.notyfEM) notyfEM.error('Selecciona una Clave.'); return; }

    const payload = {
      id_tbl_correspondencia: Number(id),
      id_cat_area_1: area1 ? Number(area1) : null,
      id_cat_area_2: area2 ? Number(area2) : null,
      id_cat_area:   Number(area3),
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
        // Refrescar estatus visible si existe en el formulario principal
        if ($('#id_cat_estatus').length) {
          $('#id_cat_estatus').val(String(res.idTurnado || 6)).selectpicker('refresh');
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
