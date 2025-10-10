// assets/js/app/letter/letter/deps-areas.js
// -------------------------------------------------------------
// Flujo ORIGINAL restaurado:
// - Al cambiar CRH => carga Área 2 y AUTOLLENA Usuario/Enlace/Unidad/Coord/Trámite
// - Al cambiar CRHTOD => carga Área 3 y AUTOLLENA Usuario/Enlace/Unidad/Coord/Trámite
// - Al cambiar Área => AUTOLLENA Usuario/Enlace/Unidad/Coord/Trámite
// - Siempre auto-selecciona el primer Trámite y dispara 'change' (para cargar Claves)
// Extra: detección de Returnado (cualquiera de las 3 áreas) y bloqueo Turnar A.
// -------------------------------------------------------------

document.addEventListener('DOMContentLoaded', function () {
  const $area1 = document.getElementById('id_cat_area_1');
  const $area2 = document.getElementById('id_cat_area_2');
  const $area3 = document.getElementById('id_cat_area');

  if (!$area1 || !$area2 || !$area3) return;

  const tokenEl = document.querySelector('meta[name="csrf-token"]');
  const token   = tokenEl ? tokenEl.getAttribute('content') : '';

  const COLLECTION_AREA_URL =
    (window.LETTER && window.LETTER.collectionAreaUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionArea')
      : '/letter/collection/collectionArea');

  /* ===================== helpers selectpicker ===================== */
  function setPickerEmpty(selector) {
    if (typeof $ === 'undefined') return;
    $(selector).html('<option value="">SELECCIONE</option>').selectpicker('refresh');
  }
  function refreshPicker(selector) {
    if (typeof $ !== 'undefined' && $.fn.selectpicker) $(selector).selectpicker('refresh');
  }
  function getVal(el) { return (el && el.value) ? String(el.value) : ''; }

  /* ===================== POST JSON ===================== */
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
     Repoblar dependientes (Usuario/Enlace/Unidad/Coord/Trámite)
     → usa el modo LEGACY del backend: POST {id:<areaId>}
     ========================================================= */
  function actualizarCamposDerivadosPorAreaId(areaId) {
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
      url: COLLECTION_AREA_URL,
      type: 'POST',
      data: { id: areaId, _token: token }, // <— LEGACY compatible
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

        setTimeout(function () {
          ['#id_usuario_enlace','#id_usuario_area','#id_cat_unidad','#id_cat_coordinacion']
            .forEach((selector) => {
              const $sel = $(selector);
              if ($sel.find('option').length === 0) setPickerEmpty(selector);
              else $sel.selectpicker('refresh');
            });

          const $tram = $('#id_cat_tramite');
          const tramOptions = $tram.find('option').not('[value=""]');
          if (tramOptions.length > 0) {
            const firstVal = tramOptions.first().val();
            $tram.val(firstVal).selectpicker('refresh').trigger('change'); // carga Claves en select.js
          } else {
            setPickerEmpty('#id_cat_tramite');
            setPickerEmpty('#id_cat_clave');
            if (typeof clearClaveData === 'function') clearClaveData();
          }
        }, 0);

        if (typeof setClaveInNuSystem === 'function') {
          setClaveInNuSystem(response.clave || '-');
        }
        if (typeof clearClaveData === 'function') clearClaveData();
      },
      error: function () {
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

  /* ===================== cadenas de áreas (A2 por A1 / A3 por A2) ===================== */
  async function cargarArea2PorArea1(area1Id, selectedId = null) {
    setPickerEmpty('#id_cat_area_2');
    setPickerEmpty('#id_cat_area');
    if (!area1Id) return;
    try {
      const json = await postJSON(COLLECTION_AREA_URL, {
        by: 'area2_by_area1',
        id_cat_area_1: Number(area1Id),
      });

      let count = 0;
      if (json.ok && Array.isArray(json.value)) {
        json.value.forEach((opt) => {
          const option = document.createElement('option');
          option.value = String(opt.id ?? '');
          option.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) option.selected = true;
          $area2.appendChild(option);
          count++;
        });
      }
      refreshPicker('#id_cat_area_2');

      // Autoselección si hay una sola opción
      if (count === 1 && $area2.options[1]) {
        $area2.value = $area2.options[1].value;
        refreshPicker('#id_cat_area_2');
        $area2.dispatchEvent(new Event('change'));
      }
    } catch (_) {
      setPickerEmpty('#id_cat_area_2');
      setPickerEmpty('#id_cat_area');
    }
  }

  async function cargarArea3PorArea2(area2Id, selectedId = null) {
    setPickerEmpty('#id_cat_area');
    if (!area2Id) return;
    try {
      const json = await postJSON(COLLECTION_AREA_URL, {
        by: 'area3_by_area2',
        id_cat_area_2: Number(area2Id),
        include_inactive: !!(window.LETTER && window.LETTER.includeInactiveArea3),
      });

      let count = 0;
      if (json.ok && Array.isArray(json.value)) {
        json.value.forEach((opt) => {
          const option = document.createElement('option');
          option.value = String(opt.id ?? '');
          option.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) option.selected = true;
          $area3.appendChild(option);
          count++;
        });
      }
      refreshPicker('#id_cat_area');

      // Autoselección si hay una sola opción
      if (count === 1 && $area3.options[1]) {
        $area3.value = $area3.options[1].value;
        refreshPicker('#id_cat_area');
        $area3.dispatchEvent(new Event('change')); // y esto llenará dependientes
      }
    } catch (_) {
      setPickerEmpty('#id_cat_area');
    }
  }

  /* ===================== Returnado: cualquiera de las 3 áreas ===================== */
  async function checkReturnadoAny() {
    try {
      const a1 = getVal($area1) ? Number(getVal($area1)) : 0;
      const a2 = getVal($area2) ? Number(getVal($area2)) : 0;
      const a3 = getVal($area3) ? Number(getVal($area3)) : 0;

      const res = await postJSON(COLLECTION_AREA_URL, {
        scope: 'returnado_flag_by_any',
        id_cat_area_1: a1,
        id_cat_area_2: a2,
        id_cat_area:   a3
      });

      if (res && res.ok && res.any_only_returnado) {
        if (typeof applyReturnadoMode === 'function') {
          applyReturnadoMode(true, res.idReturnado);
        } else {
          // Fallback duro
          ['#id_cat_area_1','#id_cat_area_2','#id_cat_area',
           '#id_usuario_area','#id_usuario_enlace',
           '#id_cat_unidad','#id_cat_coordinacion',
           '#id_cat_tramite','#id_cat_clave'
          ].forEach((s)=>$(s).prop('disabled',true).selectpicker('refresh'));
          $('#id_cat_estatus').val(String(res.idReturnado)).prop('disabled',true).selectpicker('refresh');
        }
      } else {
        if (typeof applyReturnadoMode === 'function') applyReturnadoMode(false);
      }
    } catch (_) {
      if (typeof applyReturnadoMode === 'function') applyReturnadoMode(false);
    }
  }

  /* ===================== eventos ===================== */
  $area1.addEventListener('change', async function (e) {
    const area1Id = e.target.value || '';
    await cargarArea2PorArea1(area1Id, null);
    actualizarCamposDerivadosPorAreaId(area1Id);   // ← autollenado como antes
    await checkReturnadoAny();
  });

  $area2.addEventListener('change', async function (e) {
    const area2Id = e.target.value || '';
    await cargarArea3PorArea2(area2Id, null);
    actualizarCamposDerivadosPorAreaId(area2Id);   // ← autollenado como antes
    await checkReturnadoAny();
  });

  $area3.addEventListener('change', async function (e) {
    const area3Id = e.target.value || '';
    actualizarCamposDerivadosPorAreaId(area3Id);   // ← autollenado como antes
    await checkReturnadoAny();
  });

  /* ===================== precarga (edición) ===================== */
  const area1Inicial = window.LETTER?.initials?.area1 || null;
  const area2Inicial = window.LETTER?.initials?.area2 || null;
  const area3Inicial = window.LETTER?.initials?.area3 || null;

  (async function precarga() {
    if (area1Inicial) {
      await cargarArea2PorArea1(area1Inicial, area2Inicial);
      const a2 = getVal($area2) || area2Inicial;
      if (a2) {
        await cargarArea3PorArea2(a2, area3Inicial);
        const a3 = getVal($area3) || area3Inicial;
        if (a3) actualizarCamposDerivadosPorAreaId(a3);
      } else {
        actualizarCamposDerivadosPorAreaId(area1Inicial);
      }
      await checkReturnadoAny();
    } else {
      setPickerEmpty('#id_cat_area_2');
      setPickerEmpty('#id_cat_area');
      setPickerEmpty('#id_usuario_area');
      setPickerEmpty('#id_usuario_enlace');
      setPickerEmpty('#id_cat_unidad');
      setPickerEmpty('#id_cat_coordinacion');
      setPickerEmpty('#id_cat_tramite');
      setPickerEmpty('#id_cat_clave');
    }
  })();
});
