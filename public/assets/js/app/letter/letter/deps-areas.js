// assets/js/app/letter/letter/deps-areas.js
// -------------------------------------------------------------
// Flujo ORIGINAL:
// - Cambiar CRH => carga Área 2 y AUTOLLENA Usuario/Enlace/Unidad/Coord/Trámite
// - Cambiar CRHTOD => carga Área 3 y AUTOLLENA Usuario/Enlace/Unidad/Coord/Trámite
// - Cambiar Área => AUTOLLENA Usuario/Enlace/Unidad/Coord/Trámite
// - Siempre auto-selecciona el primer Trámite y dispara 'change' (para cargar Claves)
// Extra: detección Returnado (cualquiera de las 3 áreas) y bloqueo solo si aplica.
// -------------------------------------------------------------

document.addEventListener('DOMContentLoaded', function () {
  const $area1 = document.getElementById('id_cat_area_1'); // CRH
  const $area2 = document.getElementById('id_cat_area_2'); // CRHTOD
  const $area3 = document.getElementById('id_cat_area');   // Área final
  if (!$area1 || !$area2 || !$area3) return;

  const tokenEl = document.querySelector('meta[name="csrf-token"]');
  const token = tokenEl ? tokenEl.getAttribute('content') : '';

  const COLLECTION_AREA_URL =
    (window.LETTER && window.LETTER.collectionAreaUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionArea')
      : '/letter/collection/collectionArea');

  /* ===== Helpers selectpicker ===== */
  function setPickerEmpty(selector) {
    $(selector).html('<option value="">SELECCIONE</option>').selectpicker('refresh');
  }
  function refreshPicker(id) {
    if (typeof $ !== 'undefined' && $.fn.selectpicker) {
      $(id).selectpicker('refresh');
    }
  }
  function getVal(el) { return (el && el.value) ? String(el.value) : ''; }

  /* ===== Bloqueo/Desbloqueo Turnar A por Returnado ===== */
  const TURNAR_SELECTS = [
    '#id_cat_area_1', '#id_cat_area_2', '#id_cat_area',
    '#id_usuario_area', '#id_usuario_enlace',
    '#id_cat_unidad', '#id_cat_coordinacion',
    '#id_cat_tramite', '#id_cat_clave'
  ];
  let __lockedByReturnado = false;

  function lockTurnarAAndSetReturnado(idReturnado) {
    if (__lockedByReturnado) return;
    __lockedByReturnado = true;

    if (typeof applyReturnadoMode === 'function') {
      // Usa el modo centralizado (form.js)
      applyReturnadoMode(true, idReturnado);
      return;
    }
    // Fallback si no existe applyReturnadoMode
    TURNAR_SELECTS.forEach((s)=> $(s).prop('disabled', true));
    TURNAR_SELECTS.forEach((s)=> refreshPicker(s));
    $('#id_cat_estatus').val(String(idReturnado || 8)).prop('disabled', true).selectpicker('refresh');
  }

  function unlockTurnarAIfLocked() {
    if (!__lockedByReturnado) return;
    __lockedByReturnado = false;

    if (typeof applyReturnadoMode === 'function') {
      applyReturnadoMode(false);
      return;
    }
    // Fallback si no existe applyReturnadoMode
    TURNAR_SELECTS.forEach((s)=> $(s).prop('disabled', false));
    TURNAR_SELECTS.forEach((s)=> refreshPicker(s));
    $('#id_cat_estatus').prop('disabled', false).selectpicker('refresh');
  }

  /* ===== POST JSON a LetterC@collectionArea ===== */
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

  /* =========================================================
     Repoblado de dependientes (Usuario/Enlace/Unidad/Coord/Trámite)
     → usa el modo LEGACY del backend: POST {id:<areaId>}
     → AUTOS ELECCIÓN del PRIMER Trámite (dispara change para Claves)
     ========================================================= */
  function actualizarCamposDerivadosPorAreaId(areaId) {
    // Si no hay área -> todo a "SELECCIONE" y limpiar claves
    if (!areaId) {
      setPickerEmpty('#id_usuario_area');
      setPickerEmpty('#id_usuario_enlace');
      setPickerEmpty('#id_cat_unidad');
      setPickerEmpty('#id_cat_coordinacion');
      setPickerEmpty('#id_cat_tramite');
      setPickerEmpty('#id_cat_clave');
      if (typeof clearClaveData === 'function') clearClaveData();
      if (typeof setClaveInNuSystem === 'function') setClaveInNuSystem('-');
      return;
    }

    $.ajax({
      url: URL_DEFAULT.concat('/letter/collection/collectionArea'),
      type: 'POST',
      data: { id: areaId, _token: token },
      success: function (response) {
        if (typeof foreachSelectNull === 'function') {
          foreachSelectNull(response.selectEnlace,  '#id_usuario_enlace');
          foreachSelectNull(response.selectUsuario, '#id_usuario_area');
          foreachSelectNull(response.selectUnidad,  '#id_cat_unidad');
          foreachSelectNull(response.selectCoor,    '#id_cat_coordinacion');
        }
        if (typeof foreachSelect === 'function') {
          foreachSelect(response.selectTramite, '#id_cat_tramite');
        }

        // Refrescar y asegurar "SELECCIONE" si quedaron vacíos
        setTimeout(function () {
          ['#id_usuario_enlace','#id_usuario_area','#id_cat_unidad','#id_cat_coordinacion']
            .forEach((selector) => {
              const $sel = $(selector);
              if ($sel.find('option').length === 0) setPickerEmpty(selector);
              else $sel.selectpicker('refresh');
            });

          // === AUTOS ELECCIÓN PRIMER TRÁMITE + disparo change ===
          const $tram = $('#id_cat_tramite');
          const tramOptions = $tram.find('option').not('[value=""]');
          if (tramOptions.length > 0) {
            const firstVal = tramOptions.first().val();
            $tram.val(firstVal).selectpicker('refresh').trigger('change'); // carga Claves en select.js
          } else {
            // Sin trámites -> Trámite y Clave a "SELECCIONE"
            setPickerEmpty('#id_cat_tramite');
            setPickerEmpty('#id_cat_clave');
            if (typeof clearClaveData === 'function') clearClaveData();
          }
        }, 0);

        // Encabezado/num_turno_sistema (si lo regresa tu backend)
        if (typeof setClaveInNuSystem === 'function') {
          setClaveInNuSystem(response.clave || '-');
        }
        if (typeof clearClaveData === 'function') clearClaveData();
      },
      error: function () {
        // En error, dejar todo coherente
        setPickerEmpty('#id_usuario_area');
        setPickerEmpty('#id_usuario_enlace');
        setPickerEmpty('#id_cat_unidad');
        setPickerEmpty('#id_cat_coordinacion');
        setPickerEmpty('#id_cat_tramite');
        setPickerEmpty('#id_cat_clave');
        if (typeof clearClaveData === 'function') clearClaveData();
      }
    });
  }

  /* ===== Área 2 por Área 1 ===== */
  async function cargarArea2PorArea1(area1Id, selectedId = null) {
    resetArea2();
    if (!area1Id) return;
    try {
      const json = await postJSON(COLLECTION_AREA_URL, {
        by: 'area2_by_area1',
        id_cat_area_1: area1Id,
      });
      if (json.ok && Array.isArray(json.value) && json.value.length) {
        json.value.forEach((opt) => {
          const option = document.createElement('option');
          option.value = String(opt.id ?? '');
          option.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) option.selected = true;
          $area2.appendChild(option);
        });
        refreshPicker('#id_cat_area_2');
      } else {
        resetArea2();
      }
    } catch (e) {
      console.error('AREA2_LOAD_ERROR:', e);
      resetArea2();
    }
  }

  /* ===== Área 3 por Área 2 ===== */
  async function cargarArea3PorArea2(area2Id, selectedId = null) {
    resetArea3();
    if (!area2Id) return;
    try {
      const json = await postJSON(COLLECTION_AREA_URL, {
        by: 'area3_by_area2',
        id_cat_area_2: area2Id,
        include_inactive: !!(window.LETTER && window.LETTER.includeInactiveArea3),
      });
      if (json.ok && Array.isArray(json.value) && json.value.length) {
        json.value.forEach((opt) => {
          const option = document.createElement('option');
          option.value = String(opt.id ?? '');
          option.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) option.selected = true;
          $area3.appendChild(option);
        });
        refreshPicker('#id_cat_area');
      } else {
        resetArea3();
      }
    } catch (e) {
      console.error('AREA3_LOAD_ERROR:', e);
      resetArea3();
    }
  }

  // ===== helpers limpiar selects encadenados =====
  function resetArea2() { setPickerEmpty('#id_cat_area_2'); }
  function resetArea3() { setPickerEmpty('#id_cat_area'); }

  /* ===== Detección Returnado (CRH / CRHTOD / Área) =====
     Espera del backend (cualquiera de estas dos formas):
     A) { ok:true, any_only_returnado:true, idReturnado:8 }
     B) { ok:true, only:{a1:true|false, a2:true|false, a3:true|false}, idReturnado:8 }
     Solo bloquea si el área seleccionada está relacionada **únicamente** con Returnado.
  */
  async function checkReturnadoAny() {
    try {
      const a1 = getVal($area1) ? Number(getVal($area1)) : 0;
      const a2 = getVal($area2) ? Number(getVal($area2)) : 0;
      const a3 = getVal($area3) ? Number(getVal($area3)) : 0;

      // Si no hay selección, nunca bloquear
      if (!a1 && !a2 && !a3) {
        unlockTurnarAIfLocked();
        return;
      }

      const res = await postJSON(COLLECTION_AREA_URL, {
        scope: 'returnado_flag_by_any',
        id_cat_area_1: a1,
        id_cat_area_2: a2,
        id_cat_area:   a3
      });

      let shouldBlock = false;
      if (res && res.ok) {
        if (typeof res.any_only_returnado === 'boolean') {
          // Modo simple (agrupado)
          shouldBlock = !!res.any_only_returnado;
        } else if (res.only && typeof res.only === 'object') {
          // Modo por campo (fino)
          const selectedKeys = [];
          if (a1) selectedKeys.push('a1');
          if (a2) selectedKeys.push('a2');
          if (a3) selectedKeys.push('a3');
          shouldBlock = selectedKeys.some(k => res.only[k] === true);
        }
      }

      if (shouldBlock) {
        lockTurnarAAndSetReturnado(res && res.idReturnado ? res.idReturnado : 8);
      } else {
        unlockTurnarAIfLocked();
      }
    } catch (e) {
      // En error nunca bloquees por defecto
      unlockTurnarAIfLocked();
    }
  }

  /* ===== Eventos ===== */
  $area1.addEventListener('change', function (e) {
    const area1Id = e.target.value || '';
    // Encadenado de áreas
    cargarArea2PorArea1(area1Id, null);
    resetArea3(); // al cambiar Área 1, limpia Área 3
    // Dependientes + Trámite (autoselección primer ítem)
    actualizarCamposDerivadosPorAreaId(area1Id);
    // Returnado (CRH)
    checkReturnadoAny();
  });

  $area2.addEventListener('change', function (e) {
    const area2Id = e.target.value || '';
    // Encadenado de áreas
    cargarArea3PorArea2(area2Id, null);
    // Dependientes + Trámite (autoselección primer ítem)
    actualizarCamposDerivadosPorAreaId(area2Id);
    // Returnado (CRHTOD)
    checkReturnadoAny();
  });

  // Nota: cuando cambias Área 3, tu select.js ya actualiza
  // Usuario/Enlace/Unidad/Coordinación/Trámite/Clave correctamente.
  $area3.addEventListener('change', function (e) {
    const area3Id = e.target.value || '';
    actualizarCamposDerivadosPorAreaId(area3Id);
    // Returnado (Área)
    checkReturnadoAny();
  });

  /* ===== Precarga en edición ===== */
  const area1Inicial = window.LETTER?.initials?.area1 || null;
  const area2Inicial = window.LETTER?.initials?.area2 || null;
  const area3Inicial = window.LETTER?.initials?.area3 || null;

  if (area1Inicial) {
    // Cargar cadena Área2/Área3
    cargarArea2PorArea1(area1Inicial, area2Inicial).then(() => {
      const a2 = $area2.value || area2Inicial;
      if (a2) {
        cargarArea3PorArea2(a2, area3Inicial);
      }
    });
    // Precargar dependientes + Trámite (auto 1º) con base en Área 1
    actualizarCamposDerivadosPorAreaId(area1Inicial);
    // Checar Returnado en precarga (si hay selección)
    checkReturnadoAny();
  } else {
    // create -> vacíos
    resetArea2();
    resetArea3();
    setPickerEmpty('#id_usuario_area');
    setPickerEmpty('#id_usuario_enlace');
    setPickerEmpty('#id_cat_unidad');
    setPickerEmpty('#id_cat_coordinacion');
    setPickerEmpty('#id_cat_tramite');
    setPickerEmpty('#id_cat_clave');
    // No bloquear nada al inicio
    unlockTurnarAIfLocked();
  }
});
